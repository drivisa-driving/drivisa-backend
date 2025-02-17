<?php

return [
    'user.users' => [
        'access' => 'user::users.access_users',
        'list' => 'user::users.list_users',
        'create' => 'user::users.create_user',
        'edit' => 'user::users.edit_user',
        'destroy' => 'user::users.destroy_user',
    ],
    'user.roles' => [
        'access' => 'user::roles.access_roles',
        'list' => 'user::roles.list_roles',
        'create' => 'user::roles.create_role',
        'edit' => 'user::roles.edit_role',
        'destroy' => 'user::roles.destroy_role',
    ]
];
