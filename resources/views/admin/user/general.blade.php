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
                            <img class="profile-user-img img-fluid img-circle img_replace" src="{{ asset($values->img) }}" alt="{{ $values->name }}">
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
                        </ul>
                        {{--

                        Если не Админ редактирует Админа --}}
                        @if(!$values->noAdminEditAdmin())
                            {{--

                            Если есть связанные для удаления элементы --}}
                            @if(!empty($relatedDelete))
                                @foreach($relatedDelete as $relatedTable)
                                    @if($values->$relatedTable->count())
                                        @php

                                        $relatedTableCount = true;

                                        @endphp
                                        <div>
                                            <div class="small text-secondary">@lang('s.remove_not_possible'), @lang('s.there_are_nested') {{ l($relatedTable, 'a') }}</div>
                                            @foreach($values->$relatedTable as $item)
                                                <a href="{{ route('admin.form.show', $item->id) }}">{{ $item->id }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            @empty($relatedTableCount)
                                <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right confirm_form">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn-danger mt-3 btn-block pulse">@lang('s.remove')</button>
                                </form>
                            @endempty
                        @endif
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
                                    <div class="form-group mt-0">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="img" id="img">
                                            <label class="custom-file-label" for="img">{{ $values->img ?? __('a.choose_file') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--

                        Множественный select2 --}}
                        @if(!empty($roles))
                            {!! $form::select('role_ids', $roles, $values->roles ?? null, 'role', null, ['data-placeholder' => __('s.choose')], true, $roleIdAdmin, true, 'w-100 select2', null, null) !!}
                        @endif
                        {!! $form::input('name', $values->name ?? null) !!}

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
                            <div class="col-md-6">
                                {!! $form::input('password', null, null, 'password') !!}
                            </div>
                            <div class="col-md-6">
                                {!! $form::input('password_confirmation', null, null, 'password') !!}
                            </div>
                        </div>

                        @isset($values->id)
                            <div class="row">
                                <div class="col-md-6">
                                    {!! $form::input('id', $values->id, null, 'text', true, null, null, ['disabled' => 'true']) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! $form::input('ip', $values->ip, null, 'text', true, null, null, ['disabled' => 'true']) !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {!! $form::input('updated_at', d($values->updated_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true']) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! $form::input('created_at', d($values->created_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true'])!!}
                                </div>
                            </div>
                        @endisset

                        <button type="submit" class="btn btn-primary mt-3 mr-2 pulse">{{ isset($values->id) ? __('s.save') : __('s.submit') }}</button>
                    </form>
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

                // Удаляем не подходящии правила валидации
                $('#password').rules('remove')
                $('#password_confirmation').rules('remove')

            }, false)
        </script>
    @endisset
@endsection
