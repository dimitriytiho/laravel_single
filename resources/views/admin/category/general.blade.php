@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-body">
            {{--

            Начало формы --}}
            @include('admin.inc.general_start')

            {!! $form::input('title', $values->title ?? null) !!}

            {!! $form::input('slug', $values->slug ?? null, true, null, true, null, null, [], null, null, null,
                $form::inputGroupAppend('fas fa-sync', 'cur get_slug', 'bg-white', 'text-primary', ['data-url' => route('admin.get_slug'), 'data-src' => 'title', 'title' => __('a.generate_link')])) !!}

            {!! $form::textarea('description', $values->description ?? null, null) !!}

            {!! $form::textarea('body', $values->body ?? null, null, true, null, config('admin.editor'), null, 20) !!}

            @isset($values->id)
                <div class="row">
                    <div class="col-md-3">
                        {!! $form::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="parent_id">@lang('a.parent_id')</label>
                            <select class="form-control" name="parent_id" id="parent_id" aria-invalid="false">
                                <option value="0">@lang('a.parent_id')</option>
                                {!! Menu::getView('admin_select', Menu::treeOfArr($all), '-') !!}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        {!! $form::input('sort', $values->sort ?? null, null) !!}
                    </div>
                    <div class="col-md-3 col-6">
                        {!! $form::checkbox('hide', $values->hide ?? null) !!}
                    </div>
                </div>
                {{--

                Картинка --}}
                <div class="row">
                    <div class="col-md-2">
                        <div class="row">
                            <div class="col-11">
                                <label for="img">@lang('a.img')</label>
                                <img src="{{ asset($values->img) }}" class="img-thumbnail img_replace" alt="{{ $values->title }}">
                            </div>
                            {{--

                                Удаление картинки --}}
                            @if($values->img !== config("admin.img{$class}Default"))
                                <div class="col-1 mt-3">
                                    <a href="{{ route(
                                        'admin.delete_img',
                                        [
                                            'token' => csrf_token(),
                                            'img' => $values->img,
                                            'default' => config("admin.img{$class}Default"),
                                            'table' => $table,
                                            'id' => $values->id,
                                        ]
                                        ) }}" class="text-danger p confirm_link">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="form-group">
                            <div class="form-group mt-0">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img" id="img">
                                    <label class="custom-file-label" for="img">{{ $values->img ?? __('a.choose_file') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection
