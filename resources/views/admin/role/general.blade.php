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
                    {!! $form::input('title', $values->title ?? null, true, null, true, null, null, [$disabledDelete ?? null => null]) !!}
                </div>
                <div class="col-md-6">
                    {!! $form::select('area', config('admin.user_areas'), $values->area ?? null, true, null, [empty($disabledDelete) ? null : 'disabled' => null], null, null, null, null, null, null, true) !!}
                </div>
            </div>

            {!! $form::select('permission', $files, $selected ?? null, true, null, [empty($disabledDelete) ? null : 'disabled' => null, 'data-placeholder' => __('s.choose')], null, $disabledIds ?? null, true, 'w-100 select2', null, null) !!}

            {{--{!! $form::select('allowed', $routesNames, $selected ?? null, true, null, [empty($disabledDelete) ? null : 'disabled' => null, 'data-placeholder' => __('s.choose')], null, $disabledIds ?? null, true, 'w-100 select2') !!}--}}

            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection
