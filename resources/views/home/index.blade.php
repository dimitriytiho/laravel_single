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
                <h1>@lang('s.account')</h1>
            </div>
        </div>
        <div class="row">
            <div class="col text-center mt-4">
                <a href="{{ route('logout') }}" class="btn btn-outline-primary mt-3">@lang('a.exit')</a>
            </div>
        </div>
    </div>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection


{{--@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>{{ __('You are logged in!') }}</p>
                    <a href="{{ route('logout') }}" class="btn btn-outline-primary mt-3">{{ __('Logout') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection--}}
