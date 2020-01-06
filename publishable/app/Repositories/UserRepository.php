<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Repositories\BaseRepository;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version December 3, 2019, 11:49 pm UTC
*/

class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'first_name',
        'email',
        'login',
        'theme'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }
	
	
    public function create($input) {
        $user = parent::create($input);
        $this->updateRoles($input['roles'], $user);
        return $user;
    }

    public function update($input, $id) {
        $user = parent::update($input, $id);
        $this->updateRoles($input['roles'], $user);
        return $user;
    }

    /**
     * Gestion des roles du user
     *
     * @param array $rolesToAdd
     * @param User $user
     */
    public function updateRoles($rolesToAdd,User $user)
    {
        $rolesActuel = $user->roles()->pluck('id')->toArray();
        $rolesToDelete = array_diff($rolesActuel,$rolesToAdd);

        // Suppression des roles non voulu
        foreach ($rolesToDelete as $roleId) {
            $user->removeRole(Role::findOrFail($roleId));
        }

        // Ajout des roles voulu
        foreach ($rolesToAdd as $roleId) {
            $user->assignRole(Role::findOrFail($roleId));
        }
    }
	
	
}
