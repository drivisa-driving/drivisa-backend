<?php

return [
    'validation' => [
        'confirmed' => 'Password confirmation do not match !',
        'email_unique' => 'Email already exists!'
    ],

    'form' => [
        'current_password' => 'Current password ',
        'new_password' => 'New password ',
        'confirmed' => 'Password confirmation',
        'field_name_required' => ':field field is required !',
        'field_name_email' => ':field should be a valid email address EX: john@example.com',
        'field_min_chars' => ':field should contains minimum :count characters',
        'field_date_format' => 'The :field does not match the format :format.',
        'field_date_after' => 'The :field must be a date after :date.',
        'field_required_with' => 'The :field field is required when :values is present.',
        'field_max_chars' => 'The :field may not be greater than :count characters.'
    ],

    'messages' => [
        'user_not_found' => 'User Not Found',
        'token_not_valid' => 'Token Not Valid',
        'logout_successfully' => 'Logout Successfully',
        'something_wrong' => 'Something went wrong!',
        'user_password_updated' => 'User Password Updated'
    ]
];
