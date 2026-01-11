document.addEventListener('DOMContentLoaded', () => {

    const dropdownList = document.querySelector('.notification-list-scroll');
    const badge = document.querySelector('#dropdownNotification .badge');

    // Si no existe el dropdown, no hacemos nada (evita errores en login u otras vistas)
    if (!dropdownList || !badge) {
        return;
    }

    // ============================
    // FORMATEAR FECHA
    // ============================
    function formatearFecha(fechaStr) {
        const fecha = new Date(fechaStr);

        if (isNaN(fecha)) return '';

        const dia = String(fecha.getDate()).padStart(2, '0');
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const anio = fecha.getFullYear();
        const horas = String(fecha.getHours()).padStart(2, '0');
        const minutos = String(fecha.getMinutes()).padStart(2, '0');

        return `${dia}/${mes}/${anio} ${horas}:${minutos}`;
    }

    // ============================
    // CARGAR NOTIFICACIONES
    // ============================
    async function cargarNotificaciones() {
        try {
            const res = await fetch('/notificaciones/no-leidas', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                console.warn('Respuesta inválida al cargar notificaciones');
                return;
            }

            const notificaciones = await res.json();

            dropdownList.innerHTML = '';

            if (!Array.isArray(notificaciones) || notificaciones.length === 0) {
                dropdownList.innerHTML = `
                    <li class="list-group-item bg-light text-center">
                        No tienes notificaciones
                    </li>
                `;
                badge.style.display = 'none';
                return;
            }

            // Badge
            badge.textContent = notificaciones.length;
            badge.style.display = 'inline-block';

            // Render
            notificaciones.forEach(n => {

                const fechaFormateada = formatearFecha(n.fecha);

                let background = 'bg-light';
                let titulo = '';

                if (n.tipo === 'DERIVACION') {
                    titulo = `Hoja de Ruta #${n.hoja_id} derivada`;
                } else if (n.tipo === 'ALERTA') {
                    titulo = `Alerta: Hoja de Ruta #${n.hoja_id}`;
                    background = '';
                }

                const li = document.createElement('li');
                li.className = 'list-group-item ' + background;

                if (n.tipo === 'ALERTA') {
                    li.style.backgroundColor = '#F8D7DA';
                }

                li.innerHTML = `
                    <a href="/notificaciones/ver/${n.id}" class="text-dark text-decoration-none">
                        <h6 class="mb-1">${titulo}</h6>
                        <p class="mb-0 small">${n.mensaje}</p>
                        <small class="text-muted">${fechaFormateada}</small>
                    </a>
                `;

                dropdownList.appendChild(li);
            });

        } catch (error) {
            console.error('Error cargando notificaciones:', error);
        }
    }

    // ============================
    // INICIO CONTROLADO
    // ============================
    setTimeout(() => {
        cargarNotificaciones();
        setInterval(cargarNotificaciones, 1000); // cada 5s (no cada 1s 👀)
    }, 400);

});
