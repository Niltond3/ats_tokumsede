<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class UtilController extends Controller
{
    // function testeEmail(Request $request){
	// 	$dados = $request->all();
	// 	return Mail::render('mail', $dados, function($m) use ($dados){
	// 		$m->from('mailgun@mg.aguasterrasanta.com.br', $dados->nome);
	// 		$m->to('contato@tokumsede.com.br', 'Contato TôKumSede');
	// 		$m->subject($dados->assunto);
	// 	});
	// }

	function sendEmail(Request $request){
		$dados = $request->all();
		Mail::send('mail', $dados, function($m) use ($dados){
			$m->from('nilton4856@hotmail.com', $dados['nome']);
			$m->to('niltond83@gmail.com', 'Contato TôKumSede');
			$m->subject($dados['assunto']);
		});

		$out["msg"] = "ok";
		echo json_encode($out);
	}
}
