@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-body">
            {{--

            Начало формы --}}
            @include('admin.inc.general_start')

            <div class="row">
                <div class="col-md-6">
                    {!! $form::input('title', $values->title ?? null) !!}
                </div>
                <div class="col-md-6">
                    {!! $form::input('icon', $values->icon ?? null) !!}
                </div>
            </div>

            @isset($values->id)
                <div class="row">
                    <div class="col-md-6">
                        {!! $form::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('sort', $values->sort ?? null, null) !!}
                    </div>
                </div>
            @endisset
            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection
