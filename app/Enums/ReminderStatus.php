<?php

namespace App\Enums;

enum ReminderStatus: string
{
    case ATIVO = 'ATIVO';
    case INATIVO = 'INATIVO';
    case EXCLUIDO = 'EXCLUIDO';
}
