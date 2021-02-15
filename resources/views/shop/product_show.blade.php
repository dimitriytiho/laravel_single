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
    <main class="main mb">
        <div class="container product_show">
            <section class="row mt-2 mb-5">
                <div class="col-lg-6">
                    <a href="{{ $product->img }}" class="fancybox" data-caption="{{ $product->title }}">
                        <div class="product_show_img" style="background-image: url('{{ $product->img }}');"></div>
                    </a>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <h1 class="h4">{{ $title }}</h1>

                    <table class="table table-sm table-borderless mt-4 no-wrap">
                        <tbody>
                        @if(config('shop.properties'))
                            @foreach(config('shop.properties') as $property => $arr)
                                @if($product->$property)
                                    <tr>
                                        <td class="font-weight-light py-0 min-w100">@lang("a.{$property}"):</td>
                                        <td class="text-secondary py-0">{{ $product->$property }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                    <div class="d-flex flex-wrap mt-4 mb-2">
                        <div class="product_card_body__old">{!! priceFormat($product->old_price, $product->units) !!}</div>
                        <h5 class="product_card_body__price">{!! priceFormat($product->price, $product->units) !!}</h5>
                    </div>

                    <div class="mt-2">
                        <div class="input_qty product_card_body__qty">
                            <i class="fas fa-minus pr-2 cur minus product_card_body__qty--minus" title="@lang('s.minus')"></i>
                            <input type="number" class="form-control product_card_body__qty--input" name="qty" step="1" min="1" value="1">
                            <i class="fas fa-plus pl-2 cur plus product_card_body__qty--plus" title="@lang('s.plus')"></i>
                        </div>
                        <a href="{{ route('cart_add', $product->id) }}" class="btn btn-primary pulse px-4 px-md-5 add_to_cart product_show_cart" data-product-id="{{ $product->id }}">@lang('s.add_to_cart')</a>
                    </div>
                </div>
            </section>
            {{--

            Категории --}}
            @if($product->categories->count())
                @php

                    $cats = $product->categories;

                @endphp
                <h3 class="text-center mt">Категории товара</h3>
                @include('inc.categories')
            @endif
            {{--

            Сопутствующие товары --}}
            {{--@if($product->related->count())
                @php

                    $values = $product->related;

                @endphp
                <h3 class="text-center mt">Сопутствующие товары</h3>
                @include('inc.products')
            @endif--}}
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
