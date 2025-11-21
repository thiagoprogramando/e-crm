@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="kanban-add-new-board mb-5">
            <a class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#createdModal">
                <i class="ri-add-line"></i>
                <span class="align-middle">Adicionar</span>
            </a>
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="ri-filter-line"></i>
                <span class="align-middle">Filtrar</span>
            </label>
        </div> 

        <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form action="{{ route('created-user') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalFullTitle">Novo Usuário</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="name" placeholder="Ex: João da Silva" required/>
                                    <label>Nome</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="status" id="status" class="select2 form-select" required>
                                            <option value="active">Ativo</option>
                                            <option value="inactive">Inativo</option>
                                        </select>
                                    </div>
                                    <label for="status">Status</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="email" placeholder="Ex: joao@example.com" required/>
                                    <label for="email">E-mail</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="phone" oninput="maskPhone(this)" placeholder="(00) 90000-0000"/>
                                    <label for="phone">Telefone</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="cpfcnpj" oninput="maskCpfCnpj(this)" placeholder="000.000.000-00"/>
                                    <label for="cpfcnpj">CPF/CNPJ</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="type" id="type" class="select2 form-select" required>
                                            <option value="admin">Administrador</option>
                                            <option value="collaborator">Colaborador</option>
                                            <option value="user">Consultor</option>
                                        </select>
                                    </div>
                                    <label for="type">Permissões</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                        <button type="submit" class="btn btn-success">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form action="{{ route('users') }}" method="GET" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalFullTitle">Filtrar</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="name" placeholder="Ex: João"/>
                                    <label>Nome</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="type" id="type" class="select2 form-select">
                                            <option value="  ">Todos</option>
                                            <option value="admin">Administrador</option>
                                            <option value="collaborator">Colaborador</option>
                                            <option value="user">Consultor</option>
                                        </select>
                                    </div>
                                    <label for="type">Permissões</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="email" placeholder="Ex: joao@example.com"/>
                                    <label for="email">E-mail</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="phone" oninput="maskPhone(this)" placeholder="(00) 90000-0000"/>
                                    <label for="phone">Telefone</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="cpfcnpj" oninput="maskCpfCnpj(this)" placeholder="000.000.000-00"/>
                                    <label for="cpfcnpj">CPF/CNPJ</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="status" id="status" class="select2 form-select">
                                            <option value="  ">Todos</option>
                                            <option value="active">Ativo</option>
                                            <option value="inactive">Inativo</option>
                                        </select>
                                    </div>
                                    <label for="status">Status</label>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                        <button type="submit" class="btn btn-success">Pesquisar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card bg-warning text-white mb-3 p-5">
            <figure class="mb-0">
                <blockquote class="blockquote">
                    <a onclick="onClip('{{ route('register', ['parent' => Auth::user()->uuid]) }}')">{{ route('register', ['parent' => Auth::user()->uuid]) }}</a>
                </blockquote>
                <figcaption class="blockquote-footer mb-0 text-white">
                    Link de indicação <cite title="para Auto Cadastro">para Auto Cadastro</cite>
                </figcaption>
            </figure>
        </div>
        
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($users as $user)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info">
                                    <h6 class="mb-1 fw-normal">{{ $user->name }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            {!! $user->statusLabel() !!}
                                        </div>
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-primary me-1"></span>
                                            <small>Vendas: 0 |</small>
                                        </div>
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-info me-1"></span>
                                            <small>{{ $user->typeLabel() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('deleted-user', ['uuid' => $user->uuid]) }}" method="POST" class="btn-group delete">
                                    @csrf
                                    <button type="button" class="btn btn-outline-dark btn-sm"  data-bs-toggle="modal" data-bs-target="#updatedModal{{ $user->uuid }}" title="Editar Usuário"><i class="ri-menu-search-line"></i></button>
                                    <button type="submit" class="btn btn-outline-dark btn-sm" title="Excluir Usuário"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div> 

                    <div class="modal fade" id="updatedModal{{ $user->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('updated-user', ['uuid' => $user->uuid]) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalFullTitle">Detalhes do Usuário</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" placeholder="Ex: João da Silva" value="{{ $user->parent->name ?? '---' }}" disabled/>
                                                <label>Patrocinador</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" name="name" placeholder="Ex: João da Silva" value="{{ $user->name }}"/>
                                                <label>Nome</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control cpfcnpj" name="cpfcnpj" oninput="maskCpfCnpj(this)" placeholder="000.000.000-00" value="{{ $user->cpfcnpj }}"/>
                                                <label for="cpfcnpj">CPF/CNPJ</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" name="email" placeholder="Ex: joao@example.com" value="{{ $user->email }}"/>
                                                <label for="email">E-mail</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control phone" name="phone" oninput="maskPhone(this)" placeholder="000.000.000-00" value="{{ $user->phone }}"/>
                                                <label for="phone">Telefone</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="status" id="status" class="select2 form-select">
                                                        <option value="active" @selected($user->status == 'active')>Ativo</option>
                                                        <option value="inactive" @selected($user->status == 'inactive')>Inativo</option>
                                                    </select>
                                                </div>
                                                <label for="status">Status</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <div class="select2-primary">
                                                    <select name="type" id="type" class="select2 form-select">
                                                        <option value="admin" @selected($user->type == 'admin')>Administrador</option>
                                                        <option value="collaborator" @selected($user->type == 'collaborator')>Colaborador</option>
                                                        <option value="user" @selected($user->type == 'user')>Consultor</option>
                                                    </select>
                                                </div>
                                                <label for="type">Permissões</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input type="text" class="form-control" name="address" placeholder="Ex: Rua das Flores, 123" value="{{ $user->address }}"/>
                                                <label>Endereço</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"> Fechar </button>
                                    <button type="submit" class="btn btn-success">Confirmar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection