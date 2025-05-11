<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Админка — Imageboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"/>
    <link href="{{ asset('vendor/tabler/dashboard.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/fontawesome.css') }}">
    <script src="{{ asset('vendor/feather-icons/dist/feather.js') }}"></script>
</head>
<body>
<div class="page">
    <div class="page-main">

        {{-- Шапка --}}
        <div class="header py-4">
            <div class="container">
                <div class="d-flex">
                    <a class="header-brand" href="{{ route('admin.boards.index') }}">
                        <span class="header-brand-text">Imageboard — Админка</span>
                    </a>

                    <div class="d-flex order-lg-2 ml-auto">

                        {{-- Профиль --}}
                        <div class="dropdown">
                            <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
                                <span class="avatar" style="background-image: url('/demo/faces/female/25.jpg')"></span>
                                <span class="ml-2 d-none d-lg-block">
                                    @auth
                                        <span class="text-default">{{ auth()->user()->name }}</span>
                                        <small class="text-muted d-block mt-1">{{ auth()->user()->role }}</small>
                                    @endauth
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="dropdown-icon fe fe-user"></i> Профиль</a>
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button class="dropdown-item"><i class="dropdown-icon fe fe-log-out"></i> Выйти</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
                        <span class="header-toggler-icon"></span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Навигация --}}
        <div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
            <div class="container">
                <div class="row align-items-center w-100">
                    <div class="col">
                        <ul class="nav nav-tabs border-0 flex-row">
                            <li class="nav-item">
                                <a href="{{ route('admin.boards.index') }}" class="nav-link {{ request()->routeIs('admin.boards*') ? 'active' : '' }}">
                                    <i class="fe fe-grid"></i> Борды
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.threads.index')}}" class="nav-link {{ request()->routeIs('admin.threads*') ? 'active' : '' }}">
                                    <i class="fe fe-layers"></i> Треды
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.posts.index')}}" class="nav-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
                                    <i class="fe fe-message-square"></i> Посты
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.users.index')}}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                    <i class="fe fe-users"></i> Пользователи
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Контент --}}
        <div class="content px-4 py-2 w-100">
            @yield('content')
        </div>
    </div>

    {{-- Подвал --}}
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0 text-muted">© {{ date('Y') }} Imageboard Studio</p>
        </div>
    </footer>
</div>

{{-- Скрипты --}}
<script src="{{ asset('vendor/js_for_dashboard/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/js_for_dashboard/popper.js') }}"></script>
<script src="{{ asset('vendor/js_for_dashboard/bootstrap.min.js') }}"></script>
<script> feather.replace(); </script>
@stack('scripts')
</body>
</html>
