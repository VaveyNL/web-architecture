from fastapi import APIRouter, HTTPException, Depends
from pydantic import BaseModel, Field
import aiomysql
from database import get_db
from auth import get_current_user
from routers.ws import manager

router = APIRouter()


class CommentCreate(BaseModel):
    body: str = Field(..., min_length=1, max_length=2000)
    author_name: str = Field(..., min_length=1, max_length=255)


class CommentUpdate(BaseModel):
    body: str = Field(..., min_length=1, max_length=2000)


@router.get('/api/posts/{post_id}/comments')
async def list_comments(post_id: int):
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT id, post_id, author_id, author_name, body, created_at '
            'FROM comments WHERE post_id=%s ORDER BY created_at',
            (post_id,)
        )
        rows = await cur.fetchall()
    conn.close()
    for r in rows:
        r['created_at'] = str(r['created_at'])
    return {'items': rows, 'count': len(rows)}


@router.post('/api/posts/{post_id}/comments', status_code=201)
async def create_comment(post_id: int, data: CommentCreate,
                         user=Depends(get_current_user)):
    author_id = int(user['sub'])
    conn = await get_db()
    async with conn.cursor() as cur:
        await cur.execute(
            'INSERT INTO comments (post_id, author_id, author_name, body) '
            'VALUES (%s,%s,%s,%s)',
            (post_id, author_id, data.author_name, data.body)
        )
        await conn.commit()
        new_id = cur.lastrowid
    conn.close()

    comment = {
        'id': new_id,
        'post_id': post_id,
        'author_id': author_id,
        'author_name': data.author_name,
        'body': data.body,
    }
    await manager.broadcast({'type': 'new_comment', 'comment': comment})
    return comment


@router.put('/api/comments/{comment_id}')
async def update_comment(comment_id: int, data: CommentUpdate,
                         user=Depends(get_current_user)):
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute('SELECT * FROM comments WHERE id=%s', (comment_id,))
        existing = await cur.fetchone()
        if not existing:
            conn.close()
            raise HTTPException(status_code=404, detail='Not found')
        if existing['author_id'] != int(user['sub']):
            conn.close()
            raise HTTPException(status_code=403, detail='Not your comment')
        await cur.execute(
            'UPDATE comments SET body=%s WHERE id=%s',
            (data.body, comment_id)
        )
        await conn.commit()
    conn.close()

    await manager.broadcast({
        'type': 'update_comment',
        'comment': {'id': comment_id, 'body': data.body},
    })
    return {'id': comment_id, 'body': data.body}


@router.delete('/api/comments/{comment_id}')
async def delete_comment(comment_id: int,
                         user=Depends(get_current_user)):
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute('SELECT * FROM comments WHERE id=%s', (comment_id,))
        existing = await cur.fetchone()
        if not existing:
            conn.close()
            raise HTTPException(status_code=404, detail='Not found')
        if existing['author_id'] != int(user['sub']):
            conn.close()
            raise HTTPException(status_code=403, detail='Not your comment')
        await cur.execute('DELETE FROM comments WHERE id=%s', (comment_id,))
        await conn.commit()
    conn.close()

    await manager.broadcast({'type': 'delete_comment', 'comment_id': comment_id})
    return {'ok': True}
