@if(session()->has('cart'))
    <div class="table-responsive cart_block">
        <table class="table no-wrap a-black">
            {{--<thead>
            <tr>
                <th class="font-weight-light" scope="col">@lang('s.image')</th>
                <th class="font-weight-light" scope="col">@lang('s.title')</th>
                <th class="font-weight-light" scope="col">@lang('s.qty')</th>
                <th class="font-weight-light" scope="col">@lang('s.price')</th>
                <th class="font-weight-light" scope="col"></th>
            </tr>
            </thead>--}}
            <tbody>
            @if(session()->has('cart.products'))
                @foreach(session('cart.products') as $key => $cartProduct)
                    <tr>
                        <td>
                            <a href="{{ route('product', $cartProduct->slug) }}">
                                <img src="{{ asset($cartProduct->img) }}" class="w-5" alt="{{ $cartProduct->title }}">
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('product', $cartProduct->slug) }}">{{ $cartProduct->title }}</a>
                        </td>
                        <td>
                            <a href="{{ route('cart_minus', $key) }}" class="btn cart_minus" data-cart-key="{{ $key }}">
                                <i class="fas fa-minus" title="@lang('s.minus')"></i>
                            </a>
                            <span class="cart_modal_product_qty">{{ $cartProduct->qty }}</span>
                            <a href="{{ route('cart_plus', $key) }}" class="btn cart_plus" data-cart-key="{{ $key }}">
                                <i class="fas fa-plus" title="@lang('s.plus')"></i>
                            </a>
                        </td>
                        <td>{{ $cartProduct->sum * $cartProduct->qty }}</td>
                        <td class="w-3">
                            <a href="{{ route('cart_remove', $key) }}" aria-label="@lang('s.Close')" class="cart_remove" data-cart-key="{{ $key }}" aria-hidden="true">
                                <i class="fas fa-times"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                @if(session()->has('cart.qty'))
                    <tr>
                        <th colspan="3">@lang('s.qty'):</th>
                        <th id="cart_modal_qty">{{ session('cart.qty') }}</th>
                        <th></th>
                    </tr>
                @endif
                @if(session()->has('cart.sum'))
                    <tr>
                        <th colspan="3">@lang('s.sum'):</th>
                        <th id="cart_modal_sum" data-sum="{{ session('cart.sum') }}">{!! priceFormat(session('cart.sum')) !!}</th>
                        <th></th>
                    </tr>
                @endif
            @endif
            </tbody>
        </table>
    </div>

    @empty($noBtnModal)
        <div class="text-right">
            <button class="btn btn-outline-dark pulse" data-dismiss="modal">@lang('s.Close')</button>
            <a href="{{ route('cart') }}" class="btn btn-primary pulse">@lang('s.make_an_order')</a>
        </div>
    @endempty

@else

    <div class="text-center mt-3">
        <h4 class="font-weight-light">@lang('s.cart_empty')</h4>
    </div>
    @empty($noBtnModal)
        <div class="text-right">
            <button class="btn btn-outline-dark pulse" data-dismiss="modal">@lang('s.Close')</button>
        </div>
    @endempty
@endif
