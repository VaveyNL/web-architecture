from fastapi import FastAPI
from datetime import datetime
import asyncio
import time
import os

app = FastAPI(title='Boardy API', version='0.1.0')
MESSAGES_FILE = '/var/www/boardy/data/messages.txt'

@app.get('/api/status')
async def status():
    return {
        'status': 'ok',
        'service': 'boardy-api',
        'time': str(datetime.now())
    }

@app.get('/api/messages')
async def get_messages():
    if not os.path.exists(MESSAGES_FILE):
        return {'messages': [], 'count': 0}
    messages = []
    with open(MESSAGES_FILE) as f:
        for line in f:
            parts = line.strip().split('|')
            if len(parts) >= 3:
                messages.append({
                    'date': parts[0],
                    'name': parts[1],
                    'message': parts[2]
                })
    return {'messages': messages, 'count': len(messages)}

# --- Демонстрация async ---

@app.get('/api/slow')
async def slow_query():
    """Имитация запроса к БД: 2 сек (async — не блокирует)"""
    await asyncio.sleep(2)
    return {'result': 'done', 'time': str(datetime.now())}

@app.get('/api/slow-blocking')
async def slow_blocking():
    """ПЛОХО: time.sleep блокирует весь event loop!"""
    time.sleep(2)
    return {'result': 'done', 'time': str(datetime.now())}

@app.get('/api/counter')
async def counter():
    """Процесс живёт — состояние между запросами"""
    if not hasattr(app.state, 'counter'):
        app.state.counter = 0
    app.state.counter += 1
    return {'counter': app.state.counter}
