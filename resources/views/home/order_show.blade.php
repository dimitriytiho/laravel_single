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
            <div class="row mt-4 mb">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">@lang('a.id')</th>
                                <th scope="col">@lang('a.created_at')</th>
                                <th scope="col">@lang('s.qty')</th>
                                <th scope="col">@lang('s.sum')</th>
                                <th scope="col">@lang('a.status')</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="table-light">
                                <th scope="row">{{ $values->id }}</th>
                                <td>{{ d($values->created_at, config('admin.date_format')) }}</td>
                                <td>{{ $values->qty }}</td>
                                <td>{!! priceFormat($values->sum) !!}</td>
                                <td><span class="badge badge-{{ \App\Models\Order::orderStatusColorClass($values->status) }}">@lang("a.{$values->status}")</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($values->order_product->isNotEmpty())
                    <div class="col-12">
                        <h3 class="text-center mt">@lang('a.products')</h3>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">@lang('a.img')</th>
                                    <th scope="col">@lang('a.title')</th>
                                    <th scope="col">@lang('s.qty')</th>
                                    <th scope="col">@lang('s.price')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($values->order_product as $key => $order)
                                    @isset($products[$order->product_id])
                                        <tr>
                                            <th scope="row">
                                                <a {!! $products[$order->product_id]['status'] === $statusActive ? 'href="' . route('product', $products[$order->product_id]['slug']) . '"' : null !!}>
                                                    <img src="{{ asset($products[$order->product_id]['img']) }}" class="img_mini" alt="{{ $products[$order->product_id]['title'] }}">
                                                </a>
                                            </th>
                                            <td>{{ $products[$order->product_id]['title'] }}</td>
                                            <td>{{ $order->qty }}<small> {{ $products[$order->product_id]['units'] }}</small></td>
                                            <td>{!! priceFormat($products[$order->product_id]['price']) !!}</td>
                                        </tr>
                                    @endisset
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
