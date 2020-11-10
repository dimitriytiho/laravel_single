@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if($values && count($values))
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
                @isset($parentValues)
                    <div class="col-md-2 col-sm-3 mb-5">
                        <label for="parent_values" class="sr-only"></label>
                        <select class="custom-select custom-select-sm select_change" id="parent_values" data-url="{{ route('admin.get_cookie') }}" data-key="{{ $table }}_id">
                            @foreach($parentValues as $id => $title)
                                <option value="{{ $id }}" @if($currentParentId == $id) selected @endif>{{ l($title, 'a') }}</option>
                            @endforeach
                        </select>
                    </div>
                @endisset
                <h5>@lang('a.is_nothing_here')</h5>
            </div>
        </div>
    @endif
@endsection
