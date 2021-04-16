@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-body">
            {{--

            Начало формы --}}
            @include('admin.inc.general_start')

            @empty($values->id)
                <div class="row">
                    @if($exts = config('admin.images_ext'))
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ext">@lang('a.max_size')</label>
                                <select class="form-control" name="ext" id="ext">
                                    @foreach($exts as $ket => $ext)
                                        @php

                                            if (empty($ext[0])) {
                                                $extTitle = ($ext[1] ?? null) . 'x' . ($ext[2] ?? null) . ' ' . l($ext[3] ?? null, 'a');
                                            } else {
                                                $extTitle = __('a.' . $ext[0]);
                                            }

                                        @endphp
                                        <option value="{{ $ket }}">{{ $extTitle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-4">
                        {!! $form::checkbox('webp', null, null, true, null, __('a.webp'), 'save', 'no_save') !!}
                    </div>
                </div>


                <div class="form-group">
                    <label for="files">@lang('a.files')</label>
                    <div class="custom-file overflow-hidden">
                        <input type="file" class="custom-file-input" name="files[]" id="files" multiple>
                        <label class="custom-file-label" for="files">@lang('a.choose_file')</label>
                    </div>
                </div>
            @endempty

            @isset($values->id)
                {{--


                Картинка --}}
                @if(in_array($values->ext, config('add.imgExt') ?: []))
                    <div class="row">
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col">
                                    <img src="{{ asset($values->path ?? config('add.imgDefault')) }}" class="img-thumbnail" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        {!! $form::input('name', $values->name, null, 'text', true, null, null, ['disabled' => 'true'])!!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('path', $values->path, null, 'text', true, null, null, ['disabled' => 'true'])!!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('old_name', $values->old_name, null, 'text', true, null, null, ['disabled' => 'true'])!!}
                    </div>
                    <div class="col-md-6">
                        {!! $form::input('size', $values->size, null, 'text', true, null, null, ['disabled' => 'true'])!!}
                    </div>
                </div>
            @endisset
            {{--

            Конец формы --}}
            @include('admin.inc.general_end')
        </div>
    </div>
@endsection
