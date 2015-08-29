<?php
use App\Model\Entity\User;

$config = [
    'auth_settings' => [
        'camelizedControllerNames' => true
    ],
    'public_actions' => [
        'Home' => '*',
        'Login' => [
            'login',
        ],
        'Admin.Login' => [
            'login',
        ],
        'Api.Users' => [
            'authenticate'
        ]
    ],
    'auth_actions' => [
        'Admin.Dashboard' => [
            '*' => [User::ROLE_ADMIN]
        ],
        'Admin.Users' => [
            '*' => [User::ROLE_ADMIN]
        ],
        'Admin.Profile' => [
            '*' => [User::ROLE_ADMIN]
        ],
        'Admin.Login' => [
            '*' => [User::ROLE_ADMIN]
        ],
        'ModelHistory.ModelHistory' => [
            '*' => [User::ROLE_ADMIN]
        ],
        'Notifications.NotificationContents' => [
            '*' => [User::ROLE_ADMIN]
        ],
        'Attachments.Attachments' => [
            'preview' => '*',
            'download' => '*',
            '*' => [User::ROLE_ADMIN]
        ]
    ]
];
