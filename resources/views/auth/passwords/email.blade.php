@section('titleSeo')@lang('s.forget_password')@endsection
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
    <div class="container">
        <div class="row">
            <div class="col text-center mt-4">
                <h1>@lang('s.forget_password')</h1>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <form action="{{ route('password.email') }}" method="post" class="validate my-4" novalidate>
                    @csrf
                    {!! hidden('g-recaptcha-response') !!}
                    {!! input('email', 'forget_password', true, 'email') !!}
                    {!! btn('submit') !!}

                    <div class="d-block d-sm-inline-block mt-3 mt-sm-0">
                        <a class="btn" href="{{ route('login') }}">@lang('s.login')</a>
                        <a class="btn" href="{{ route('register') }}">@lang('s.register')</a>
                    </div>
                    {!! recaptchaText('a-black mt-4') !!}
                </form>

            </div>
        </div>
    </div>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
