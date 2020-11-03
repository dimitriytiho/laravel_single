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
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5">{{ $title }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 my-4">
                    <form method="post" action="{{ route('contact_us') }}" class="validate spinner_submit" novalidate>
                        @csrf
                        {!! input('name', null, true, null, null) !!}
                        {!! input('tel', null, true, 'tel', null) !!}
                        {!! input('email', null, true, 'email', null) !!}
                        {!! textarea('message', null, true, null, true) !!}
                        {!! checkboxSwitch('accept', true, true, true) !!}

                        <button type="submit" class="btn btn-primary mt-3">@lang('s.submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
{{--

Этот код будет выведен после всех скриптов --}}
{{--@push('novalidate')
    <script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
    <script>
        $(function() {
            $('.validate').attr('novalidate', '');
            $('#tel').mask('+7(999)999-99-99');
        })
    </script>
@endpush--}}
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
