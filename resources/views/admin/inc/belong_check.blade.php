@if(isset($currentParent->title) || isset($values->$belongTable->title))
    <div class="card-header">
        <div class="card-title">
            <b>@lang('a.selected'):</b>
            <span> {{ l($currentParent->title ?? $values->$belongTable->title, 'a') }}</span>
        </div>
    </div>
@endif
