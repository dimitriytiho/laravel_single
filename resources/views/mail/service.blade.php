@if(!empty($body))
    <p style="font-size: 16px;">{!! $body !!}</p>
@endif

@if(!empty($values['title']))
    <p style="font-size: 16px;">{{ $values['title'] }}</p>
@endif

@if(!empty($values['btn']) && !empty($values['link']))
    <br>
    <a href="{{ $values['link'] }}" style="padding: 10px 20px; font-size: 16px; color: #fff; background-color: {{ $color }}; display: inline-block; border-radius: 2px; text-decoration: none;">{{ $values['btn'] }}</a>
@endif
