<?php

namespace App\Models\Submissao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Evento extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'nome', 'descricao', 'tipo', 'dataInicio', 'dataFim', 'fotoEvento',
      'enderecoId', 'coordenadorId', 'numMaxTrabalhos', 'numMaxCoautores', 'hasResumo', 'coord_comissao_organizadora_id',
      'evento_pai_id',
  ];

  public function endereco(){
      return $this->belongsTo('App\Models\Submissao\Endereco', 'enderecoId');
  }

  public function atividade(){
      return $this->hasMany('App\Models\Submissao\Atividade', 'eventoId');
  }

  public function areas(){
      return $this->hasMany('App\Models\Submissao\Area', 'eventoId');
  }

  public function arquivoInfos(){
    return $this->hasMany('App\Models\Submissao\ArquivoInfo', 'evento_id');
  }

  public function modalidades(){
    return $this->hasMany('App\Models\Submissao\Modalidade', 'evento_id');
}

  public function coordenador(){
      return $this->belongsTo('App\Models\Users\User', 'coordenadorId');
  }


  public function coordComissaoCientifica(){
      return $this->belongsTo('App\Models\Users\CoordComissaoCientifica');
  }

  // public function revisors(){
  //     return $this->belongsToMany('App\Revisor', 'evento_revisor', 'evento_id', 'revisor_id');
  // }

  public function revisors() {
    return $this->hasMany('App\Models\Users\Revisor', 'evento_id');
  }

  function usuariosDaComissao(){
    return $this->belongsToMany('App\Models\Users\User','comissao_cientifica_eventos','evento_id','user_id');
  }

  function formEvento() {
    return $this->hasOne('App\Models\Submissao\FormEvento', 'eventoId');
  }

  function formSubTrab() {
    return $this->hasOne('App\Models\Submissao\FormSubmTraba', 'eventoId');
  }

  function trabalhos() {
    return $this->hasMany('App\Models\Submissao\Trabalho', 'eventoId');
  }

  public function usuariosDaComissaoOrganizadora() {
    return $this->belongsToMany('App\Models\Users\User', 'comissao_organizadora_eventos', 'evento_id', 'user_id');
  }

  public function promocoes() {
    return $this->hasMany('App\Models\Inscricao\Promocao', 'evento_id');
  }

  public function cuponsDeDesconto() {
    return $this->hasMany('App\Models\Inscricao\CupomDeDesconto', 'evento_id');
  }

  // public function revisores(){
  //   return $this->belongsToMany('App\Revisor', 'evento_revisors', 'evento_id', 'revisor_id')->withPivot('convite_aceito')->withTimestamps();
  // }

  public function categoriasParticipantes() {
    return $this->hasMany('App\Models\Inscricao\CategoriaParticipante', 'evento_id');
  }

  public function camposFormulario() {
    return $this->hasMany('App\Models\Inscricao\CampoFormulario', 'evento_id');
  }

  public function inscricaos() {
    return $this->hasMany('App\Models\Inscricao\Inscricao');
  }

  public function subeventos() {
    return $this->hasMany('App\Models\Submissao\Evento', 'evento_pai_id');
  }

  public function eventoPai() {
    return $this->belongsTo('App\Models\Submissao\Evento', 'evento_pai_id');
  }



  public function inscritos() {
    $users_inscricoes = DB::table('inscricaos AS i')
                ->join('users AS u', 'u.id', 'i.user_id')
                ->join('eventos AS e', 'e.id', 'i.evento_id')
                ->where('i.evento_id', $this->id)
                ->distinct('u.email')
                ->select('u.name AS nome', 'e.nome AS evento', 'u.email')
                ->get();
    

    if ($this->subeventos->count() > 0) {
      foreach ($this->subeventos as $subevento) {
        $users_inscricoes_sub = DB::table('inscricaos AS i')
                            ->join('users AS u', 'u.id', 'i.user_id')
                            ->join('eventos AS e', 'e.id', 'i.evento_id')
                            ->where('i.evento_id', $subevento->id)
                            ->distinct('u.email')
                            ->select('u.name AS nome', 'e.nome AS evento', 'u.email')
                            ->get();
        $users_inscricoes = $users_inscricoes->merge($users_inscricoes_sub);              
      }
    }

    return $users_inscricoes;
  }
}
