@php

    use App\Helpers\Admin\Img;

    $cookie_locale = Cookie::get('locale');
    if ($cookie_locale) {
        app()->setLocale($cookie_locale);
    }
    $locale = app()->getLocale();

    $path_segment = class_basename(request()->path());
    $create_edit = $path_segment === 'edit' || $path_segment === 'create';

    $table = $table ?? null;
    $class = $class ?? null;

@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset(config('add.img') . '/omegakontur/admin/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-iphone.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/omegakontur/admin/touch-icon-ipad.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-iphone-retina.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-ipad-retina.png') }}">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="{{ asset('lte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/toastr/toastr.min.css') }}">
    {{--

    Для файлового менеджера --}}
    @if ($path_segment === 'files')
        <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/append.css') }}">
    <title>{{ $title ?? Main::site('name') }}</title>
    <meta name="description" content=" " />
</head>
<body class="hold-transition sidebar-mini @if (request()->cookie('sidebar_mini') !== 'full') sidebar-collapse @endif">
<!-- Site wrapper -->
<div class="wrapper">

    @include('admin.inc.header')

    @include('admin.inc.aside')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">

                @include('admin.inc.message')

                @include('admin.inc.top_panel')

            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            @yield('content')
        </section>
    </div>
    <!-- /.content-wrapper -->

    @include('admin.inc.footer')

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<!-- InputMask -->
{{--<script src="{{ asset('lte/plugins/moment/moment.min.js') }}"></script>--}}
<script src="{{ asset('lte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
<!-- jquery-validation -->
<script src="{{ asset('lte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('lte/plugins/toastr/toastr.min.js') }}"></script>

<script src="{{ asset('lte/dist/js/adminlte.min.js') }}"></script>
{{--

Для файлового менеджера --}}
@if ($path_segment === 'files')
    <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endif
{{--

    Для страницы редактирования --}}
@if ($create_edit)
    {{--

    Выбор редактора кода --}}
    @if (config('admin.editor') === 'ckeditor')
        <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.config.height = '600px'
        </script>
        {{-- CKEDITOR.config.filebrowserImageBrowseUrl = '/file-manager/ckeditor'

        --}}
    @elseif (config('admin.editor') === 'codemirror')
        <script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var codemirror = document.querySelector('.codemirror')
                if (codemirror) {
                    editor = CodeMirror.fromTextArea(codemirror, {
                        tabMode: 'indent',
                        lineNumbers: true,
                        lineWrapping: true,
                        matchBrackets: true,
                        indentUnit: 4
                    })
                    editor.setSize('auto', 'auto')
                    //editor.setSize('auto', 'auto')
                }
            }, false)

        </script>
    @endif
@endif
<script>
    var _token = '{{ session()->token() }}',
        siteName = '{{ Main::site('name') ?: ' ' }}',
        requestPath = '{{ route('admin.main') }}',
        spinner = $('#spinner'),
        spinnerBtn = '<span class="spinner-grow spinner-grow-sm mr-2"></span>',
        {{--

        Dropzone --}}
        acceptedImagesExt = '{{ Img::acceptedImagesExt() }}',
        imgMaxSizeHD = {{ config('admin.imgMaxSizeHD') }},
        imgMaxSize = {{ config('admin.imgMaxSize') }},
        imgMaxSizeSM = {{ config('admin.imgMaxSizeSM') }},
        maxFilesOne = {{ config('admin.maxFilesOne') }},
        maxFilesMany = {{ config('admin.maxFilesMany') }},
        defaultImg = '{{ config("admin.img{$class}Default") }}',

        table = '{{ $table }}',
        currentClass = '{{ $class }}',

        imgRequestName = '{{ $imgRequestName ?? '' }}',
        imgUploadID = '{{ $imgUploadID ?? "" }}',
        curID = '{{ auth()->user()->id ?? "" }}'

    {!! \App\Helpers\Locale::translationsJson() !!}
</script>
<script src="{{ asset('js/append.js') }}"></script>
{{--


Вывод scripts из видов --}}
@yield('scripts')
</body>
</html>
