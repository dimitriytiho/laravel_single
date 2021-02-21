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

            {!! $form::input('title', $values->title ?? null, true, null, true, null, null, [$disabledDelete ?? null => null]) !!}
            {{--@php

                if (isset($values->id)) {
                    $value = $values->value ?: '0';
                } else {
                    $value = null;
                }

            @endphp--}}

            @if(isset($values->type) && $values->type === (config('admin.setting_type')[1] ?? 'checkbox'))
                {!! $form::checkbox('value', $values->value ?? null) !!}
            @else
                {!! $form::input('value', $values->value ?? null, null) !!}
            @endif

            <div class="row">
                <div class="col-md-6">
                    {!! $form::select('type', config('admin.setting_type'), $values->type ?? null) !!}
                </div>
                <div class="col-md-6">
                    {!! $form::input('section', $values->section ?? null, null) !!}
                </div>
            </div>

            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection
