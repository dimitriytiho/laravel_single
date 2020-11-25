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

            {!! $form::select('portfolio_id', $parentValues, $values->portfolio_id ?? null, 'parent_id', null, null, true) !!}

            {!! $form::textarea('body', $values->body ?? null, null, true, null, config('admin.editor'), null, 20) !!}

            @isset($values->id)
                <div class="row">
                    <div class="col-md-6">
                        {!! $form::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('sort', $values->sort ?? null, null) !!}
                    </div>
                </div>
            @endisset
            {{--


            Картинка --}}
            <div class="row">
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-11">
                            <label for="img">@lang('a.img')</label>
                            <img src="{{ asset($values->img ?? config("admin.img{$class}Default")) }}" class="img-thumbnail img_replace" alt="{{ $values->title ?? null }}">
                        </div>
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
            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection
