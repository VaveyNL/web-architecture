"""
JWT-аутентификация для FastAPI.
Извлекает Bearer-токен из заголовка Authorization, проверяет подпись
общим секретом (тот же, что в PHP api/me.php), возвращает payload.
"""

import jwt
from fastapi import Header, HTTPException

SECRET_KEY = 'boardy-jwt-secret-2026-change-me-in-production'
ALGORITHM = 'HS256'


async def get_current_user(authorization: str = Header(None)):
    if not authorization or not authorization.startswith('Bearer '):
        raise HTTPException(status_code=401, detail='Token required')

    token = authorization.split(' ', 1)[1]

    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail='Token expired')
    except jwt.InvalidTokenError:
        raise HTTPException(status_code=401, detail='Invalid token')

    return payload