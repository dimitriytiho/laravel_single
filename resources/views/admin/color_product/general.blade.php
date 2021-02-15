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
            {{--

            Ссылки на товар и цвет --}}
            <div class="row">
                <div class="col card-header d-flex flex-wrap a-dark mb-2 pb-4">
                    <p class="card-title font-weight-bold pr-2">@lang('a.product'):</p>
                    <a href="{{ route('admin.product.edit', $product->id) }}" class="pr-2">
                        <img src="{{ $product->img }}" class="img_sm" alt="{{ $product->title }}">
                        <div class="text-center text-sm img_sm_text">{{ $product->title }}</div>
                    </a>
                    <p class="card-title font-weight-bold pr-2">@lang('a.color'):</p>
                    <a href="{{ route('admin.color.edit', $color->id) }}" class="pr-2">
                        <img src="{{ $color->img }}" class="img_sm" alt="{{ $color->title }}">
                        <div class="text-center text-sm img_sm_text">{{ $color->title }}</div>
                    </a>
                </div>
            </div>

            {!! $form::input('title', $values->title ?? null, null) !!}

            {!! $form::textarea('description', $values->description ?? null, null) !!}

            {!! $form::textarea('body', $values->body ?? null, null, true, null, config('admin.editor'), null, 20) !!}

            <div class="row">
                <div class="col-md-4">
                    {!! $form::input('price', $values->price ?? null, null, 'number', true, null, null, ['step' => '0.01', 'min' => '0']) !!}
                </div>
                <div class="col-md-4">
                    {!! $form::input('old_price', $values->old_price ?? null, null, 'number', true, null, null, ['step' => '0.01', 'min' => '0']) !!}
                </div>
                <div class="col-md-4">
                    {!! $form::input('discount', $values->discount ?? null, null, 'number', true, null, null, ['step' => '0.01', 'min' => '0']) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    {!! $form::input('article', $values->article ?? null, null) !!}
                </div>
                <div class="col-md-4">
                    {!! $form::input('weight', $values->weight ?? null, null) !!}
                </div>
                <div class="col-md-4">
                    {!! $form::input('size', $values->size ?? null, null) !!}
                </div>
            </div>

            @isset($values->id)
                <div class="row">
                    <div class="col-md-4">
                        {!! $form::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                    </div>
                    <div class="col-md-4">
                        {!! $form::input('sort', $values->sort ?? null, null) !!}
                    </div>
                    <div class="col-md-4">
                        {!! $form::select('labels', $labels, $values->labels, true, null, ['data-placeholder' => __('s.choose')], true, null, true, 'w-100 select2') !!}
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
                            @if ($values->img !== config("admin.img{$class}Default"))
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
