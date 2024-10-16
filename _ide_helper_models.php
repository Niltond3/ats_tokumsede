<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $nome
 * @property string|null $email
 * @property string|null $login
 * @property string|null $senha
 * @property string|null $acessos
 * @property string|null $status
 * @property string|null $tipoAdministrador
 * @property string|null $dataCadastro
 * @property int|null $idDistribuidor
 * @property string|null $remember_token
 * @property string|null $token_fcm
 * @property string|null $token_fcm_mobile
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador query()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereAcessos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereDataCadastro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereIdDistribuidor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereSenha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereTipoAdministrador($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereTokenFcm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrador whereTokenFcmMobile($value)
 */
	class Administrador extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $nome
 * @property string|null $dddTelefone
 * @property string|null $telefone
 * @property int|null $sexo
 * @property string|null $dataNascimento
 * @property string|null $email
 * @property string|null $senha
 * @property int|null $tipoPessoa
 * @property int|null $precoAcertado
 * @property string|null $cpf
 * @property string|null $cnpj
 * @property int|null $logado
 * @property int|null $rating
 * @property int|null $status
 * @property string|null $regId
 * @property string|null $ultimoLogin
 * @property string|null $appVersion
 * @property string|null $outrosContatos
 * @property string|null $remember_token
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereAppVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereCnpj($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereDataNascimento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereDddTelefone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereLogado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereOutrosContatos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente wherePrecoAcertado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereRegId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereSenha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereSexo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereTelefone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereTipoPessoa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cliente whereUltimoLogin($value)
 */
	class Cliente extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $nome
 * @property string|null $cnpj
 * @property string|null $email
 * @property string|null $dddTelefone
 * @property string|null $telefonePrincipal
 * @property string|null $outrosContatos
 * @property int|null $status
 * @property int $idEnderecoDistribuidor
 * @property int $idHorarioFuncionamento
 * @property int $idTaxaEntrega
 * @property int $idNovoHorarioFuncionamento
 * @property string|null $tipoDistribuidor
 * @property int|null $idDistribuidor
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereCnpj($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereDddTelefone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereIdDistribuidor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereIdEnderecoDistribuidor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereIdHorarioFuncionamento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereIdNovoHorarioFuncionamento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereIdTaxaEntrega($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereOutrosContatos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereTelefonePrincipal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Distribuidor whereTipoDistribuidor($value)
 */
	class Distribuidor extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $logradouro
 * @property string|null $numero
 * @property string|null $bairro
 * @property string|null $complemento
 * @property string|null $cep
 * @property string|null $cidade
 * @property string|null $estado
 * @property string|null $referencia
 * @property string|null $apelido
 * @property int|null $atual
 * @property int $idCliente
 * @property string|null $observacao
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente query()
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereApelido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereAtual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereBairro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereCep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereCidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereComplemento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereIdCliente($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereLogradouro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereObservacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereReferencia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnderecoCliente whereStatus($value)
 */
	class EnderecoCliente extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

