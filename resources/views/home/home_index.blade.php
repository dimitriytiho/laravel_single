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
    <div class="container mb">
        <section class="row">
            <div class="col text-center mt-4">
                <h1>{{ $title }}</h1>
            </div>
        </section>

        <section class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center rounded py-4">
                    <div class="media">
                        <img src="{{ asset(auth()->user()->img) }}" class="rounded-circle img-thumbnail img_mini mr-3" alt="{{ auth()->user()->name }}">
                        <div class="media-body mt-4">
                            <div>{{ auth()->user()->name }}@lang('s.welcome')</div>
                            @if(auth()->user()->Admin())
                                <a href="{{ route('admin.main') }}">@lang('a.dashboard')</a>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('logout') }}" title="@lang('a.exit')">
                        <i class="fas fa-sign-out-alt fa-2x"></i>
                    </a>
                </div>
            </div>
        </section>

        <section class="row mt-4">
            <div class="col px-2 a-black">
                <a href="{{ route('home.order_index') }}" class="info-box">
                    <div class="info-box-icon bg-info">
                        <i class="fas fa-shopping-cart text-white"></i>
                    </div>
                    <div class="info-box-content">
                        <div class="info-box-text">История заказов</div>
                    </div>
                </a>
            </div>
            {{--<div class="col px-2 a-black">
                <a href="{{ route('home.category_index') }}" class="info-box">
                    <div class="info-box-icon bg-success">
                        <i class="fas fa-sitemap text-white"></i>
                    </div>
                    <div class="info-box-content">
                        <div class="info-box-text">История категорий</div>
                    </div>
                </a>
            </div>--}}
            {{--<div class="col px-2 a-black">
                <a href="{{ route('home.product_index') }}" class="info-box">
                    <div class="info-box-icon bg-warning">
                        <i class="fas fas fa-boxes text-dark"></i>
                    </div>
                    <div class="info-box-content">
                        <div class="info-box-text">История товаров</div>
                    </div>
                </a>
            </div>--}}
            <div class="col px-2 a-black">
                <a href="{{ route('home.user_index') }}" class="info-box">
                    <div class="info-box-icon bg-danger">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="info-box-content">
                        <div class="info-box-text">@lang('s.personal_info')</div>
                        {{--<div class="info-box-bold">10<small>%</small>
                        </div>--}}
                    </div>
                </a>
            </div>
        </section>

        <section class="row">
            <div class="col-12">
                <h3 class="text-center mt">@lang('s.information')</h3>
            </div>
            <div class="col">
                <div class="table-responsive">
                    <table class="table no-wrap">
                        <tbody>
                        <tr>
                            <td class="font-weight-light border-0 min-w100">@lang('s.score')</td>
                            <td class="border-0">{{ auth()->user()->score }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-light min-w100">@lang('s.time')</td>
                            <td id="clock">{{ $time }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-light min-w100">@lang('s.date')</td>
                            <td>{{ $date }}</td>
                        </tr>
                        {{--<tr>
                            <td class="font-weight-light min-w100">Ip</td>
                            <td>{{ $ip }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-light min-w100">@lang('s.browser_details')</td>
                            <td>
                                <div>{{ $agent }}</div>
                                <div>{{ $accept }}</div>
                            </td>
                        </tr>--}}
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                clock()

                function clock() {
                    var d = new Date(),
                        hours = d.getHours(),
                        minutes = d.getMinutes(),
                        seconds = d.getSeconds()

                    if (hours <= 9) hours = '0' + hours
                    if (minutes <= 9) minutes = '0' + minutes
                    if (seconds <= 9) seconds = '0' + seconds
                    dateTime =  hours + ':' + minutes + ':' + seconds

                    if (document.layers) {
                        document.layers.doc_time.document.write(dateTime)
                        document.layers.doc_time.document.close()
                    } else {
                        document.getElementById('clock').innerHTML = dateTime
                    }
                    setTimeout('clock()', 1000)
                }
                {{-- http://usefulscript.ru/current_time.php --}}
            </script>
        </section>
    </div>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include('inc.footer')
@endsection
