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
                <tr class="d-table-row d-sm-none">
                    <td colspan="5">
                        <i class="fas fa-hand-point-up fa-lg pr-2"></i>
                        <i class="fas fa-arrows-alt-h fa-lg text-black-50"></i>
                    </td>
                </tr>
                @foreach(session('cart.products') as $key => $cartProduct)
                    <tr>
                        <td>
                            <a href="{{ route('product', $cartProduct->slug) }}">
                                <img src="{{ asset($cartProduct->img) }}" class="w-5" alt="{{ $cartProduct->title }}">
                            </a>
                        </td>
                        <td class="min-w240">
                            <a href="{{ route('product', $cartProduct->slug) }}">{{ $cartProduct->title }}</a>
                        </td>
                        <td class="min-w150">
                            <a href="{{ route('cart_minus', $key) }}" class="btn px-3 cart_minus one_click" data-cart-key="{{ $key }}">
                                <i class="fas fa-minus" title="@lang('s.minus')"></i>
                            </a>
                            <span class="cart_modal_product_qty">{{ $cartProduct->qty }}</span>
                            <a href="{{ route('cart_plus', $key) }}" class="btn px-3 cart_plus one_click" data-cart-key="{{ $key }}">
                                <i class="fas fa-plus" title="@lang('s.plus')"></i>
                            </a>
                        </td>
                        <th class="min-w100">{!! priceFormat($cartProduct->sum * $cartProduct->qty) !!}</th>
                        <td class="w-3">
                            <a href="{{ route('cart_remove', $key) }}" aria-label="@lang('s.Close')" class="cart_remove one_click" data-cart-key="{{ $key }}" aria-hidden="true">
                                <i class="fas fa-times"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                {{--

                Кол-во и сумма --}}
                @if(session()->has('cart.qty'))
                    <tr>
                        <td colspan="3">@lang('s.qty'):</td>
                        <th id="cart_modal_qty">{{ session('cart.qty') }}</th>
                        <td></td>
                    </tr>
                @endif
                @if(session()->has('cart.sum'))
                    <tr>
                        <td colspan="3">@lang('s.sum'):</td>
                        <th id="cart_modal_sum" data-sum="{{ session('cart.sum') }}">{!! priceFormat(session('cart.sum')) !!}</th>
                        <td></td>
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
