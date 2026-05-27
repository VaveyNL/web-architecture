import os
import jwt
from fastapi import Header, HTTPException

# Путь к публичному ключу Passport. В Docker ключ лежит в общем volume.
_KEY_PATH = os.environ.get('OAUTH_PUBLIC_KEY', '/opt/boardy-api/oauth-public.key')
ALGORITHM = 'RS256'

_public_key = None


def _get_key():
    """Ленивая загрузка ключа: читаем при первом запросе, а не при импорте.
    Нужно потому, что в Docker Laravel генерирует ключ уже после старта FastAPI."""
    global _public_key
    if _public_key is None:
        with open(_KEY_PATH, 'r') as f:
            _public_key = f.read()
    return _public_key


async def get_current_user(authorization: str = Header(None)):
    if not authorization or not authorization.startswith('Bearer '):
        raise HTTPException(status_code=401, detail='Authorization header required')
    token = authorization.split(' ', 1)[1]
    try:
        payload = jwt.decode(
            token,
            _get_key(),
            algorithms=[ALGORITHM],
            options={'verify_aud': False},
        )
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail='Token expired')
    except jwt.InvalidTokenError as e:
        raise HTTPException(status_code=401, detail=f'Invalid token: {e}')
    return payload
