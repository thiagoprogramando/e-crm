@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}"/>

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
                <form action="{{ route('created-template') }}" method="POST" enctype="multipart/form-data" id="formCreate" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalFullTitle">Novo Template</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="title" placeholder="Ex: Limpa Nome Contrato" required/>
                                    <label>Título</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <p class="text-warning"><strong>Variáveis disponíveis:</strong></p>

                                <div class="sale-status me-2 mb-2">
                                    <span class="badge bg-info me-1 mb-2" onclick="onClip('@{{ CUSTOMER_NAME }}')">CUSTOMER_NAME (Nome do Cliente)</span>
                                    <span class="badge bg-info me-1 mb-2" onclick="onClip('@{{ CUSTOMER_CPFCNPJ }}')">CUSTOMER_CPFCNPJ (CPF ou CNPJ do Cliente)</span>
                                    <span class="badge bg-info me-1 mb-2" onclick="onClip('@{{ CUSTOMER_EMAIL }}')">CUSTOMER_EMAIL (Email do Cliente)</span>
                                    <span class="badge bg-info me-1 mb-2" onclick="onClip('@{{ CUSTOMER_PHONE }}')">CUSTOMER_PHONE (Telefone do Cliente)</span>

                                    <span class="badge bg-warning me-1 mb-2" onclick="onClip('@{{ COMPANY_NAME }}')">COMPANY_NAME (Nome da Empresa)</span>
                                    <span class="badge bg-warning me-1 mb-2" onclick="onClip('@{{ COMPANY_CPFCNPJ }}')">COMPANY_CPFCNPJ (CPF ou CNPJ da Empresa)</span>
                                    <span class="badge bg-warning me-1 mb-2" onclick="onClip('@{{ COMPANY_EMAIL }}')">COMPANY_EMAIL (Email da Empresa)</span>
                                    <span class="badge bg-warning me-1 mb-2" onclick="onClip('@{{ COMPANY_PHONE }}')">COMPANY_PHONE (Telefone da Empresa)</span>
                                    <span class="badge bg-warning me-1 mb-2" onclick="onClip('@{{ COMPANY_ADDRESS }}')">COMPANY_ADDRESS (Endereço da Empresa)</span>

                                    <span class="badge bg-success me-1 mb-2" onclick="onClip('@{{ PRODUCT_TITLE }}')">PRODUCT_TITLE (Título do Produto)</span>
                                    <span class="badge bg-success me-1 mb-2" onclick="onClip('@{{ PRODUCT_VALUE }}')">PRODUCT_VALUE (Valor do Produto)</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="full-editor">
                                    <h6>Contrato de Prestação de Serviço</h6>
                                    <p>
                                        De um lado, '@{{ CUSTOMER_NAME }}' denominada CONTRATANTE, e de outro lado, '@{{ COMPANY_NAME }}' denominada CONTRATADA.
                                    </p>
                                </div>
                                <textarea name="content" id="content" hidden></textarea>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="product_id" id="product_id" class="select2 form-select" required>
                                            <option value=" ">Escolha um Produto</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-options='@json($product->options)'>{{ $product->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="product_id">Produto</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="access" id="access" class="select2 form-select">
                                            <option value="  ">Todos</option>
                                            <option value="admin">Administradores</option>
                                            <option value="collaborator">Colaboradores</option>
                                            <option value="user">Consultores/Vendedores</option>
                                        </select>
                                    </div>
                                    <label for="ac">Acesso</label>
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
                <form action="{{ route('templates') }}" method="GET" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalFullTitle">Filtrar</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="title" placeholder="Ex: Como viajar para o Japão?"/>
                                    <label>Título</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="product_id" id="product_id" class="select2 form-select" required>
                                            <option value=" ">Escolha um Produto</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-options='@json($product->options)'>{{ $product->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="product_id">Produto</label>
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
        
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($templates as $template)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info">
                                    <h6 class="mb-1 fw-normal">{{ $template->title }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-info me-1"></span>
                                            <small>Contratos: {{ $template->contracts->count() }}</small>
                                        </div> 
                                    </div>
                                </div>
                                <form action="{{ route('deleted-template', ['uuid' => $template->uuid]) }}" method="POST" class="btn-group delete">
                                    @csrf
                                    <a href="{{ route('template', ['uuid' => $template->uuid]) }}" class="btn btn-outline-dark btn-sm" title="Editar Template"><i class="ri-menu-search-line"></i></a>
                                    <button type="submit" class="btn btn-outline-dark btn-sm" title="Excluir Template"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div> 
                @endforeach
            </div>
            <div class="card-footer">
                {{ $templates->links() }}
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fullToolbar = [
                [
                { font: [] },
                { size: [] }
                ],
                ['bold', 'italic', 'underline', 'strike'],
                [
                { color: [] },
                { background: [] }
                ],
                [
                { script: 'super' },
                { script: 'sub' }
                ],
                [
                { header: '1' },
                { header: '2' },
                'blockquote',
                'code-block'
                ],
                [
                { list: 'ordered' },
                { list: 'bullet' },
                { indent: '-1' },
                { indent: '+1' }
                ],
                [{ 'align': [] }],
                [{ direction: 'rtl' }],
                ['link', 'image', 'video', 'formula'],
                ['clean']
            ];

            const quill = new Quill('.full-editor', {
                bounds: '.full-editor',
                placeholder: 'Digite o conteúdo do contrato...',
                modules: {
                formula: true,
                toolbar: fullToolbar
                },
                theme: 'snow'
            });

            const create = document.getElementById('formCreate');
            create.addEventListener('submit', function (event) {
                event.preventDefault();
                const html = quill.root.innerHTML.trim();
                document.getElementById('content').value = html;
                create.submit();
            });
        });
    </script>
@endsection