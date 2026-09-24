@extends('layouts.guest')

@section('title', 'Nosotros - Painting Mistery')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    {{-- HERO --}}
    <section class="relative bg-gray-950 overflow-hidden">
        <img src="{{ asset('images/hero.jpeg') }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-25">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/70 to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 text-center">
            <span class="text-red-400 font-semibold text-xs uppercase tracking-[0.3em]">Quiénes somos</span>
            <h1 class="text-4xl sm:text-6xl font-extrabold text-white mt-4 leading-tight">
                Arte, pintura y <span class="text-red-500">pasión</span><br class="hidden sm:block"> sobre dos ruedas
            </h1>
            <p class="text-gray-300 max-w-2xl mx-auto mt-6 text-base sm:text-lg">
                Taller especializado en pintura, restauración y personalización de motocicletas en Melgar, Tolima.
            </p>
            <div class="flex flex-wrap justify-center gap-3 mt-8 text-xs font-semibold">
                <a href="#historia" class="px-4 py-2 rounded-full bg-white/10 text-white hover:bg-red-600 transition">Historia</a>
                <a href="#calavera" class="px-4 py-2 rounded-full bg-white/10 text-white hover:bg-red-600 transition">Nuestro logo</a>
                <a href="#mision" class="px-4 py-2 rounded-full bg-white/10 text-white hover:bg-red-600 transition">Misión y visión</a>
                <a href="#valores" class="px-4 py-2 rounded-full bg-white/10 text-white hover:bg-red-600 transition">Lo que nos define</a>
            </div>
        </div>
    </section>

    {{-- HISTORIA --}}
    <section id="historia" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <div class="absolute -inset-3 bg-red-600/10 rounded-3xl rotate-2"></div>
                <img src="{{ asset('images/hero.jpeg') }}" alt="Taller Painting Mistery"
                     class="relative rounded-3xl shadow-xl w-full h-80 sm:h-[26rem] object-cover">
                <div class="absolute -bottom-5 -right-2 sm:right-6 bg-red-600 text-white rounded-2xl px-5 py-3 shadow-lg">
                    <p class="text-2xl font-extrabold leading-none">Melgar</p>
                    <p class="text-[11px] uppercase tracking-wider opacity-90">Tolima, Colombia</p>
                </div>
            </div>
            <div>
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">Nuestra historia</span>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-2 mb-5">De la afición a un taller con identidad propia</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Painting Mistery nació de una pasión sencilla: ver una motocicleta convertirse en algo único.
                    Lo que empezó como el gusto por la pintura y el detalle se transformó en un taller de restauración
                    especializado en pintura para motos, con base en Melgar, Tolima.
                </p>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Hoy creamos diseños exclusivos y personalizados, cuidando cada paso del proceso para entregar
                    trabajos en perfecto estado y con acabados de nivel profesional. Y porque creemos en el oficio,
                    también lo enseñamos: nuestra academia forma a quienes quieren aprender desde cero.
                </p>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 text-center">
                        <p class="text-xl font-extrabold text-red-600">4.3★</p><p class="text-[11px] text-gray-500">en Google</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 text-center">
                        <p class="text-xl font-extrabold text-red-600">62</p><p class="text-[11px] text-gray-500">opiniones</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 text-center">
                        <p class="text-xl font-extrabold text-red-600">100%</p><p class="text-[11px] text-gray-500">a tu medida</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CALAVERA --}}
    <section id="calavera" class="py-20 bg-gray-950 text-white relative overflow-hidden">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-red-600/10 blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <span class="text-red-400 font-semibold text-xs uppercase tracking-widest">Nuestro logo</span>
                <h2 class="text-3xl font-extrabold mt-2 mb-5">La calavera: <span class="text-red-500">el misterio detrás de la marca</span></h2>
                <p class="text-gray-300 leading-relaxed mb-4">
                    La calavera es un símbolo histórico del mundo motero: libertad, carácter y desafío al miedo.
                    En Painting Mistery la elegimos porque resume lo que hacemos — tomar algo desgastado o común
                    y darle una segunda vida con personalidad.
                </p>
                <ul class="space-y-3 mt-6">
                    @foreach([
                        ['Actitud','Fiel a la cultura de las motos: espíritu libre y sin miedo a destacar.'],
                        ['Transformación','Como cada pieza que pasa por el taller: se renueva por completo.'],
                        ['Misterio','Cada diseño es una sorpresa hasta que se revela terminado.'],
                    ] as $p)
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-red-500 shrink-0"></span>
                        <p class="text-sm text-gray-400"><strong class="text-white">{{ $p[0] }}.</strong> {{ $p[1] }}</p>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="order-1 lg:order-2 flex justify-center">
                <div class="relative">
                    <div class="absolute inset-0 rounded-full bg-red-600/30 blur-2xl"></div>
                    <img src="{{ asset('images/logo-painting-mistery.png') }}" alt="Logo Painting Mistery"
                         class="relative h-64 w-64 sm:h-80 sm:w-80 rounded-full object-cover border-4 border-red-600 shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    {{-- MISION / VISION --}}
    <section id="mision" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">Hacia dónde vamos</span>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-2">Misión y visión</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="relative overflow-hidden rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl transition p-8 sm:p-10">
                    <span class="absolute -right-4 -top-6 text-[8rem] font-black text-red-50 select-none leading-none">01</span>
                    <div class="relative">
                        <div class="h-14 w-14 rounded-2xl bg-red-600 text-white flex items-center justify-center mb-6 shadow-lg shadow-red-600/30">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Misión</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Transformar motocicletas a través de la pintura y la personalización, ofreciendo productos
                            y procesos de la más alta calidad, y formar a nuevos artistas del oficio mediante cursos prácticos.
                        </p>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-3xl bg-gray-900 text-white shadow-sm hover:shadow-xl transition p-8 sm:p-10">
                    <span class="absolute -right-4 -top-6 text-[8rem] font-black text-white/5 select-none leading-none">02</span>
                    <div class="relative">
                        <div class="h-14 w-14 rounded-2xl bg-red-600 text-white flex items-center justify-center mb-6 shadow-lg shadow-red-600/30">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Visión</h3>
                        <p class="text-gray-300 leading-relaxed">
                            Ser el taller de referencia en pintura y personalización de motocicletas en el Tolima,
                            y hacer crecer nuestra academia para formar más estudiantes cada año.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- VALORES --}}
    <section id="valores" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">Lo que nos define</span>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-2">Lo que encuentras en Painting Mistery</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['Diseño personalizado','Tu idea hecha realidad en pintura, adaptada a tu moto.','M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                    ['Accesorios y repuestos','Piezas de calidad para distintas marcas y modelos.','M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10'],
                    ['Academia','Cursos prácticos para aprender el oficio desde cero.','M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['Hecho con amor','Cada trabajo refleja nuestra pasión por las motos.','M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                ] as $v)
                <div class="group rounded-2xl border border-gray-100 p-7 hover:border-red-200 hover:-translate-y-1 hover:shadow-lg transition">
                    <div class="h-14 w-14 rounded-xl bg-red-100 group-hover:bg-red-600 flex items-center justify-center mb-5 transition">
                        <svg class="h-7 w-7 text-red-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $v[2] }}"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">{{ $v[0] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $v[1] }}</p>
                </div>
                @endforeach
            </div>

            <div class="mt-14 rounded-3xl bg-gradient-to-r from-red-700 to-red-500 text-white p-8 sm:p-12 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-extrabold">¿Listo para darle vida a tu moto?</h3>
                    <p class="text-red-100 text-sm mt-1">Escríbenos y cotizamos tu diseño.</p>
                </div>
                <a href="https://wa.me/573144557602" target="_blank" class="bg-white text-red-600 font-bold px-7 py-3 rounded-xl text-sm hover:bg-red-50 transition shrink-0">Hablar por WhatsApp</a>
            </div>
        </div>
    </section>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')
    <script>document.addEventListener('DOMContentLoaded', syncUI);</script>
</div>
@endsection
