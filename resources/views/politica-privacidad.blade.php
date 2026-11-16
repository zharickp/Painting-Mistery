@extends('layouts.guest')

@section('title', 'Política de tratamiento de datos personales')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    <section class="bg-black">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <span class="text-red-400 font-semibold text-xs uppercase tracking-[0.3em]">Protección de datos</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mt-3 leading-tight">
                Política de tratamiento de datos personales
            </h1>
            <p class="text-gray-300 mt-4 text-sm">
                Vigente desde el {{ \App\Http\Controllers\LandingController::POLITICA_VIGENTE_DESDE }}.
            </p>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-gray-700 text-sm sm:text-base leading-relaxed">

            <p>
                En Painting Mistery cuidamos tus datos. Aquí te contamos, de forma sencilla, qué datos pedimos,
                para qué los usamos y qué puedes hacer con ellos, según la Ley 1581 de 2012 de protección de datos personales.
            </p>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">¿Quiénes somos?</h2>
                <p>
                    Painting Mistery, en Calle 4 # 35-42, casa 13, barrio Sicomoro, Melgar, Tolima.
                    Puedes escribirnos a <a href="mailto:paintingmistery20@gmail.com" class="text-red-600 hover:underline">paintingmistery20@gmail.com</a>
                    o al WhatsApp +57 314 455 7602.
                </p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">¿Qué datos pedimos?</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Tu nombre, tu documento, tu correo y tu teléfono.</li>
                    <li>Tu contraseña, que se guarda cifrada: nadie puede verla, ni siquiera nosotros.</li>
                    <li>Tus compras, inscripciones a cursos, lista de deseos y reseñas.</li>
                </ul>
                <p class="mt-2">No guardamos números de tarjetas ni claves bancarias.</p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">¿Para qué los usamos?</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Crear tu cuenta y ayudarte si olvidas la contraseña.</li>
                    <li>Atender tus compras e inscripciones y entregarte tus comprobantes.</li>
                    <li>Contactarte sobre tus pedidos o tus cursos.</li>
                    <li>Mostrar tu nombre junto a las reseñas que publiques.</li>
                    <li>Mantener la seguridad del sitio.</li>
                </ul>
                <p class="mt-2">
                    No vendemos ni compartimos tus datos con otras empresas para publicidad.
                </p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">¿Qué puedes hacer con tus datos?</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Consultarlos cuando quieras, sin costo.</li>
                    <li>Actualizarlos o corregirlos. Tu nombre y teléfono los cambias tú mismo en <strong>Mi cuenta &gt; Perfil</strong>.</li>
                    <li>Pedirnos que dejemos de usarlos o que los borremos. Solo conservamos los datos de tus compras durante el tiempo que exige la ley.</li>
                    <li>Si no te respondemos, quejarte ante la Superintendencia de Industria y Comercio.</li>
                </ul>
                <p class="mt-2">
                    Para cualquiera de estas solicitudes escríbenos al correo con tu nombre y tu documento.
                    Te respondemos en máximo 10 días hábiles si es una consulta, y en máximo 15 días hábiles si es un reclamo.
                </p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">¿Cómo los protegemos?</h2>
                <p>
                    El sitio usa conexión segura (HTTPS), cada persona solo ve lo que le corresponde y hacemos
                    copias de seguridad de la información. Usamos cookies solo para mantener tu sesión abierta y
                    guardar tu carrito, no para publicidad.
                </p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Tu autorización</h2>
                <p>
                    Al crear tu cuenta aceptas esta política. Si la cambiamos, lo publicaremos en esta misma página.
                </p>
            </div>
        </div>
    </section>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')
    <script>document.addEventListener('DOMContentLoaded', syncUI);</script>
</div>
@endsection
