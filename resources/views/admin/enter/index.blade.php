@extends('admin.layouts.enter')
{{--

Вывод контента

--}}
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <h2>{{ $title }}</h2>
        </div>
        <div class="card-body">

            <form action="{{ route('enter') }}" method="post" class="validate mt-2" novalidate>
                @csrf
                <input type="hidden" name="g-recaptcha-response">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <label for="email" class="sr-only"></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="@lang('a.email')" value="{{ old('email') }}">
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    <label for="password" class="sr-only"></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="@lang('a.password')">
                </div>

                <div class="row">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-block pulse">@lang('s.submit')</button>
                    </div>
                    <div class="col-6">
                        <div class="icheck-primary text-right">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">@lang('a.remember')</label>
                        </div>
                    </div>
                </div>
            </form>

            <p class="login-box-msg mt-4">
                {!! recaptchaText('a-dark') !!}
            </p>
            <a href="{{ route('index') }}" class="text-center d-block">@lang('s.home')</a>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
