{!! Form::open(['route' => ['back.users.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
	<?php 
	$user = \App\Models\User::where('id', '=', $id)->first();
	?> 
	@if(auth()->user()->can('view', $user))  
    <a href="{{ route('back.users.show', $id) }}" class='btn'>
       <i class="material-icons text-success">remove_red_eye</i>
    </a>
	@endif
	
	@if(auth()->user()->can('update', $user))   
    <a href="{{ route('back.users.edit', $id) }}" class='btn'>
        <i class="material-icons text-info">edit</i>
    </a>
	@endif
  
	@if(auth()->user()->can('delete', $user)) 
    {!! Form::button('<i class="material-icons text-danger">delete</i>', [
        'type' => 'submit',
        'class' => 'btn',
        'onclick' => "return confirm('" . _i("Êtes-vous sûr?") ."')"
    ]) !!}
	@endif
</div>
{!! Form::close() !!}
