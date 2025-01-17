<?php

namespace App\Models\Submissao;

use Illuminate\Database\Eloquent\Model;

class ArquivoInfo extends Model
{
    protected $fillable = ['nome', 'path'];

    public function evento(){
        return $this->belongsTo('App\Models\Submissao\Evento');
    }
}
