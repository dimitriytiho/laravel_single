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
    <main class="main text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7">

                    <i class="fas fa-cog fa-5x fa-spin text-primary my-5"></i>

                    <h1 class="h3 pt-4">@lang('s.Preventive_work_go')</h1>

                    @if (Main::site('email'))
                        <p class="my-5">{!! __('s.Preventive_work_contact', ['email' => Main::site('email') ?: ' ']) !!}@if (Main::site('tel')) @lang('s.or_call') {{ Main::site('tel') }}@endif.</p>
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
