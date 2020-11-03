@extends('admin.layouts.admin')

@section('content')
    <section class="row">
        {{--

        Проверим, есть ли у роли разрешения на класс --}}
        @if (config('add.shop') && $isAdmin || config('add.shop') && $permission->contains('Order'))
            {!! $construct::smallBox('info', 'fas fa-shopping-cart', $countTable['Order'] ?? '0', 'Orders', 'admin.order.index') !!}
        @endif
        @if ($isAdmin || $permission->contains('Form'))
            {!! $construct::smallBox('success', 'far fa-comment-alt', $countTable['Form'] ?? '0', 'Forms', 'admin.form.index') !!}
        @endif
        @if ($isAdmin || $permission->contains('Page'))
            {!! $construct::smallBox('warning', 'fas fa-columns', $countTable['Page'] ?? '0', 'Pages', 'admin.page.index') !!}
        @endif
        @if ($isAdmin || $permission->contains('User'))
            {!! $construct::smallBox('danger', 'fas fa-user-friends', $countTable['User'] ?? '0', 'Users', 'admin.user.index') !!}
        @endif
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

    @if ($isAdmin && !config('add.auth'))
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
