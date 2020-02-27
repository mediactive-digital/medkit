@php

use App\Models\MailTemplate;

$user = auth()->user();

@endphp

{!! Form::open(['route' => ['back.mail_templates.destroy', $id], 'method' => 'delete']) !!}
    <div class="btn-group">
        @if ($model->mailable == MailTemplate::TEST_MAIL_CLASS)
        <a href="{{ route('back.mail_templates.test', $id) }}" class="btn" data-toggle="tooltip" data-placement="right" title="{{ _i('Tester le template mail') }}">
            <i class="material-icons text-Secondary">mail</i>
        </a>
        @endif

        @if($user->can('view', $model))
            <a href="{{ route('back.mail_templates.show', $id) }}" class="btn">
               <i class="material-icons text-success">remove_red_eye</i>
            </a>
        @endif

        @if($user->can('update', $model))
            <a href="{{ route('back.mail_templates.edit', $id) }}" class="btn">
                <i class="material-icons text-info">edit</i>
            </a>
        @endif

        @if($user->can('delete', $model))
            {!! Form::button('<i class="material-icons text-danger">delete</i>', [
                'type' => 'submit',
                'class' => 'btn',
                'onclick' => 'return confirm(\'' . _i('Êtes-vous sûr ?') . '\');'
            ]) !!}
        @endif
    </div>
{!! Form::close() !!}
