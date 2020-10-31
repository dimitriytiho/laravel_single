@isset($thead)
    <div class="table-responsive">
        <table class="table border">
            <thead>
            <tr>
                @foreach($thead as $field => $val)
                    <th scope="col">
                        <span>{{ l($field, 'a') }}</span>
                        {!! $dbSort::viewIcons($field, $view, $route) !!}
                    </th>
                @endforeach
                <th scope="col">@lang('a.action')</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($values as $key => $item)
                <tr @if ($item->status === config('add.page_statuses')[0]) class="table-active"@endif>
                    @foreach ($thead as $field => $val)
                        <td>
                            @switch($val)
                                {{--

                                Если значение l, то переводим фразу --}}
                                @case('l')
                                    {{ l($item->$field, 'a') }}
                                    @break
                                {{--

                                Если значение t, то выводим дату --}}
                                @case('t')
                                    {{ d($item->$field, config('admin.date_format')) }}
                                    @break

                                @default
                                    {{ $item->$field }}
                            @endswitch
                        </td>
                    @endforeach
                    <td class="d-flex">
                        <a href="{{ Route::has("admin.{$route}.edit") ? route("admin.{$route}.edit", $item->id) :  route("admin.{$route}.show", $item->id) }}" class="btn btn-info btn-sm mr-1 pulse" title="@lang('a.edit')">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        {{--


                        Для User class и не Админ не показывает кнопку Удалить на Админах --}}
                        @if (!($class === 'User' && !auth()->user()->isAdmin() && in_array($item->roles[0]->roleAdminId, $item->rolesIds())))
                            <form action="{{ route("admin.{$route}.destroy", $item->id) }}" method="post" class="confirm_form">
                                @method('delete')
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm pulse" title="@lang('a.Remove')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endisset
