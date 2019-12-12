@extends('medKitTheme::_layouts.back.app')

@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{!! _i('Permissions') !!}</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
             @include('flash::message')
             <div class="row">
                 <div class="col-lg-12">
                     <div class="card">
                         <div class="card-header">
                             <i class="material-icons">format_align_justify</i>
                             {!! _i('Permissions') !!}
                             <a class="pull-right" href="{!! route('back.permissions.create') !!}">
                                <i class="material-icons">add_box</i>
                             </a>
                         </div>
                         <div class="card-body">
                             @include('permissions.table')
                              <div class="pull-right mr-3">
                                     
                              </div>
                         </div>
                     </div>
                  </div>
             </div>
         </div>
    </div>
@endsection

