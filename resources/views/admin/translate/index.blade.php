@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if($values->isNotEmpty())
        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    @include('admin.inc.search')
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::a.action")</th>
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">{{ $locale }}</th>
                            @if(!empty($translation))
                                @foreach($translation as $k => $v)
                                    <th scope="col" class="font-weight-light">{{ $k }}</th>
                                @endforeach
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($values as $id => $title)
                            <tr>
                                <th scope="row">
                                    <a href="{{ route("admin.{$route}.edit", $id) }}" class="font-weight-light">
                                        <i class="fas fa-eye" title="@lang("{$lang}::a.edit")"></i>
                                    </a>
                                </th>
                                <td>{{ Str::limit($id, 20) }}</td>
                                <td>{{ Str::limit($title, 20) }}</td>
                                @if(!empty($translation))
                                    @foreach($translation as $k => $v)
                                        <td class="font-weight-light">{{ isset($translation[$k][$id]) ? Str::limit($translation[$k][$id], 20) : null }}</td>
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{--

        Подключаем пагинацию --}}
        @include('admin.inc.pagination')
        <div class="row">
            <div class="col">
                <div class="border text-secondary rounded my-4 p-4">
                    <span class="font-weight-light">@lang("{$lang}::a.example_use_in_views")</span>
                    <span>@@lang("{$lang}::t.Labels")</span>
                </div>
            </div>
        </div>
    @endif
@endsection
