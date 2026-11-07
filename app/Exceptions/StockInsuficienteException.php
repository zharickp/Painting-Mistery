<?php

namespace App\Exceptions;

use DomainException;

/**
 * No hay unidades suficientes en el inventario para lo que se quiere vender.
 * El mensaje ya viene listo para mostrárselo a la persona.
 */
class StockInsuficienteException extends DomainException
{
}
