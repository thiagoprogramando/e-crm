@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}"/>

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card mb-3">
            <div class="card-body p-3">
                <h5 class="card-title mb-1">Template: {{ $template->title }}</h5>

                <form action="{{ route('updated-template', ['uuid' => $template->uuid]) }}" method="POST" id="formUpdate">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" class="form-control" name="title" placeholder="Ex: Limpa Nome Contrato" value="{{ $template->title }}"/>
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
                                {!! $template->content !!}
                            </div>
                            <textarea name="content" id="content" hidden></textarea>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="form-floating form-floating-outline mb-2">
                                <div class="select2-primary">
                                    <select name="product_id" id="product_id" class="select2 form-select" required>
                                        <option value=" ">Escolha um Produto</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" @selected($product->id == $template->product_id)>{{ $product->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="product_id">Produto</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="form-floating form-floating-outline mb-2">
                                <div class="select2-primary">
                                    <select name="access" id="access" class="select2 form-select">
                                        <option value="  ">Todos</option>
                                        <option value="admin" @selected($template->access == 'admin')>Administradores</option>
                                        <option value="collaborator" @selected($template->access == 'collaborator')>Colaboradores</option>
                                        <option value="user" @selected($template->access == 'user')>Consultores/Vendedores</option>
                                    </select>
                                </div>
                                <label for="ac">Acesso</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 text-end">
                            <div class="btn-group w-100 mt-2" role="group">
                                <a href="{{ route('templates') }}" class="btn btn-outline-danger"> Fechar </a>
                                <button type="submit" class="btn btn-success">Confirmar</button>
                            </div>
                        </div>
                    </div>
                </form>
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

            const update = document.getElementById('formUpdate');
            update.addEventListener('submit', function (event) {
                event.preventDefault();
                const html = quill.root.innerHTML.trim();
                document.getElementById('content').value = html;
                update.submit();
            });
        });
    </script>
@endsection