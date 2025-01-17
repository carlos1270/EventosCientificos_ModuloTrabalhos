<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Models\Users\User;
use App\Models\Users\ComissaoEvento;
use App\Models\Submissao\Evento;
use App\Models\Submissao\Area;
use App\Models\Users\Revisor;
use App\Models\Submissao\Trabalho;
use App\Models\Submissao\Atribuicao;
use App\Models\Submissao\FormEvento;
use App\Models\Submissao\FormSubmTraba;
use App\Mail\EmailParaUsuarioNaoCadastrado;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ComissaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function informacoes(Request $request) {
        $evento = Evento::find($request->eventoId);

        $areas = Area::where('eventoId', $evento->id)->get();
        $areasId = Area::where('eventoId', $evento->id)->select('id')->get();
        $trabalhosId = Trabalho::whereIn('areaId', $areasId)->select('id')->get();
        $revisores = Revisor::where('evento_id', $evento->id)->get();
        $numeroRevisores = count($revisores);
        $trabalhosEnviados = Trabalho::whereIn('areaId', $areasId)->count();
        $trabalhosPendentes = Trabalho::whereIn('areaId', $areasId)->where('avaliado', 'processando')->count();
        $trabalhosAvaliados = 0;
        foreach ($trabalhosId as $trabalho) {
          $trabalhosAvaliados += $trabalho->atribuicoes()->where('parecer', '!=', 'processando')->count();
        }
        $etiquetas = FormEvento::where('eventoId', $evento->id)->first(); //etiquetas do card de eventos
        $etiquetasSubTrab = FormSubmTraba::where('eventoId', $evento->id)->first();
        $numeroComissao = count($evento->usuariosDaComissao);



        return view('coordenador.informacoes', [
                                                    'evento'                  => $evento,
                                                    'trabalhosEnviados'       => $trabalhosEnviados,
                                                    'trabalhosAvaliados'      => $trabalhosAvaliados,
                                                    'trabalhosPendentes'      => $trabalhosPendentes,
                                                    'numeroRevisores'         => $numeroRevisores,
                                                    'numeroComissao'          => $numeroComissao,

                                                  ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationData = $request->validate([
            'emailMembroComissao' => 'required|string|email',
            // 'especProfissional'=>'required|string',
        ]);

        $user = User::where('email',$request->input('emailMembroComissao'))->first();
        $evento = Evento::find($request->eventoId);
        $this->authorize('isCoordenadorOrCoordenadorDasComissoes', $evento);

        if($user == null){
          $passwordTemporario = Str::random(8);
          $coord = User::find($evento->coordenadorId);
          Mail::to($request->emailMembroComissao)->send(new EmailParaUsuarioNaoCadastrado(Auth()->user()->name, '  ', 'Comissao', $evento->nome, $passwordTemporario, ' ', $coord));
          $user = User::create([
            'email' => $request->emailMembroComissao,
            'password' => bcrypt($passwordTemporario),
            'usuarioTemp' => true,
          ]);
        } else {
            $usuarioDaComissa = $evento->usuariosDaComissao()->where('user_id', $user->id)->first();
            if ($usuarioDaComissa != null) {
                return redirect()->back()->withErrors(['cadastrarComissao' => 'Esse usuário já é membro da comissão.'])->withInput($validationData);
            }
        }

        // dd($user->id);
        $evento->usuariosDaComissao()->save($user);

        // $comissaoEventos->eventosId = $request->input('eventoId');
        // $comissaoEventos->userId = $user->id;
        // // $comissaoEventos->especProfissional = $request->input('especProfissional');
        // $comissaoEventos->save();


        $evento = Evento::find($request->input('eventoId'));
        $areas = Area::where('eventoId', $evento->id)->get();
        $revisores = $evento->revisores;
        $users = $evento->usuariosDaComissao;

        return redirect()->back()->with(['mensagem' => 'Membro da comissão cadastrado com sucesso!']);
    }


    public function coordenadorComissao(Request $request){
        $evento = Evento::find($request->input('eventoId'));
        $this->authorize('isCoordenador', $evento);

        $evento->coord_comissao_cientifica_id = $request->input('coordComissaoId');
        $evento->save();

        $areas = Area::where('eventoId', $evento->id)->get();
        $revisores = $evento->revisores;
        // dd($ComissaoEventos);
        $users = $evento->usuariosDaComissao;
        // return view('coordenador.detalhesEvento', [
        //                                                 'evento'    => $evento,
        //                                                 'areas'     => $areas,
        //                                                 'revisores' => $revisores,
        //                                                 'users'     => $users,
        //                                             ]);
        return redirect()->back()->with(['mensagem' => 'Coordenador da comissão científica salvo com sucesso!']);
    }

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $evento = Evento::find($request->evento_id);
        $this->authorize('isCoordenadorOrCoordenadorDasComissoes', $evento);

        $evento->usuariosDaComissao()->detach($id);

        return redirect()->back()->with(['mensagem' => 'Membro da comissão removido com sucesso!']);
    }
}
