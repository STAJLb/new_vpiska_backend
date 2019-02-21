<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/users/login',
        'api/users/register',
        'api/users/update',
        'api/users/update/image',
        'api/parties/create',
        'api/party/members',
        'api/parties/reviews/add',
        'api/users/update/rating',
        'api/users/notes/',
        'api/parties/reports',
        //V1
        'api/v1/users/login',
        'api/v1/users/register',
        'api/v1/users/update',
        'api/v1/users/update/image',
        'api/v1/parties/create',
        'api/v1/parties/members',
        'api/v1/parties/reviews/add',
        'api/v1/users/update/rating',
        'api/v1/users/notes/',
        'api/v1/users/token/update',
        'api/v1/users/check-update-rating',
        //V2
        'api/v2/*',
       // 'api/v2/users/login',
//        'api/v2/users/register',
//        'api/v2/users/update',
//        'api/v2/avatars/update/image',
//        'api/v2/parties',
//        'api/v2/members',
//        'api/v2/reviews/',
//        'api/v2/ratings/update',
//        'api/v2/notes/update',
//        'api/v2/purchases',
//        'api/v2/tokens/update',
//        'api/v2/users/check-update-rating',

    ];
}
