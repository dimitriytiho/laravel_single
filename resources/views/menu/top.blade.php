@php

    $menuTop = \App\Models\Menu::get(1);

@endphp
@if($menuTop->count())
    <ul class="navbar-nav">
        @foreach($menuTop as $key => $item)
            <li class="nav-item">
                <a href="{{ $item['slug'] }}" class="nav-link {{ $item['class'] }}"{{ $item['target'] ? ' target="_blank"' : null }}>{{ l($item['title']) }}</a>
            </li>
        @endforeach
    </ul>
@endif
