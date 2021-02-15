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
                <div class="col-md-4">
                    {!! $form::select('units', config('shop.units'), $values->units ?? null) !!}
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
                </div>

                <div class="row">
                    {{--


                    Связи множественные select2 --}}
                    @if(!empty($related))
                        @foreach ($related as $tableName => $items)
                            <div class="col-md-4">
                                {!! $form::select($tableName, $items, $values->$tableName ?? null, $tableName, null, ['data-placeholder' => __('s.choose')], true, null, true, 'w-100 select2', null, null) !!}
                            </div>
                        @endforeach
                    @endif
                    {{--


                    Связи внутри класса select2 --}}
                    @if(!empty($relatedMethods))
                        @foreach ($relatedMethods as $relatedMethod)
                            <div class="col-md-4">
                                {!! $form::select($relatedMethod, ${$table}, $values->$relatedMethod ?? null, $relatedMethod, null, ['data-placeholder' => __('s.choose')], true, $values->id ?? null, true, 'w-100 select2', null, null) !!}
                            </div>
                        @endforeach
                    @endif
                    {{--


                    Связанные таблицы. Многие к одному --}}
                    @if(!empty($relatedManyToOneItems))
                        @foreach ($relatedManyToOneItems as $itemName => $items)
                            <div class="col-md-4">
                                @php

                                    // Получаем название колонки как в БД
                                    $columnName = Str::singular($itemName) . '_id';

                                @endphp
                                {!! $form::select($columnName, $items, $values->$columnName ?? null, $itemName, null, null, true, null, null, null, '<option value="0">' . __('s.choose') . '</option>') !!}
                            </div>
                        @endforeach
                    @endif
                </div>
                {{--

                 Ссылки на редактирование цветов --}}
                @if($values->colors->isNotEmpty())
                    <div class="row">
                        <div class="col-12 mt-2">
                            <p class="font-weight-bold">{{ __('a.edit') . ' ' . Str::lower(__('a.colors')) }}</p>
                        </div>
                        <div class="col-12 mb-3 a-dark">
                            @foreach($values->colors as $key => $color)
                                <a href="{{ route('admin.color-product.edit', $color->pivot->id) }}">
                                    <img src="{{ $color->img }}" class="img_sm" alt="{{ $color->title }}">
                                    <div class="text-center text-sm img_sm_text">{{ $color->title }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
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
