@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-body">
            @include('admin.inc.search')

            @if($values->isNotEmpty() && !empty($queryArr))
                @foreach($values as $item)
                    <div class="table-responsive">
                        <table class="table border">
                            <tbody>
                            <tr>
                                <td class="d-flex">
                                    <a href="{{ route("admin.{$route}.show", $item->id) }}" class="btn btn-info btn-sm mr-1 pulse" title="@lang('a.edit')">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                </td>
                                <td>
                                    <div class="user-block">
                                        <a href="{{ route("admin.{$route}.show", $item->id) }}">
                                            <img class="img-circle img-bordered-sm" src="{{ asset($item->user->img) }}" alt="{{ $item->user->name }}">
                                        </a>
                                        <span class="username font-weight-normal">{{ $item->user->name }}</span>
                                        <span class="description">{{ $item->user->tel }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>{!! priceFormat($item->sum) !!}</div>
                                    <div class="text-sm font-weight-light">@lang('s.qty') {{ $item->qty }}</div>
                                </td>
                                <td>
                                    <div>@lang('a.id') {!! $item->id !!}</div>
                                    <div class="text-sm font-weight-light">{{ d($item->created_at, config('admin.date_format'))  }}</div>
                                </td>
                                <td>
                                    <a href="{{ route("admin.{$route}.show", $item->id) }}" class="btn btn-{{ \App\Models\Order::orderStatusColorClass($item->status) }} btn-sm mt-2 px-2 text-truncate">@lang("s.{$item->status}")</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @else
                <h5 class="mt-4">@lang('a.is_nothing_here')</h5>
            @endif
        </div>
        <div class="card-footer">
            @include('admin.inc.pagination')
        </div>
    </div>
@endsection
