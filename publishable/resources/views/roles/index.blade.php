@extends('medKitTheme::_layouts.back.app')

@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{{ _i('Roles') }}</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
             @include('flash::message')
             <div class="row">
                 <div class="col-lg-12">
                     <div class="card">
                         <div class="card-header">
                             <i class="material-icons">format_align_justify</i>
                             {{ _i('Roles') }}
							 
							@if(auth()->user()->can('create', App\Models\Role::class)) 
                             <a class="pull-right" href="{{ route('back.roles.create') }}">
                                <i class="material-icons">add_box</i>
                             </a> 
							@endif
                         </div>
                         <div class="card-body">
                             @include('roles.table')
                              <div class="pull-right mr-3">
                                     
                              </div>
                         </div>
                     </div>
                  </div>
             </div>
         </div>
    </div>
@endsection

