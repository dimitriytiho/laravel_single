@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if (!empty($currentParentId))
        <div class="card">
            @isset ($values->menuName->title)
                <div class="card-header">
                    <div class="card-title">
                        <b>@lang('a.selected'):</b>
                        <span> {{ l($values->menuName->title, 'a') }}</span>
                    </div>
                </div>
            @endisset
            <div class="card-body">
                {{--

                Начало формы --}}
                @include('admin.inc.general_start')

                {!! $form::hidden('belong_id', $currentParentId) !!}

                <div class="row">
                    <div class="col-md-6">
                        {!! $form::input('title', $values->title ?? null) !!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('slug', $values->slug ?? null, null) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {!! $form::input('target', $values->target ?? null, null) !!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('item', $values->item ?? null, null) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {!! $form::input('class', $values->class ?? null, null) !!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('attr', $values->attr ?? null, null) !!}
                    </div>
                </div>

                @isset ($values->id)
                    <div class="row">
                        <div class="col-md-4">
                            {!! $form::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                @if (!empty($all))
                                    {!! $form::select('parent_id', $all, $values->parent_id ?? null, true, null, null, true, $values->id) !!}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            {!! $form::input('sort', $values->sort ?? null, null) !!}
                        </div>
                    </div>
                @endisset
                {{--

                Конец формы --}}
                @include('admin.inc.general_end')
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

{{--


Этот код будет выведен после всех скриптов --}}
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Удаляем не подходящии правила валидации
            $('#slug').rules('remove')

        }, false)
    </script>
@endsection