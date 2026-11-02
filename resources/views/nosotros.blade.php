@extends('layouts.guest')

@section('title', 'Nosotros - Painting Mistery')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    {{-- PORTADA: texto a la izquierda y logo a la derecha --}}
    <section class="relative bg-black overflow-hidden">
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="order-2 lg:order-1">
                <span class="text-red-400 font-semibold text-xs uppercase tracking-[0.3em]">Quiénes somos</span>
                <h1 class="text-4xl font-extrabold text-white mt-3 leading-tight">
                    Painting <span class="text-red-500">Mistery</span>
                </h1>
                <p class="text-gray-300 max-w-xl mt-4 text-base sm:text-lg leading-relaxed">
                    Somos una empresa dedicada a la pintura, restauración y personalización de motos,
                    además de ofrecer accesorios para motocicletas y cursos de aprendizaje.
                </p>
            </div>
            <div class="order-1 lg:order-2 flex justify-center">
                <div class="relative">
                    <div class="absolute inset-0 rounded-full bg-red-600/30 blur-2xl"></div>
                    <img src="{{ asset('images/logo-painting-mistery.png') }}" alt="Logo de Painting Mistery"
                         class="relative h-48 w-48 rounded-full object-cover border-4 border-red-600 shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    {{-- HISTORIA --}}
    <section id="historia" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <div class="absolute -inset-3 bg-red-600/10 rounded-3xl rotate-2"></div>
                <img src="{{ asset('images/hero.jpeg') }}" alt="Painting Mistery"
                     class="relative rounded-3xl shadow-xl w-full h-80 sm:h-[26rem] object-cover">
                <div class="absolute -bottom-5 -right-2 sm:right-6 bg-red-600 text-white rounded-2xl px-5 py-3 shadow-lg">
                    <p class="text-2xl font-extrabold leading-none">Melgar</p>
                    <p class="text-[11px] uppercase tracking-wider opacity-90">Tolima, Colombia</p>
                </div>
            </div>
            <div>
                <h2 class="font-extrabold text-gray-900 leading-tight mb-6" style="font-size: clamp(2.25rem, 4vw, 3rem);">Nuestra <span class="text-red-600">historia</span></h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Painting Mistery nació del gusto por las motos y por el trabajo bien hecho.
                    Empezamos pintando y restaurando motos en Melgar, Tolima, cuidando cada detalle,
                    desde la preparación de la pieza hasta el acabado final.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Con el tiempo sumamos la venta de accesorios y repuestos para motocicletas, y
                    abrimos cursos para quienes quieren aprender de pintura y restauración de motos.
                </p>
            </div>
        </div>
    </section>

    {{-- MISION / VISION --}}
    <section id="mision" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900">Misión y visión</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="relative overflow-hidden rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl transition p-8 sm:p-10">
                    <span class="absolute -right-4 -top-6 text-[8rem] font-black text-red-50 select-none leading-none">01</span>
                    <div class="relative">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Misión</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Brindar servicios de personalización y restauración de motocicletas, ofreciendo repuestos,
                            accesorios e insumos de calidad que satisfagan las necesidades de nuestros clientes.
                            Nos enfocamos en el detalle, la innovación y la atención personalizada para transformar
                            cada motocicleta en una pieza única.
                        </p>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-3xl bg-gray-900 text-white shadow-sm hover:shadow-xl transition p-8 sm:p-10">
                    <span class="absolute -right-4 -top-6 text-[8rem] font-black text-white/5 select-none leading-none">02</span>
                    <div class="relative">
                        <h3 class="text-2xl font-bold mb-3">Visión</h3>
                        <p class="text-gray-300 leading-relaxed">
                            Ser una empresa reconocida a nivel regional por la calidad en la personalización y
                            restauración de motocicletas, destacándonos por nuestro trabajo profesional, la formación
                            de nuevos talentos a través de capacitaciones y el uso de herramientas tecnológicas que
                            mejoren la experiencia de nuestros clientes.
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
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">Qué ofrecemos</span>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-2">Lo que encuentras en Painting Mistery</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['Productos','Accesorios y repuestos de calidad para distintas marcas y modelos de motocicletas.','M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10'],
                    ['Cursos','Cursos de aprendizaje de pintura y restauración de motocicletas.','M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['Servicios','Diseños personalizados para tu motocicleta, así como trabajos de restauración y pintura.','M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                ] as $v)
                <div class="group rounded-2xl bg-white border border-gray-100 p-7 hover:border-red-200 hover:-translate-y-1 hover:shadow-lg transition">
                    <div class="h-14 w-14 rounded-xl bg-red-100 group-hover:bg-red-600 flex items-center justify-center mb-5 transition">
                        <svg class="h-7 w-7 text-red-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $v[2] }}"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">{{ $v[0] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $v[1] }}</p>
                </div>
                @endforeach
            </div>

            <div class="mt-14 rounded-3xl bg-gray-900 text-white p-8 sm:p-12 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-extrabold">¿Quieres pintar o personalizar tu moto?</h3>
                    <p class="text-gray-400 text-sm mt-1">Escríbenos y cotizamos tu diseño.</p>
                </div>
                <a href="https://wa.me/573144557602" target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-bold px-7 py-3 rounded-xl text-sm transition shrink-0">Hablar por WhatsApp</a>
            </div>
        </div>
    </section>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')
    <script>document.addEventListener('DOMContentLoaded', syncUI);</script>
</div>
@endsection
