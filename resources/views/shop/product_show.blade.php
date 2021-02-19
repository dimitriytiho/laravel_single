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
                    @php
                        $i = 1;
                    @endphp
                    <a href="{{ $product->img }}" class="fancybox_thumbs" rel="gallery-{{ $i }}" data-fancybox="gallery" data-caption="{{ $product->title }}" data-thumb="{{ asset($product->img) }}">
                        <div class="product_show_img" style="background-image: url('{{ asset($product->img) }}');"></div>
                    </a>
                    @if($product->product_galleries->isNotEmpty())
                        @foreach($product->product_galleries as $key => $gallery)
                            <a href="{{ $gallery->img }}" class="js-none fancybox_thumbs" rel="gallery-{{ ++$i }}" data-fancybox="gallery" data-caption="{{ $product->title }}" data-thumb="{{ asset($gallery->img) }}">
                                <div class="product_show_img" style="background-image: url('{{ asset($gallery->img) }}');"></div>
                            </a>
                        @endforeach
                        <div class="product_show_gallery slick_show_gallery slick_slider_arrow arrow_hide">
                            @php
                                $i = 1;
                            @endphp
                            <div class="product_show_gallery__item fancybox_click fancybox_hover" data-gallery="gallery-{{ $i }}" style="background-image: url('{{ asset($product->img) }}');"></div>
                            @foreach($product->product_galleries as $key => $gallery)
                                <div class="product_show_gallery__item fancybox_click fancybox_hover" data-gallery="gallery-{{ ++$i }}" style="background-image: url('{{ asset($gallery->img) }}');"></div>
                            @endforeach

                            <div class="product_show_gallery__item fancybox_click fancybox_hover" data-gallery="gallery-2" style="background-image: url('//localhost:3000/img/product-gallery/2021_02/602e0eb56af74.jpg');"></div>
                            <div class="product_show_gallery__item fancybox_click fancybox_hover" data-gallery="gallery-2" style="background-image: url('//localhost:3000/img/product-gallery/2021_02/602e0eb56af74.jpg');"></div>
                            <div class="product_show_gallery__item fancybox_click fancybox_hover" data-gallery="gallery-2" style="background-image: url('//localhost:3000/img/product-gallery/2021_02/602e0eb56af74.jpg');"></div>
                            <div class="product_show_gallery__item fancybox_click fancybox_hover" data-gallery="gallery-2" style="background-image: url('//localhost:3000/img/product-gallery/2021_02/602e0eb56af74.jpg');"></div>
                            <div class="product_show_gallery__item fancybox_click fancybox_hover" data-gallery="gallery-2" style="background-image: url('//localhost:3000/img/product-gallery/2021_02/602e0eb56af74.jpg');"></div>

                        </div>
                    @endif
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <h1 class="h3">{{ $title }}</h1>
                    <table class="table table-sm table-borderless mt-5 no-wrap">
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
                        <div class="product_card_body__old">{!! priceFormat($product->old_price) !!}</div>
                        <h5 class="product_card_body__price">{!! priceFormat($product->price) !!}</h5>
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
                <div class="col-12">{{ $product->body }}</div>
            </section>
            {{--

            Категории --}}
            @if($product->categories->count())
                <h3 class="text-center mt">Категории товара</h3>
                @php

                    $cats = $product->categories;

                @endphp
                @include('inc.categories')
            @endif
            {{--

            Сопутствующие товары --}}
            @if($product->related->count())
                <h3 class="text-center mt">Сопутствующие товары</h3>
                @php

                    $products = $product->related;

                @endphp
                @include('inc.products')
            @endif
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
