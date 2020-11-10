@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ isset($id) ? route("admin.{$route}.update", $id) : route("admin.{$route}.store") }}" method="post" class="validate" novalidate>
                @if(isset($id))
                    @method('put')
                @endif
                @csrf

                {!! $constructor::input('id', $id ?? null) !!}

                @if(!empty($locales))
                    @foreach($locales as $locale)
                        {!! $constructor::input($locale, $values[$locale] ?? null) !!}
                    @endforeach
                @endif

                <div>
                    <button type="submit" class="btn btn-primary mt-3 pulse">{{ isset($id) ? __("{$lang}::s.save") : __("{$lang}::s.submit") }}</button>
                </div>
            </form>
            @if(isset($id))
                <form action="{{ route("admin.{$route}.destroy", $id) }}" method="post" class="text-right confirm-form">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn-outline-primary mt-3 position-relative t--3 pulse">@lang("{$lang}::s.remove")</button>
                </form>
            @endif
        </div>
    </div>
@endsection
