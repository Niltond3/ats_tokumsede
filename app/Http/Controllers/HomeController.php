<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Entregador;
use App\Models\Distribuidor;
use App\Models\EnderecoDistribuidor;
use Illuminate\Support\Facades\Auth;
use \Barryvdh\Debugbar\Facades\Debugbar;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * Just for testing Vue components
         */
        // $this->middleware('auth');
        //\Auth::loginUsingId(101);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home-one');
    }

    public function homeOne()
    {
        return view('home-one');
    }

    public function homeTwo()
    {
        return view('home-two');
    }

    public function token(Request $request)
    {
        $administrador = Auth::user();
        $request->mobile?$administrador->token_fcm_mobile = $request->token:$administrador->token_fcm = $request->token;
        $administrador->save();
        return;
    }

    function getHomepage(Request $request){
        $guard = $request->guard;

        $auth = auth();

        $authCheck = auth()->check();

        if ($guard) {
            $auth = auth()->guard($guard);
            $authCheck = $auth->check();
        };

        Debugbar::info($authCheck);

        if(!$authCheck) return response('Sua sessão expirou. Por favor, refaça seu login.', 400);

            $user = $auth->user();

            if($user->status=="Excluido"){
                Auth::logout();
                return response('Usúário foi Removido. Contate o suporte.', 400);
            }

            if($user->getTable() == "cliente") {

                $total['clientesAtivos'] = Cliente::where('status', Cliente::ATIVO)->count();
                return $total;
            }

            if ($user->tipoAdministrador == "Entregador") {
                $total['clientesAtivos']        = Cliente::where('status', Cliente::ATIVO)->count();
                $total['entregadoresAtivos']    = Entregador::where('idDistribuidor', $user->idDistribuidor)->where('status', Entregador::ATIVO)->count();
                $total['pedidosEntregues']      = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->count();
                $total['pedidosAceitos']        = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ACEITO)->count();
                $total['pedidos']               = Pedido::where('idDistribuidor', $user->idDistribuidor)->count();

                $total['pedidosPlataforma']     = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('origem', Pedido::PLATAFORMA)->count();
                $total['pedidosAndroid']        = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('origem', Pedido::APP_ANDROID)->count();
                $total['pedidosIos']            = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('origem', Pedido::APP_IOS)->count();
                $total['percentualPlataforma']  = round(($total['pedidosPlataforma']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5
                $total['percentualAndroid']     = round(($total['pedidosAndroid']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5
                $total['percentualIos']         = round(($total['pedidosIos']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5

                $total['pedidosAno']            = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('pedido.horarioPedido','>=',date('Y-00-00 00:00:00'))->count();
                $total['pedidosMes']            = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('pedido.horarioPedido','>=',date('Y-m-00 00:00:00'))->count();
                $total['pedidosSemana']         = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('pedido.horarioPedido','>=',date('Y-m-d 00:00:00', strtotime('-'.date('w').' days')))->count();
                $total['pedidosDia']            = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('pedido.horarioPedido','>=',date('Y-m-d 00:00:00'))->count();

                return $total;
            } else if ($user->tipoAdministrador == "Distribuidor") {
                $total['clientesAtivos']        = Cliente::where('status', Cliente::ATIVO)->count();
                $total['entregadoresAtivos']    = Entregador::where('idDistribuidor', $user->idDistribuidor)->where('status', Entregador::ATIVO)->count();
                $total['pedidosEntregues']      = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->count();
                $total['pedidosAceitos']        = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ACEITO)->count();
                $total['pedidos']               = Pedido::where('idDistribuidor', $user->idDistribuidor)->count();

                $total['pedidosPlataforma']     = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('origem', Pedido::PLATAFORMA)->count();
                $total['pedidosAndroid']        = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('origem', Pedido::APP_ANDROID)->count();
                $total['pedidosIos']            = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('origem', Pedido::APP_IOS)->count();
                $total['percentualPlataforma']  = $total['pedidos']>0?round(($total['pedidosPlataforma']/$total['pedidos']*100)/5, 0)*5 : 0;//Arredondado p multiplo  de 5
                $total['percentualAndroid']     = $total['pedidos']>0?round(($total['pedidosAndroid']/$total['pedidos']*100)/5, 0)*5 : 0;//Arredondado p multiplo  de 5
                $total['percentualIos']         = $total['pedidos']>0?round(($total['pedidosIos']/$total['pedidos']*100)/5, 0)*5 : 0;//Arredondado p multiplo  de 5

                $total['pedidosAno']            = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('pedido.horarioPedido','>=',date('Y-00-00 00:00:00'))->count();
                $total['pedidosMes']            = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('pedido.horarioPedido','>=',date('Y-m-00 00:00:00'))->count();
                $total['pedidosSemana']         = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('pedido.horarioPedido','>=',date('Y-m-d 00:00:00', strtotime('-'.date('w').' days')))->count();
                $total['pedidosDia']            = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('pedido.horarioPedido','>=',date('Y-m-d 00:00:00'))->count();

                $total['faturamento'] = true;
                $total['faturamentoAnoPassado']    = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-00-00 00:00:00', strtotime('-1 years')))->where('pedido.horarioEntrega','<',date('Y-00-00 00:00:00'))->sum('total');
                $total['faturamentoMesPassado']    = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-00 00:00:00', strtotime('-1 months')))->where('pedido.horarioEntrega','<',date('Y-m-00 00:00:00'))->sum('total');
                $total['faturamentoSemanaPassada'] = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-d 00:00:00', strtotime('-'.(date('w')+7).' days')))->where('pedido.horarioEntrega','<',date('Y-m-d 00:00:00', strtotime('-'.date('w').' days')))->sum('total');
                $total['faturamentoDiaPassado']    = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-d 00:00:00', strtotime('-1 days')))->where('pedido.horarioEntrega','<',date('Y-m-d 00:00:00'))->sum('total');
                $total['faturamentoAno']           = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-00-00 00:00:00'))->sum('total');
                $total['faturamentoMes']           = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-00 00:00:00'))->sum('total');
                $total['faturamentoSemana']        = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-d 00:00:00', strtotime('-'.date('w').' days')))->sum('total');
                $total['faturamentoDia']           = Pedido::where('idDistribuidor', $user->idDistribuidor)->where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-d 00:00:00'))->sum('total');

                $total['percentualAno']            = $total['faturamentoAnoPassado'] != 0 ? number_format(($total['faturamentoAno']/$total['faturamentoAnoPassado']*100), 1, '.', '') : '';
                $total['percentualMes']            = $total['faturamentoMesPassado'] != 0 ? number_format(($total['faturamentoMes']/$total['faturamentoMesPassado']*100), 1, '.', '') : '';
                $total['percentualSemana']         = $total['faturamentoSemanaPassada'] != 0 ? number_format(($total['faturamentoSemana']/$total['faturamentoSemanaPassada']*100), 1, '.', '') : '';
                $total['percentualDia']            = $total['faturamentoDiaPassado'] != 0 ? number_format(($total['faturamentoDia']/$total['faturamentoDiaPassado']*100), 1, '.', '') : '';

                return $total;
            } else if ($user->tipoAdministrador == "Administrador"){
                $total['clientesAtivos']        = Cliente::where('status', Cliente::ATIVO)->count();
                $total['entregadoresAtivos']    = Entregador::where('status', Entregador::ATIVO)->count();
                $total['pedidosEntregues']      = Pedido::where('status', Pedido::ENTREGUE)->count();
                $total['pedidosAceitos']        = Pedido::where('status', Pedido::ACEITO)->count();
                $total['pedidos']               = Pedido::count();

                $total['pedidosPlataforma']     = Pedido::where('origem', Pedido::PLATAFORMA)->count();
                $total['pedidosAndroid']        = Pedido::where('origem', Pedido::APP_ANDROID)->count();
                $total['pedidosIos']            = Pedido::where('origem', Pedido::APP_IOS)->count();
                $total['percentualPlataforma']  = round(($total['pedidosPlataforma']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5
                $total['percentualAndroid']     = round(($total['pedidosAndroid']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5
                $total['percentualIos']         = round(($total['pedidosIos']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5

                $total['pedidosAno']            = Pedido::where('pedido.horarioPedido','>=',date('Y-00-00 00:00:00'))->count();
                $total['pedidosMes']            = Pedido::where('pedido.horarioPedido','>=',date('Y-m-00 00:00:00'))->count();
                $total['pedidosSemana']         = Pedido::where('pedido.horarioPedido','>=',date('Y-m-d 00:00:00', strtotime('-'.date('w').' days')))->count();
                $total['pedidosDia']            = Pedido::where('pedido.horarioPedido','>=',date('Y-m-d 00:00:00'))->count();

                // $distribuidor = Distribuidor::find(auth()->user()->idDistribuidor);
                // $enderecoDistribuidor = EnderecoDistribuidor::find($distribuidor->idEnderecoDistribuidor);
                // $lat = $enderecoDistribuidor->latitude;
                // $lon = $enderecoDistribuidor->longitude;
                // $curl = curl_init();
                // curl_setopt_array($curl, [
                //     CURLOPT_RETURNTRANSFER => 1,
                //     CURLOPT_URL => 'http://apiadvisor.climatempo.com.br/api/v1/locale/city?name='.$enderecoDistribuidor->cidade.'&state=PB&token=369885905c72968dcf620c05857d7350'
                // ]);
                // $clima = json_decode(curl_exec($curl));
                // curl_setopt_array($curl, [
                //     CURLOPT_RETURNTRANSFER => 1,
                //     CURLOPT_URL => 'http://apiadvisor.climatempo.com.br/api/v1/weather/locale/'.$clima[0]->id.'/current?token=369885905c72968dcf620c05857d7350'
                // ]);
                // $clima = json_decode(curl_exec($curl));
                // curl_close($curl);
                //return $clima
                $total['faturamento'] = true;
                $total['faturamentoAnoPassado']    = Pedido::where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-00-00 00:00:00', strtotime('-1 years')))->where('pedido.horarioEntrega','<',date('Y-00-00 00:00:00'))->sum('total');
                $total['faturamentoMesPassado']    = Pedido::where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-00 00:00:00', strtotime('-1 months')))->where('pedido.horarioEntrega','<',date('Y-m-00 00:00:00'))->sum('total');
                $total['faturamentoSemanaPassada'] = Pedido::where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-d 00:00:00', strtotime('-'.(date('w')+7).' days')))->where('pedido.horarioEntrega','<',date('Y-m-d 00:00:00', strtotime('-'.date('w').' days')))->sum('total');
                $total['faturamentoDiaPassado']    = Pedido::where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-d 00:00:00', strtotime('-1 days')))->where('pedido.horarioEntrega','<',date('Y-m-d 00:00:00'))->sum('total');
                $total['faturamentoAno']           = Pedido::where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-00-00 00:00:00'))->sum('total');
                $total['faturamentoMes']           = Pedido::where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-00 00:00:00'))->sum('total');
                $total['faturamentoSemana']        = Pedido::where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-d 00:00:00', strtotime('-'.date('w').' days')))->sum('total');
                $total['faturamentoDia']           = Pedido::where('status', Pedido::ENTREGUE)->where('pedido.horarioEntrega','>=',date('Y-m-d 00:00:00'))->sum('total');

                $total['percentualAno']            = $total['faturamentoAnoPassado'] != 0 ? number_format(($total['faturamentoAno']/$total['faturamentoAnoPassado']*100), 1, '.', '') : '';
                $total['percentualMes']            = $total['faturamentoMesPassado'] != 0 ? number_format(($total['faturamentoMes']/$total['faturamentoMesPassado']*100), 1, '.', '') : '';
                $total['percentualSemana']         = $total['faturamentoSemanaPassada'] != 0 ? number_format(($total['faturamentoSemana']/$total['faturamentoSemanaPassada']*100), 1, '.', '') : '';
                $total['percentualDia']            = $total['faturamentoDiaPassado'] != 0 ? number_format(($total['faturamentoDia']/$total['faturamentoDiaPassado']*100), 1, '.', '') : '';

                // $total['faturamentoAnoPassado']    = 'R$'.number_format($total['faturamentoAnoPassado'], 2, ',', '.');    // $total['faturamentoMesPassado']    = 'R$'.number_format($total['faturamentoMesPassado'], 2, ',', '.');    // $total['faturamentoSemanaPassada'] = 'R$'.number_format($total['faturamentoSemanaPassada'], 2, ',', '.'); // $total['faturamentoDiaPassado']    = 'R$'.number_format($total['faturamentoDiaPassado'], 2, ',', '.');   // $total['faturamentoAno']           = 'R$'.number_format($total['faturamentoAno'], 2, ',', '.');           // $total['faturamentoMes']           = 'R$'.number_format($total['faturamentoMes'], 2, ',', '.');           // $total['faturamentoSemana']        = 'R$'.number_format($total['faturamentoSemana'], 2, ',', '.');        // $total['faturamentoDia']           = 'R$'.number_format($total['faturamentoDia'], 2, ',', '.');
                return $total;
            } else {
                $total['clientesAtivos']        = Cliente::where('status', Cliente::ATIVO)->count();
                $total['entregadoresAtivos']    = Entregador::where('status', Entregador::ATIVO)->count();
                $total['pedidosEntregues']      = Pedido::where('status', Pedido::ENTREGUE)->count();
                $total['pedidosAceitos']        = Pedido::where('status', Pedido::ACEITO)->count();
                $total['pedidos']               = Pedido::count();

                $total['pedidosPlataforma']     = Pedido::where('origem', Pedido::PLATAFORMA)->count();
                $total['pedidosAndroid']        = Pedido::where('origem', Pedido::APP_ANDROID)->count();
                $total['pedidosIos']            = Pedido::where('origem', Pedido::APP_IOS)->count();
                $total['percentualPlataforma']  = round(($total['pedidosPlataforma']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5
                $total['percentualAndroid']     = round(($total['pedidosAndroid']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5
                $total['percentualIos']         = round(($total['pedidosIos']/$total['pedidos']*100)/5, 0)*5;//Arredondado p multiplo  de 5

                $total['pedidosAno']            = Pedido::where('pedido.horarioPedido','>=',date('Y-00-00 00:00:00'))->count();
                $total['pedidosMes']            = Pedido::where('pedido.horarioPedido','>=',date('Y-m-00 00:00:00'))->count();
                $total['pedidosSemana']         = Pedido::where('pedido.horarioPedido','>=',date('Y-m-d 00:00:00', strtotime('-'.date('w').' days')))->count();
                $total['pedidosDia']            = Pedido::where('pedido.horarioPedido','>=',date('Y-m-d 00:00:00'))->count();

                return $total;
            }

    }
}
