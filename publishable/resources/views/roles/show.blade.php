@extends('medKitTheme::_layouts.back.app')

@section('content')
     <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{!! route('back.roles.index') !!}">{!! _i('Role') !!}</a>
            </li>
            <li class="breadcrumb-item active">{!! _i('Détail') !!}</li>
     </ol>
     <div class="container-fluid">
          <div class="animated fadeIn">
                 
                 <div class="row">
                 
					@if(auth()->user()->can('role-has-permissions_view_all'))
                     <div class="col-lg-6">
					@endif
                    
					@if(auth()->user()->cannot('role-has-permissions_view_all'))
                     <div class="col-lg-12">
					@endif
                         <div class="card">
                             <div class="card-header">
                                 <strong>{!! _i('Détails') !!}</strong> 
                                  <a href="{!! route('back.roles.index') !!}" class="btn btn-light">
                                    <i class="material-icons">arrow_back</i> 
                                    {!! _i('Retour') !!}
                                    </a>
                             </div>
                             <div class="card-body">
                                 @include('roles.show_fields')
                             </div>
                         </div>
                     </div>
					 
					@if(auth()->user()->can('role-has-permissions_view_all'))
					 <div class="col-lg-6">
                         <div class="card">
                             <div class="card-header">
                                 <strong>{!! _i('Permissions pour ') !!}: {!! $role->name !!}</strong> 
                                  <a href="{!! route('back.permissions.index') !!}" class="btn btn-light">
                                    <i class="material-icons">arrow_back</i> 
                                    {!! _i('Liste des permissions') !!} 
                                    </a>
                             </div>
                             <div class="card-body">
                                 @include('roles.show_permissions')
                             </div>
                         </div>
                     </div>
					@endif
					 
                 </div>
          </div>
    </div> 
@endsection
