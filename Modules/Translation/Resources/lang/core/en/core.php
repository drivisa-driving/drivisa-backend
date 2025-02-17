<?php

return [
    'validation' => [
        'field_name_required' => ':field field is required !',
        'field_name_email' => ':field should be a valid email address EX: john@example.com',
        'field_min_chars' => ':field should contains minimum :count characters',
        'field_date_format' => 'The :field does not match the format :format.',
        'field_date_after' => 'The :field must be a date after :date.',
        'field_required_with' => 'The :field field is required when :values is present.',
        'field_max_chars' => 'The :field may not be greater than :count characters.',
    ],

    'messages' => [
        'something_wrong' => 'Something went wrong!'
    ]
];
