@php

    $leftMenu = \App\Helpers\Admin\LeftMenu::getTree();

@endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ session()->get('back_link_site', route('index')) }}" class="brand-link">
        <img src="{{ asset("{$img}/omegakontur/touch-icon-iphone-retina.png") }}" alt="{{ config('add.dev') }}" class="brand-image img-circle">
        <span class="brand-text font-weight-light">@lang('a.website')</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if($leftMenu)
                    @foreach($leftMenu as $key => $item)
                        {{--


                        Показываем только разрешенный контроллеры --}}
                        @if(

    isset($item['slug']) && $isAdmin
    ||
    isset($item['slug']) && isset($item['class']) && $permission->contains($item['class'])

    )
                            <li class="nav-item @if(

    $item['slug'] === '/' && request()->path() === config('add.admin')
    ||
    $item['slug'] !== '/' && Str::contains(request()->path(), $item['slug'])
    ||
    $item['slug'] !== '/' && Str::contains(request()->path(), $item['slugs'] ?? null)

    ) menu-is-opening menu-open active @endif">
                                <a href="/{{ config('add.admin') . $item['slug'] }}" class="nav-link">
                                    <i class="nav-icon {{ $item['item'] }}"></i>
                                    <p>
                                        @lang("a.{$item['title']}")
                                        @if(!empty($item['child']))
                                            <i class="right fas fa-angle-left"></i>
                                        @endif
                                        {{--

                                        Для показа кол-ва для нужного элемента получите его в Admin/AppController --}}
                                        @if(!empty($countTable[$item['class']]))
                                            <span class="badge badge-info right">{{ $countTable[$item['class']] }}</span>
                                        @endif
                                    </p>
                                </a>
                                {{--

                                Вложенный цикл --}}
                                @if(!empty($item['child']))
                                    <ul class="nav nav-treeview">
                                        @foreach($item['child'] as $child)
                                            {{--


                                            Показываем только разрешенный контроллеры --}}
                                            @if($isAdmin || $permission->contains($child['class']))
                                                <li class="nav-item">
                                                    <a href="/{{ config('add.admin') . $child['slug'] }}" class="nav-link @if(request()->path() === config('add.admin') . $child['slug']) active @endif">
                                                        <i class="{{ $child['item'] }} nav-icon"></i>
                                                        <p>{{ l($child['title'], 'a') }}</p>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
