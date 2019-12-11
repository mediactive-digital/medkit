<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;

use App\Http\Requests\PermissionRequest;

use App\Models\Permission;

use App\Repositories\PermissionRepository;

use App\DataTables\PermissionDataTable;

use Kris\LaravelFormBuilder\FormBuilder;

use Flash;

class PermissionController extends AppBaseController {

    /** 
     * @var \App\Repositories\PermissionRepository $permissionRepository
     */
    private $permissionRepository;

    public function __construct(PermissionRepository $permissionRepo) {
		 
		$this->authorizeResource( \App\Models\Permission::class );
		
        $this->permissionRepository = $permissionRepo;
    }

    /**
     * Display a listing of the Permission.
     *
     * @param \App\DataTables\PermissionDataTable $permissionDataTable
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PermissionDataTable $permissionDataTable) {

        return $permissionDataTable->render('permissions.index');
    }

    /**
     * Show the form for creating a new Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Permission $permission, FormBuilder $formBuilder) {

        $form = $formBuilder->create('App\Forms\PermissionForm', [
            'method' => 'POST',
            'url' => route('back.permissions.index')
        ]);

        return view('permissions.create')
            ->with('form', $form);
    }

    /**
     * Store a newly created Permission in storage.
     *
     * @param \App\Http\Requests\PermissionRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request) {

        $input = $request->all();

        $permission = $this->permissionRepository->create($input);

        Flash::success('Permission saved successfully.');

        return redirect(route('back.permissions.index'));
    }

    /**
     * Display the specified Permission.
     *
     * @param \App\Models\Permission $permission
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission ) {
		
		$id = $permission->id;
        $permission = $this->permissionRepository->find($id);

        if (empty($permission)) {

            Flash::error('Permission not found');

            return redirect(route('back.permissions.index'));
        }

        return view('permissions.show')->with('permission', $permission);
    }

    /**
     * Show the form for editing the specified Permission.
     *
     * @param \App\Models\Permission $permission
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission, FormBuilder $formBuilder) {
		
		$id = $permission->id;
        $permission = $this->permissionRepository->find($id);

        if (empty($permission)) {

            Flash::error('Permission not found');

            return redirect(route('back.permissions.index'));
        }

        $form = $formBuilder->create('App\Forms\PermissionForm', [
            'method' => 'patch',
            'url' => route('back.permissions.update', $permission->id),
            'model' => $permission
        ]);

        return view('permissions.edit')
            ->with('form', $form)
            ->with('permission', $permission);
    }

    /**
     * Update the specified Permission in storage.
     *
     * @param \App\Models\Permission $permission
     * @param \App\Http\Requests\PermissionRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Permission $permission, PermissionRequest $request) {
		
		$id = $permission->id;
        $permission = $this->permissionRepository->find($id);

        if (empty($permission)) {

            Flash::error('Permission not found');

            return redirect(route('back.permissions.index'));
        }

        $permission = $this->permissionRepository->update($request->all(), $id);

        Flash::success('Permission updated successfully.');

        return redirect(route('back.permissions.index'));
    }

    /**
     * Remove the specified Permission from storage.
     *
     * @param \App\Models\Permission $permission
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission ) {
		
		$id = $permission->id;
        $permission = $this->permissionRepository->find($id);

        if (empty($permission)) {

            Flash::error('Permission not found');

            return redirect(route('back.permissions.index'));
        }

        $this->permissionRepository->delete($id);

        Flash::success('Permission deleted successfully.');

        return redirect(route('back.permissions.index'));
    }
}
