<?php

namespace App\Models;

use App\Models\Distribuidor;
use App\Models\EnderecoCliente;
use App\Models\Entregador;
use App\Models\ItemPedido;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    const DESPACHADO = 6;
    const ENTREGUE = 7;
    const ACEITO = 8;
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
    const CLIENTE = 4;

    //RELACIONAMENTO
    public function distribuidor(): BelongsTo
    {
        return $this->belongsTo(Distribuidor::class, 'idDistribuidor');
    }
    public function endereco(): BelongsTo
    {
        return $this->belongsTo(EnderecoCliente::class, 'idEndereco')->with('cliente');
    }
    public function entregador(): BelongsTo
    {
        return $this->belongsTo(Entregador::class, 'idEntregador');
    }
    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'idPedido');
    }

    public function getClienteAttribute(): mixed {
        return $this->endereco->cliente;
    }
    public function getRatingAttribute() {
        return $this->endereco->rating;
    }
}
