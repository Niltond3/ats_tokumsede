<?php

namespace App\Enums;

enum TipoAdministrador: string {
    case ADMIN = 'Administrador';
    case DISTRIBUIDOR = 'Distribuidor';
    case ATENDENTE = 'Atendente';
}
