@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">@lang('a.cache')</div>
        </div>
        <div class="card-body">
            <div class="row">

                {!! $construct::infoBox('info', 'fas fa-database', 'db_caches', 'remove', 'admin.additionally', 'cache=db', 'confirm_link') !!}

                {!! $construct::infoBox('success', 'far fa-star', 'view_caches', 'remove', 'admin.additionally', 'cache=views', 'confirm_link') !!}

                {!! $construct::infoBox('warning', 'far fa-flag', 'route_caches', 'remove', 'admin.additionally', 'cache=routes', 'confirm_link') !!}

                {!! $construct::infoBox('danger', 'fas fa-cog', 'config_caches', 'remove', 'admin.additionally', 'cache=config', 'confirm_link') !!}

            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <div class="card-title">@lang('a.every')</div>
        </div>
        <div class="card-body">
            <div class="row">

                {!! $construct::infoBox('maroon', 'fas fa-project-diagram', 'refresh_seo', 'run', 'admin.additionally', 'upload=run', 'confirm_link', 'col-md-6') !!}

                {!! $construct::infoBox('olive', 'fas fa-memory', 'backup_files_db', 'run', 'admin.additionally', 'backup=run', 'confirm_link', 'col-md-6') !!}

            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <div class="card-title">@lang('a.terminal')</div>
        </div>
        <div class="card-body">
            <form action="{{ route("admin.{$view}") }}" method="post" class="mt-2">
                @csrf
                <div class="input-group">
                    <label for="command" class="sr-only">@lang('a.terminal')</label>
                    <input type="text" name="command" id="command" class="form-control">
                    <span class="input-group-append">
                    <button type="submit" class="btn btn-primary pulse get_disabled_spinner">@lang('a.run')</button>
                  </span>
                </div>
            </form>
        </div>
    </div>


    <div class="card card-outline card-primary collapsed-card mt-4">
        <div class="card-header">
            <h3 class="card-title">@lang('a.example_commands')</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool pulse" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body" style="display: none;">
            <p>php artisan make:controller NameController <span class="font-weight-light text-secondary">({{ __('a.make:controller') }} Name)</span></p>
            <p>php artisan make:model Name <span class="font-weight-light text-secondary">({{ __('a.make:model') }} Name)</span></p>
            <p>php artisan make:model Admin/Name <span class="font-weight-light text-secondary">({{ __('a.create') . __('a.model') }} Name{{ __('a.in') }}app/Models/Admin)</span></p>
            <p>php artisan make:model Name -m -c <span class="font-weight-light text-secondary">({{ __('a.create') . __('a.controller') }},{{ __('a.model') }},{{ __('a.migration_') }})</span></p>
            <p>php artisan make:controller Admin/NameController --resource <span class="font-weight-light text-secondary">({{ __('a.create') . __('a.controller') }} Name{{ __('a.for') }}CRUD)</span></p>
            <p>php artisan make:controller NameController --resource --model=Name <span class="font-weight-light text-secondary">({{ __('a.create') . __('a.controller') . __('a.for') }}CRUD{{ __('a.with_model') }})</span></p>
            <p>php artisan make:middleware Name <span class="font-weight-light text-secondary">({{ __('a.make:middleware') }} Name)</span></p>
            <br>
            <p>php artisan migrate <span class="font-weight-light text-secondary">({{ __('a.migrate') }})</span></p>
            <p>php artisan migrate:rollback <span class="font-weight-light text-secondary">({{ __('a.migrate:rollback') }})</span></p>
            <p>php artisan make:migration create_names_table --create=names <span class="font-weight-light text-secondary">({{ __('a.create_migration_table') }} names)</span></p>
            <p>php artisan make:migration change_names_table --table=names <span class="font-weight-light text-secondary">({{ __('a.change_migration_keep_data_table') }})</span></p>
            <p>php artisan make:migration add_ip_columns_to_users_table --table=users <span class="font-weight-light text-secondary">({{ __('a.change_add_column_migration_keep_data_table') }})</span></p>
            <p>php artisan make:import ProductsImport --model=Product <span class="font-weight-light text-secondary">({{ __('a.creating_model_to_import') }})</span></p>
            <p class="mb-0">php artisan make:export ProductsExport --model=Product <span class="font-weight-light text-secondary">({{ __('a.creating_model_to_export') }})</span></p>
        </div>
        <!-- /.card-body -->
    </div>
@endsection
