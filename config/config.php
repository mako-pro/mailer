<?php

return
[
    'settings' =>
    [
        'host_name'       => '',
        'mail_method'     => 'smtp',
        'smtp_server'     => 'smtp.gmail.com',
        'smtp_port'       => 587,
        'smtp_username'   => 'user.name@gmail.com',
        'smtp_password'   => '******',
        'smtp_secure'     => true,
        'from_email'      => 'from.address@gmail.com',
        'from_name'       => 'From Name',
    ],
    'messages' =>
    [
        'default' =>
        [
            'subject'     => 'Default Message',
            'view'        => 'mailer::default',
            'images'      => [],
            'attachments' => [],
        ],
    ],
];
