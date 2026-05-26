import { startLogin, refreshToken, getToken } from '/js/auth.js';

const { useState, useEffect, useRef } = React;

function Comments({ postId, userName }) {
    const [token, setToken] = useState(getToken());
    const [items, setItems] = useState([]);
    const [text, setText] = useState('');
    const [editId, setEditId] = useState(null);
    const [editText, setEditText] = useState('');
    const tokenRef = useRef(token);
    tokenRef.current = token;

    async function authedFetch(url, options = {}) {
        const t = tokenRef.current;
        let res = await fetch(url, {
            ...options,
            headers: { ...(options.headers || {}), 'Authorization': 'Bearer ' + t },
        });
        if (res.status === 401) {
            const nt = await refreshToken();
            if (!nt) return null;
            setToken(nt);
            tokenRef.current = nt;
            res = await fetch(url, {
                ...options,
                headers: { ...(options.headers || {}), 'Authorization': 'Bearer ' + nt },
            });
        }
        return res;
    }

    async function load() {
        const res = await fetch(`/api/posts/${postId}/comments`);
        const data = await res.json();
        setItems(data.items || []);
    }

    useEffect(() => {
        load();
        const ws = new WebSocket(`ws://${location.host}/ws`);
        ws.onmessage = (e) => {
            const msg = JSON.parse(e.data);
            if (msg.type === 'new_comment' && msg.comment.post_id == postId) {
                setItems(prev => [...prev, msg.comment]);
            } else if (msg.type === 'update_comment') {
                setItems(prev => prev.map(c =>
                    c.id === msg.comment.id ? { ...c, body: msg.comment.body } : c));
            } else if (msg.type === 'delete_comment') {
                setItems(prev => prev.filter(c => c.id !== msg.comment_id));
            } else if (msg.type === 'user_renamed') {
                setItems(prev => prev.map(c =>
                    c.author_id == msg.user_id ? { ...c, author_name: msg.new_name } : c));
            }
        };
        return () => ws.close();
    }, [postId]);

    async function add() {
        if (!text.trim()) return;
        const res = await authedFetch(`/api/posts/${postId}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ body: text, author_name: userName }),
        });
        if (res && res.ok) setText('');
    }

    async function save(id) {
        const res = await authedFetch(`/api/comments/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ body: editText }),
        });
        if (res && res.ok) setEditId(null);
    }

    async function del(id) {
        if (!confirm('Удалить комментарий?')) return;
        await authedFetch(`/api/comments/${id}`, { method: 'DELETE' });
    }

    if (!token) {
        return (
            <div style={{ padding: '10px 0' }}>
                <button className="btn btn-primary" onClick={startLogin}>
                    Войти, чтобы комментировать
                </button>
            </div>
        );
    }

    return (
        <div>
            <h4>Комментарии ({items.length})</h4>
            {items.map(c => (
                <div key={c.id} className="card mb-2">
                    <div className="card-body">
                        {editId === c.id ? (
                            <div>
                                <textarea className="form-control mb-2"
                                    value={editText}
                                    onChange={e => setEditText(e.target.value)} />
                                <button className="btn btn-sm btn-success"
                                    onClick={() => save(c.id)}>Сохранить</button>{' '}
                                <button className="btn btn-sm btn-secondary"
                                    onClick={() => setEditId(null)}>Отмена</button>
                            </div>
                        ) : (
                            <div>
                                <p className="mb-1">{c.body}</p>
                                <small className="text-muted">{c.author_name}</small>
                                <div className="mt-1">
                                    <button className="btn btn-sm btn-outline-secondary"
                                        onClick={() => { setEditId(c.id); setEditText(c.body); }}>
                                        ✎</button>{' '}
                                    <button className="btn btn-sm btn-outline-danger"
                                        onClick={() => del(c.id)}>🗑</button>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            ))}
            <div className="mt-3">
                <textarea className="form-control mb-2" placeholder="Ваш комментарий"
                    value={text} onChange={e => setText(e.target.value)} />
                <button className="btn btn-primary" onClick={add}>Отправить</button>
            </div>
        </div>
    );
}

const mount = document.getElementById('comments-app');
ReactDOM.createRoot(mount).render(
    <Comments postId={mount.dataset.postId}
              userName={mount.dataset.userName} />
);
