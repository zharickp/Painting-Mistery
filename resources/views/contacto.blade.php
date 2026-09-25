@extends('layouts.guest')

@section('title', 'Contacto - Painting Mistery')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    {{-- CONTACTO --}}
    <section id="contacto" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header oscuro estilo referencia --}}
            <div class="bg-gray-900 rounded-2xl px-8 py-10 mb-10 text-center">
                <span class="text-red-400 font-semibold text-xs uppercase tracking-widest">Contáctanos</span>
                <h2 class="text-3xl font-bold text-white mt-2">¿En qué podemos ayudarte?</h2>
                <p class="text-gray-400 text-sm mt-2">Escríbenos y te respondemos lo antes posible.</p>
            </div>

            {{-- 4 tarjetas de contacto rápido --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                <a href="https://www.instagram.com/painting_mistery/" target="_blank"
                   class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-100 hover:border-pink-200 hover:shadow-md transition group">
                    <div class="h-14 w-14 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);">
                        <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-800 text-sm">Instagram</p>
                        <p class="text-gray-400 text-xs">@painting_mistery</p>
                    </div>
                </a>
                <a href="https://wa.me/573144557602" target="_blank"
                   class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
                    <div class="h-14 w-14 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-800 text-sm">WhatsApp</p>
                        <p class="text-gray-400 text-xs">+57 314 455 7602</p>
                    </div>
                </a>
                <a href="mailto:paintingmistery20@gmail.com"
                   class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-100 hover:border-red-200 hover:shadow-md transition group">
                    <div class="h-14 w-14 rounded-full bg-red-600 flex items-center justify-center">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-800 text-sm">Correo</p>
                        <p class="text-gray-400 text-xs">paintingmistery20<br>@gmail.com</p>
                    </div>
                </a>
                <div class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-100">
                    <div class="h-14 w-14 rounded-full bg-orange-500 flex items-center justify-center">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-800 text-sm">Horario</p>
                        <p class="text-gray-400 text-xs">Lun–Sáb 8am–6pm<br>Dom: previa cita</p>
                    </div>
                </div>
            </div>

            {{-- Formulario con logo + campos --}}
            <div class="bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    {{-- Lado izquierdo: imagen/logo --}}
                    <div class="bg-gray-900 flex flex-col items-center justify-center p-12 gap-4">
                        <img src="{{ asset('images/logo-painting-mistery.png') }}"
                             onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s';"
                             alt="Painting Mistery" class="h-28 w-28 rounded-full object-cover border-4 border-red-600 shadow-2xl">
                        <div class="text-center">
                            <p class="text-white font-extrabold text-xl">Painting <span class="text-red-500">Mistery</span></p>
                            <p class="text-gray-400 text-sm mt-1">Melgar, Tolima – Colombia</p>
                        </div>
                        <div class="flex gap-3 mt-4">
                            <a href="https://www.instagram.com/painting_mistery/" target="_blank" class="h-9 w-9 rounded-full flex items-center justify-center hover:opacity-80 transition" style="background:linear-gradient(135deg,#f09433,#dc2743,#bc1888)">
                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="https://wa.me/573144557602" target="_blank" class="h-9 w-9 rounded-full bg-green-500 flex items-center justify-center hover:opacity-80 transition">
                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            <a href="https://www.facebook.com/Paintingmistery" target="_blank" class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center hover:opacity-80 transition">
                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        </div>
                    </div>
                    {{-- Formulario derecho --}}
                    <div class="p-8 bg-white">
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Formulario de contacto</h3>
                        <p class="text-gray-400 text-sm mb-6">Déjanos tu inquietud y te contactamos.</p>
                        <div class="space-y-4" id="contactForm">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Nombre</label>
                                    <input id="cNombre" type="text" placeholder="Tu nombre"
                                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Apellido</label>
                                    <input id="cApellido" type="text" placeholder="Tu apellido"
                                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Correo</label>
                                <input id="cCorreo" type="email" placeholder="tu@correo.com"
                                       class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Número de celular</label>
                                <input id="cTelefono" type="tel" placeholder="+57 300 000 0000"
                                       class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">¿En qué podemos ayudarte?</label>
                                <textarea id="cMensaje" rows="4" placeholder="Cuéntanos..."
                                          class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition resize-none"></textarea>
                            </div>
                            <button onclick="enviarContacto()"
                                    class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg text-sm transition">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Enviar por WhatsApp
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- UBICACIÓN --}}
    <section id="ubicacion" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">¿Dónde estamos?</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">Visítanos en Melgar</h2>
                <p class="text-gray-400 text-sm mt-2">Te esperamos en nuestro taller, Melgar – Tolima, Colombia.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                {{-- Info lateral --}}
                <div class="space-y-5 flex flex-col justify-center">
                    <div class="flex gap-4 items-start p-4 rounded-xl border border-gray-100 hover:border-red-200 hover:shadow-sm transition">
                        <div class="bg-red-100 rounded-xl h-11 w-11 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5">Dirección</h3>
                            <p class="text-gray-600 text-sm font-medium">Cl. 4 #35-42 casa 13</p>
                            <p class="text-gray-400 text-xs">Sicomoro, Melgar – Tolima, Colombia</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:shadow-sm transition">
                        <div class="bg-green-100 rounded-xl h-11 w-11 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5">WhatsApp</h3>
                            <a href="https://wa.me/573144557602" target="_blank" class="text-green-600 text-sm hover:underline font-medium">+57 314 455 7602</a>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start p-4 rounded-xl border border-gray-100 hover:border-red-200 hover:shadow-sm transition">
                        <div class="bg-red-100 rounded-xl h-11 w-11 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5">Email</h3>
                            <a href="mailto:paintingmistery20@gmail.com" class="text-red-600 text-sm hover:underline">paintingmistery20@gmail.com</a>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start p-4 rounded-xl border border-gray-100 hover:border-orange-200 hover:shadow-sm transition">
                        <div class="bg-orange-100 rounded-xl h-11 w-11 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5">Horario</h3>
                            <p class="text-gray-600 text-sm">Lun – Sáb: 8:00 am – 6:00 pm</p>
                            <p class="text-gray-400 text-xs">Domingos: previa cita</p>
                        </div>
                    </div>
                </div>
                {{-- Mapa Melgar, Tolima --}}
                <div class="lg:col-span-2 rounded-2xl overflow-hidden shadow-md border border-gray-100" style="min-height:360px;">
                    <iframe
                        src="https://maps.google.com/maps?q=Cl.+4+%2335-42+casa+13%2C+Sicomoro%2C+Melgar%2C+Tolima%2C+Colombia&hl=es&z=17&output=embed"
                        width="100%" height="100%" style="border:0; min-height:360px;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')

    @include('partials.carrito-wishlist-modales')

    @include('partials.tienda-scripts')

    <script>
    document.addEventListener('DOMContentLoaded', syncUI);

    // ── Formulario contacto → WhatsApp ──────────────────────────────
    function enviarContacto() {
        const nombre   = document.getElementById('cNombre').value.trim();
        const apellido = document.getElementById('cApellido').value.trim();
        const correo   = document.getElementById('cCorreo').value.trim();
        const telefono = document.getElementById('cTelefono').value.trim();
        const mensaje  = document.getElementById('cMensaje').value.trim();

        if (!nombre || !mensaje) {
            alert('Por favor completa al menos tu nombre y tu mensaje.');
            return;
        }

        const texto = `¡Hola Painting Mistery! 🏍️\n\n*Nombre:* ${nombre} ${apellido}\n*Correo:* ${correo || 'No indicado'}\n*Teléfono:* ${telefono || 'No indicado'}\n\n*Mensaje:*\n${mensaje}`;
        window.open('https://wa.me/573144557602?text=' + encodeURIComponent(texto), '_blank');
    }
    </script>

</div>
@endsection
