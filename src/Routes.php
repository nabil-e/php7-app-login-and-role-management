<?php declare (strict_types = 1);

return [
    [
        'GET',
        '/',
        'WinYum\FrontPage\Presentation\FrontPageController#show'
    ],
    [
        'GET',
        '/statuspage', // ajout Jefferson
        'WinYum\FrontPage\Presentation\FrontPageController#CheckSiteHealth'
    ],
    [
        'GET',
        '/submit',
        'GenericApp\Submission\Presentation\SubmissionController#show'
    ],
    [
        'POST',
        '/submit',
        'GenericApp\Submission\Presentation\SubmissionController#submit'
    ],
    [
        'GET',
        '/login',
        'WinYum\User\Presentation\LoginController#show'
    ],
    [
        'POST',
        '/login',
        'WinYum\User\Presentation\LoginController#login'
    ],
    [
        'GET',
        '/logout',
        'WinYum\User\Presentation\LogoutController#logout'
    ],

    // Admin routes
    [
        'GET',
        '/admin',
        'WinYum\Admin\Presentation\AccessAdminController#show'
    ],
    [
        'GET',
        '/admin/register',
        'WinYum\User\Presentation\RegistrationController#show'
    ],
    [
        'POST',
        '/admin/register',
        'WinYum\User\Presentation\RegistrationController#register'
    ],
    [
        'GET',
        '/admin/roles',
        'WinYum\SubmissionRole\Presentation\SubmissionRoleController#show'
    ],
    [
        'POST',
        '/admin/roles',
        'WinYum\SubmissionRole\Presentation\SubmissionRoleController#submit'
    ],
    [
        'GET',
        '/admin/userscard',
        'WinYum\Admin\Presentation\RetrieveUsersController#show'
    ],
    [
        'GET',
        '/admin/userstable',
        'WinYum\Admin\Presentation\RetrieveUsersController#show'
    ],
    [
        'GET',
        '/admin/edituser/{id}',
        'WinYum\Admin\Presentation\EditUserController#show'
    ],
    [
        'POST',
        '/admin/edituser/{id}',
        'WinYum\Admin\Presentation\UpdateUserController#updateUser'
    ],
    [
        'GET',
        '/admin/deleteuser/{id}',
        'WinYum\Admin\Presentation\DeleteUserController#deleteUser'
    ],
    [
        'GET',
        '/admin/permissions',
        'WinYum\Admin\Presentation\RetrievePermissionsController#show'
    ]

];