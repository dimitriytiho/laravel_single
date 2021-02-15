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

            {!! $form::input('title', $values->title ?? null) !!}

            {!! $form::input('slug', $values->slug ?? null, true, null, true, null, null, [], null, null, null,
                $form::inputGroupAppend('fas fa-sync', 'cur get_slug', 'bg-white', 'text-primary', ['data-url' => route('admin.get_slug'), 'data-src' => 'title', 'title' => __('a.generate_link')])) !!}

            <div class="row">
                <div class="col-md-4">
                    {!! $form::input('price', $values->price ?? null, null, 'number', true, null, null, ['step' => '0.01', 'min' => '0']) !!}
                </div>
                <div class="col-md-4">
                    {!! $form::input('old_price', $values->old_price ?? null, null, 'number', true, null, null, ['step' => '0.01', 'min' => '0']) !!}
                </div>
                <div class="col-md-4">
                    {!! $form::input('discount', $values->discount ?? null, null, 'number', true, null, null, ['step' => '0.01', 'min' => '0']) !!}
                </div>
            </div>

            <div class="row">
                {{--


                Связи множественные select2 --}}
                @if(!empty($related))
                    @foreach ($related as $tableName => $items)
                        <div class="col-md-4">
                            {!! $form::select($tableName, $items, $values->$tableName ?? null, $tableName, null, ['data-placeholder' => __('s.choose')], true, null, true, 'w-100 select2', null, null) !!}
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="row">
                <div class="col-md-4">
                    {!! $form::input('start', $values->start ?? null, null) !!}
                </div>
                <div class="col-md-4">
                    {!! $form::checkbox('all', $values->all ?? null) !!}
                </div>
            </div>

            {!! $form::textarea('description', $values->description ?? null, null) !!}

            {!! $form::textarea('body', $values->body ?? null, null, true, null, config('admin.editor'), null, 20) !!}

            @isset($values->id)
                <div class="row">
                    <div class="col-md-6">
                        {!! $form::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('sort', $values->sort ?? null, null) !!}
                    </div>
                </div>
                {{--

                Картинка --}}
                <div class="row">
                    <div class="col-md-2">
                        <div class="row">
                            <div class="col-11">
                                <label for="img">@lang('a.img')</label>
                                <img src="{{ asset($values->img) }}" class="img-thumbnail img_replace" alt="{{ $values->title }}">
                            </div>
                            {{--

                                Удаление картинки --}}
                            @if($values->img !== config("admin.img{$class}Default"))
                                <div class="col-1 mt-3">
                                    <a href="{{ route(
                                        'admin.delete_img',
                                        [
                                            'token' => csrf_token(),
                                            'img' => $values->img,
                                            'default' => config("admin.img{$class}Default"),
                                            'table' => $table,
                                            'id' => $values->id,
                                        ]
                                        ) }}" class="text-danger p confirm_link">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="form-group">
                            <div class="form-group mt-0">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img" id="img">
                                    <label class="custom-file-label" for="img">{{ $values->img ?? __('a.choose_file') }}</label>
                                </div>
                            </div>
                        </div>
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


Этот код будет выведен в head --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('lte/plugins/daterangepicker/daterangepicker.css') }}">
@endsection
{{--


Этот код будет выведен после всех скриптов --}}
@section('scripts')
    <script src="{{ asset('lte/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/moment/moment-with-locales.js') }}"></script>
    <script src="{{ asset('lte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            {{--

            Руссификация дат --}}
            moment.locale('ru')
            {{--

            Значение input --}}
            var dateInput = $('#start'),
                dateInputVal = dateInput.val()
            {{--

            Daterangepicker --}}
            dateInput.daterangepicker({
                showDropdowns: true,
                timePicker: true,
                timePicker24Hour: true,
                locale: {
                    applyLabel: 'Применить',
                    cancelLabel: 'Отмена',
                    fromLabel: 'От',
                    toLabel: 'До',
                    format: '{{ config('admin.date_format_js') }}'
                },
            }).val(dateInputVal)


        }, false)
    </script>
@endsection
