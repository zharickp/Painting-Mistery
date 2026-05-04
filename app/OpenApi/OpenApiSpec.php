<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Painting Mistery API",
    version: "1.0.0",
    description: "Documentación de la API"
)]
#[OA\Server(
    url: "http://127.0.0.1:8000/api",
    description: "Servidor local"
)]
class OpenApiSpec {}
