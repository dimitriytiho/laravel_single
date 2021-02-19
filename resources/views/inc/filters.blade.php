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
                    @if($group->status === $statusActive)
                        <div class="col-6 col-md-4 col-lg-12">
                            <h3 class="h6 mt-4 mb-2">{{ $group->title }}</h3>
                            @switch($group->type)
                                @case('checkbox')
                                    @if($group->filters->isNotEmpty())
                                        @foreach($group->filters as $key => $filter)
                                            {!! checkbox($filter->title, 'filter_form', null, in_array($filter->slug, $filtersArr), null, null, $filter->slug) !!}
                                        @endforeach
                                    @endif
                                @break

                                @case('radio')
                                    @if($group->filters->isNotEmpty())
                                        @foreach($group->filters as $key => $filter)
                                            {!! radio($group->slug, $filter->slug, 'filter_form', null, in_array($filter->slug, $filtersArr), null, $filter->title) !!}
                                        @endforeach
                                    @endif
                                @break

                                {{--@case('select')
                                    {!! select($filter->title) !!}
                                @break--}}
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
