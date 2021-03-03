@if($products->isNotEmpty())
    <div class="row products mt-4">
        @foreach ($products as $item)
            <div class="{{ empty($col9) ? 'col-xl-3 col-lg-4 col-md-6' : 'col-xl-4 col-md-6' }} product">
                {{--

                Labels --}}
                @if($item->labels->isNotEmpty())
                    <div class="product_label">
                        @foreach($item->labels as $label)
                            @if($statusActive === $label->status)
                                @php

                                    $labelColor = $label->color ? "style='background-color: {$label->color};'" : null;

                                @endphp
                                <div class="product_label__item {{ $labelColor ? 'text-white' : null }}" {!! $labelColor !!}>
                                    @if($label->icon)
                                        <i class="{{ $label->icon }} product_label__item--i"></i>
                                    @endif
                                    @if($label->title)
                                        <span class="product_label__item--title">{{ $label->title }}</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                <div class="card product_card">
                    <a href="{{ route('product', $item->slug) }}" class="product_card__img">
                        <img src="{{ asset($item->img) }}" class="card-img-top product_card__img--img" alt="{{ $item->title }}">
                    </a>
                    <div class="card-body product_card_body">
                        <h4 class="card-title product_card_body__title a-black">
                            <a href="{{ route('product', $item->slug) }}" class="product_card_body__title--a">{{ $item->title }}</a>
                        </h4>

                        <p class="product_card_body__article">{{ $item->article ? 'Артикул:' : null }} <span class="product_card_body__article--span">{{ $item->article }}</span></p>

                        <div class="product_card_body__price">
                            <span class="product_card_body__price--now">{!! priceFormat($item->price) !!}</span>
                            <span class="product_card_body__price--old">{!! priceFormat($item->old_price) !!}</span>
                        </div>

                        <div class="product_card_body_hide">
                            <div class="input_qty product_card_body__qty">
                                <i class="fas fa-minus pr-2 cur minus product_card_body__qty--minus" title="@lang('s.minus')"></i>
                                <input type="number" class="form-control product_card_body__qty--input" name="qty" step="1" min="1" value="1">
                                <i class="fas fa-plus pl-2 cur plus product_card_body__qty--plus" title="@lang('s.plus')"></i>
                            </div>

                            <div class="product_card_body__btn">
                                <a href="{{ route('cart_add', $item->id) }}" class="btn btn-primary pulse add_to_cart" data-product-id="{{ $item->id }}">@lang('s.add_to_cart')</a>{{--add_to_cart--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{--


        Пагинация --}}
        @if(method_exists($products, 'links'))
            <div class="col-12 mt-4 mb-5">
                <div class="d-flex justify-content-center">{{ $products->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>
@else
    <div class="row">
        <div class="col">
            <h5 class="my-5 px-3 text-center">@lang('a.is_nothing_here')</h5>
        </div>
    </div>
@endif
