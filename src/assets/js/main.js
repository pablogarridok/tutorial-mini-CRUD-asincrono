console.log('main.js cargado correctamente');

const API_URL = '/api.php';

const msg = document.getElementById('msg');
const tbody = document.getElementById('tbody');
const form = document.getElementById('formCreate');

function mostrarMensaje(texto, ok=true) {
  msg.textContent = texto;
  msg.className = ok ? 'ok' : 'error';
}

async function cargarLista() {
  const res = await fetch(API_URL + '?action=list');
  const json = await res.json();
  if (!json.ok) return mostrarMensaje(json.error, false);
  pintarTabla(json.data);
}

function pintarTabla(data) {
  tbody.innerHTML = '';
  data.forEach((u, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${i}</td>
      <td>${u.nombre}</td>
      <td>${u.email}</td>
      <td><button data-index="${i}" class="btn-del">Eliminar</button></td>
    `;
    tbody.appendChild(tr);
  });
}

form.addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(form);
  const body = { nombre: fd.get('nombre'), email: fd.get('email') };
  const res = await fetch(API_URL + '?action=create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  });
  const json = await res.json();
  if (!json.ok) return mostrarMensaje(json.error, false);
  mostrarMensaje('Usuario agregado ✅');
  form.reset();
  pintarTabla(json.data);
});

tbody.addEventListener('click', async e => {
  if (!e.target.classList.contains('btn-del')) return;
  const index = e.target.dataset.index;
  const res = await fetch(API_URL + '?action=delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ index })
  });
  const json = await res.json();
  if (!json.ok) return mostrarMensaje(json.error, false);
  mostrarMensaje('Usuario eliminado ❌');
  pintarTabla(json.data);
});

cargarLista();
