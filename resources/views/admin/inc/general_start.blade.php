<form action="{{ isset($values->id) ? route("admin.{$route}.update", $values->id) : route("admin.{$route}.store") }}" method="post" class="validate" enctype="multipart/form-data" novalidate>
    @isset($values->id)
        @method('put')
    @endisset
    @csrf
