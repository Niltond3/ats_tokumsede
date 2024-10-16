<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    use \Znck\Eloquent\Traits\BelongsToThrough;
    protected $table = 'pedido';
    protected $appends = ['cliente'];
    protected $fillable = [
        'idDistribuidor',//chave estrangeira
        'total',
        'formaPagamento',
        'trocoPara',
        'horarioPedido',
        'horarioAceito',
        'horarioDespache',
        'horarioCancelado',
        'horarioEntrega',
        'status',
        'aceitoPor',
        'canceladoPor',
        'despachadoPor',
        'entreguePor',
        'editadoPor',
        'idEndereco',//chave estrangeira
        'agendado',
        'dataAgendada',
        'horaInicio',
        'horaFim',
        'origem',
        'obs',
        'retorno',
        'taxaEntrega',
        'statusChange',
        'idAdministrador',//chave estrangeira
        'idEntregador',//chave estrangeira
        'distanciaEntrega'
    ];
    public $timestamps = false;
    //STATUS
    const PENDENTE = 1;
    const CANCELADO_USUARIO = 2;
    const CANCELADO_NAO_LOCALIZADO = 3;
    const CANCELADO_TROTE = 4;
    const RECUSADO = 5;
    const ACEITO = 8;
    const DESPACHADO = 6;
    const ENTREGUE = 7;
    //FORMA_PAGAMENTO
    const OUTROS = 0;
    const DINHEIRO = 1;
    const CARTAO = 2;
    const PIX = 3;
    const TRANSFERENCIA = 4;
    const IFOOD = 5;
    //ORIGEM
    const APP_ANDROID = 1;
    const APP_IOS = 2;
    const PLATAFORMA = 3;

    //RELACIONAMENTO
    public function distribuidor()
    {
        return $this->belongsTo('App\Distribuidor', 'idDistribuidor');
    }
    public function endereco()
    {
        return $this->belongsTo('App\EnderecoCliente', 'idEndereco')->with('cliente');
    }
    public function entregador()
    {
        return $this->belongsTo('App\Entregador', 'idEntregador');
    }
    public function itens()
    {
        return $this->hasMany('App\ItemPedido', 'idPedido');
    }

    public function getClienteAttribute() {
        return $this->endereco->cliente;
    }
    // public function getRatingAttribute() {
    //     return $this->endereco->rating;
    // }
}
