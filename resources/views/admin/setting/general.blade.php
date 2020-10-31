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
            {!! $form::input('value', $values->value ?? null, null) !!}

            {!! $form::input('section', $values->section ?? null, null) !!}

            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection
