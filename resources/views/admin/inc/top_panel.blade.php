@php

    $breadcrumbs = \App\Helpers\Admin\LeftMenu::breadcrumbs();

@endphp
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>{{ $title ?? null }}</h1>
    </div>
    @if(request()->path() !== config('add.admin'))
        <div class="col-sm-6 mt-1">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.main') }}">@lang('a.dashboard')</a>
                </li>
                @if($breadcrumbs)
                    @foreach($breadcrumbs as $key => $item)
                        @if(!empty($item['end']))
                            <li class="breadcrumb-item active">{{ $title ?? __("a.{$item['title']}") }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="/{{ config('add.admin') . $item['slug'] }}">@lang("a.{$item['title']}")</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ol>
        </div>
    @endif
</div>
