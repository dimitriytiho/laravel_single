@extends('admin.layouts.admin')

@section('content')
    <section class="row">
        @if (config('add.shop'))
            {!! $construct::smallBox('info', 'fas fa-shopping-cart', $count_orders ?? '0', 'Orders', 'admin.order.index') !!}
        @endif
        {!! $construct::smallBox('success', 'far fa-comment-alt', $count_forms ?? '0', 'Forms', 'admin.form.index') !!}
        {!! $construct::smallBox('warning', 'fas fa-columns', $count_pages ?? '0', 'Pages', 'admin.page.index') !!}
        {!! $construct::smallBox('danger', 'fas fa-user-friends', $count_users ?? '0', 'Users', 'admin.user.index') !!}
    </section>

    <section class="card mt-3">
        <div class="card-body">
            <div class="user-block">
                <img class="img-circle img-bordered-sm" src="{{ auth()->user()->img }}" alt="user image">
                <span class="username">
                  <a href="{{ route('admin.user.edit', auth()->user()->id) }}">{{ auth()->user()->name }}</a>
                </span>
                <span class="description">@lang('a.welcome')</span>
            </div>
        </div>
    </section>

    @if (!config('add.auth'))
        <section class="card card-light mt-4">
            <div class="card-header">
                <h3 class="card-title">@lang('a.key_to_enter')</h3>
            </div>
            <div class="card-body">
                <p>
                    <strong><i class="fas fa-key mr-1"></i> @lang('a.Change')</strong>
                </p>

                {!! $form::input('key', $key ?? null, null, null, null, null, null, [], null, null, null,
                    $form::inputGroupAppend('fas fa-share', 'cur', 'bg-white', 'text-primary', ['data-url' => route('admin.key_to_enter'), 'id' => 'key_to_enter', 'title' => __('a.generate_link')])) !!}
            </div>
        </section>
    @endif

    <div class="py-5"></div>
@endsection
