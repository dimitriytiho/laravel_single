@if (!empty($values))
    <table style="border: 1px solid #ddd; border-collapse: collapse; width: 100%;">
        <tbody>
        @foreach ($values as $k => $v)
            <tr style="text-align: left;">
                <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">@lang("s.{$k}")</td>
                <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $v }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
