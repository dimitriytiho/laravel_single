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
    @if($values)
        <main class="main">
            <div class="container">
                {{--<div class="row bg-dark-50 py-3 mt-0 mt-sm-3">
                    <div class="col-lg-7">
                        <a href="{{ $values->img }}" class="fancybox" data-caption="{{ $values->title }}">
                            <div class="cover w-100" style="background-image: url('{{ $values->img }}');"></div>
                        </a>
                    </div>
                    <div class="col-lg-5 mt-4 mt-lg-0">
                        <div class="h1 mt-4">{{ $values->title }}</div>
                        <div class="mt-4 mb-1">{!! $values->body !!}</div>
                        <p class="font-weight-bold mt-3">{{ $values->weight }}</p>
                        @if($values->categories->count())
                            <div class="d-flex flex-wrap justify-content-between my-3">
                                <span>Категория:</span>
                                <span>
                                    @foreach($values->categories as $cat)
                                        <a href="{{ route('category', $cat->slug) }}" class="d-block text-right">{{ $cat->title }}</a>
                                    @endforeach
                                </span>
                            </div>
                        @endif
                        <h4 class="mb-3">
                            <em class="text-primary">{!! priceFormat($values->price) !!}</em>
                        </h4>
                        <a href="#"
                           class="btn btn_dark product__info--btn add_to_cart"></a>
                    </div>
                </div>--}}
            </div>
        </main>
    @endif
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
