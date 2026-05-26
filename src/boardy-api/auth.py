"""
RS256-аутентификация для FastAPI.
Проверяет Bearer-токен публичным ключом Passport (асимметричная подпись).
"""
import jwt
from fastapi import Header, HTTPException

PUBLIC_KEY = open('/opt/boardy-api/oauth-public.key').read()
ALGORITHM = 'RS256'


async def get_current_user(authorization: str = Header(None)):
    if not authorization or not authorization.startswith('Bearer '):
        raise HTTPException(status_code=401, detail='Token required')

    token = authorization.split(' ', 1)[1]
    try:
        payload = jwt.decode(
            token, PUBLIC_KEY,
            algorithms=[ALGORITHM],
            options={'verify_aud': False},
        )
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail='Token expired')
    except jwt.InvalidTokenError as e:
        raise HTTPException(status_code=401, detail=f'Invalid token: {e}')

    return payload
