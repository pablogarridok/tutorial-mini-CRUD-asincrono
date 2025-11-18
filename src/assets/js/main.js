const URL_API_SERVIDOR = '/api.php';
const nodoCuerpoTablaUsuarios = document.getElementById('tbody');
const nodoFilaEstadoVacio = document.getElementById('fila-estado-vacio');
const formularioAltaUsuario = document.getElementById('formCreate');
const nodoZonaMensajesEstado = document.getElementById('msg');
const nodoBotonAgregarUsuario = document.getElementById('boton-agregar-usuario');
const nodoIndicadorCargando = document.getElementById('indicador-cargando');

function mostrarMensajeDeEstado(tipoEstado, textoMensaje) {
  nodoZonaMensajesEstado.className = tipoEstado;
  nodoZonaMensajesEstado.textContent = textoMensaje;
  if (tipoEstado !== '') setTimeout(() => {
    nodoZonaMensajesEstado.className = '';
    nodoZonaMensajesEstado.textContent = '';
  }, 2000);
}

function activarEstadoCargando() {
  if (nodoBotonAgregarUsuario) nodoBotonAgregarUsuario.disabled = true;
  if (nodoIndicadorCargando) nodoIndicadorCargando.hidden = false;
}
function desactivarEstadoCargando() {
  if (nodoBotonAgregarUsuario) nodoBotonAgregarUsuario.disabled = false;
  if (nodoIndicadorCargando) nodoIndicadorCargando.hidden = true;
}

function convertirATextoSeguro(entrada) {
  return String(entrada).replaceAll('&', '&amp;').replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;').replaceAll('"', '&quot;')
                        .replaceAll("'", '&#39;');
}

function renderizarTablaDeUsuarios(arrayUsuarios) {
  nodoCuerpoTablaUsuarios.innerHTML = '';
  if (Array.isArray(arrayUsuarios) && arrayUsuarios.length > 0) {
    if (nodoFilaEstadoVacio) nodoFilaEstadoVacio.hidden = true;
  } else {
    if (nodoFilaEstadoVacio) nodoFilaEstadoVacio.hidden = false;
    return;
  }
  arrayUsuarios.forEach((usuario, idx) => {
    const nodoFila = document.createElement('tr');
    nodoFila.innerHTML = `
      <td>${idx + 1}</td>
      <td>${convertirATextoSeguro(usuario?.nombre ?? '')}</td>
      <td>${convertirATextoSeguro(usuario?.email ?? '')}</td>
      <td>
        <button type="button" data-posicion="${idx}" aria-label="Eliminar usuario ${idx + 1}">Eliminar</button>
      </td>
    `;
    nodoCuerpoTablaUsuarios.appendChild(nodoFila);
  });
}

async function obtenerYMostrarListadoDeUsuarios() {
  try {
    const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=list`);
    const cuerpoJson = await respuestaHttp.json();
    if (!cuerpoJson.ok) throw new Error(cuerpoJson.error || 'No fue posible obtener el listado.');
    renderizarTablaDeUsuarios(cuerpoJson.data);
  } catch (error) {
    mostrarMensajeDeEstado('error', error.message);
  }
}

formularioAltaUsuario?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const datos = new FormData(formularioAltaUsuario);
  const nuevoUsuario = {
    nombre: String(datos.get('nombre') || '').trim(),
    email: String(datos.get('email') || '').trim(),
  };
  if (!nuevoUsuario.nombre || !nuevoUsuario.email) {
    mostrarMensajeDeEstado('error', 'Los campos Nombre y Email son obligatorios.');
    return;
  }
  try {
    activarEstadoCargando();
    const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=create`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(nuevoUsuario),
    });
    const cuerpoJson = await respuestaHttp.json();
    if (!cuerpoJson.ok) throw new Error(cuerpoJson.error || 'No fue posible crear el usuario.');
    renderizarTablaDeUsuarios(cuerpoJson.data);
    formularioAltaUsuario.reset();
    mostrarMensajeDeEstado('ok', 'Usuario agregado correctamente.');
  } catch (error) {
    mostrarMensajeDeEstado('error', error.message);
  } finally { desactivarEstadoCargando(); }
});

nodoCuerpoTablaUsuarios?.addEventListener('click', async (e) => {
  const btn = e.target.closest('button[data-posicion]');
  if (!btn) return;
  const idx = parseInt(btn.dataset.posicion, 10);
  if (!Number.isInteger(idx)) return;
  if (!window.confirm('¿Deseas eliminar este usuario?')) return;
  try {
    const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=delete`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ index: idx }),
    });
    const cuerpoJson = await respuestaHttp.json();
    if (!cuerpoJson.ok) throw new Error(cuerpoJson.error || 'No fue posible eliminar el usuario.');
    renderizarTablaDeUsuarios(cuerpoJson.data);
    mostrarMensajeDeEstado('ok', 'Usuario eliminado correctamente.');
  } catch (error) {
    mostrarMensajeDeEstado('error', error.message);
  }
});

obtenerYMostrarListadoDeUsuarios();
