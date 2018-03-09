<?php namespace JBonnyDev\UserPermissions\Models;

use Model;
use Rainlab\User\Models\User as UserModel;
use Rainlab\User\Models\UserGroup as UserGroupModel;

class Permission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jbonnydev_userpermissions_permissions';

    public $belongsToMany = [
        'users' => ['Rainlab\User\Models\User',
            'table' => 'jbonnydev_userpermissions_user_permission',
            'key' => 'permission_id',
            'otherKey' => 'user_id',
            'timestamps' => true,
            'pivot' => ['permission_state'],
        ],
        'groups' => ['Rainlab\User\Models\UserGroup',
            'table' => 'jbonnydev_userpermissions_group_permission',
            'key' => 'permission_id',
            'otherKey' => 'group_id',
            'timestamps' => true,
            'pivot' => ['permission_state'],
        ],
    ];

    public function afterCreate()
    {
        $this->addNewPermissionToUsers();
        $this->addNewPermissionToUserGroups();
    }

    protected function addNewPermissionToUsers()
    {
        $users = UserModel::all();
        if($users)
        {
            foreach($users as $user)
            {
                $user->user_permissions()->attach($this->id, ['permission_state' => 'inherit']);
            }
        }
    }

    protected function addNewPermissionToUserGroups()
    {
        $usergroups = UserGroupModel::all();
        if($usergroups)
        {
            foreach($usergroups as $usergroup)
            {
                $usergroup->user_permissions()->attach($this->id, ['permission_state' => 'deny']);
            }
        }
    }

}