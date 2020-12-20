@section('titleSeo')@lang('s.register')@endsection
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
                <h1>@lang('s.register')</h1>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <form action="{{ route('register') }}" method="post" class="validate my-4" novalidate>
                    @csrf
                    {!! hidden('g-recaptcha-response') !!}
                    {!! input('name', 'register') !!}
                    {!! input('tel', 'register', true, 'tel') !!}
                    {!! input('email', 'register', true, 'email') !!}
                    {!! input('password', 'register', true, 'password') !!}
                    {!! input('password_confirmation', 'register', true, 'password') !!}
                    {!! checkboxSwitch('accept', 'register', true, true) !!}
                    {!! btn('submit') !!}

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
