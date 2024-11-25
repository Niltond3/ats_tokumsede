<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientePotencial extends Model
{
   	//STATUS
	const ACESSO_COMUM = 1;
	const SOLICITA_CONTATO = 2;
	const JA_CONTATADO = 3;
}
