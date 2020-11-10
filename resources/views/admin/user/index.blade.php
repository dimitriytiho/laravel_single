@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if($values->isNotEmpty())
        <div class="card">
            <div class="card-body">
                @include('admin.inc.search')

                @include('admin.inc.for_index')
            </div>
            <div class="card-footer">
                @include('admin.inc.pagination')
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
