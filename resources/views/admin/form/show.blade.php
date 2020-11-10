@extends('admin.layouts.admin')
{{--

Вывод контента --}}
@section('content')
    @if($values->count())
        <div class="row">
            <div class="col-md-4">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" src="{{ asset($values->user->img) }}" alt="User profile picture">
                        </div>

                        <h3 class="profile-username text-center mb-4">{{ $values->user->name }}</h3>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>@lang('a.user_id')</b> <span class="float-right">{{ $values->user_id }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('a.email')</b> <span class="float-right">{{ $values->user->email }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('a.tel')</b> <span class="float-right">{{ $values->user->tel }}</span>
                            </li>
                        </ul>

                        <a href="{{ route("admin.user.edit", $values->user->id) }}" class="btn btn-primary btn-block pulse"><b>@lang('s.go')</b></a>
                    </div>
                </div>
                <!-- /.card Profile Image -->
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                    <th scope="row">@lang('a.message')</th>
                                    <td>{{ $values->message }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('a.id')</th>
                                    <td>{{ $values->id }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('a.ip')</th>
                                    <td>{{ $values->ip }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('a.created_at')</th>
                                    <td class="text-secondary">{{ d($values->created_at, config('admin.date_format')) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('a.updated_at')</th>
                                    <td class="text-secondary">{{ d($values->updated_at, config('admin.date_format')) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right confirm_form">
                            @method('delete')
                            @csrf
                            <button type="submit" class="btn btn-danger mt-3 pulse">@lang('s.remove')</button>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    @endif
@endsection
