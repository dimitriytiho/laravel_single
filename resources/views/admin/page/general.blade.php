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

            @isset ($values->id)
                <div class="row">
                    <div class="col-md-4">
                        {!! $form::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @if (!empty($all))
                                {!! $form::select('parent_id', $all, $values->parent_id ?? null, true, null, null, true, $values->id) !!}
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        {!! $form::input('sort', $values->sort ?? null, null) !!}
                    </div>
                </div>
            @endisset
            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection