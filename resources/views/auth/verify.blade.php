@section('titleSeo')@lang('s.verify_email')@endsection
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
                <h1>@lang('s.verify_email')</h1>
            </div>
        </div>
        @if(session('resent'))
            <div class="row my-4">
                <div class="col">
                    <div class="alert alert-success py-3" role="alert">@lang('s.fresh_verification_link')</div>
                </div>
            </div>
        @endif
        @if(Route::has('verification.resend'))
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">

                    <div class="mt-4">@lang('s.check_email_verification_link')</div>
                    <div class="font-weight-bold">@lang('s.not_receive_email')</div>
                    <form action="{{ route('verification.resend') }}" method="post" class="validate my-4" novalidate>
                        @csrf
                        {!! hidden('g-recaptcha-response') !!}
                        {!! btn('request_another') !!}
                        {{--{!! recaptchaText('a-black mt-4') !!}--}}
                    </form>

                </div>
            </div>
        @endif
    </div>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection



{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if(session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
--}}
