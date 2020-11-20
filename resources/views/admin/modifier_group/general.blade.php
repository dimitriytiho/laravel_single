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

            <div class="row">
                @isset($values->id)
                    <div class="col-md-3">
                        {!! $form::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                    </div>
                    <div class="col-md-3">
                        {!! $form::input('sort', $values->sort ?? null, null) !!}
                    </div>
                @endisset

                <div class="col-md-3">
                    {!! $form::select('type', config('shop.modifier_type'), $values->type ?? null) !!}
                </div>
                <div class="col-md-3">
                    {!! $form::select('function', config('shop.modifier_function'), $values->function ?? null) !!}
                </div>
            </div>

            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection
