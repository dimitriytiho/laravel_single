@if(!empty($viewName) && isset($item) && isset($id) && isset($i))
    <option
        value="{{ $id }}"
        {{ $id == Main::get('parent_id') ? 'selected' : null }}
        {{ $id == Request::segment(3) ? 'disabled' : null }}
    >
        {{ empty($tab) ? null : "{$tab} " }}
        {{ $item['title'] . " {$id}" }}
    </option>
    @isset($item['child'])
        {!! \App\Models\Menu::getView($viewName, $item['child'], "{$tab}-") !!}
    @endisset
@endif
