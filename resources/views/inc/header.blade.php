<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="{{ route('index') }}">{{ Main::site('name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="hamburger"></span>
            <span class="hamburger"></span>
            <span class="hamburger"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                @if (config('add.shop'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('catalog') }}">Каталог</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('index') }}/contacts">Контакты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact_us') }}">Связаться с нами</a>
                </li>
                @if (config('add.auth'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('enter') }}">Вход</a>
                    </li>
                @endif
                {{--@if (auth()->check() && auth()->user()->Admin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.main') }}">Админ</a>
                    </li>
                @endif--}}
            </ul>
            @if (config('add.search'))
                <form action="{{ route('search') }}" class="form-inline my-2 my-lg-0 search_js" autocomplete="off">
                    <input type="text" class="form-control mr-sm-2 search_js__input" name="s" placeholder="@lang('a.search')" aria-label="Search" value="{{ $searchQuery ?? null }}">
                    <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">@lang('a.search')</button>
                    <div class="search_js__child"></div>
                </form>
            @endif
        </div>
    </nav>
</header>
