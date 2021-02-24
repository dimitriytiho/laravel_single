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
                    {!! $form::input('title', $values->title ?? null, null) !!}
                </div>
                <div class="col-md-6">
                    {!! $form::input('color', $values->color ?? null, null) !!}
                </div>
                <div class="col-md-6">
                    {!! $form::input('icon', $values->icon ?? null, null) !!}
                </div>
                <div class="col-md-6">
                    {!! $form::input('discount', $values->discount ?? null, null, 'number', true, null, null, ['step' => '0.01', 'min' => '0']) !!}
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
{{--


Этот код будет выведен после всех скриптов --}}
@section('scripts')
    @isset($values->id)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Удаляем неподходящии правила валидации
                $('#title').rules('remove')

            }, false)
        </script>
    @endisset
@endsection
