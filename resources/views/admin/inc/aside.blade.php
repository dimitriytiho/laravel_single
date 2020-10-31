@php

    $leftMenu = \App\Helpers\Admin\LeftMenu::getTree();

@endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ session()->get('back_link_site', route('index')) }}" class="brand-link">
        <img src="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-iphone-retina.png') }}" alt="AdminLTE Logo" class="brand-image img-circle">
        <span class="brand-text font-weight-light">@lang('a.Website')</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if ($leftMenu)
                    @foreach ($leftMenu as $key => $item)
                        <li class="nav-item @if ($item['slug'] !== '/' && Str::contains(request()->path(), $item['slug']) || request()->path() === config('add.admin') && $item['slug'] === '/') menu-is-opening menu-open active @endif">
                            <a href="/{{ config('add.admin') . $item['slug'] }}" class="nav-link">
                                <i class="nav-icon {{ $item['item'] }}"></i>
                                <p>
                                    @lang("a.{$item['title']}")
                                    @if (!empty($item['child']))
                                        <i class="right fas fa-angle-left"></i>
                                    @endif
                                    {{--<span class="badge badge-info right">6</span>--}}
                                </p>
                            </a>
                            @if (!empty($item['child']))
                                <ul class="nav nav-treeview">
                                    @foreach ($item['child'] as $child)
                                        <li class="nav-item">
                                            <a href="/{{ config('add.admin') . $child['slug'] }}" class="nav-link @if (request()->path() === config('add.admin') . $child['slug']) active @endif">
                                                <i class="{{ $child['item'] }} nav-icon"></i>
                                                <p>{{ l($child['title'], 'a') }}</p>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
