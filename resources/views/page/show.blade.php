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
    @if ($values)
        <main class="main">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h1 class="font-weight-light text-secondary mt-5">{{ $values->title }}</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col my-4">
                        {!! $values->body !!}
                    </div>
                </div>
            </div>
        </main>
    @endif
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
