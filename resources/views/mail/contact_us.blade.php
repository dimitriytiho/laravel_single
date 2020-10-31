<table style="border: 1px solid #ddd; border-collapse: collapse; width: 100%;">
    <tbody>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">@lang('s.name')</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['name'] ?? null }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">@lang('s.email')</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['email'] ?? null }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">@lang('s.tel')</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['tel'] ?? null }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">@lang('s.message')</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['message'] ?? null }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">@lang('s.accept')</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ isset($values['accept']) && $values['accept'] == '1' ? __('s.received') : __('s.refusal') }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">@lang('s.date')</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['date'] ?? $values['date'] }}</td>
    </tr>
    </tbody>
</table>
