@extends('medKitTheme::_layouts.back.app')

@section('content')
     <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{!! route('back.mail_templates.index') !!}">{!! _i('Mail Template') !!}</a>
            </li>
            <li class="breadcrumb-item active">{!! _i('Détail') !!}</li>
     </ol>
     <div class="container-fluid">
          <div class="animated fadeIn">

                 <div class="row">
                     <div class="col-lg-12">
                         <div class="card">
                             <div class="card-header">
                                 <strong>{!! _i('Détails') !!}</strong>
                                  <a href="{!! route('back.mail_templates.index') !!}" class="btn btn-light">
                                    <i class="material-icons">arrow_back</i>
                                    {!! _i('Retour') !!}
                                    </a>
                             </div>
                             <div class="card-body">
                                 @include('mail_templates.show_fields')
                             </div>
                         </div>
                     </div>
                 </div>
          </div>
    </div>
@endsection
