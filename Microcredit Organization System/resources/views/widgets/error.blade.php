@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="text-danger p-1">{{$error}}</div>
    @endforeach
@endif
