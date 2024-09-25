<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;



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

	// function sendEmail(Request $request){
    //     echo json_encode($request);
	// 	$dados = $request->all();
    //     Mail::
	// 	Mail::send('mail', $dados, function($m) use ($dados){
	// 		$m->from('nilton4856@hotmail.com', $dados['nome']);
	// 		$m->to('niltond83@gmail.com', 'Contato TôKumSede');
	// 		$m->subject($dados['assunto']);
	// 	});

	// 	$out["msg"] = "ok";
	// 	echo json_encode($out);
	// }

    public function sendEmail(Request $request)
    {
        // Mail::to('niltond83@gmail.com')->send(new TestMail($request->name, $request->email, $request->message));
        // Laravel 8
        // $data["email"] = "test@gmail.com";
        // $data["title"] = "Techsolutionstuff";
        // $data["body"] = "This is test mail with attachment";

        // $files = [
        //     public_path('attachments/test_image.jpeg'),
        //     public_path('attachments/test_pdf.pdf'),
        // ];

        // Mail::send('mail.test_mail', $data, function($message)use($data, $files) {
        //     $message->to($data["email"])
        //             ->subject($data["title"]);

        //     foreach ($files as $file){
        //         $message->attach($file);
        //     }
        // });

        // $mailData = [
        //     'title' => 'This is Test Mail',
        //     'files' => [
        //         public_path('attachments/test_image.jpeg'),
        //         public_path('attachments/test_pdf.pdf'),
        //     ]
        // ];

        // Mail::to('to@gmail.com')->send(new TestMail($mailData));

        // echo "Mail send successfully !!";
    }
}
