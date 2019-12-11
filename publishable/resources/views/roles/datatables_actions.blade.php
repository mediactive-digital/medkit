{!! Form::open(['route' => ['back.roles.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('back.roles.show', $id) }}" class='btn'>
       <i class="material-icons text-success">remove_red_eye</i>
    </a>
    <a href="{{ route('back.roles.edit', $id) }}" class='btn'>
        <i class="material-icons text-info">edit</i>
    </a>
    {!! Form::button('<i class="material-icons text-danger">delete</i>', [
        'type' => 'submit',
        'class' => 'btn',
        'onclick' => "return confirm('" . _i("Êtes-vous sûr?") ."')"
    ]) !!}
</div>
{!! Form::close() !!}
