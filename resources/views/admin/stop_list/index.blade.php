@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-body">
            @include('admin.inc.search')
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody id="stop_list_all">
                            @if($values->isNotEmpty())
                                @foreach($values as $key => $item)
                                    @include("admin.{$view}.item")
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    @include('admin.inc.pagination')
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-danger card-outline">
                <div class="table-responsive">
                    <table class="table">
                        <tbody id="stop_list_in">
                        @if($inactive->isNotEmpty())
                            @foreach($inactive as $key => $item)
                                @include("admin.{$view}.item")
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
