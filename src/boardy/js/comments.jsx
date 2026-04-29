const { useState, useEffect } = React;
const API = 'http://localhost:8000';
const POST_ID = 1;

function CommentList() {
    const [items, setItems] = useState([]);
    const [text, setText] = useState('');
    const [editId, setEditId] = useState(null);
    const [editText, setEditText] = useState('');
    const [jwt, setJwt] = useState(null);

    // Получаем JWT при загрузке
    useEffect(() => {
        fetch('/api/me.php', { credentials: 'include' })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (data && data.token) {
                    setJwt(data.token);
                    console.log('JWT получен:', data.token);
                } else {
                    console.log('Не залогинен — JWT не получен');
                }
            })
            .catch(err => console.error('me.php error:', err));
    }, []);

    // Загрузка комментариев (GET — без токена, чтение публичное)
    const load = async () => {
        const res = await fetch(`${API}/api/posts/${POST_ID}/comments`);
        const data = await res.json();
        setItems(data.items);
    };

    useEffect(() => { load(); }, []);

    // Хелпер: заголовки с Bearer (если токен есть)
    const authHeaders = () => {
        const h = { 'Content-Type': 'application/json' };
        if (jwt) h['Authorization'] = 'Bearer ' + jwt;
        return h;
    };

    const add = async () => {
        if (!text.trim()) return;
        const res = await fetch(`${API}/api/posts/${POST_ID}/comments`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({ body: text })
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            alert('Ошибка: ' + (err.detail || res.status));
            return;
        }
        setText('');
        load();
    };

    const save = async (id) => {
        const res = await fetch(`${API}/api/comments/${id}`, {
            method: 'PUT',
            headers: authHeaders(),
            body: JSON.stringify({ body: editText })
        });
        if (!res.ok) {
            alert('Ошибка ' + res.status);
            return;
        }
        setEditId(null);
        load();
    };

    const del = async (id) => {
        if (!confirm('Удалить?')) return;
        const res = await fetch(`${API}/api/comments/${id}`, {
            method: 'DELETE',
            headers: authHeaders()
        });
        if (!res.ok) {
            alert('Ошибка ' + res.status);
            return;
        }
        load();
    };

    return (
        <div>
            {!jwt && (
                <div className="alert alert-warning">
                    Чтобы добавлять и редактировать — <a href="/login.php">войдите</a>.
                </div>
            )}

            {items.map(item => (
                <div key={item.id} className="card mb-2">
                    <div className="card-body">
                        <strong>{item.author_name}</strong>
                        <small className="text-muted ms-2">{item.created_at}</small>
                        {editId === item.id ? (
                            <div className="input-group mt-2">
                                <input className="form-control" value={editText}
                                    onChange={e => setEditText(e.target.value)} />
                                <button className="btn btn-success" onClick={() => save(item.id)}>Сохранить</button>
                                <button className="btn btn-secondary" onClick={() => setEditId(null)}>Отмена</button>
                            </div>
                        ) : (
                            <div>
                                <p className="mt-1 mb-1">{item.body}</p>
                                {jwt && (
                                    <>
                                        <button className="btn btn-sm btn-outline-secondary me-1"
                                            onClick={() => { setEditId(item.id); setEditText(item.body); }}>Ред.</button>
                                        <button className="btn btn-sm btn-outline-danger"
                                            onClick={() => del(item.id)}>Удалить</button>
                                    </>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            ))}

            {jwt && (
                <div className="input-group mt-3">
                    <input className="form-control" placeholder="Комментарий"
                        value={text} onChange={e => setText(e.target.value)} />
                    <button className="btn btn-primary" onClick={add}>Отправить</button>
                </div>
            )}
        </div>
    );
}

ReactDOM.createRoot(document.getElementById('app')).render(<CommentList />);