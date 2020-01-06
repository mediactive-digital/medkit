{!! Form::open(['route' => ['back.roles.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
	<?php 
	$role = \App\Models\Role::where('id', '=', $id)->first();
	?> 
	@if(auth()->user()->can('view', $role))  
    <a href="{{ route('back.roles.show', $id) }}" class='btn'>
       <i class="material-icons text-success">remove_red_eye</i>
    </a>
	@endif
	
	@if(auth()->user()->can('update', $role))   
    <a href="{{ route('back.roles.edit', $id) }}" class='btn'>
        <i class="material-icons text-info">edit</i>
    </a>
	@endif
  
	@if(auth()->user()->can('delete', $role)) 
    {!! Form::button('<i class="material-icons text-danger">delete</i>', [
        'type' => 'submit',
        'class' => 'btn',
        'onclick' => "return confirm('" . _i("Êtes-vous sûr?") ."')"
    ]) !!}
	@endif
</div>
{!! Form::close() !!}
