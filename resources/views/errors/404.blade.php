{{--

Наследуем шаблон --}}
@extends('layouts.default')
{{--

Подключается блок header --}}
@section('header')
    @include('inc.header')
@endsection
{{--


Вывод контента

--}}
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5 mb-4">@lang('s.page_not_found')</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="mt-3 mb-5">@lang('s.whoops_no_page')</p>
                    <div>
                        <a href="javascript:history.back()" class="btn btn-outline-dark"><i class="fa fa-arrow-left"></i> @lang('s.back')</a>
                        <a href="{{ route('index') }}" class="btn btn-primary"><i class="fa fa-home"></i> @lang('s.home')</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
