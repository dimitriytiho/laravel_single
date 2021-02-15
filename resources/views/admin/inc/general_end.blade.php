    @isset($values->id)
        <div class="row">
            <div class="col-md-4">
                {!! $form::input('id', $values->id, null, 'text', true, null, null, ['disabled' => 'true']) !!}
            </div>
            <div class="col-md-4">
                {!! $form::input('updated_at', d($values->updated_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true']) !!}
            </div>
            <div class="col-md-4">
                {!! $form::input('created_at', d($values->created_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true'])!!}
            </div>
        </div>
    @endisset
    <div>
        <span id="btn-sticky">
            <button type="submit" class="btn btn-primary mt-3 mr-2 pulse">{{ isset($values->id) ? __('s.save') : __('s.submit') }}</button>
        </span>
        @if(isset($values->slug) && Route::has($view))
            <a href="{{ route($view, $values->slug) }}" class="btn btn-outline-info mt-3 pulse" target="_blank">@lang('s.go')</a>
        @endif
    </div>
</form>
{{--

В моделе должен быть метод с название таблицы, реализующий связь --}}
@if(isset($values) && !empty($relatedDelete))
    @foreach($relatedDelete as $relatedTable)
        @if(isset($values->$relatedTable) && $values->$relatedTable->count())
            @php

                $relatedTableCount = true;
                $routeName = Str::singular($relatedTable);
                $routeAction = Route::has("admin.{$routeName}.edit") ? "admin.{$routeName}.edit" : "admin.{$routeName}.show";

            @endphp
            <div class="text-right">
                <div class="small text-secondary">@lang('s.remove_not_possible'),<br>@lang('s.there_are_nested') {{ l($relatedTable, 'a') }}</div>
                @foreach($values->$relatedTable as $item)
                    <a href="{{ route($routeAction, $item->id) }}">{{ $item->id }}</a>
                @endforeach
            </div>
        @endif
     @endforeach
@endif

@if(
    isset($values->id)
    && Route::has("admin.{$route}.destroy")
    && !('user' === $c && $values->noAdminEditAdmin())
    && empty($relatedTableCount)
    && empty($disabledDelete)
    )
    <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right confirm_form">
        @method('delete')
        @csrf
        <button type="submit" class="btn btn-danger mt-3 pulse">@lang('s.remove')</button>
    </form>
@endif
