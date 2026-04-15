const API = 'http://localhost:8000';
const POST_ID = 1;

function esc(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

async function loadItems() {
    const res = await fetch(`${API}/api/posts/${POST_ID}/comments`);
    const data = await res.json();
    document.getElementById('list').innerHTML = data.items.map(item => `
        <div style="border:1px solid #ccc;padding:10px;margin:5px 0">
            <strong>${esc(item.author_name)}</strong>
            <p>${esc(item.body)}</p>
            <small>${esc(item.created_at)}</small>
        </div>
    `).join('');
}

document.getElementById('btn').addEventListener('click', async () => {
    const body = document.getElementById('body').value.trim();
    if (!body) return;
    await fetch(`${API}/api/posts/${POST_ID}/comments`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({body: body})
    });
    document.getElementById('body').value = '';
    loadItems();
});

loadItems();
