<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $user->name !!}</p>
</div>

<!-- First Name Field -->
<div class="form-group">
    {!! Form::label('first_name', 'First Name:') !!}
    <p>{!! $user->first_name !!}</p>
</div>

<!-- Email Field -->
<div class="form-group">
    {!! Form::label('email', 'Email:') !!}
    <p>{!! $user->email !!}</p>
</div>

<!-- Login Field -->
<div class="form-group">
    {!! Form::label('login', 'Login:') !!}
    <p>{!! $user->login !!}</p>
</div>

<!-- Theme Field -->
<div class="form-group">
    {!! Form::label('theme', 'Theme:') !!}
    <p>{!! $user->theme !!}</p>
</div>

