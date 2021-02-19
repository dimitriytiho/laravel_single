@if(empty($noShowErrorPage) && auth()->check() && auth()->user()->Admin())

    <div class="panel-dashboard d-none d-lg-block">
        <a href="{{ session()->get('back_link_admin', route('admin.main')) }}" class="panel-dashboard__icons" title="@lang('a.Dashboard')">
            <i class="fas fa-tachometer-alt"></i>
        </a>
        @if((int)Main::get('id') && Route::has('admin.' . Main::get('view') . '.edit'))
            <a href="{{ route('admin.' . Main::get('view') . '.edit', Main::get('id')) }}" class="panel-dashboard__icons" target="_blank" title="@lang('a.edit')">
                <i class="fas fa-edit"></i>
            </a>
        @endif
    </div>
@endif
