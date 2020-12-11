@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route("admin.{$route}.update", $values->id) }}" method="post" class="validate">
                @method('put')
                @csrf
                <div class="row">
                    @if(config('admin.order_statuses'))
                        <div class="col">
                            <div class="btn-group btn-group-toggle btn_radio mt-3" data-toggle="buttons">
                                @foreach(config('admin.order_statuses') as $status)
                                    <label class="btn btn-{{ \App\Models\Order::orderStatusColorClass($status) }} @if($status === $values->status) active @endif">
                                        <input type="radio" name="status" id="{{ $status }}" value="{{ $status }}" @if($status === $values->status) checked @endif>@lang("s.{$status}")
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="col">
                        {!! $form::textarea('note', $values->note ?? null, null, null) !!}
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3 pulse">@lang('s.save')</button>
            </form>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <a {{ auth()->user()->checkPermission('Admin\User') ? 'href=' . route('admin.user.edit', $values->user->id) : null }} class="text-dark">
                            <img class="profile-user-img img-fluid img-circle img_replace" src="{{ asset($values->user->img) }}" alt="{{ $values->user->name }}">
                        </a>
                    </div>

                    <h3 class="profile-username text-center mb-4">{{ $values->user->name }}</h3>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>@lang('a.user_id')</b> <span class="float-right">{{ $values->user->id }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>@lang('a.email')</b> <span class="float-right">{{ $values->user->email }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>@lang('a.tel')</b> <span class="float-right">{{ $values->user->tel }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>@lang('a.address')</b> <span class="float-right">{{ $values->user->address }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>@lang('a.ip')</b> <span class="float-right">{{ $values->user->ip }}</span>
                        </li>
                    </ul>
                    <div class="badge badge-{{ $values->user->status }} d-block mt-4 py-2">@lang("a.{$values->user->status}")</div>
                </div>
            </div>
            <!-- /.card Profile Image -->
        </div>

        <div class="col-md-8">
            <div class="card card-success card-outline">
                <div class="card-body">
                    @if($values->order_product->count())
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                @foreach($values->order_product as $order)
                                    @isset($products[$order->product_id])
                                        <tr>
                                            <td>
                                                <a {!! auth()->user()->checkPermission('Admin\Product') ? 'href=' . route('admin.product.edit', $order->product_id) : null !!} class="text-dark">
                                                    <img src="{{ $products[$order->product_id]['img'] }}" class="img-size-64" alt="{{ $products[$order->product_id]['title'] }}">
                                                </a>
                                            </td>
                                            <td>{{ $products[$order->product_id]['title'] }}</td>
                                            <td>{{ $order->qty }}<small> {{ $products[$order->product_id]['units'] }}</small></td>
                                            {{--<td>
                                                @if(
        !empty($order->modifiers)
        && @unserialize($order->modifiers) !== false
        || $order->modifiers === 'b:0;'
        )
                                                    @foreach(unserialize($order->modifiers) as $modifier)
                                                        <div class="font-weight-bold">{!! $modifier['title'] !!}</div>
                                                    @endforeach
                                                @endif
                                            </td>--}}
                                            <td>{{ $order->message }}</td>
                                        </tr>
                                    @endisset
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    {{--@if($values->products)
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                @foreach($values->products as $product)
                                    <tr>
                                        <td>
                                            <a {{ auth()->user()->isAdmin() ? 'href=' . route('admin.product.edit', $product->id) : null }} class="text-dark">
                                                <img src="{{ asset($product->img) }}" class="img-size-64" alt="{{ $product->title }}">
                                            </a>
                                        </td>
                                        <td>{{ $product->title }}</td>
                                        <td>{!! empty($orderProduct[$product->id]['qty']) ? null : $orderProduct[$product->id]['qty'] . '<small>шт.</small>' !!}</td>
                                        <td>
                                            @if(
    !empty($orderProduct[$product->id]['modifiers'])
    && @unserialize($orderProduct[$product->id]['modifiers']) !== false
    || $orderProduct[$product->id]['modifiers'] === 'b:0;'
    )
                                                @foreach(unserialize($orderProduct[$product->id]['modifiers']) as $modifier)
                                                    <div class="font-weight-bold">{!! $modifier['title'] !!}</div>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ empty($orderProduct[$product->id]['message']) ? null : $orderProduct[$product->id]['message'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif--}}
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>@lang('s.message')</th>
                    {{--<th>@lang('s.delivery')</th>
                    <th>@lang('s.delivery_sum')</th>--}}
                    <th>@lang('s.qty')</th>
                    <th>@lang('s.sum')</th>
                    {{--<th>@lang('a.payment')</th>
                    <th>@lang('a.paid')</th>--}}
                    <th>ID</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $values->message }}</td>
                    {{--<td>@lang("s.{$values->delivery}")</td>
                    <td>{{ $values->delivery_sum ?: '0' }}</td>--}}
                    <td>{{ $values->qty }}</td>
                    <td>{!! priceFormat($values->sum) !!}</td>
                    {{--<td>@lang("s.{$values->payment}")</td>
                    <td>{{ $values->paid ? __('a.paid') : __('a.payment_failed') }}</td>--}}
                    <td>{{ $values->id }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-2 mr-4 mb-4">
            @if(auth()->user()->isAdmin())
                <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right confirm_form">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn-danger pulse">@lang('s.remove')</button>
                </form>
            @endif
        </div>
    </div>
@endsection
