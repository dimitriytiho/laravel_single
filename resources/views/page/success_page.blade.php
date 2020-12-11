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
    <main class="main text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 mt-0 mt-md-5">

                    <i class="fas fa-check-circle fa-6x text-success py-5"></i>

                    <h1 class="h3 pt-4">{{ $title }}</h1>

                    <p class="my-5">@lang('s.Your_order_was_successfully_received')</p>
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
