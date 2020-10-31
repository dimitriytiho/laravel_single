@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if ($values->isNotEmpty())
        <div class="card">
            <div class="card-body">
                @include('admin.inc.search')

                @include('admin.inc.for_index')
            </div>
            <div class="card-footer">
                @include('admin.inc.pagination')
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <b>@lang('a.example_use_in_views')</b>
                <span>@{{ Main::site('name') }}</span>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <h5>@lang('a.is_nothing_here')</h5>
            </div>
        </div>
    @endif
@endsection
