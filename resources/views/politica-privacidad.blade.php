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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 text-gray-700 text-sm sm:text-base leading-relaxed">

            <p>
                En Painting Mistery respetamos la privacidad de nuestros clientes. Esta política explica qué datos
                personales recogemos en este sitio web, para qué los usamos y cómo puede ejercer sus derechos, de
                acuerdo con la Ley 1581 de 2012 y el Decreto 1377 de 2013 (incorporado en el Decreto 1074 de 2015).
            </p>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">1. Responsable del tratamiento</h2>
                <ul class="space-y-1">
                    <li><strong>Nombre:</strong> Painting Mistery</li>
                    <li><strong>Dirección:</strong> Calle 4 # 35-42, casa 13, barrio Sicomoro, Melgar, Tolima, Colombia</li>
                    <li><strong>Correo:</strong> <a href="mailto:paintingmistery20@gmail.com" class="text-red-600 hover:underline">paintingmistery20@gmail.com</a></li>
                    <li><strong>WhatsApp:</strong> +57 314 455 7602</li>
                </ul>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">2. Datos que recogemos</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Datos de identificación: nombres, apellidos, tipo y número de documento y, si lo indica, su género.</li>
                    <li>Datos de contacto: correo electrónico y número de teléfono.</li>
                    <li>Datos de la cuenta: la contraseña, que se guarda cifrada y nadie puede leer.</li>
                    <li>Datos de sus compras e inscripciones: productos, cursos, fechas, valores, método de pago y número de comprobante. No guardamos números de tarjetas ni claves bancarias.</li>
                    <li>Lista de deseos, carrito de compras y las reseñas que publique.</li>
                    <li>Datos técnicos de seguridad: la dirección IP y el navegador desde el que se realizan algunas acciones en la cuenta.</li>
                </ul>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">3. Para qué usamos sus datos</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Crear y administrar su cuenta, verificar su correo y permitirle recuperar la contraseña.</li>
                    <li>Procesar sus compras e inscripciones a cursos, registrar los pagos y generar las órdenes de venta y los comprobantes.</li>
                    <li>Comunicarnos con usted sobre sus pedidos, sus cursos o las solicitudes que nos haga.</li>
                    <li>Publicar en el sitio las reseñas que usted escriba, junto con su nombre.</li>
                    <li>Mantener la seguridad del sistema y llevar un registro de las acciones realizadas en él.</li>
                    <li>Elaborar reportes internos de ventas y cumplir las obligaciones legales y contables de la empresa.</li>
                </ul>
                <p class="mt-3">
                    No vendemos ni compartimos sus datos con terceros para fines comerciales. Solo los conocen los
                    proveedores que nos prestan los servicios necesarios para que el sitio funcione, como el
                    servidor donde está alojado y el servicio de envío de correos, y únicamente para esa labor.
                </p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">4. Sus derechos</h2>
                <p class="mb-2">Como titular de los datos, usted puede:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Conocer, actualizar y rectificar sus datos personales.</li>
                    <li>Pedir prueba de la autorización que nos dio.</li>
                    <li>Ser informado sobre el uso que les hemos dado a sus datos.</li>
                    <li>Revocar la autorización o pedir que eliminemos sus datos, cuando no exista una obligación legal o contractual de conservarlos.</li>
                    <li>Presentar quejas ante la Superintendencia de Industria y Comercio.</li>
                    <li>Consultar sus datos de forma gratuita.</li>
                </ul>
                <p class="mt-3">
                    Puede actualizar sus nombres y su teléfono en cualquier momento desde <strong>Mi cuenta &gt; Perfil</strong>.
                    Los datos de compras ya realizadas se conservan durante el tiempo que exige la ley, aunque la cuenta se desactive.
                </p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">5. Cómo hacer una consulta o un reclamo</h2>
                <p>
                    Escríbanos a <a href="mailto:paintingmistery20@gmail.com" class="text-red-600 hover:underline">paintingmistery20@gmail.com</a>
                    indicando su nombre, su número de documento, lo que solicita y un medio para responderle.
                </p>
                <ul class="list-disc pl-5 space-y-1 mt-2">
                    <li><strong>Consultas:</strong> se responden en máximo 10 días hábiles. Si no es posible, le informaremos el motivo y la nueva fecha, que no superará 5 días hábiles más.</li>
                    <li><strong>Reclamos</strong> (corrección, actualización, eliminación o incumplimiento): se responden en máximo 15 días hábiles, prorrogables hasta por 8 días hábiles más, informándole el motivo.</li>
                </ul>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">6. Seguridad de la información</h2>
                <p>
                    El sitio usa conexión cifrada (HTTPS), las contraseñas se guardan cifradas, cada usuario
                    solo accede a la información que le corresponde según su rol y se hacen copias de seguridad
                    periódicas de la información.
                </p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">7. Cookies y almacenamiento en el navegador</h2>
                <p>
                    Usamos cookies propias para mantener su sesión iniciada y proteger los formularios, y guardamos
                    en su navegador el contenido del carrito y de la lista de deseos. No usamos cookies de publicidad.
                </p>
            </div>

            <div class="pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">8. Autorización y vigencia</h2>
                <p>
                    Al crear una cuenta, usted acepta esta política y autoriza el tratamiento de sus datos para las
                    finalidades descritas. Sus datos se conservarán mientras tenga una cuenta activa y durante el
                    tiempo que exijan las normas legales y contables. Cualquier cambio en esta política se publicará
                    en esta página.
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
