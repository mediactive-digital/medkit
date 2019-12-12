@extends('medKitTheme::_layouts.back.app')

@section('content')
    <ol class="breadcrumb">
          <li class="breadcrumb-item">
             <a href="{!! route('back.roles.index') !!}">{!! _i('Role') !!}</a>
          </li>
          <li class="breadcrumb-item active">{!! _i('Editer') !!}</li>
        </ol>
    <div class="container-fluid">
         <div class="animated fadeIn">
             
             <div class="row">
                 <div class="col-lg-12">
                      <div class="card">
                          <div class="card-header">
                              <i class="material-icons">edit</i>
                              <strong>{!! _i('Editer') !!} {!! _i('Role') !!}</strong>
                          </div>
                          <div class="card-body"> 
                                {!! form($form) !!}
                            </div>
                        </div>
                    </div>
                </div>
         </div>
    </div>
@endsection