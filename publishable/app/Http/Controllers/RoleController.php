<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;

use App\Http\Requests\RoleRequest;

use App\Models\Role;

use App\Repositories\RoleRepository;

use App\DataTables\RoleDataTable;

use Kris\LaravelFormBuilder\FormBuilder;

use Flash;

class RoleController extends AppBaseController {

    /** 
     * @var \App\Repositories\RoleRepository $roleRepository
     */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepo) {
		 
		$this->authorizeResource( \App\Models\Role::class );
		
        $this->roleRepository = $roleRepo;
        
        // Si aucun role selectioné, on crée l'array vide
        if(!request()->request->has('permissions')){
            request()->request->add(['permissions'=>[]]);
        }
    }

    /**
     * Display a listing of the Role.
     *
     * @param \App\DataTables\RoleDataTable $roleDataTable
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoleDataTable $roleDataTable) {

        return $roleDataTable->render('roles.index');
    }

    /**
     * Show the form for creating a new Role.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Role $role, FormBuilder $formBuilder) {

        $form = $formBuilder->create('App\Forms\RoleForm', [
            'method' => 'POST',
            'url' => route('back.roles.index')
        ]);

        return view('roles.create')
            ->with('form', $form);
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param \App\Http\Requests\RoleRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request) {

        $input = $request->all();

        $role = $this->roleRepository->create($input);
		
		$user	 = \Auth::user();
		if ($user->can('role-has-permissions_edit_all')) { 
			$role->permissions()->attach($request->permissions);
		}

        Flash::success( _i("Rôle enregistré avec succès.") );

        return redirect(route('back.roles.index'));
    }

    /**
     * Display the specified Role.
     *
     * @param \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role ) {
		
		$id = $role->id;
        $role = $this->roleRepository->find($id);

        if (empty($role)) {

            Flash::error( _i("Rôle introuvable") );

            return redirect(route('back.roles.index'));
        }
		
		$allPermission = \App\Models\Permission::all()
			->sortBy('name'); 
		
        return view('roles.show')
			->with('role', $role)
			->with('rolePermissions', $role->permissions()->get() ) 
			->with('permissions', $allPermission ) ;    
    }

    /**
     * Show the form for editing the specified Role.
     *
     * @param \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role, FormBuilder $formBuilder) {
		
		$id = $role->id;
        $role = $this->roleRepository->find($id);

        if (empty($role)) {

            Flash::error(_i("Rôle introuvable") );

            return redirect(route('back.roles.index'));
        }

        $form = $formBuilder->create('App\Forms\RoleForm', [
            'method' => 'patch',
            'url' => route('back.roles.update', $role->id),
            'model' => $role
        ]);
  
        return view('roles.edit')
            ->with('form', $form) 
            ->with('role', $role); 
    }

    /**
     * Update the specified Role in storage.
     *
     * @param \App\Models\Role $role
     * @param \App\Http\Requests\RoleRequest $request
     *
     * @return \Illuminate\Http\Response 
     */
    public function update(Role $role, RoleRequest $request) {
		
		$id = $role->id;
        $role = $this->roleRepository->find($id);

        if (empty($role)) {

            Flash::error(_i("Rôle introuvable") );

            return redirect(route('back.roles.index'));
        }

        $role = $this->roleRepository->update($request->all(), $id);

		
		$user	 = \Auth::user();
		if ($user->can('role-has-permissions_edit_all')) { 
			$role->permissions()->sync($request->permissions); 
		}
        
        Flash::success(_i("Rôle mis à jour avec succès.") );

        return redirect(route('back.roles.index'));
    }
  
    /**
     * Remove the specified Role from storage.
     *
     * @param \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role ) {
		
		$id = $role->id;
        $role = $this->roleRepository->find($id);

        if (empty($role)) {

            Flash::error(_i("Rôle introuvable") );

            return redirect(route('back.roles.index'));
        }

        $this->roleRepository->delete($id);

        Flash::success(_i("Rôle supprimé avec succès.") );

        return redirect(route('back.roles.index'));
    }
}
