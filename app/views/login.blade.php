<div class="page-header">
    <h1>Login</h1>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    @foreach ($errors->all('<p>:message</p>') as $error)
    {{ $error }}
    @endforeach
</div>
@endif

{{ Form::open(array('url' => 'login', 'role' => 'form')) }}

<div class="form-group">
    {{ Form::label('username', 'Username') }}
    {{ Form::text('username', Input::old('username'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
    {{ Form::label('password', 'Password') }}
    {{ Form::password('password', array('class' => 'form-control')) }}
</div>

{{ Form::token() }}

{{ Form::submit('Login', array('class' => 'btn btn-primary')) }}

{{ Form::close() }}
