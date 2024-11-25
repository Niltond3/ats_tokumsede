<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Administrador;
use App\Models\Distribuidor;
use App\Models\Composicao;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Pedido;
use Aws\Sns\SnsClient;

abstract class Controller extends BaseController
{
	public $composicoesArray = array();

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function maior30Minutos($dataAgendada) {
	    $unix_data1 = strtotime(date("Y-m-d H:i:s"));
	    $unix_data2 = strtotime($dataAgendada);

	    $nMinutos = ($unix_data2 - $unix_data1) / 60;

	    if ($nMinutos > 30) {
	        return false;
	    } else {
	        return true;
	    }
	}
	/**
     * @param int $idDistribuidor
	 * @param Produto Object $produto
	 * @param int $qtd um inteiro representando a quantidade que está sendo acrescida ou diminuída do estoque
	 * @param boolean $soma, usado para definir se o estoque está sendo atualizado para mais ou para menos.
	 * 						Quando 'true' indica adição de estoque, quando 'false' indica subtração.
	 *
     *
     */
	function atualizaEstoque($idDistribuidor, $produto, $qtd, $soma){
		if($produto->composicao){
			$componentes = Composicao::where("idComposicao", $produto->id)->get();
			for($i = 0; $i < count($componentes); $i++){
				$componente = Produto::find($componentes[$i]->idComponente);
				$this->atualizaEstoque($idDistribuidor, $componente, ($qtd * $componentes[$i]->quantidade), $soma);
			}
		}
		if($produto->componente){
			$composicoes = Composicao::where("idComponente", $produto->id)->get();
			for($i = 0; $i < count($composicoes); $i++){
				$composicao = Produto::find($composicoes[$i]->idComposicao);
				$this->addComposicao($composicao);
			}
		}
		$estoque = Estoque::where([["idDistribuidor", $idDistribuidor], ["idProduto", $produto->id]])->get()->first();
		// $ultimaCompra = Compra::with('itens')->where([["idDistribuidor", $idDistribuidor], ["status", Compra::ACEITO]])->get()->first();
		// $ultimaCompra = DB::table('compra')->where([["idDistribuidor", $idDistribuidor], ["status", Compra::ACEITO]])
		// 	->join('itensCompra')->where($produto->id, '=', 'itensCompra.idProduto')
		// 	->select('itensCompra.*')
		// 	->get();
		if($soma){
			$estoque->quantidade += $qtd;
		}else{
			$estoque->quantidade -= $qtd;
			if($estoque->quantidade<=0){
				$administradores = Administrador::where([['idDistribuidor', $idDistribuidor],['status','Ativo']])->orwhere([['tipoAdministrador', 'Administrador'],['status', 'Ativo']])->get();
				$distribuidor = Distribuidor::select('nome')->find($idDistribuidor);
				$headers = array(
					'Authorization: key=AAAA92nZhZY:APA91bFbwC0HrbjmBGjQIrXtPrPZcH5gmCFK9y1jlQucH03VlNOHlO45Ru5Dk69iplWGYcnsVUbhG2hMH5AgoZzU9GCK0DmFplBjLz-QAmlFM5YOpmFFOr5ak--7l-yLahiaJKPPIUct',
					'Content-Type: application/json'
				);
				$msg = array(
					'title' => 'Estoque '.$produto->nome.' Zerado',
					'body' => 'Distribuidor '.$distribuidor->nome.'. O produto '.$produto->nome.' não será apresentado no aplicativo do cliente enquanto o estoque estiver zerado.',
					'tag' => 'Produto '.$produto->id,
					'icon' => '/images/logo-icon.png',
					'click_action' => 'https://adm.tokumsede.com'
				);
				foreach ($administradores as $administrador) {
					// set only for one for safety
					if($administrador->token_fcm!=null){
						$fields = array(
							'priority' => 'high',
							'to' => $administrador->token_fcm,
							'notification' => $msg
						);
						$ch = curl_init();
						curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
						curl_setopt( $ch,CURLOPT_POST, true );
						curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
						curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
						curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, true );
						curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
						$result = curl_exec($ch );
						curl_close( $ch );
					}
					// set only for one for safety
					if($administrador->token_fcm_mobile!=null){
						$fields = array(
							'priority' => 'high',
							'to' => $administrador->token_fcm_mobile,
							'notification' => $msg
						);
						$ch = curl_init();
						curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
						curl_setopt( $ch,CURLOPT_POST, true );
						curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
						curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
						curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, true );
						curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
						$result = curl_exec($ch );
						curl_close( $ch );
					}
				}
			}
		}
		$estoque->save();
	}
	function addComposicao($produto){
		if(!in_array($produto->id, $this->composicoesArray)){
			array_push($this->composicoesArray, $produto->id);
		}
		if($produto->componente){
		$composicoes = Composicao::where("idComponente", $produto->id)->get();
			for($i = 0; $i < count($composicoes); $i++){
				$composicao = Produto::find($composicoes[$i]->idComposicao);
				$this->addComposicao($composicao);
			}
		}
	}
	function atualizaComposicoes($idDistribuidor){
		foreach($this->composicoesArray as $composicaoId){
			$min = PHP_INT_MAX;
			$comp = Composicao::where("idComposicao", $composicaoId)->get();
			foreach($comp as $c){
				$estoqueComponente = Estoque::where([["idDistribuidor", $idDistribuidor], ["idProduto", $c->idComponente]])->get()->first();
				$auxMin = intval($estoqueComponente->quantidade/$c->quantidade);
				$min = $auxMin < $min ? $auxMin : $min;
			}
			$estoqueComposicao = Estoque::where([["idDistribuidor", $idDistribuidor], ["idProduto", $composicaoId]])->get()->first();
			$estoqueComposicao->quantidade = $min;
			$estoqueComposicao->save();
		}
	}

    function escape($variavel) {

		if (!isset($_SESSION))
			session_start();

		if (isset($_GET[$variavel])) {
			return stripslashes($_GET[$variavel]);
		} elseif (isset($_POST[$variavel])) {
			return stripslashes($_POST[$variavel]);
		} elseif (isset($_SESSION[$variavel])) {
			return @stripslashes($_SESSION[$variavel]);
		} else {
			return null;
		}

	}
	function gcmSend($registrationIds, $idCliente, $idPedido, $status, $resposta, $origem, $alert){

		switch($status){
			case Pedido::PENDENTE:
				$title = "Pedido n° $idPedido efetuado!";
				$message = "Seu pedido foi efetuado com sucesso. Aguarde confirmação do distribuidor.";
				break;
			case Pedido::CANCELADO_USUARIO:
				$title = "Pedido n° $idPedido cancelado!";
				$message = "Seu pedido foi cancelado.";
				break;
			case Pedido::CANCELADO_NAO_LOCALIZADO:
				$title = "Pedido n° $idPedido cancelado.";
				$message = "Seu endereço não foi localizado pelo entregador.";
				break;
			case Pedido::CANCELADO_TROTE:
				$title = "Pedido n° $idPedido cancelado.";
				$message = "Seu pedido foi cancelado pelo distribuidor.";
				break;
			case Pedido::RECUSADO:
				$title = "Pedido n° $idPedido cancelado.";
				$message = $resposta == NULL || $resposta == ""  ? "Seu pedido foi cancelado pelo distribuidor." : $resposta;
				break;
			case Pedido::ACEITO:
				$title = "Pedido n° $idPedido aceito!";
				$message = "Seu pedido está a caminho.";
				break;
			case Pedido::ENTREGUE:
				$title = "Pedido n° $idPedido entregue.";
				$message = "Seu pedido já foi entregue. Obrigado por utilizar nossos serviços.";
				break;
		}

		if($origem == Pedido::APP_ANDROID){
			return $this->sendAndroidGCM($registrationIds, $idCliente, $idPedido, $status, $resposta, $origem, $title, $message);
		}else if($origem == Pedido::APP_IOS){
			//return $this->sendIos($registrationIds, $idCliente, $idPedido, $status, $resposta, $origem, $title, $message);
			return $this->sendIOSGCM($registrationIds, $idCliente, $idPedido, $status, $resposta, $origem, $title, $message, $alert);
		}

	}

	function sendAndroidGCM($registrationIds, $idCliente, $idPedido, $status, $resposta, $origem, $title, $message){
		$msg = array(
			'message' 	=> $message,
			'title'		=> $title,
			'vibrate'	=> $status == Pedido::ENTREGUE ? 0 : 1,
			'sound'		=> $status == Pedido::ENTREGUE ? 0 : 1,
			'idCliente' => $idCliente,
			'idPedido' 	=> $idPedido,
			'status' 	=> $status,
			'resposta' 	=> $resposta,
			'alert'		=> '1',
			'image' 	=> 'www/ic_laucher.png',
//			'content-available' => 1,
			'notId'		=> microtime(true),
			'style' 	=> 'inbox',
//			'ledColor'  => [0, 0, 0, 255],
//			'vibrationPattern' =>  [100, 300, 100, 300],
			'priority'	=> 1,
			'badge' 	=> 1
		);
		$fields = array(
			'registration_ids' 	=> array($registrationIds),
			'data'			=> $msg
		);

		$ok = $this->sendCurl($fields, $origem);
		return $ok;
	}

	function sendIOSGCM($registrationIds, $idCliente, $idPedido, $status, $resposta, $origem, $title, $message, $alert){
		/*
		$notification = array(
			'title' => $title,
			'body' => $message
		);
		$data = array(
			'idCliente' => $idCliente,
			'idPedido' 	=> $idPedido,
			'status' 	=> $status,
			'resposta' 	=> $resposta,
			'image' 	=> 'www/ic_laucher.png',
			'content-available' => 1
		);
		$aps = array(
			'to' 				=> $registrationIds,
			'notification'  	=> $notification,
			'data'				=> $data,
			'content-available' => 1,
			'vibrate'			=> $status == Pedido::ENTREGUE ? 0 : 1,
			'sound'				=> $status == Pedido::ENTREGUE ? 0 : 1,
			'priority'			=> 'high'
		);
		$fields = array(
			'aps' 	=> $aps,
			'notId'	=> microtime(true)
		);

		 */

		if($alert){
			$aps = array(
				'alert' => array(
					'title' => $title,
					'body' 	=> $message
				),
				'sound'	=> 'default',
				'badge' => 1,
				'content-available' => 1
			);
			$fields = array(
				'aps' 				=> $aps,
				'notId'				=> intval(microtime(true)),
				'title' 			=> $title,
				'body' 				=> $message,
				'idCliente' 		=> $idCliente,
				'idPedido' 			=> $idPedido,
				'status' 			=> $status,
				'resposta' 			=> $resposta,
				'alert'				=> '1',
				'image' 			=> 'www/ic_laucher.png',
				'priority'			=> 'high'
			);
		}else{
			$aps = array(
				'sound'	=> 'default',
				'badge' => 1,
				'content-available' => 1
			);
			$fields = array(
				'aps' 				=> $aps,
				'notId'				=> intval(microtime(true)),
				'idCliente' 		=> $idCliente,
				'idPedido' 			=> $idPedido,
				'status' 			=> $status,
				'resposta' 			=> $resposta,
				'alert'				=> '0',
				'image' 			=> 'www/ic_laucher.png',
				'priority'			=> 'high'
			);
		}

		//$ok = $this->sendCurl($fields, $origem);
		$ok = $this->sendSns($fields, $registrationIds, $idCliente, $alert);
		return $ok;
	}

	function sendSns($fields, $token, $idCliente, $alert){
		$sanboxIos = "arn:aws:sns:sa-east-1:174353285667:app/APNS_SANDBOX/ToKumSede";
		$productionIos = "arn:aws:sns:sa-east-1:174353285667:app/APNS/ToKumSede";
		$client = SnsClient::factory(array(
		    'credentials' => array(
		        'key'    => env('AWS_ACCESS_KEY_ID' ),
		        'secret' => env('AWS_SECRET_ACCESS_KEY' ),
			),
			'version' => 'latest',
			'region'  => 'sa-east-1',
			'http'    => [
				'verify' => false
			]
		));
		// 1 - CreatePlatformEndpoint
		$endPoint = $client->createPlatformEndpoint(array(
			'PlatformApplicationArn' => $productionIos,
			'Token' => $token,
			'CustomUserData' => 'Cliente'.$idCliente
		));
		//'PlatformApplicationArn' => $sanboxIos,

		// 2 - Publish
		$result = $client->publish(array(
		    'TargetArn' => $endPoint['EndpointArn'],
		    'MessageStructure' => 'json',
		    'Message' => json_encode(array('default' => ($alert ? $fields['title'] : '-' ), 'APNS' => json_encode($fields)))
		));
		//'Message' => json_encode(array('default' => ($alert ? $fields['title'] : '-' ), 'APNS_SANDBOX' => json_encode($fields)))

		// 3 - DeleteEndpoint
		//$client->deleteEndpoint(array('EndpointArn' => $endPoint['EndpointArn']));

		if($result['MessageId']){
			return true;
		}else{
			return false;
		}

	}


	function sendAndroid($registrationIds, $idCliente, $idPedido, $status, $resposta, $origem, $title, $message){

		$data = array(
			'idCliente' => $idCliente,
			'idPedido' 	=> $idPedido,
			'status' 	=> $status,
			'resposta' 	=> $resposta,
			'title'		=> $title,
			'message'	=> $message
		);

		$notification = array(
			'body' 			=> $message,
			'title'			=> $title,
			'sound'			=> 'default',
			'icon'			=> 'large_icon',
			'click_action'	=> 'FCM_PLUGIN_ACTIVITY',
			'sound'			=> 'default'
		);

		$fields1 = array(
			'to' 				=> $registrationIds,
			'notification' 		=> $notification,
			'data'				=> $data,
			'priority'			=> 'high',
			'content_available' => true
		);

		$ok = $this->sendCurl($fields1, $origem);

		return $ok;
	}

	function sendCurl($fields, $origem){
		//https://gcm-http.googleapis.com/gcm/send
		//https://fcm.googleapis.com/fcm/send
		//https://android.googleapis.com/gcm/send

        // $context = stream_context_create(array('ssl'=>array(
        //     'verify_peer' => true,
        //     'cafile' => '/var/www/tokumsede/etc/certificados/ca-bundle.crt'
        // )));
		// libxml_set_streams_context($context);

		$headers = array (
			'Authorization:key='.GOOGLE_API_KEY,//'AIzaSyBJvOYNx0GwuUKv6GVo2WMo7mYP7vAwk18',
			'Content-Type:application/json'
		);

		$ch = curl_init();
		if($origem == Pedido::APP_ANDROID){
			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		}else if($origem == Pedido::APP_IOS){
			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		}else{
			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		}
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );

		$ok = true;
		if (curl_errno($ch)){
	        $ok = false;
	        //echo 'GCM error: ' . curl_error($ch);
	    }else{
	    	//echo var_dump($result);
	    }
		curl_close($ch);

		return $ok;
	}

	function sendIos($registrationIds, $idCliente, $idPedido, $status, $resposta, $origem, $title, $message){

		$data = array(
			'idCliente' => $idCliente,
			'idPedido' 	=> $idPedido,
			'status' 	=> $status,
			'resposta' 	=> $resposta
		);

		$notification = array(
			'title'		=> $title,
			'body' 		=> $message
		);

		$fields['aps'] = array(
			'registration_ids' 	=> array($registrationIds),
			'notification'		=> $notification,
			'content-available' => 1,
			'vibrate'			=> $status == Pedido::ENTREGUE ? 0 : 1,
			'sound'				=> $status == Pedido::ENTREGUE ? 0 : 1,
			'data'				=> $data
		);

		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', CERTIFICADOS.'aps_developer_identity.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', 'tokumsede');

		// Open a connection to the APNS server
		//$gateway = 'gateway.push.apple.com:2195';
		$gateway = 'gateway.sandbox.push.apple.com:2195';
		$fp = stream_socket_client($gateway, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp){
			echo "Failed to connect: $err $errstr" . PHP_EOL;
			return false;
		}

		// Encode the payload as JSON
		$payload = json_encode($fields);
		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $registrationIds) . pack('n', strlen($payload)) . $payload;
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));

		// Close the connection to the server
		fclose($fp);
		if (!$result){
			echo 'Message not delivered' . PHP_EOL;
			return false;
		}else{
			echo 'Message successfully delivered' . PHP_EOL;
			return true;
		}

	}
}
