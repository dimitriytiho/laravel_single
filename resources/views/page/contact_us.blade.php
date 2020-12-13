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
                    <form method="post" action="{{ route('post_contact_us') }}" class="validate" novalidate>
                        @csrf
                        @guest
                            {!! input('name', 'contact') !!}
                            {!! input('tel', 'contact', true, 'tel') !!}
                            {!! input('email', 'contact', true, 'email') !!}
                        @endguest
                        {!! textarea('message', 'contact', true, false, false, false, false, false, 5) !!}
                        @guest
                        {!! checkboxSwitch('accept', 'contact', true, true) !!}
                        @endguest

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
