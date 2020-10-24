{{--

Наследуем шаблон --}}
@extends('layouts.default')
{{--

Подключается блок header --}}
@section('header')
    @include('inc.header')
@endsection

{{-- Вывод контента --}}
@section('content')
    <main class="main">
        <div class="container no-wrap">
            <div class="row">
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5 mb-4">@lang('s.Preventive_work')</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="mt-3 mb-5">@lang('s.Preventive_work_go')</p>
                    @if (Main::site('email'))
                        <p class="mt-3 mb-5">{!! __('s.Preventive_work_contact', ['email' => Main::site('email') ?: ' ']) !!}@if (Main::site('tel')) @lang('s.or_call') {{ Main::site('tel') }}@endif.</p>
                    @endif
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
