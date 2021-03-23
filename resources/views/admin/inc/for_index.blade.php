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
                    @switch($field)
                        {{--


                        Если есть поле user_id, то вместо этого покажем данные пользователя --}}
                        @case('user_id')
                            @isset($userFields)
                                @foreach($userFields as $userField)
                                    <th scope="col">
                                        <span>{{ l($userField, 'a') }}</span>
                                    </th>
                                @endforeach
                            @endisset
                        @break

                        @default
                            <th scope="col">
                                <span>{{ l($field, 'a') }}</span>
                                @if(in_array($field, $queryArr))
                                    {!! $dbSort::viewIcons($field, $view, $route) !!}
                                @endif
                            </th>
                    @endswitch
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($values as $key => $item)
                <tr @if($item->status && $item->status !== $statusActive && !empty($view) && $view !== 'user') class="table-active"@endif>
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
                        @switch($field)
                            {{--


                            Если есть поле user_id, то вместо этого покажем данные пользователя --}}
                            @case('user_id')
                                @if($item->user && !empty($userFields))
                                    @foreach($userFields as $key => $userField)
                                        <td>
                                            @if(!$key && auth()->user()->checkPermission('Admin\User'))
                                                <a href="{{ route('admin.user.edit', $item->user->id) }}">{{ l($item->user->$userField, 'a') }}</a>
                                            @else
                                                {{ l($item->user->$userField, 'a') }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            @break

                            @default
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
                                            @if($item->$field)
                                                <img src="{{ asset($item->$field) }}" class="img-size-64" alt="">
                                            @endif
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
                        @endswitch
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
