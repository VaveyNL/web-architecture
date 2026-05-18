from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from datetime import datetime
import aiomysql
from database import get_db
from routers import comments
from routers import ws

app = FastAPI(title='Boardy API', version='0.3.0')

app.add_middleware(
    CORSMiddleware,
    allow_origins=['*'],
    allow_methods=['*'],
    allow_headers=['*'],
)

app.include_router(comments.router)
app.include_router(ws.router)


@app.post('/internal/broadcast')
async def internal_broadcast(request: Request):
    data = await request.json()
    await ws.manager.broadcast({'type': 'new_post', 'post': data})
    return {'ok': True}


@app.get('/api/status')
async def status():
    return {'status': 'ok', 'time': str(datetime.now())}


@app.get('/api/messages')
async def get_messages():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT posts.body AS message, users.name, '
            'posts.created_at FROM posts '
            'JOIN users ON posts.author_id = users.id '
            'ORDER BY posts.created_at DESC'
        )
        messages = await cur.fetchall()
    conn.close()
    for m in messages:
        m['created_at'] = str(m['created_at'])
    return {'messages': messages, 'count': len(messages)}


@app.get('/api/users')
async def get_users():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT id, name, email, created_at FROM users'
        )
        users = await cur.fetchall()
    conn.close()
    for u in users:
        u['created_at'] = str(u['created_at'])
    return {'users': users, 'count': len(users)}
