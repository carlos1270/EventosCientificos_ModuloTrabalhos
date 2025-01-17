@extends('coordenador.detalhesEvento')

@section('menu')
    <!-- Trabalhos -->
    <div id="divListarTrabalhos" style="display: block">

      <div class="row ">
        <div class="col-sm-6">
            <h1 class="">Trabalhos</h1>
        </div>

        <div class="col-sm-3"></div>
        <div class="col-sm-3">
          <form method="GET" action="{{route('distribuicao')}}">
            <input type="hidden" name="eventoId" value="{{$evento->id}}">
            <button onclick="event.preventDefault();" data-toggle="modal" data-target="#modalDistribuicaoAutomatica" class="btn btn-primary" style="width:100%">
              {{ __('Distribuir trabalhos') }}
            </button>
          </form>
          <a class="btn btn-primary col-sm" href="{{route('evento.downloadResumos', $evento)}}">Baixar resumos</a>
        </div>
      </div>

    {{-- Tabela Trabalhos --}}
    <div class="btn-group mb-2" role="group" aria-label="Button group with nested dropdown">

        <div class="btn-group" role="group">
        <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Opções
        </button>
        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
            <a class="dropdown-item" href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'titulo', 'asc', 'rascunho'])}}">
                Todos
            </a>
            <a class="dropdown-item" href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'titulo', 'asc', 'arquivado'])}}">
                Arquivados
            </a>
            <a class="dropdown-item disabled" href="#" >
                Submetidos
            </a>
            <a class="dropdown-item disabled" href="#" >
                Aprovados
            </a>
            <a class="dropdown-item disabled" href="#" >
                Corrigidos
            </a>
            <a class="dropdown-item disabled" href="#" >
                Rascunhos
            </a>
        </div>
        </div>
    </div>
    @foreach ($trabalhosPorModalidade as $trabalhos)
        <div class="row justify-content-center" style="width: 100%;">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        @if(!is_null($trabalhos->first()))
                            <h5 class="card-title">Modalidade: <span class="card-subtitle mb-2 text-muted" >{{$trabalhos[0]->modalidade->nome}}</span>
                        @endif
                        <div class="row table-trabalhos">
                        <div class="col-sm-12">

                            <form action="{{route('atribuicao.check')}}" method="post">
                            @csrf
                            {{-- <div class="row">
                                <div class="col-sm-9"></div>
                                <div class="col-sm-3">
                                <button type="submit" class="btn btn-primary" style="width:100%">
                                    {{ __('Distribuir em lote') }}
                                </button>
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="col-sm-12">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                </div>
                            </div>

                            <input type="hidden" name="eventoId" value="{{$evento->id}}">
                            <br>
                            <table class="table table-hover table-responsive-lg table-sm table-striped">
                                <thead>
                                <tr>
                                    <th scope="col" style="text-align:center">
                                        @if(!is_null($trabalhos->first()))
                                        <input type="checkbox" id="selectAllCheckboxes{{$trabalhos[0]->modalidade->id}}" onclick="marcarCheckboxes({{$trabalhos[0]->modalidade->id}})">
                                        <label for="selectAllCheckboxes{{$trabalhos[0]->modalidade->id}}" style="margin-bottom: 0px;">Selecionar</label>
                                        @else
                                        Selecionar
                                        @endif
                                    </th>
                                    <th scope="col">
                                    Título
                                    <a href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'titulo', 'asc'])}}">
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                    </a>
                                    <a href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'titulo', 'desc'])}}">
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                    </a>
                                    </th>
                                    <th scope="col">
                                    Área
                                    <a href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'areaId', 'asc'])}}">
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                    </a>
                                    <a href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'areaId', 'desc'])}}">
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                    </a>
                                    </th>
                                    <th scope="col">
                                    Autor
                                    <a href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'autor', 'asc'])}}">
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                    </a>
                                    <a href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'autor', 'desc'])}}">
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                    </a>
                                    </th>
                                    <th scope="col">
                                    Revisores
                                    {{-- <a href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'areaId', 'desc'])}}">
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                    </a>
                                    <a href="{{route('coord.listarTrabalhos',[ 'eventoId' => $evento->id, 'areaId', 'desc'])}}">
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                    </a> --}}
                                    </th>
                                    <th scope="col">Data</th>
                                    <th scope="col">Atribuir</th>
                                    <th scope="col">Arquivar</th>
                                    <th scope="col">Editar</th>
                                </tr>
                                </thead>

                                <tbody>
                                @php $i = 0; @endphp
                                @foreach($trabalhos as $trabalho)

                                <tr>
                                    <td style="text-align:center">
                                        <input type="checkbox" aria-label="Checkbox for following text input" name="id[]" class="modalidade{{$trabalho->modalidade->id}}" value="{{$trabalho->id}}">
                                    </td>
                                    <td>
                                        @if ($trabalho->arquivo && count($trabalho->arquivo) > 0)
                                            <a href="{{route('downloadTrabalho', ['id' => $trabalho->id])}}">
                                                <span class="d-inline-block text-truncate" class="d-inline-block" tabindex="0" data-toggle="tooltip" title="{{$trabalho->titulo}}" style="max-width: 150px;">
                                                    {{$trabalho->titulo}}
                                                </span>
                                            </a>
                                        @else
                                            <span class="d-inline-block text-truncate" class="d-inline-block" tabindex="0" data-toggle="tooltip" title="{{$trabalho->titulo}}" style="max-width: 150px;">
                                                {{$trabalho->titulo}}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" class="d-inline-block" tabindex="0" data-toggle="tooltip" title="{{$trabalho->area->nome}}" style="max-width: 150px;">
                                        {{$trabalho->area->nome}}
                                        </span>

                                    </td>
                                    <td>{{$trabalho->autor->name}}</td>
                                    <td>
                                        {{count($trabalho->atribuicoes)}}
                                        {{-- @if (count($trabalho->atribuicoes) == 0)
                                        Nenhum revisor atribuído
                                        @elseif (count($trabalho->atribuicoes) == 1)
                                        {{count($trabalho->atribuicoes)}}
                                        @endif --}}
                                    </td>
                                    <td>
                                        {{ date("d/m/Y H:i", strtotime($trabalho->created_at) ) }}

                                    </td>
                                    <td style="text-align:center">
                                        <a href="#" data-toggle="modal" data-target="#modalTrabalho{{$trabalho->id}}">
                                        <i class="fas fa-file-alt"></i>
                                        </a>

                                    </td>

                                    <td style="text-align:center">
                                        @if ($trabalho->status == 'arquivado')
                                            <a href="{{ route('trabalho.status', [$trabalho->id, 'rascunho']) }}" class="btn btn-info" >
                                                <i class="fas fa-folder-open"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('trabalho.status', [$trabalho->id, 'arquivado'] ) }}" class="btn btn-info" >
                                                <i class="fas fa-archive"></i>
                                            </a>
                                        @endif
                                    </td>
                                        <td style="text-align:center">
                                            <a href="{{ route('coord.trabalho.edit', ['id' => $trabalho->id]) }}" >
                                                <i class="fas fa-edit"></i>
                                            </a>

                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>
<!-- End Trabalhos -->
<!-- Modal Trabalho -->
<div class="modal fade" id="modalDistribuicaoAutomatica" tabindex="-1" role="dialog" aria-labelledby="modalDistribuicaoAutomatica" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #114048ff; color: white;">
        <h5 class="modal-title" id="exampleModalCenterTitle">Distribuir trabalhos automaticamente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="GET" action="{{ route('distribuicaoAutomaticaPorArea') }}" id="formDistribuicaoPorArea">
        <div class="modal-body">
          <input type="hidden" name="eventoId" value="{{$evento->id}}">
          <div class="row">
            <div class="col-sm-12">
                <input type="hidden" name="distribuirTrabalhosAutomaticamente" value="{{$evento->id}}">
                <label for="areaId" class="col-form-label">{{ __('Área') }}</label>
                <select class="form-control @error('área') is-invalid @enderror" id="areaIdformDistribuicaoPorArea" name="área" required>
                    <option value="" disabled selected hidden>-- Área --</option>
                    @foreach($areas as $area)
                        <option value="{{$area->id}}">{{$area->nome}}</option>
                    @endforeach
                </select>

                @error('área')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
          </div>
          <div class="row">
              <div class="col-sm-12">
                  <label for="numeroDeRevisoresPorTrabalho" class="col-form-label">{{ __('Número de revisores por trabalho') }}</label>
              </div>
          </div>
          <div class="row justify-content-center">
              <div class="col-sm-12">
                  <input id="numeroDeRevisoresPorTrabalhoInput" type="number" min="1" class="form-control @error('numeroDeRevisoresPorTrabalho') is-invalid @enderror" name="numeroDeRevisoresPorTrabalho" value="{{ old('numeroDeRevisoresPorTrabalho') }}" required autocomplete="numeroDeRevisoresPorTrabalho" autofocus>

                  @error('numeroDeRevisoresPorTrabalho')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                  @enderror
              </div>

          </div>{{-- end row--}}
        </div>
      </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        <button id="numeroDeRevisoresPorTrabalhoButton" onclick="document.getElementById('formDistribuicaoPorArea').submit();" type="button" class="btn btn-primary">Distribuir</button>
      </div>
    </div>
  </div>
</div>

@foreach ($trabalhosPorModalidade as $trabalhos)
    @foreach ($trabalhos as $trabalho)
        <!-- Modal Trabalho -->
    <div class="modal fade" id="modalTrabalho{{$trabalho->id}}" tabindex="-1" role="dialog" aria-labelledby="labelModalTrabalho{{$trabalho->id}}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #114048ff; color: white;">
            <h5 class="modal-title" id="exampleModalCenterTitle">Trabalho</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <div class="row justify-content-center">
                <div class="col-sm-6">
                <h5>Título</h5>
                <p id="tituloTrabalho">{{$trabalho->titulo}}</p>
                </div>
                <div class="col-sm-6">
                <h5>Autores</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">E-mail</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Vinculação</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{$trabalho->autor->email}}</td>
                        <td>{{$trabalho->autor->name}}</td>
                        <td>Autor</td>
                    </tr>
                    @foreach ($trabalho->coautors as $coautor)
                    @if($coautor->user->id != $trabalho->autorId)
                    <tr>
                        <td>{{$coautor->user->email}}</td>
                        <td>{{$coautor->user->name}}</td>
                        <td>
                        Coautor
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @if ($trabalho->resumo != "")
                <div class="row justify-content-center">
                <div class="col-sm-12">
                    <h5>Resumo</h5>
                    <p id="resumoTrabalho">{{$trabalho->resumo}}</p>
                </div>
                </div>
            @endif
            @if (count($trabalho->atribuicoes) > 0)
                <div class="row justify-content-center">
                <div class="col-sm-12">
                    <h5>Revisores atribuidos ao trabalho</h5>
                </div>
            @else
                <div class="row justify-content-center">
                <div class="col-sm-12">
                </div>
            @endif
            @foreach ($trabalho->atribuicoes as $i => $revisor)
                @if ($i % 3 == 0) </div><div class="row"> @endif
                <div class="col-sm-4">
                    <div class="card" style="width: 13.5rem; text-align: center;">
                    <img class="" src="{{asset('img/icons/user.png')}}" width="100px" alt="Revisor" style="position: relative; left: 30%; top: 10px;">
                    <div class="card-body">
                        <h6 class="card-title">{{$revisor->user->name}}</h6>
                        <strong>E-mail</strong>
                        <p class="card-text">{{$revisor->user->email}}</p>
                        <form action="{{ route('atribuicao.delete', ['id' => $revisor->id]) }}" method="post">
                        @csrf
                        <input type="hidden" name="eventoId" value="{{$evento->id}}">
                        <input type="hidden" name="trabalho_id" value="{{$trabalho->id}}">
                        <button type="submit" class="btn btn-primary" id="removerRevisorTrabalho">Remover Revisor</button>
                        </form>
                    </div>
                    </div>
                </div>
            @endforeach
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">
                <h5>Adicionar Revisor</h5>
                </div>
            </div>
            <form action="{{ route('distribuicaoManual') }}" method="post">
                @csrf
                <input type="hidden" name="trabalhoId" value="{{$trabalho->id}}">
                <input type="hidden" name="eventoId" value="{{$evento->id}}">
                <div class="row" >
                <div class="col-sm-9">
                    <div class="form-group">
                    <select name="revisorId" class="form-control" id="selectRevisorTrabalho">
                        <option value="" disabled selected>-- E-mail do revisor --</option>
                        @foreach ($evento->revisors()->where([['modalidadeId', $trabalho->modalidade->id], ['areaId', $trabalho->area->id]])->get() as $revisor)
                        @if (!$trabalho->atribuicoes->contains($revisor))
                            <option value="{{$revisor->id}}">{{$revisor->user->name}} ({{$revisor->user->email}})</option>
                        @endif
                        @endforeach
                    </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-primary" id="addRevisorTrabalho">Adicionar Revisor</button>
                </div>
            </form>
            </div>
            </div>
            <div class="modal-footer">


            </div>
        </div>
        </div>
    </div>
    @endforeach
@endforeach
@endsection

@section('javascript')
    @parent
    <script>
        function marcarCheckboxes(id) {
            $(".modalidade" + id).prop('checked', $('#selectAllCheckboxes'+id).is(":checked"));
        }
    </script>
@endsection
