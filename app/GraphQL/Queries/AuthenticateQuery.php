<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Closure;
use Log;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class AuthenticateQuery extends Query
{
    protected $attributes = [
        'name' => 'Authenticate',
    ];

    private $jwt;
    
    public function __construct(JWTAuth $jwt)
    {
       $this->jwt = $jwt;
    }
    public function type(): Type
    {
        return GraphQL::type('Auth');
    }

    public function args():array
    {
        return [
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'rules' => ['required','email']
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::string(),
                'rules' => ['required']
            ],
        ];
    }
    public function validationErrorMessages(array $args = []): array
    {
        return [
            'email.required' => 'email field is required.',
            'email.email' => 'email format invalid',
            'password.required' => 'password field is required.'
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = $this->jwt->attempt($args)) {
                return Error::createLocatedError('Invalid Credentials!');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
                return Error::createLocatedError('Something went wrong. Could not create token');
        }
        // Get logged User detail
        $user = Auth::user();

        return ["ID"=>$user->id,"Email"=>$user->email,"Token"=>$token];
    }
}