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
            @if($values->isNotEmpty())
                <div class="row mt-4 mb">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">@lang('a.action')</th>
                                    <th scope="col">@lang('a.id')</th>
                                    <th scope="col">@lang('a.created_at')</th>
                                    <th scope="col">@lang('s.qty')</th>
                                    <th scope="col">@lang('s.sum')</th>
                                    <th scope="col">@lang('a.status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($values as $key => $item)
                                    <tr>
                                        <th scope="row">
                                            <a href="{{ route('home.order_show', $item->id) }}" class="btn btn-primary btn-sm mr-1 pulse" title="@lang('a.view')">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </th>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ d($item->created_at, config('admin.date_format')) }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{!! priceFormat($item->sum) !!}</td>
                                        <td><span class="badge badge-{{ \App\Models\Order::orderStatusColorClass($item->status) }}">@lang("a.{$item->status}")</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <div class="text-center">
                            <p class="font-weight-light text-secondary mt-4">{{ __('a.shown') . $values->count() . __('a.of') .  $values->total() }}</p>
                            <div class="mt-3">{{ $values->links() }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col text-center mt-5 mb">
                        <h5>@lang('a.is_nothing_here')</h5>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
