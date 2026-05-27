from contextlib import asynccontextmanager
import asyncio
import json
import os
 
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import redis.asyncio as aioredis
import aiomysql
 
from database import get_db
from routers import comments, ws
 
# Адрес Redis: в Docker - имя контейнера 'redis', локально - 127.0.0.1
REDIS_URL = os.environ.get(
    'REDIS_URL',
    f"redis://{os.environ.get('REDIS_HOST', '127.0.0.1')}:6379",
)
 
 
async def redis_subscriber():
    """Фоновая задача: слушает Redis, реагирует на события."""
    r = aioredis.from_url(REDIS_URL)
    pubsub = r.pubsub()
    await pubsub.subscribe('new_post', 'user.renamed')
    print('[redis] subscriber запущен: new_post, user.renamed', flush=True)
 
    async for message in pubsub.listen():
        print(f"[redis] получено: {message}", flush=True)
        if message['type'] != 'message':
            continue
        channel = message['channel'].decode()
        data = json.loads(message['data'])
 
        if channel == 'new_post':
            await ws.manager.broadcast({'type': 'new_post', 'post': data})
 
        elif channel == 'user.renamed':
            conn = await get_db()
            async with conn.cursor() as cur:
                await cur.execute(
                    'UPDATE comments SET author_name=%s WHERE author_id=%s',
                    (data['new_name'], data['id'])
                )
                await conn.commit()
            conn.close()
            await ws.manager.broadcast({
                'type': 'user_renamed',
                'user_id': data['id'],
                'new_name': data['new_name'],
            })
 
 
@asynccontextmanager
async def lifespan(app: FastAPI):
    task = asyncio.create_task(redis_subscriber())
    yield
    task.cancel()
 
 
app = FastAPI(title='Boardy API', version='0.5.0', lifespan=lifespan)
 
app.add_middleware(
    CORSMiddleware,
    allow_origins=['http://localhost'],
    allow_credentials=True,
    allow_methods=['*'],
    allow_headers=['*'],
)
 
app.include_router(comments.router)
app.include_router(ws.router)
 
 
@app.get('/api/status')
async def status():
    return {'status': 'ok'}
