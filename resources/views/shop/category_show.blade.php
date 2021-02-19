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
                <div class="col text-center">
                    <h1>{{ $title }}</h1>
                </div>
            </div>
            <div class="row">
                {{--

                Filters --}}
                <div class="col-lg-3 mt-4">
                    @include('inc.filters')
                </div>
                <div class="col-lg-9 products_js">
                    @php

                        $col9 = true;

                    @endphp
                    @include('inc.products')
                </div>
                <div class="col-12 mb">
                    {!! $categories->body !!}
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
