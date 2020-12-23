{{--

Наследуем шаблон --}}
@extends('layouts.default')
{{--

Подключается блок header --}}
@section('header')
    @include('inc.header')
@endsection
{{--


Вывод контента

--}}
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <h1>{{ $title }}</h1>
                </div>
            </div>
            <div class="row justify-content-center mt-4 mb">
                <div class="col-md-10">
                    <form action="{{ route('home.user_edit') }}" method="post" class="bg-light rounded shadow-mini border_left_primary p-4 validate" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="row mt-1">
                            <div class="col-md-6 text-center">
                                <img src="{{ asset($values->img) }}" class="rounded-circle img-thumbnail img_sm img_replace" alt="{{ $values->name }}">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_edit_img">@lang('a.img')</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="img" id="user_edit_img">
                                        <label class="custom-file-label" for="img">@lang('a.choose_file')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {!! input('name', 'user_edit', true, false, $values->name, true) !!}
                            </div>
                            <div class="col-md-6">
                                {!! input('surname', 'user_edit', false, false, $values->surname, true, __('a.surname')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {!! input('patronymic', 'user_edit', false, false, $values->patronymic, true, __('a.patronymic')) !!}
                            </div>
                            <div class="col-md-6">
                                {!! input('date_of_birth', 'user_edit', false, false, $values->date_of_birth, true, __('a.date_of_birth')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {!! input('email', 'user_edit', true, 'email', $values->email, true) !!}
                            </div>
                            <div class="col-md-6">
                                {!! input('tel', 'user_edit', false, 'tel', $values->tel, true) !!}
                            </div>
                        </div>

                        {!! input('address', 'user_edit', false, false, $values->address, true) !!}

                        <div class="row">
                            <div class="col-md-6">
                                {!! input('specialization', 'user_edit', false, false, $values->specialization, true, __('a.specialization')) !!}
                            </div>
                            <div class="col-md-6">
                                {!! input('discharge', 'user_edit', false, false, $values->discharge, true, __('a.discharge')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {!! input('password', 'user_edit', false, 'password', false, true) !!}
                            </div>
                            <div class="col-md-6">
                                {!! input('password_confirmation', 'user_edit', false, 'password', false, true) !!}
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary pulse mt-2">@lang('s.save')</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
{{--


Этот код будет выведен в head --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('lte/plugins/daterangepicker/daterangepicker.css') }}">
@endsection
{{--


Этот код будет выведен после всех скриптов --}}
@section('js')
    <script src="{{ asset('lte/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/moment/moment-with-locales.js') }}"></script>
    <script src="{{ asset('lte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(function () {
            {{--

            Руссификация дат --}}
            moment.locale('ru')
            {{--

            Значение input --}}
            var dateInput = $('#user_edit_date_of_birth'),
                dateInputVal = dateInput.val()
            {{--

            Daterangepicker --}}
            dateInput.daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1940,
                /*autoUpdateInput: false, function(chosen_date) {
                    $('#date_of_birth').val(chosen_date.format('YYYY-MM-DD'))
                },*/
                locale: {
                    applyLabel: 'Применить',
                    cancelLabel: 'Отмена',
                    fromLabel: 'От',
                    toLabel: 'До',
                    //format: 'YYYY-MM-DD hh:mm:ss'
                },
                maxYear: parseInt(moment().format('YYYY'), 10)
            }, function(start, end, label) {
                var years = moment().diff(start, 'years')
                if (years < 8) {
                    alert('Дата должна быть больше 7 лет')
                }
            }).val(dateInputVal)
            {{--



            Удаляем неподходящии правила валидации --}}
            $('#user_edit_tel').rules('remove')
            $('#user_edit_password').rules('remove')
            $('#user_edit_password_confirmation').rules('remove')

        })
    </script>
@endsection
