<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;

use App\Http\Requests\usersRequest;

use App\Models\Role;
use App\Repositories\usersRepository;

use App\DataTables\usersDataTable;

use Kris\LaravelFormBuilder\FormBuilder;

use Laracasts\Flash\Flash;

class usersController extends AppBaseController {

    /**
     * @var \App\Repositories\usersRepository $usersRepository
     */
    private $usersRepository;

    public function __construct(usersRepository $usersRepo) {

        $this->usersRepository = $usersRepo;

        // Si aucun role selectioné, on crée l'array vide
        if(!request()->request->has('roles')){
            request()->request->add(['roles'=>[]]);
        }
    }

    /**
     * Display a listing of the users.
     *
     * @param \App\DataTables\usersDataTable $usersDataTable
     *
     * @return \Illuminate\Http\Response
     */
    public function index(usersDataTable $usersDataTable) {

        return $usersDataTable->render('users.index');
    }

    /**
     * Show the form for creating a new users.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FormBuilder $formBuilder) {

        $form = $formBuilder->create('App\Forms\usersForm', [
            'method' => 'POST',
            'url' => route('back.users.index')
        ]);

        return view('users.create')
            ->with('form', $form);
    }

    /**
     * Store a newly created users in storage.
     *
     * @param \App\Http\Requests\usersRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(usersRequest $request) {

        $input = $request->all();

        $users = $this->usersRepository->create($input);

        Flash::success('Users saved successfully.');

        return redirect(route('back.users.index'));
    }


    /**
     * Display the specified users.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id) {

        $users = $this->usersRepository->find($id);

        if (empty($users)) {

            Flash::error('Users not found');

            return redirect(route('back.users.index'));
        }

        return view('users.show')->with('users', $users);
    }

    /**
     * Show the form for editing the specified users.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id, FormBuilder $formBuilder) {

        $users = $this->usersRepository->find($id);

        if (empty($users)) {

            Flash::error('Users not found');

            return redirect(route('back.users.index'));
        }

        $form = $formBuilder->create('App\Forms\usersForm', [
            'method' => 'patch',
            'url' => route('back.users.update', $users->id),
            'model' => $users
        ]);

        return view('users.edit')
            ->with('form', $form)
            ->with('users', $users);
    }

    /**
     * Update the specified users in storage.
     *
     * @param int $id
     * @param \App\Http\Requests\usersRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, usersRequest $request) {

        $users = $this->usersRepository->find($id);

        if (empty($users)) {

            Flash::error('Users not found');

            return redirect(route('back.users.index'));
        }

        $users = $this->usersRepository->update($request->all(), $id);

        foreach ($request->get('roles') as $roleId) {
            $users->assignRole(Role::findOrFail($roleId));
        }

        Flash::success('Users updated successfully.');

        return redirect(route('back.users.index'));
    }

    /**
     * Remove the specified users from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(int $id) {

        $users = $this->usersRepository->find($id);

        if (empty($users)) {

            Flash::error('Users not found');

            return redirect(route('back.users.index'));
        }

        $this->usersRepository->delete($id);

        Flash::success('Users deleted successfully.');

        return redirect(route('back.users.index'));
    }
}
