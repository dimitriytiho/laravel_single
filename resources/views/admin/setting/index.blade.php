@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-body">
            @include('admin.inc.search')

            @if($values->isNotEmpty())
                @include('admin.inc.for_index')
            @else
                <h5 class="mt-4">@lang('a.is_nothing_here')</h5>
            @endif
        </div>
        <div class="card-footer">
            @include('admin.inc.pagination')
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <span>@lang('a.example_use_in_views')</span>
            <b>@{{ Main::site('name') }}</b>
        </div>
    </div>
@endsection
