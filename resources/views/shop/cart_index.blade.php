{{--

Наследуем шаблон --}}
@extends('layouts.default')
{{--

Подключается блок header --}}
@section('header')
    @include('inc.header')
@endsection
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col text-center mt-4">
                    <h1>{{ $title }}</h1>
                </div>
            </div>
            <div class="bg-dark-50 pt-2 pb-5 px-4">
                <div class="row">
                    <div class="col pt-4 no_js">
                        @include("{$viewPath}.cart_modal")
                    </div>
                </div>
                @if(session()->has('cart.products'))
                    {{--


                        Форма заказа --}}
                    <div class="mt-3 mb-5 no-wrap">
                        <form method="post" action="{{ route('make_order') }}" name="order" class="validate form-dark" novalidate>
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    @csrf
                                    {!! hidden('g-recaptcha-response', '', 'data-id="g-recaptcha-response"') !!}
                                    {!! input('name', null, true, null, null) !!}
                                    {!! input('tel', null, true, 'tel', null) !!}
                                    {!! input('email', null, true, null, null) !!}
                                    {!! textarea('address', null, null, null, null, null, 'd-none') !!}
                                    {!! textarea('message', null, null, null, null, null, null, null, 3, true, __('s.comment')) !!}
                                    {!! checkboxSwitch('accept', null, true, true) !!}
                                    {!! btn('submit') !!}
                                    {!! recaptchaText('a-black mt-4') !!}
                                </div>
                            </div>
                        </form>
                    </div>

                @else

                    <div class="text-center mb-3">
                        <a href="{{ route('catalog') }}" class="btn btn-primary pulse mt-4">@lang('s.catalog')</a>
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection
{{--

Подключается блок footer

--}}
@section('footer')
    @include('inc.footer')
@endsection
