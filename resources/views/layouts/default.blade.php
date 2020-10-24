{{--

Основной шаблон по-умолчанию

--}}
<!doctype html>
<html lang="{{ app()->getLocale() }}" class="no-js">
<head>
    <meta charset="utf-8">
    {{--

    Если не нужно индексировать сайт, то true, если нужно, то false --}}
    @if (!config('add.not_index_website'))
        <meta name="robots" content="index, follow" />
    @endif
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('touch-icon-iphone.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('touch-icon-ipad.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('touch-icon-iphone-retina.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('touch-icon-ipad-retina.png') }}">
    <link rel="cononical" href="{{ $cononical }}">
    <script src="{{ asset('js/modernizr3.6.0.webp.js') }}"></script>
    {{-- <link href="//fonts.googleapis.com/css?family=Roboto:300,400,700&amp;subset=cyrillic" rel="stylesheet"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    {!! $getMeta !!}
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"> --}}
    @include('inc.warning')
    {{--

    Здесь можно добавить файлы css --}}
    @yield('css')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
{{--

Панель администратора --}}
{{--{!! PanelDashboard::init() !!}--}}
<div class="app" id="app">
    <div class="content-block">
        @yield('header')
        @include('inc.message')

        <div class="content" id="content">
            {{--

            Хлебные крошки --}}
            @isset ($breadcrumbs)
                <div class="container mt-3">
                    <div class="row">
                        <div class="col">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @foreach ($breadcrumbs as $item)
                                        @if ($item['end'])
                                            <li class="breadcrumb-item active" aria-current="page">{{ $item['title'] }}</li>
                                        @else
                                            <li class="breadcrumb-item">
                                                <a href="{{ $item['link'] }}">{{ $item['title'] }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            @endisset

            @yield('content')
        </div>
        <div id="bottom-block"></div>
    </div>

    <div class="footer-block">
        @yield('footer')
    </div>
</div>
{{--

Стрелка вверх --}}
<div class="scale-out" id="btn-up" title="@lang('s.move_to_top')">
    {!! icon('arrow-up', 16, 16) !!}
</div>
{{--

Прелодер спинер --}}
<div id="spinner">
    <div class="spinner-block">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>
<script src="{{ asset('js/before.js') }}"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
{{--


CDN ленивой загрузки картинок
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script> --}}
{{--


Google ReCaptcha, если есть в настройках указан секретный ключ --}}
@if (config('add.recaptcha_secret_key'))
    <script src="//www.google.com/recaptcha/api.js"></script>
@endif
@if (config('add.recaptcha_secret_key'))
    <script src="//www.google.com/recaptcha/api.js"></script>
@endif
{{--

@if (!request()->is('/'))
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous" defer></script>
@endif
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous" defer></script>

--}}
{{--<script src="{{ asset('js/svg4everybody.min.js') }}"></script>--}}
<script>
    {{--svg4everybody()--}}
    {{-- Поддержка Svg из sprite во всех браузерах
    https://github.com/jonathantneal/svg4everybody --}}

    var _token = '{{ session()->token() }}',
        path = '{{ route('index') }}',
        site_name = '{{ Main::site('name') ?: ' ' }}',
        site_tel = '{{ Main::site('tel') ?: ' ' }}',
        site_email = '{{ Main::site('email') ?: ' ' }}',
        img_path = '{{ $img }}',
        main_color = '{{ config('add.scss')['primary'] ?? '#ccc' }}',
        {{--slug = '{{ str_replace('-', '_', request()->path()) }}',
        height = '{{ config('add.height') ?? 600 }}',
        cookieTime = '{{ config('admin.cookie') ?? 5184000 }}',
        cookieUrl = '{{ route('set_cookie') }}',--}}
        spinner = $('#spinner'),
        spinnerBtn = '<span class="spinner-grow spinner-grow-sm mr-2"></span>'

    {!! \App\Helpers\Locale::translationsJson() !!}
</script>
{{--

Если в контенте есть скрипты, то они выведятся здесь, через метод Main::getDownScript() --}}
@if (Main::get('scripts'))
    @foreach (Main::get('scripts') as $script)
        {!! $script . PHP_EOL !!}
    @endforeach
@endif
{{--

Вывод js кода из вида pages.contact_us --}}
@stack('novalidate')
{{--

Здесь можно добавить файлы js --}}
@yield('js')
<script src="{{ asset('js/app.js') }}" defer></script>
@include('inc.counters')
</body>
</html>
