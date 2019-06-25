<?php
return [
    
    'yearquarter_lookahead' => 14,
    'yearquarter_maxactive' => 4,
    'yearquarter_max'       => 'Z999',
    'timezone'              => 'America/Los_Angeles',
    'common_course_char'    => '&',
    'app_version'           => '1.2.0.1',
    'admin_group'           => env('ADMIN_GROUP', null),
    'api_internal_domain'   => env('API_INTERNAL_DOMAIN', 'internal.localhost.test'),
];