<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;

use App\Http\Requests\UserRequest;

use App\Models\User;

use App\Repositories\UserRepository;

use App\DataTables\UserDataTable;

use Kris\LaravelFormBuilder\FormBuilder;

use Flash;

class UserController extends AppBaseController {

    /** 
     * @var \App\Repositories\UserRepository $userRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepo) {
		 
		//$this->authorizeResource( \App\Models\User::class );
		
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param \App\DataTables\UserDataTable $userDataTable
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $userDataTable) {

        return $userDataTable->render('users.index');
    }

    /**
     * Show the form for creating a new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user, FormBuilder $formBuilder) {

        $form = $formBuilder->create('App\Forms\UserForm', [
            'method' => 'POST',
            'url' => route('back.users.index')
        ]);

        return view('users.create')
            ->with('form', $form);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param \App\Http\Requests\UserRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request) {

        $input = $request->all();

        $user = $this->userRepository->create($input);

        Flash::success('User saved successfully.');

        return redirect(route('back.users.index'));
    }

    /**
     * Display the specified User.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user ) {
		
		$id = $user->id;
        $user = $this->userRepository->find($id);

        if (empty($user)) {

            Flash::error('User not found');

            return redirect(route('back.users.index'));
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, FormBuilder $formBuilder) {
		
		$id = $user->id;
        $user = $this->userRepository->find($id);

        if (empty($user)) {

            Flash::error('User not found');

            return redirect(route('back.users.index'));
        }

        $form = $formBuilder->create('App\Forms\UserForm', [
            'method' => 'patch',
            'url' => route('back.users.update', $user->id),
            'model' => $user
        ]);

        return view('users.edit')
            ->with('form', $form)
            ->with('user', $user);
    }

    /**
     * Update the specified User in storage.
     *
     * @param \App\Models\User $user
     * @param \App\Http\Requests\UserRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UserRequest $request) {
		
		$id = $user->id;
        $user = $this->userRepository->find($id);

        if (empty($user)) {

            Flash::error('User not found');

            return redirect(route('back.users.index'));
        }

        $user = $this->userRepository->update($request->all(), $id);

        Flash::success('User updated successfully.');

        return redirect(route('back.users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user ) {
		
		$id = $user->id;
        $user = $this->userRepository->find($id);

        if (empty($user)) {

            Flash::error('User not found');

            return redirect(route('back.users.index'));
        }

        $this->userRepository->delete($id);

        Flash::success('User deleted successfully.');

        return redirect(route('back.users.index'));
    }
}
