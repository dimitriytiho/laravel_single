@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="row justify-content-center">
        @isset($values->id)
            <div class="col-md-4">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img_sm img-circle img_replace" src="{{ asset($values->img) }}" alt="{{ $values->name }}">
                            {{--

                            Удаление картинки --}}
                            @if($values->img !== config("admin.img{$class}Default"))
                                <a href="{{ route(
                                        'admin.delete_img',
                                        [
                                            'token' => csrf_token(),
                                            'img' => $values->img,
                                            'default' => config("admin.img{$class}Default"),
                                            'table' => $table,
                                            'id' => $values->id,
                                        ]
                                        ) }}" class="text-danger p close confirm_link">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>

                        <h3 class="profile-username text-center mb-4">{{ $values->name }}</h3>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>@lang('a.user_id')</b> <span class="float-right">{{ $values->id }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('a.email')</b> <span class="float-right">{{ $values->email }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('a.tel')</b> <span class="float-right">{{ $values->tel }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('a.address')</b> <span class="float-right">{{ $values->address }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('a.ip')</b> <span class="float-right">{{ $values->ip }}</span>
                            </li>
                        </ul>
                        <div class="badge badge-{{ $values->status }} d-block mt-4 py-2">@lang("a.{$values->status}")</div>
                    </div>
                </div>
                <!-- /.card Profile Image -->
            </div>
        @endisset

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">

                    @include('admin.inc.general_start')

                    {!! $form::textarea('note', $values->note ?? null, null) !!}
                    <div class="row">
                        <div class="col-md-6">
                            {!! $form::select('status', $statuses, $values->status ?? null) !!}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="img">@lang('a.img')</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img" id="img">
                                    <label class="custom-file-label" for="img">{{ $values->img ?? __('a.choose_file') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--

                    Связи множественные select2 --}}
                    @if (!empty($related))
                        @foreach ($related as $tableName => $items)
                            {!! $form::select($tableName, $items, $values->$tableName ?? null, $tableName, null, ['data-placeholder' => __('s.choose')], true, null, true, 'w-100 select2', null, null) !!}
                        @endforeach
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            {!! $form::input('name', $values->name ?? null) !!}
                        </div>
                        <div class="col-md-6">
                            {!! $form::input('score', $values->score ?? null, null, 'number', true, null, null, ['step' => '0.01', 'min' => '0']) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {!! $form::input('email', $values->email ?? null, true, 'email') !!}
                        </div>
                        <div class="col-md-6">
                            {!! $form::input('tel', $values->tel ?? null, null, 'tel') !!}
                        </div>
                    </div>

                    {!! $form::input('address', $values->address ?? null, null) !!}

                    <div class="row">
                        @if(config('shop.add_address'))
                            @foreach(config('shop.add_address') as $item)
                                <div class="col-md-3 col-6">
                                    {!! $form::input($item, $values->$item ?? null, null, null, true, l($item, 's')) !!}
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {!! $form::input('password', null, null, 'password') !!}
                        </div>
                        <div class="col-md-6">
                            {!! $form::input('password_confirmation', null, null, 'password') !!}
                        </div>
                    </div>
                    @empty($values->id)
                        {!! $form::checkbox('accept', null, true, true, 'mb-4', __('s.accept'), 'yes', 'no') !!}
                    @endempty
                    {{--

                    Конец формы --}}
                    @include('admin.inc.general_end')
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection
{{--


Этот код будет выведен после всех скриптов --}}
@section('scripts')
    @isset($values->id)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Удаляем неподходящии правила валидации
                $('#password').rules('remove')
                $('#password_confirmation').rules('remove')

            }, false)
        </script>
    @endisset
@endsection
