<style>
@keyframes pulsoMicro { 0% { transform: scale(1); } 45% { transform: scale(1.3); } 100% { transform: scale(1); } }
.animate-pulso { animation: pulsoMicro .35s ease; }
</style>
<script>
// ── Carrito & Wishlist (localStorage, compartido en todas las páginas; con sesión de cliente se guardan en su cuenta) ──
const CART_KEY = 'pm_carrito';
const WISH_KEY = 'pm_wishlist';

function getCarrito() { try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch(e) { return []; } }
function saveCarrito(c) { localStorage.setItem(CART_KEY, JSON.stringify(c)); syncUI(); if (typeof guardarEnCuenta === 'function') guardarEnCuenta(); }
function getWish() { try { return JSON.parse(localStorage.getItem(WISH_KEY)) || []; } catch(e) { return []; } }
function saveWish(w) { localStorage.setItem(WISH_KEY, JSON.stringify(w)); syncUI(); if (typeof guardarEnCuenta === 'function') guardarEnCuenta(); }

function fmt(n) { return '$' + Number(n).toLocaleString('es-CO'); }

// ── Microanimación genérica: pulso breve al agregar/quitar/seleccionar ──
function pulso(el) {
    if (!el) return;
    el.classList.remove('animate-pulso');
    void el.offsetWidth;
    el.classList.add('animate-pulso');
}

function estrellasHTML(promedio, tamano) {
    const size = tamano || 'h-4 w-4';
    let html = '';
    for (let i = 1; i <= 5; i++) {
        const lleno = i <= Math.round(promedio);
        html += `<svg class="${size} ${lleno ? 'text-yellow-400' : 'text-gray-200'}" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>`;
    }
    return html;
}

// ── Acordeón genérico (usado en la descripción del producto) ──
function abrirAcordeon(id) {
    const panel = document.getElementById(id);
    const icon  = document.getElementById(id + 'Icon');
    panel.style.maxHeight = panel.scrollHeight + 'px';
    if (icon) icon.style.transform = 'rotate(180deg)';
}

function toggleAcordeon(id) {
    const panel = document.getElementById(id);
    const icon  = document.getElementById(id + 'Icon');
    const abierto = panel.style.maxHeight && panel.style.maxHeight !== '0px';
    panel.style.maxHeight = abierto ? '0px' : panel.scrollHeight + 'px';
    if (icon) icon.style.transform = abierto ? 'rotate(0deg)' : 'rotate(180deg)';
}

let prevCartCount = null, prevWishCount = null;
function syncUI() {
    const cart = getCarrito();
    const wish = getWish();
    const cartCount = cart.reduce((s,i) => s + i.qty, 0);
    const wishCount = wish.length;

    const cb = document.getElementById('navCartBadge');
    const wb = document.getElementById('navWishBadge');
    if (cb) {
        cb.textContent = cartCount;
        cb.classList.toggle('hidden', cartCount === 0);
        if (prevCartCount !== null && cartCount !== prevCartCount) pulso(cb);
    }
    if (wb) {
        wb.textContent = wishCount;
        wb.classList.toggle('hidden', wishCount === 0);
        if (prevWishCount !== null && wishCount !== prevWishCount) pulso(wb);
    }
    prevCartCount = cartCount;
    prevWishCount = wishCount;

    // Actualizar iconos de corazón en las tarjetas
    document.querySelectorAll('.prod-card').forEach(card => {
        const id = card.dataset.id;
        const icon = card.querySelector('.wish-icon');
        if (!icon) return;
        const inWish = wish.some(w => w.id == id);
        icon.setAttribute('fill', inWish ? '#dc2626' : 'none');
        icon.setAttribute('stroke', inWish ? '#dc2626' : 'currentColor');
    });

    // Hook para que cada página actualice su propio ícono de wishlist (ej. producto/show)
    if (typeof window.alSincronizarUI === 'function') window.alSincronizarUI();
}

// ── Wishlist ──
function toggleWishItem(id, nombre, precio, imagen) {
    let wish = getWish();
    const idx = wish.findIndex(w => w.id == id);
    if (idx >= 0) {
        wish.splice(idx, 1);
        showToast('Eliminado de lista de deseos');
    } else {
        wish.push({ id, nombre, precio, imagen });
        showToast('Agregado a tu lista de deseos');
    }
    saveWish(wish);
}

function toggleWish(card, btn) {
    toggleWishItem(card.dataset.id, card.dataset.nombre, card.dataset.precio, card.dataset.imagen);
    pulso(btn || card.querySelector('.wish-icon'));
}

function abrirWishlist() {
    renderWishlist();
    document.getElementById('wishlistModal').classList.remove('hidden');
}
function cerrarWishlist() { document.getElementById('wishlistModal').classList.add('hidden'); }

function renderWishlist() {
    const wish = getWish();
    const body = document.getElementById('wishlistBody');
    if (wish.length === 0) {
        body.innerHTML = '<div class="text-center py-12 text-gray-400"><svg class="h-12 w-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg><p class="text-sm">Tu lista de deseos está vacía</p></div>';
        return;
    }
    body.innerHTML = wish.map(item => `
        <div class="flex gap-4 items-center border border-gray-100 rounded-xl p-3">
            <div class="h-16 w-16 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                ${item.imagen ? `<img src="${item.imagen}" class="h-full w-full object-cover">` : '<div class="h-full w-full flex items-center justify-center"><svg class="h-6 w-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>'}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm truncate">${item.nombre}</p>
                <p class="text-red-600 font-bold text-sm">${fmt(item.precio)}</p>
            </div>
            <div class="flex flex-col gap-2">
                <button onclick="wishToCart('${item.id}'); renderWishlist();"
                    class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition font-medium whitespace-nowrap">
                    Al carrito
                </button>
                <button onclick="quitarDeWish('${item.id}'); renderWishlist();"
                    class="text-xs border border-gray-200 text-gray-500 hover:border-red-300 hover:text-red-600 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                    Quitar
                </button>
            </div>
        </div>
    `).join('');
}

function wishToCart(id) {
    const wish = getWish();
    const item = wish.find(w => w.id == id);
    if (!item) return;
    // La lista de deseos no guarda el stock: se consulta antes de agregar.
    consultarExistencias([item.id]).then(datos => {
        const d = datos && datos[item.id];
        const stock = d ? (d.disponible ? d.stock : 0) : null;
        if (addToCart({ id: item.id, nombre: item.nombre, precio: item.precio, imagen: item.imagen, stock }, 1)) {
            showToast('Agregado al carrito');
        }
    });
}

function quitarDeWish(id) {
    let wish = getWish();
    wish = wish.filter(w => w.id != id);
    saveWish(wish);
}

// ── Carrito ──
// Cada producto del carrito guarda su stock ("stock"): nunca se deja pedir
// más unidades de las que hay. El stock se vuelve a consultar al abrir el
// carrito y el servidor lo revisa otra vez al pagar.
function stockDe(valor) {
    if (valor === undefined || valor === null || valor === '') return null;
    const n = parseInt(valor, 10);
    return isNaN(n) ? null : Math.max(0, n);
}

/** Agrega unidades respetando el stock. Devuelve true si agregó todo lo pedido. */
function addToCart(prod, qty) {
    let cart = getCarrito();
    const idx = cart.findIndex(c => c.id == prod.id);
    const stock = stockDe(prod.stock) ?? (idx >= 0 ? stockDe(cart[idx].stock) : null);
    const enCarrito = idx >= 0 ? cart[idx].qty : 0;
    let nueva = enCarrito + qty;

    if (stock !== null) {
        if (stock <= 0) { showToast('Este producto está agotado'); return false; }
        if (enCarrito >= stock) {
            showToast(`Ya tienes en el carrito las ${stock} unidades disponibles`);
            return false;
        }
        if (nueva > stock) {
            showToast(`Solo hay ${stock} disponibles: se agregaron ${stock - enCarrito}`);
            nueva = stock;
            qty = -1; // agregado parcial: quien llama no muestra "agregado"
        }
    }

    if (idx >= 0) { cart[idx].qty = nueva; if (stock !== null) cart[idx].stock = stock; }
    else { cart.push({ id: prod.id, nombre: prod.nombre, precio: prod.precio, imagen: prod.imagen, qty: nueva, stock }); }
    saveCarrito(cart);
    return qty !== -1;
}
function addToCartDesdeCard(card, btn) {
    const ok = addToCart({ id: card.dataset.id, nombre: card.dataset.nombre, precio: parseFloat(card.dataset.precio), imagen: card.dataset.imagen, stock: card.dataset.stock }, 1);
    if (ok) showToast('Agregado al carrito');
    pulso(btn);
}

/** Pide al servidor stock y precio actuales: { id: {stock, disponible, precio, nombre} }. */
async function consultarExistencias(ids) {
    if (!ids.length) return {};
    try {
        const r = await fetch('{{ route("tienda.existencias") }}?ids=' + encodeURIComponent(ids.join(',')), { headers: { 'Accept': 'application/json' } });
        return r.ok ? await r.json() : null;
    } catch (e) { return null; }
}

let carritoAvisos = [];

/** Actualiza stock y precio del carrito con lo que hay realmente y ajusta cantidades. */
async function refrescarExistencias() {
    const cart = getCarrito();
    const datos = await consultarExistencias(cart.map(i => i.id));
    if (!datos) return;
    const avisos = [];
    let cambio = false;
    const nuevo = [];
    cart.forEach(item => {
        const d = datos[item.id];
        if (!d) { nuevo.push(item); return; }
        const stock = d.disponible ? d.stock : 0;
        if (item.stock !== stock) { item.stock = stock; cambio = true; }
        if (d.precio !== null && Number(item.precio) !== Number(d.precio)) { item.precio = d.precio; cambio = true; }
        if (!d.disponible) {
            avisos.push(`"${item.nombre}" ya no está a la venta y se quitó del carrito.`);
            cambio = true;
            return;
        }
        if (stock > 0 && item.qty > stock) {
            avisos.push(`${stock === 1 ? "Solo queda 1 unidad" : "Solo hay " + stock + " unidades"} de "${item.nombre}": se ajustó la cantidad.`);
            item.qty = stock;
            cambio = true;
        }
        nuevo.push(item);
    });
    if (avisos.length) carritoAvisos = avisos;
    if (cambio) saveCarrito(nuevo);
    if (!document.getElementById('carritoModal').classList.contains('hidden')) renderCarrito();
}

function abrirCarrito() {
    carritoAvisos = [];
    renderCarrito();
    refrescarExistencias();
    document.getElementById('carritoModal').classList.remove('hidden');
    // rAF para que la transición de entrada se anime (si quitamos "hidden" y
    // trasladamos en el mismo tick, el navegador no dibuja el estado inicial)
    requestAnimationFrame(() => {
        document.getElementById('carritoPanel').classList.remove('translate-x-full');
    });
}
function cerrarCarrito() {
    const panel = document.getElementById('carritoPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => document.getElementById('carritoModal').classList.add('hidden'), 300);
}

function renderCarrito() {
    const cart = getCarrito();
    const body = document.getElementById('carritoBody');
    const footer = document.getElementById('carritoFooter');
    if (cart.length === 0) {
        body.innerHTML = '<div class="text-center py-12 text-gray-400"><svg class="h-12 w-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg><p class="text-sm">El carrito está vacío</p></div>';
        footer.classList.add('hidden');
        return;
    }
    let total = 0;
    const avisosHTML = carritoAvisos.length
        ? `<div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-800 text-xs px-3 py-2.5 space-y-1">${carritoAvisos.map(a => `<p>${a}</p>`).join('')}</div>`
        : '';
    body.innerHTML = avisosHTML + cart.map(item => {
        const sub = item.precio * item.qty;
        total += sub;
        const stock = stockDe(item.stock);
        const agotado = stock === 0;
        const enTope = stock !== null && item.qty >= stock;
        const textoStock = stock === null ? ''
            : agotado ? '<p class="text-xs font-semibold text-red-600 mt-1">Agotado: quítalo para poder pagar</p>'
            : `<p class="text-xs mt-1 ${stock <= 3 ? 'text-amber-600 font-semibold' : 'text-gray-400'}">${stock === 1 ? 'Queda 1 disponible' : stock + ' disponibles'}</p>`;
        return `
        <div class="flex gap-4 items-center border border-gray-100 rounded-xl p-3">
            <div class="h-16 w-16 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                ${item.imagen ? `<img src="${item.imagen}" class="h-full w-full object-cover">` : ''}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm truncate">${item.nombre}</p>
                <p class="text-red-600 font-bold text-sm">${fmt(item.precio)}</p>
                <div class="flex items-center gap-1 mt-1">
                    <button onclick="cambiarQtyCarrito('${item.id}', -1)" class="h-6 w-6 rounded border border-gray-200 text-gray-600 hover:bg-gray-100 text-sm font-bold flex items-center justify-center transition">−</button>
                    <span class="text-sm font-semibold w-6 text-center">${item.qty}</span>
                    <button onclick="cambiarQtyCarrito('${item.id}', 1)" ${enTope ? 'disabled title="No hay más unidades disponibles"' : ''} class="h-6 w-6 rounded border border-gray-200 text-gray-600 hover:bg-gray-100 text-sm font-bold flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed">+</button>
                </div>
                ${textoStock}
            </div>
            <div class="text-right flex-shrink-0">
                <p class="font-bold text-gray-700 text-sm">${fmt(sub)}</p>
                <button onclick="quitarDelCarrito('${item.id}')" class="text-xs text-red-400 hover:text-red-600 mt-1 transition">Quitar</button>
            </div>
        </div>`;
    }).join('');

    footer.classList.remove('hidden');
    document.getElementById('carritoTotal').textContent = fmt(total);

}

function cambiarQtyCarrito(id, delta) {
    let cart = getCarrito();
    const idx = cart.findIndex(c => c.id == id);
    if (idx < 0) return;
    const stock = stockDe(cart[idx].stock);
    let nueva = Math.max(1, cart[idx].qty + delta);
    if (stock !== null && nueva > stock) {
        showToast(stock > 0 ? `Solo hay ${stock} disponibles` : 'Este producto está agotado');
        nueva = Math.max(1, stock);
    }
    cart[idx].qty = nueva;
    saveCarrito(cart);
    renderCarrito();
}

function quitarDelCarrito(id) {
    let cart = getCarrito().filter(c => c.id != id);
    saveCarrito(cart);
    renderCarrito();
}

// ── Envía el carrito local (localStorage) al carrito real del backend
//    antes de ir al checkout, para que la compra tenga exactamente lo que
//    el cliente ve en el modal. ──
async function irACheckout(btn) {
    const cart = getCarrito();
    if (cart.length === 0) { showToast('Tu carrito está vacío'); return; }

    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Cargando…';

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const r = await fetch('{{ route("carrito.sincronizar") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ items: cart.map(i => ({ id: i.id, qty: i.qty })) }),
        });
        const j = await r.json();
        // El servidor corrigió cantidades (stock insuficiente o producto retirado):
        // se aplican al carrito y el cliente las ve antes de pagar.
        if (j.ajustes && Object.keys(j.ajustes).length) {
            let c = getCarrito();
            Object.entries(j.ajustes).forEach(([id, qty]) => {
                const i = c.findIndex(x => x.id == id);
                if (i < 0) return;
                if (qty <= 0) c.splice(i, 1);
                else { c[i].qty = qty; c[i].stock = qty; }
            });
            carritoAvisos = j.avisos || [];
            saveCarrito(c);
            renderCarrito();
            btn.disabled = false;
            btn.innerHTML = original;
            return;
        }
        if (!r.ok || !j.ok) {
            showToast(j.message || 'No se pudo validar el carrito.');
            btn.disabled = false;
            btn.innerHTML = original;
            return;
        }

        window.location.href = '{{ route("checkout.mostrar") }}';
    } catch (e) {
        showToast('Error de conexión. Intenta de nuevo.');
        btn.disabled = false;
        btn.innerHTML = original;
    }
}

// ── Toast ──
let toastTimer;
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.remove('hidden');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.add('hidden'), 2800);
}

document.addEventListener('DOMContentLoaded', syncUI);

// Al volver de iniciar sesión desde el carrito (?carrito=1) se abre el carrito
// para que el cliente siga con su compra, y se limpia la dirección.
document.addEventListener('DOMContentLoaded', () => {
    const url = new URL(window.location.href);
    if (url.searchParams.get('carrito') !== '1') return;
    url.searchParams.delete('carrito');
    history.replaceState(null, '', url.pathname + url.search + url.hash);
    abrirCarrito();
});
</script>
