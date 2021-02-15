@if($cats->isNotEmpty())
    <div class="row categories mt-4 mb {{ request()->is('/') ? 'slick-slider slider_simple slick_slider_arrow arrow_hide slick_slider_dots' : null }}">
        @foreach ($cats as $item)
            <div class="col-xl-3 col-lg-4 col-md-6 a-black category">
                <a href="{{ route('category', $item->slug) }}" class="category_item">
                    <div class="category_item__img">
                        <img src="{{ asset($item->img) }}" class="card-img-top category_item__img--img" alt="{{ $item->title }}">
                    </div>
                    <div class="category_item__text">
                        <h4 class="h5 category_item__text--title">{{ $item->title }}</h4>
                        <p class="category_item__text--count">{{ $item->products->count() }} {{ \App\Models\Product::getWordProduct($item->products->count()) }}</p>
                    </div>
                </a>
            </div>
        @endforeach
        {{--


        Пагинация --}}
        @if(method_exists($cats, 'links'))
            <div class="col-12 mt-2 mb-5">
                <div class="d-flex justify-content-center">{{ $cats->links() }}</div>
            </div>
        @endif
    </div>
@else
    <div class="row">
        <div class="col">
            <h5 class="my-5 px-3 text-center">@lang('a.is_nothing_here')</h5>
        </div>
    </div>
@endif
