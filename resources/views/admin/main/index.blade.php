@extends('admin.layouts.admin')

@section('content')
    <section class="row">
        {{--

        Проверим, есть ли у роли разрешения на класс --}}
        @if(config('add.shop'))

            @if($isAdmin || $permission->contains('Order'))
                {!! $construct::smallBox('info', 'fas fa-shopping-cart', $countTable['Order'] ?? '0', 'orders', 'admin.order.index') !!}
            @endif

            @if($isAdmin || $permission->contains('Category'))
                {!! $construct::smallBox('success', 'fas fa-sitemap', $countTable['Category'] ?? '0', 'categories', 'admin.category.index') !!}
            @endif

            @if($isAdmin || $permission->contains('Product'))
                {!! $construct::smallBox('warning', 'fas fa-boxes', $countTable['Product'] ?? '0', 'products', 'admin.product.index') !!}
            @endif

        @else

            @if($isAdmin || $permission->contains('Form'))
                {!! $construct::smallBox('success', 'far fa-comment-alt', $countTable['Form'] ?? '0', 'forms', 'admin.form.index') !!}
            @endif

            @if($isAdmin || $permission->contains('Page'))
                {!! $construct::smallBox('warning', 'fas fa-columns', $countTable['Page'] ?? '0', 'pages', 'admin.page.index') !!}
            @endif

        @endif

        @if($isAdmin || $permission->contains('User'))
            {!! $construct::smallBox('danger', 'fas fa-user-friends', $countTable['User'] ?? '0', 'users', 'admin.user.index') !!}
        @endif
    </section>

    <section class="card mt-3">
        <div class="card-body">
            <div class="user-block">
                <img class="img-circle img-bordered-sm" src="{{ auth()->user()->img }}" alt="user image">
                <span class="username">
                  @if($isAdmin || $permission->contains('User'))
                        <a href="{{ route('admin.user.edit', auth()->user()->id) }}">{{ auth()->user()->name }}</a>
                    @else
                        <span>{{ auth()->user()->name }}</span>
                    @endif
                </span>
                <span class="description">@lang('a.welcome')</span>
            </div>
        </div>
    </section>

    @if($isAdmin && !config('add.auth'))
        <section class="card card-light mt-4">
            <div class="card-header">
                <h3 class="card-title">@lang('a.key_to_enter')</h3>
            </div>
            <div class="card-body">
                <p>
                    <strong><i class="fas fa-key mr-1"></i> @lang('a.change')</strong>
                </p>

                <form method="post" action="{{ route('admin.key_to_enter') }}" id="key_to_enter2">
                    @csrf
                    {!! $form::input('key', $key ?? null, null, null, null, null, null, [], null, null, null,
                    $form::inputGroupAppend('fas fa-share', 'cur click_submit', 'bg-white', 'text-primary', ['title' => __('s.to_change_key')])) !!}
                </form>
            </div>
        </section>
    @endif

    <div class="py-5"></div>
@endsection
