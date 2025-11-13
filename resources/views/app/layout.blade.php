<!doctype html>

<html lang="pt-br" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-template="horizontal-menu-template" data-style="light">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME') }} | {{ env('APP_DESCRIPTION') }}</title>
        <meta name="description" content=""/>

        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}"/>
        <link rel="preconnect" href="https://fonts.googleapis.com"/>
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet"/>

        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/remixicon/remixicon.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}"/>

        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-statistics.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-analytics.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/shepherd/shepherd.css') }}"/>

        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css"/>

        <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
        <script src="{{ asset('assets/js/config.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.js') }}"></script>
        <script src="{{ asset('assets/js/sweetalert.js') }}"></script>
        <script src="{{ asset('assets/js/mask.js') }}"></script>
    </head>

    <body>
        <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
            <div class="layout-container">
                <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="container-xxl">
                        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-6">
                            <a href="{{ route('app') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="{{ asset('assets/img/logo.png') }}">
                                </span>
                            </a>

                            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                                <i class="ri-close-fill align-middle"></i>
                            </a>
                        </div>

                        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                                <i class="ri-menu-fill ri-22px"></i>
                            </a>
                        </div>

                        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                            <ul class="navbar-nav flex-row align-items-center ms-auto">
                                <li class="nav-item navbar-search-wrapper me-1 me-xl-0">
                                    <a class="nav-link btn btn-text-secondary rounded-pill search-toggler fw-normal" href="javascript:void(0);">
                                        <i class="ri-search-line ri-22px scaleX-n1-rtl"></i>
                                    </a>
                                </li>
                                
                                {{-- <li class="nav-item">
                                    <a title="Sua licença expira em {{ Auth::user()->daysToPlanExpiration() }} dias" class="nav-link btn rounded-pill btn-icon text-nowrap d-inline-flex position-relative me-4" href="javascript:void(0);">
                                        <span class="ri-hourglass-2-fill ri-22px text-secondary"></span>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-white badge-notifications">{{ Auth::user()->daysToPlanExpiration() }}</span>
                                    </a>
                                </li> --}}

                                {{-- <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-4 me-xl-1">
                                    <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <i class="ri-notification-2-line ri-22px"></i>
                                        <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end py-0">
                                        <li class="dropdown-menu-header border-bottom py-50">
                                            <div class="dropdown-header d-flex align-items-center py-2">
                                                <h6 class="mb-0 me-auto">Notification</h6>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge rounded-pill bg-label-primary fs-xsmall me-2">8 New</span>
                                                    <a href="javascript:void(0)" class="btn btn-text-secondary rounded-pill btn-icon dropdown-notifications-all" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read">
                                                        <i class="ri-mail-open-line text-heading ri-20px"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="dropdown-notifications-list scrollable-container">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 me-3">
                                                            <div class="avatar">
                                                                <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('assets/img/avatars/man.png') }}" alt class="rounded-circle"/>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="small mb-1">Congratulation Lettie 🎉</h6>
                                                            <small class="mb-1 d-block text-body">Won the monthly best seller gold badge</small>
                                                            <small class="text-muted">1h ago</small>
                                                        </div>
                                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                                            <a href="javascript:void(0)" class="dropdown-notifications-read">
                                                                <span class="badge badge-dot"></span>
                                                            </a>
                                                            <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                                                <span class="ri-close-line ri-20px"></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="border-top">
                                            <div class="d-grid p-4">
                                                <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                                                    <small class="align-middle">View all notifications</small>
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </li> --}}
                                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <div class="avatar avatar-online">
                                            <img src="{{ Auth::user()->photo ? asset('storage/'.Auth::user()->photo) : asset('assets/img/avatars/man.png') }}" alt="Perfil de {{ Auth::user()->name }}" class="rounded-circle"/>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        
                                        <li>
                                            <a class="dropdown-item" href="">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-2">
                                                        <div class="avatar avatar-online">
                                                            <img src="{{ Auth::user()->photo ? asset('storage/'.Auth::user()->photo) : asset('assets/img/avatars/man.png') }}" alt="Perfil de {{ Auth::user()->name }}" class="rounded-circle" />
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span class="fw-medium d-block small">{{ Auth::user()->name }}</span>
                                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user', ['uuid' => Auth::user()->uuid]) }}">
                                                <i class="ri-user-3-line ri-22px me-3"></i>
                                                <span class="align-middle">Perfil</span>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ env('APP_SUPPORT') }}">
                                                <i class="ri-question-line ri-22px me-3"></i><span class="align-middle">Suporte</span>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="d-grid px-4 pt-2 pb-1">
                                                <a class="btn btn-sm btn-outline-danger d-flex" href="{{ route('logout') }}">
                                                    <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        <form action="{{ route('sales') }}" method="GET" class="navbar-search-wrapper search-input-wrapper container-xxl d-none">
                            @csrf
                            <input type="text" name="name" class="form-control search-input border-0" placeholder="Pesquisar..." aria-label="Pesquisar..." />
                            <i class="ri-close-fill search-toggler cursor-pointer"></i>
                        </form>
                    </div>
                </nav>

                <div class="layout-page">
                    <div class="content-wrapper">
                    
                        <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
                            <div class="container-xxl d-flex h-100">
                                <ul class="menu-inner">
                                    <li class="menu-item">
                                        <a href="{{ route('app') }}" class="menu-link menu-toggle">
                                            <i class="menu-icon tf-icons ri-home-fill"></i>
                                            <div data-i18n="Início">Início</div>
                                        </a>
                                        <ul class="menu-sub">
                                            <li class="menu-item">
                                                <a href="{{ route('app') }}" class="menu-link">
                                                    <i class="menu-icon tf-icons ri-home-fill"></i>
                                                    <div data-i18n="Início">Início</div>
                                                </a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="{{ route('user', ['uuid' => Auth::user()->uuid]) }}#invoices" class="menu-link">
                                                    <i class="menu-icon ri-bill-line ri-22px scaleX-n1-rtl"></i>
                                                    <div data-i18n="Minhas Faturas">Minhas Faturas</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                    @if (isset($subscriptions) && $subscriptions->count() > 0)
                                        <li class="menu-item">
                                            <a href="{{ route('subscriptions') }}" class="menu-link">
                                                <i class="menu-icon tf-icons ri-shopping-cart-line"></i>
                                                <div data-i18n="Planos">Planos</div>
                                            </a>
                                        </li>
                                    @endif

                                    <li class="menu-item">
                                        <a href="" class="menu-link menu-toggle">
                                            <i class="menu-icon tf-icons ri-file-shield-2-line"></i>
                                            <div data-i18n="Processos">Processos</div>
                                        </a>
                                        <ul class="menu-sub">
                                            <li class="menu-item">
                                                <a href="{{ route('lists') }}" class="menu-link">
                                                    <i class="menu-icon tf-icons ri-list-check-3"></i>
                                                    <div data-i18n="Listas">Listas</div>
                                                </a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="{{ route('sales') }}" class="menu-link">
                                                    <i class="menu-icon tf-icons ri-user-follow-line"></i>
                                                    <div data-i18n="Vendas">Vendas</div>
                                                </a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="{{ route('products')}}" class="menu-link">
                                                    <i class="menu-icon tf-icons ri-barcode-line"></i>
                                                    <div data-i18n="Produtos">Produtos</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="menu-item">
                                        <a href="" class="menu-link menu-toggle">
                                            <i class="menu-icon tf-icons ri-contract-line"></i>
                                            <div data-i18n="Contratos">Contratos</div>
                                        </a>
                                        <ul class="menu-sub">
                                            @if (Auth::user()->type == 'admin')
                                                <li class="menu-item">
                                                    <a href="{{ route('templates') }}" class="menu-link">
                                                        <i class="menu-icon tf-icons ri-file-edit-line"></i>
                                                        <div data-i18n="Templates">Templates</div>
                                                    </a>
                                                </li>
                                            @endif
                                            <li class="menu-item">
                                                <a href="{{ route('contracts') }}" class="menu-link">
                                                    <i class="menu-icon tf-icons ri-file-text-line"></i>
                                                    <div data-i18n="Contratos">Contratos</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                    {{-- <li class="menu-item">
                                        <a href="" class="menu-link menu-toggle">
                                            <i class="menu-icon tf-icons ri-puzzle-line"></i>
                                            <div data-i18n="Integrações">Integrações</div>
                                        </a>
                                        <ul class="menu-sub">
                                            <li class="menu-item">
                                                <a href="" class="menu-link">
                                                    <i class="menu-icon tf-icons ri-webhook-line"></i>
                                                    <div data-i18n="APIs">APIs</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li> --}}

                                    @if (Auth::user()->type === 'admin')
                                        <li class="menu-item">
                                            <a href="javascript:void(0)" class="menu-link menu-toggle">
                                                <i class="menu-icon tf-icons ri-list-settings-line"></i>
                                                <div data-i18n="Administração">Administração</div>
                                            </a>
                                            <ul class="menu-sub">
                                                <li class="menu-item">
                                                    <a href="{{ route('products') }}" class="menu-link">
                                                        <div data-i18n="Produtos">Produtos</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item">
                                                    <a href="{{ route('products', ['type' => 'subscription']) }}" class="menu-link">
                                                        <div data-i18n="Planos">Planos</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item">
                                                    <a href="{{ route('users') }}" class="menu-link">
                                                        <div data-i18n="Usuários">Usuários</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif

                                    {{-- 
                                    <li class="menu-item">
                                        <a href="{{ route('notebooks') }}" class="menu-link">
                                            <i class="menu-icon tf-icons ri-book-open-fill"></i>
                                            <div data-i18n="Cadernos">Cadernos</div>
                                        </a>
                                    </li>

                                    @if (Auth::user()->role === 'admin')
                                        <li class="menu-item">
                                            <a href="javascript:void(0)" class="menu-link menu-toggle">
                                                <i class="menu-icon tf-icons ri-book-open-fill"></i>
                                                <div data-i18n="Administração">Administração</div>
                                            </a>
                                            <ul class="menu-sub">
                                                <li class="menu-item">
                                                    <a href="{{ route('contents') }}" class="menu-link">
                                                        <div data-i18n="Conteúdos">Conteúdos</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item">
                                                    <a href="{{ route('boards') }}" class="menu-link">
                                                        <div data-i18n="Bancas">Bancas</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item">
                                                    <a href="{{ route('products') }}" class="menu-link">
                                                        <div data-i18n="Produtos">Produtos</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="menu-item">
                                            <a href="javascript:void(0)" class="menu-link menu-toggle">
                                                <i class="menu-icon tf-icons ri-account-pin-box-fill"></i>
                                                <div data-i18n="Pessoas">Pessoas</div>
                                            </a>
                                            <ul class="menu-sub">
                                                <li class="menu-item">
                                                    <a href="{{ route('users', ['role' => 'student']) }}" class="menu-link">
                                                        <div data-i18n="Estudantes">Estudantes</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item">
                                                    <a href="{{ route('users', ['role' => 'teacher']) }}" class="menu-link">
                                                        <div data-i18n="Professores">Professores</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item">
                                                    <a href="{{ route('users', ['role' => 'admin']) }}" class="menu-link">
                                                        <div data-i18n="Administradores">Administradores</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif --}}

                                    {{-- <li class="menu-item">
                                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                                            <i class="menu-icon tf-icons ri-bookmark-fill"></i>
                                            <div data-i18n="Produtos & Planos">Produtos & Planos</div>
                                        </a>
                                        <ul class="menu-sub">
                                            <li class="menu-item">
                                                <a href="" class="menu-link">
                                                    <div data-i18n="Planos">Planos</div>
                                                </a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="" class="menu-link">
                                                    <div data-i18n="Produtos">Produtos</div>
                                                </a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="" class="menu-link">
                                                    <div data-i18n="Simulados">Simulados</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li> --}}
                                </ul>
                            </div>
                        </aside>

                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="row align-items-start g-3">
                                @if ($errors->any())
                                    <div class="alert alert-outline-danger" role="alert">
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif

                                @yield('content')
                            </div>
                        </div>

                        <footer class="content-footer footer">
                            <div class="container-xxl">
                                <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                    <div class="text-body mb-2 mb-md-0">
                                        © Todos os direitos reservados | Desenvolvido por <a href="https://expressoftwareclub.com/" target="_blank" class="footer-link">Express - Software & Consultoria</a>
                                    </div>
                                    <div class="d-none d-lg-inline-block">
                                        <a href="" target="_blank" class="footer-link me-4">Termos & Condições</a>
                                        <a href="" target="_blank" class="footer-link d-none d-sm-inline-block">Privacidade & Cookies</a>
                                    </div>
                                </div>
                            </div>
                        </footer>

                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>

        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/bloodhound/bloodhound.js') }}"></script>
        
        <script src="{{ asset('assets/js/main.js') }}"></script>

        <script src="{{ asset('assets/js/forms-selects.js') }}"></script> 
        <script src="{{ asset('assets/js/forms-tagify.js') }}"></script>
        <script src="{{ asset('assets/js/forms-typeahead.js') }}"></script>
        
        <script src="{{ asset('assets/js/ui-popover.js') }}"></script>
        
        <script>
            @if(session('error'))
                Swal.fire({
                    title: 'Erro!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    timer: 2000
                })
            @endif

            @if(session('infor'))
                Swal.fire({
                    title: 'Atenção!',
                    text: '{{ session('infor') }}',
                    icon: 'info',
                    timer: 2000
                })
            @endif
            
            @if(session('success'))
                Swal.fire({
                    title: 'Sucesso!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 2000
                })
            @endif

            document.addEventListener('DOMContentLoaded', function () {
                applyMasks(document);
                document.querySelectorAll('form.delete').forEach(form => {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Tem certeza?',
                            text: 'Você realmente deseja excluir este registro?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sim',
                            confirmButtonColor: '#008000',
                            cancelButtonText: 'Não',
                            cancelButtonColor: '#FF0000',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                document.querySelectorAll('form.confirm').forEach(form => {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Confirme sua senha!',
                            text: 'Para continuar, digite sua senha.',
                            icon: 'info',
                            input: 'password',
                            inputPlaceholder: 'Digite sua senha',
                            inputAttributes: {
                                autocapitalize: 'off',
                                autocorrect: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Confirmar',
                            confirmButtonColor: '#008000',
                            cancelButtonText: 'Cancelar',
                            cancelButtonColor: '#FF0000',
                            preConfirm: (password) => {
                                if (!password) {
                                    Swal.showValidationMessage('Senha é obrigatória!');
                                }
                                return password;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'password';
                                input.value = result.value;
                                form.appendChild(input);

                                form.submit();
                            }
                        });
                    });
                });

                document.querySelectorAll('input[type="file"].submit').forEach(input => {
                    input.addEventListener("change", function() {
                        this.closest("form").submit();
                    });
                });
            });

            $(document).on('shown.bs.modal', '.modal', function () {
                applyMasks(this);
            });

            function applyMasks(context) {
                context.querySelectorAll('.money').forEach(el => el.value && maskValue(el));
                context.querySelectorAll('.performance').forEach(el => el.value && maskPerformance(el));
                context.querySelectorAll('.phone').forEach(el => el.value && maskPhone(el));
                context.querySelectorAll('.cpfcnpj').forEach(el => el.value && maskCpfCnpj(el));
                context.querySelectorAll('.address').forEach(el => el.value && consultAddress(el));
            }

            function onClip(text) {
                navigator.clipboard.writeText(text).then(() => {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Copiado',
                        icon: 'success',
                        timer: 5000
                    });
                }).catch(err => {
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Não copiado, tente novamente!',
                        icon: 'error',
                        timer: 5000
                    });
                });
            }
        </script>
    </body>
</html>