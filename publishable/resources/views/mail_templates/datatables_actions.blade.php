{!! Form::open(['route' => ['back.mail_templates.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <?php
    $mailTemplates = \App\Models\MailTemplate::where('id', '=', $id)->first();
    ?>
    @if(auth()->user()->can('view', $mailTemplates))
        <a href="{{ route('back.mail_templates.show', $id) }}" class='btn'>
           <i class="material-icons text-success">remove_red_eye</i>
        </a>
    @endif

    @if(auth()->user()->can('update', $mailTemplates))
        <a href="{{ route('back.mail_templates.edit', $id) }}" class='btn'>
            <i class="material-icons text-info">edit</i>
        </a>
    @endif

    @if(auth()->user()->can('delete', $mailTemplates))
        {!! Form::button('<i class="material-icons text-danger">delete</i>', [
            'type' => 'submit',
            'class' => 'btn',
            'onclick' => "return confirm('" . _i("Êtes-vous sûr?") ."')"
        ]) !!}
    @endif
</div>
{!! Form::close() !!}
