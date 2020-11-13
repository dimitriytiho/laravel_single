@php

    $guardedIds = $guardedIds ?? [];

@endphp
@if(!empty($queryArr) && !empty($thead))
    <div class="table-responsive">
        <table class="table border">
            <thead>
            <tr>
                <th scope="col">@lang('a.action')</th>
                @foreach($thead as $field => $val)
                    <th scope="col">
                        <span>{{ l($field, 'a') }}</span>
                        @if(in_array($field, $queryArr))
                            {!! $dbSort::viewIcons($field, $view, $route) !!}
                        @endif
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($values as $key => $item)
                <tr @if($item->status === config('add.page_statuses')[0]) class="table-active"@endif>
                    <td class="d-flex">
                        <a href="{{ Route::has("admin.{$route}.edit") ? route("admin.{$route}.edit", $item->id) :  route("admin.{$route}.show", $item->id) }}" class="btn btn-info btn-sm mr-1 pulse" title="@lang('a.edit')">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        {{--

                        Если не запрещено показывать кнопку Удалить --}}
                        {{--@empty ($deleteBtn)
                            <!--

                            Если есть в массиве id запрещенные для показа -->
                            @if(!in_array($item->id, $guardedIds))
                                <!--


                                Для User class и не Админ не показывает кнопку Удалить на Админах -->
                                @if(!($class === 'User' && $item->noAdminEditAdmin()))
                                    <form action="{{ route("admin.{$route}.destroy", $item->id) }}" method="post" class="confirm_form">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm pulse" title="@lang('a.Remove')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endempty--}}
                    </td>
                    @foreach($thead as $field => $val)
                        <td>
                            @switch($val)
                                {{--

                                Если значение l, то переводим фразу --}}
                                @case('l')
                                    {{ l($item->$field, 'a') }}
                                    @break
                                {{--

                                Если значение img, то выводим картинку --}}
                                @case('img')
                                    <img src="{{ asset($item->$field) }}" class="img-size-64" alt="">
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
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
