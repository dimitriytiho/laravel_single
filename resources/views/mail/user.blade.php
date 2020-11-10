<p style="font-size: 16px">{!! $body !!}</p>
@if(isset($values) && is_array($values))
    <ul>
        @foreach($values as $v)
            <li>{{ $v }}</li>
        @endforeach
    </ul>
@endif
