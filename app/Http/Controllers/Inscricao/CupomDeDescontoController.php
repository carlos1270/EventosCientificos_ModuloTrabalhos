<?php

namespace App\Http\Controllers\Inscricao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submissao\Evento;
use Carbon\Carbon;
use App\Models\Inscricao\CupomDeDesconto;

class CupomDeDescontoController extends Controller
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $evento = Evento::find($request->evento_id);
        $this->authorize('isCoordenadorOrComissaoOrganizadora', $evento);
        $validadeData = $request->validate([
            'criarCupom'    => 'required',
            'identificador' => 'required|unique:cupom_de_descontos',
            'quantidade'    => 'required',
            'tipo_valor'    => 'required',
            'valor'         => 'required',
            'início'        => 'required|date',
            'fim'           => 'required|date|after:início',
        ]);

        if ($request->valor <= 0) {
            return redirect()->back()->withErrors(['valor' => 'Digite um valor positivo.'])->withInput($validadeData);
        }

        $cupomDeDesconto = new CupomDeDesconto();
        $cupomDeDesconto->evento_id             = $evento->id;
        $cupomDeDesconto->identificador         = $request->identificador;
        $cupomDeDesconto->valor                 = $request->valor;

        if ($request->quantidade == 0) {
            $cupomDeDesconto->quantidade_aplicacao  = -1;
        } else if ($request->quantidade < 0) {
            return redirect()->back()->withErrors(['quantidade' => 'Digite um valor positivo.'])->withInput($validadeData);
        } else {
            $cupomDeDesconto->quantidade_aplicacao = $request->quantidade;
        }

        $cupomDeDesconto->inicio                = $request->input('início');
        $cupomDeDesconto->fim                   = $request->fim;

        if ($request->tipo_valor == "porcentagem") {
            $cupomDeDesconto->porcentagem = true;
        } else {
            $cupomDeDesconto->porcentagem = false;
        }

        $cupomDeDesconto->save();

        return redirect()->back()->with(['mensagem' => 'Cupom salvo com sucesso!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        // dd($request);
        $cupom = CupomDeDesconto::find($id);
        $evento = $cupom->evento;
        $this->authorize('isCoordenadorOrComissaoOrganizadora', $evento);

        $validadeData = $request->validate([
            'editarCupom'                       => 'required',
            'identificador_cupom_'.$cupom->id   => 'required',
            'quantidade_cupom_'.$cupom->id      => 'required',
            'tipo_valor_cupom_'.$cupom->id      => 'required',
            'valor_cupom_'.$cupom->id           => 'required',
            'início_cupom_'.$cupom->id          => 'required|date',
            'fim_cupom_'.$cupom->id             => 'required|date|after:início_cupom_'.$cupom->id,
        ]);

        if ($request->input('valor_cupom_'.$cupom->id) < 0) {
            return redirect()->back()->withErrors(['valor_cupom_'.$cupom->id => 'Digite um valor positivo.'])->withInput($validadeData);
        }

        if ($request->input('quantidade_cupom_'.$cupom->id) == 0) {
            $cupom->quantidade_aplicacao  = -1;
        } else if ($request->input('quantidade_cupom_'.$cupom->id) < 0) {
            return redirect()->back()->withErrors(['quantidade_cupom_'.$cupom->id => 'Digite um valor positivo.'])->withInput($validadeData);
        } else {
            $cupom->quantidade_aplicacao = $request->input('quantidade_cupom_'.$cupom->id);
        }

        $cupom->identificador         = $request->input('identificador_cupom_'.$cupom->id);
        $cupom->valor                 = $request->input('valor_cupom_'.$cupom->id);
        $cupom->inicio                = $request->input('início_cupom_'.$cupom->id);
        $cupom->fim                   = $request->input('fim_cupom_'.$cupom->id);

        if ($request->input('tipo_valor_cupom_'.$cupom->id) == "porcentagem") {
            $cupom->porcentagem = true;
        } else {
            $cupom->porcentagem = false;
        }

        $cupom->update();

        return redirect()->back()->with(['mensagem' => 'Cupom atualizado com sucesso!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cupom = CupomDeDesconto::find($id);

        $this->authorize('isCoordenadorOrComissaoOrganizadora', $cupom->evento);
        // Checar se o cupom foi aplicado em alguma inscrição antes de excluir
        $cupom->delete();
        
        return redirect()->back()->with(['mensagem' => 'Cupom excluido com sucesso!']);
    }

    public function checar(Request $request)
    {
        if ($request->nome != null && $request->evento_id != null) {
            $cupom = CupomDeDesconto::where([['evento_id', $request->evento_id], ['identificador', $request->nome]])->first();
            $agora = Carbon::now('America/Recife');
            if ($cupom != null) {
                if ($agora < Carbon::parse($cupom->inicio) || $agora > Carbon::parse($cupom->fim . " 23:59:59.999")) {
                    return response()->json("expirado.", 419);
                } else {
                    return response()->json("OK.", 200);
                }
            } else {
                return response()->json("Cupom inválido.", 404);
            }
        }
        return abort(404);
    }
}
