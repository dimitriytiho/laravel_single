@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="btn-group mb-4" role="group" aria-label="Buttons group">
                @if(!empty($queryArr))
                    @foreach($queryArr as $v)
                        <a href="{{ route('admin.import_export') . '?' . $v }}" class="btn btn-info pulse @if($query === $v) active @endif">@lang('a.' . Str::plural($v))</a>
                    @endforeach
                @endif
            </div>
            <form action="{{ $routeImport }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{--


                Табы --}}
                <ul class="nav nav-tabs" role="tablist" id="import-export">
                    <li class="nav-item">
                        <a class="nav-link active" id="export-tab" data-toggle="tab" href="#export" role="tab" aria-controls="export" aria-selected="true">@lang('a.export')</a>
                    </li>
                    @if($routeImport)
                        <li class="nav-item">
                            <a class="nav-link" id="import-tab" data-toggle="tab" href="#import" role="tab" aria-controls="import" aria-selected="false">@lang('a.import')</a>
                        </li>
                    @endif
                </ul>
                {{--

                Контент табов --}}
                <div class="tab-content">
                    <div class="tab-pane fade show active pt-4" id="export" role="tabpanel" aria-labelledby="export-tab">
                        <a class="btn btn-primary pulse mt-3 get_disabled" href="{{ $routeExport }}">@lang('a.export')</a>
                    </div>
                    @if($routeImport)
                        <div class="tab-pane fade pt-4" id="import" role="tabpanel" aria-labelledby="import-tab">
                            <div class="custom-file mt-3">
                                <input type="file" name="import_file" class="custom-file-input">
                                <label class="custom-file-label" for="import_file">@lang('a.choose_file')</label>
                            </div>
                            <button class="btn btn-primary pulse mt-3 get_disabled">@lang('a.import')</button>
                        </div>
                    @endif
                </div>
                {{-- Конец табов


                --}}
            </form>
        </div>
    </div>
@endsection
