@isset($item)
    <tr>
        <td>
            <img src="{{ asset($item->img) }}" class="img-size-50" alt="{{ $item->title }}">
        </td>
        <td>{{ $item->title }}</td>
        <td>
            <button class="btn btn-{{ $item->status === $statusActive ? 'info' : 'danger' }} btn-sm pulse stop_list_item" data-id="{{ $item->id }}" data-status="{{ $item->status === $statusActive ? config('add.page_statuses')[0] ?? 'inactive' : $statusActive }}" data-url="{{ route('admin.stop-list.update') }}">
                <i class="fas fa-{{ $item->status === $statusActive ? 'plus' : 'times' }}"></i>
            </button>
        </td>
    </tr>
@endisset
