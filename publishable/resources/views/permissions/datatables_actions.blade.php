{!! Form::open(['route' => ['back.permissions.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
	<?php 
	$permission = \App\Models\Permission::where('id', '=', $id)->first();
	?> 
	@if(auth()->user()->can('view', $permission))  
    <a href="{{ route('back.permissions.show', $id) }}" class='btn'>
       <i class="material-icons text-success">remove_red_eye</i>
    </a>
	@endif
	
	@if(auth()->user()->can('update', $permission))   
    <a href="{{ route('back.permissions.edit', $id) }}" class='btn'>
        <i class="material-icons text-info">edit</i>
    </a>
	@endif
  
	@if(auth()->user()->can('delete', $permission)) 
    {!! Form::button('<i class="material-icons text-danger">delete</i>', [
        'type' => 'submit',
        'class' => 'btn',
        'onclick' => "return confirm('" . _i("Êtes-vous sûr?") ."')"
    ]) !!}
	@endif
</div>
{!! Form::close() !!}
