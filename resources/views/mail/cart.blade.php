@if (!empty($body))
    <p style="font-size: 16px">{!! $body !!}</p>
@endif
@if (!empty($values) && is_array($values))
    <table style="border: 1px solid #ddd; border-collapse: collapse; width: 100%;">
        <thead>
        <tr style="background: #f9f9f9;">
            <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">ID</td>
            <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">Название</td>
            <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">Кол-во</td>
            <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">Цена</td>
        </tr>
        </thead>
        <tbody>
        @foreach ($values as $productId => $product)
            @if (is_int($productId))
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">{{ $productId }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">
                        <a href="{{ route('product', $product['slug']) }}" style="text-decoration: none; color: {{ config('add.scss.primary', '#ccc') }};">{{ $product['title'] }}</a>
                    </td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">{{ $product['qty'] }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">{{ $product['price'] }}</td>
                </tr>
            @endif
        @endforeach
        @if (!empty($values['qty']))
            <tr>
                <th colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: left;">@lang('s.total'):</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">{{ $values['qty'] }}</th>
            </tr>
        @endif
        @if (!empty($values['sum']))
            <tr>
                <th colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: left;">@lang('s.sum'):</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">{{ $values['sum'] }}</th>
            </tr>
        @endif
        </tbody>
    </table>
@endif
