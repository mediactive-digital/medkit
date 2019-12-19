<!-- Mailable Field -->
<div class="form-group">
    {!! Form::label('mailable', 'Mailable:') !!}
    <p>{!! $mailTemplate->mailable !!}</p>
</div>

<!-- Subject Field -->
<div class="form-group">
    {!! Form::label('subject', 'Subject:') !!}
    <p>{!! $mailTemplate->subject !!}</p>
</div>

<!-- Html Template Field -->
<div class="form-group">
    {!! Form::label('html_template', 'Html Template:') !!}
    <p>{!! $mailTemplate->html_template !!}</p>
</div>

<!-- Text Template Field -->
<div class="form-group">
    {!! Form::label('text_template', 'Text Template:') !!}
    <p>{!! $mailTemplate->text_template !!}</p>
</div>

