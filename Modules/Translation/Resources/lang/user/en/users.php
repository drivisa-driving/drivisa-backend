<?php

return [
    'access_users' => 'Access users management page',
    'list_users' => 'List users',
    'create_user' => 'Create user',
    'edit_user' => 'Edit user',
    'destroy_user' => 'Delete user',
    'form' => [
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'username' => 'Username',
        'email' => 'Email address',
        'password' => 'Password',
        'current_password' => 'Current password',
        'new_password' => 'New password',
        'password_confirmation' => 'Password confirmation',
        'activation_code' => 'Activation code',
    ],
    'messages' => [
        'user_created' => 'User was created successfully',
        'user_updated' => 'User was updated successfully',
        'user_deleted' => 'User was deleted successfully',
        'user_password_updated' => 'Your password updated successfully',
        'user_not_found' => 'User not found !',
        'user_blocked' => 'User is blocked !',
        'token_not_valid' => 'Token is not valid !',
        'logout_successfully' => 'User logout successfully !',

    ],
    'validation' => [
        'email_unique' => 'This email have been used before. If you have registered before, you can login to your account',
        'required_without' => 'The :field field is required when :values is not present.',
        'password_incorrect' => 'The :field is incorrect.',
        'confirmed' => 'The :field confirmation does not match.',
    ]
];
