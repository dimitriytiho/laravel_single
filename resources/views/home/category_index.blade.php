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
            @include('inc.categories')
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
