<section class="row filter">
    {{--@dump($minPrice)
    @dump($maxPrice)
    @dump($filterGroups[0]->filters[1]->title)--}}
    {{--


    Цена ionRangeSlider --}}
    <div class="col-12 filter_price">
        <input type="text" class="js_range_slider" name="filter_price" id="filter_price">
    </div>
    {{--


    Фильтры --}}
    @if(isset($filterGroups) && $filterGroups->isNotEmpty())
        <div class="col-12 filter_form">
            <div class="row">
                @foreach($filterGroups as $group)
                    @if($group->status === $statusActive && $group->filters->isNotEmpty())
                        <div class="col-6 col-md-4 col-lg-12">
                            <h3 class="h6 mt-4 mb-2">{{ $group->title }}</h3>
                            @switch($group->type)
                                {{--


                                Checkbox --}}
                                @case('checkbox')
                                    @foreach($group->filters as $key => $filter)
                                        @if($group->status === $statusActive)
                                            {!! checkbox($filter->title, 'filter_form', null, in_array($filter->slug, $filtersArr), null, null, $filter->slug) !!}
                                        @endif
                                    @endforeach
                                @break
                                {{--


                                Radio --}}
                                @case('radio')
                                    @php

                                        $slugsArr = $group->filters->pluck('slug')->toArray();

                                    @endphp
                                    @foreach($group->filters as $key => $filter)
                                        @php

                                            $checked = array_intersect($slugsArr, $filtersArr) ? in_array($filter->slug, $filtersArr) : $filter->default;

                                        @endphp
                                        @if($group->status === $statusActive)
                                            {!! radio($group->slug, $filter->slug, 'filter_form', null, $checked, 'filter_change_js', $filter->title) !!}
                                        @endif
                                    @endforeach
                                @break
                                {{--


                                Select --}}
                                @case('select')
                                    @php

                                        $options = '';
                                        $slugsArr = $group->filters->pluck('slug')->toArray();
                                        $selected = array_intersect($slugsArr, $filtersArr) ? null : 'selected';
                                        $options .= "<option value='' {$selected}>" . __('s.choose') . "</option>\n";

                                    @endphp
                                    {{--

                                    Собираем options для select --}}
                                    @foreach($group->filters as $key => $filter)
                                        @if($group->status === $statusActive)
                                            @php

                                                $selected = array_intersect($slugsArr, $filtersArr) && in_array($filter->slug, $filtersArr) ? 'selected' : null;
                                                $options .= "<option value='{$filter->slug}' {$selected}>{$filter->title}</option>\n";

                                            @endphp
                                        @endif
                                    @endforeach
                                    @if($options)
                                    <div class="form-group mb-0">
                                        <label for="filter_form_{{ $group->slug }}" class="sr-only">{{ $filter->title }}</label>
                                        <select name="{{ $group->slug }}" id="filter_form_{{ $group->slug }}" class="custom-select custom-select-sm filter_change_js">
                                            {!! $options !!}
                                        </select>
                                    </div>
                                    @endif
                                @break
                            @endswitch
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
    {{--


    Кнопка сброса Get параметров --}}
    <div class="col-lg-12 text-right">
        <button class="btn btn-outline-primary mt-4 reset @if(!request()->query()) js-none @endif">@lang('s.reset')</button>
    </div>
</section>

@section('css')
    <link rel="stylesheet" href="{{ asset('css/ion.rangeSlider.min.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/ion.rangeSlider.min.js') }}"></script>
    <script>
        $('.js_range_slider').ionRangeSlider({
            type: 'double',
            min: {{ $minPrice }},
            max: {{ $maxPrice }},
            from: {{ $minPrice }},
            to: {{ $maxPrice }},
            postfix: ' ₽',
            grid: true
        })
    </script>
@endsection
