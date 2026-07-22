@php
    $enlaceCursos    = request()->routeIs('inicio') ? '#cursos' : route('inicio') . '#cursos';
    $enlaceNosotros  = request()->routeIs('inicio') ? '#sobre-nosotros' : route('inicio') . '#sobre-nosotros';
    $enlaceContacto  = request()->routeIs('inicio') ? '#contacto' : route('inicio') . '#contacto';
@endphp
{{-- FOOTER --}}
<footer style="background: linear-gradient(135deg, #0f0f0f 0%, #1a0000 50%, #0f0f0f 100%);" class="pt-14 pb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 pb-10 border-b border-red-900/30">

            {{-- Marca --}}
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
                         alt="Logo" class="h-12 w-12 rounded-full object-cover border-2 border-red-600 shadow-lg shadow-red-900/50">
                    <div>
                        <span class="text-white font-extrabold text-lg block">Painting <span class="text-red-500">Mistery</span></span>
                        <span class="text-gray-500 text-xs">Melgar, Tolima 🇨🇴</span>
                    </div>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">
                    Especialistas en pintura automotriz, accesorios y formación profesional para motocicletas.
                </p>
                <div class="flex gap-1 items-center">
                    <div class="h-1 w-8 bg-red-600 rounded-full"></div>
                    <div class="h-1 w-4 bg-red-800 rounded-full"></div>
                    <div class="h-1 w-2 bg-red-900 rounded-full"></div>
                </div>
            </div>

            {{-- Links --}}
            <div>
                <h4 class="text-red-500 font-bold text-xs uppercase tracking-widest mb-5">Navegación</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('inicio') }}" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Inicio</a></li>
                    <li><a href="{{ route('tienda.index') }}" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Tienda</a></li>
                    <li><a href="{{ $enlaceCursos }}" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Cursos</a></li>
                    <li><a href="{{ $enlaceNosotros }}" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Sobre nosotros</a></li>
                    <li><a href="{{ $enlaceContacto }}" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Contacto</a></li>
                    <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Iniciar sesión</a></li>
                </ul>
            </div>

            {{-- Redes sociales --}}
            <div>
                <h4 class="text-red-500 font-bold text-xs uppercase tracking-widest mb-5">Síguenos</h4>

                {{-- Iconos con colores originales de cada red --}}
                <div class="flex flex-wrap gap-3 mb-5">

                    {{-- WhatsApp - verde oficial #25D366 --}}
                    <a href="https://wa.me/573144557602" target="_blank" rel="noopener"
                       class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg"
                       style="background-color:#25D366;" title="WhatsApp">
                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>

                    {{-- Instagram - gradiente oficial --}}
                    <a href="https://www.instagram.com/painting_mistery/" target="_blank" rel="noopener"
                       class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg"
                       style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);"
                       title="Instagram">
                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>

                    {{-- Facebook - azul oficial #1877F2 --}}
                    <a href="https://www.facebook.com/Paintingmistery" target="_blank" rel="noopener"
                       class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg"
                       style="background-color:#1877F2;" title="Facebook">
                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>

                    {{-- TikTok - negro con contraste --}}
                    <a href="https://www.tiktok.com/@paintingmisteryoficial" target="_blank" rel="noopener"
                       class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg border border-gray-600"
                       style="background-color:#010101;" title="TikTok">
                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V9.2a8.16 8.16 0 004.77 1.52V7.27a4.85 4.85 0 01-1-.58z"/>
                        </svg>
                    </a>

                    {{-- Email - rojo de marca --}}
                    <a href="mailto:paintingmistery20@gmail.com"
                       class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg"
                       style="background-color:#dc2626;" title="Email">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </a>
                </div>

                <div class="space-y-1.5 text-xs text-gray-500">
                    <p class="flex items-center gap-2">
                        <svg class="h-3.5 w-3.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <a href="https://wa.me/573144557602" target="_blank" class="hover:text-green-400 transition">+57 314 455 7602</a>
                    </p>
                    <p class="flex items-center gap-2">
                        <svg class="h-3.5 w-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:paintingmistery20@gmail.com" class="hover:text-red-400 transition">paintingmistery20@gmail.com</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-600">
            <p>&copy; {{ date('Y') }} Painting Mistery. Todos los derechos reservados.</p>
            <p>Hecho con ❤️ en Melgar, Tolima — Colombia 🇨🇴</p>
        </div>
    </div>
</footer>

{{-- BOTÓN FLOTANTE INSTAGRAM (izquierda) --}}
<a href="https://www.instagram.com/painting_mistery/" target="_blank" rel="noopener"
   class="fixed bottom-6 left-4 z-50 h-14 w-14 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 hover:-translate-y-1 transition-all duration-300"
   style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);"
   title="Síguenos en Instagram">
    <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
    </svg>
</a>

{{-- BOTÓN FLOTANTE WHATSAPP (derecha) --}}
<a href="https://wa.me/573144557602?text=Hola!%20Vi%20tu%20página%20y%20me%20gustaría%20más%20información%20sobre%20Painting%20Mistery%20🏍️" target="_blank" rel="noopener"
   class="fixed bottom-6 right-4 z-50 h-14 w-14 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 hover:-translate-y-1 transition-all duration-300"
   style="background-color:#25D366;"
   title="Chatea por WhatsApp">
    <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
</a>

<style>
/* Pulso en los botones flotantes */
.fixed a, a.fixed {
    animation: none;
}
@keyframes pulse-ring {
    0% { box-shadow: 0 0 0 0 rgba(37,211,102,.6); }
    70% { box-shadow: 0 0 0 12px rgba(37,211,102,0); }
    100% { box-shadow: 0 0 0 0 rgba(37,211,102,0); }
}
a[href*="wa.me"].fixed {
    animation: pulse-ring 2.5s infinite;
}
</style>
