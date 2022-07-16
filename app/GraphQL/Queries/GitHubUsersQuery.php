<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use GraphQL\Error\Error;
use Closure;
use Log;

class GitHubUsersQuery extends Query
{
    protected $attributes = [
        'name' => 'GitHubUsers',
    ];

    private $jwt;
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }
    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        try {
            $this->auth =$this->jwt->parseToken()->authenticate();
        } catch (\Exception $e) {
            $this->auth = null;
        }
        return (boolean) $this->auth;
    }
    public function type(): Type
    {
        return Type::listOf(GraphQL::type('GitHubUser'));
    }

    public function args():array
    {
        return [
            'usernames' => [
                'name' => 'usernames',
                'type' => Type::listOf(Type::string()),
                'rules' => ['required','Max:10']
            ],
        ];
    }
    public function validationErrorMessages(array $args = []): array
    {
        return [
            'usernames.required' => 'Please enter at least one (1) username.',
            'usernames.max' => 'You can only enter a maximum of ten (10) usernames.'
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $currentUser = Auth::user();
        $gitHubUsers = [];
        // Connect to Redis.
        $redis = Redis::connection();
        // Sort usernames alphabetically.
        sort($args['usernames']);

        // Loop to all usernames
        foreach($args['usernames'] as $gitHubUser) {
            // check if already cached
            if($data = Redis::get($currentUser->id . "." . $gitHubUser)) {
                $userData = json_decode($data, true);
            } else {
                // Get data from GitHub.
                $client = new \GuzzleHttp\Client();
                $userEndPoint = "https://api.github.com/users/" . $gitHubUser;
                try {
                    $response = $client->request('GET', $userEndPoint, 
                        [
                            'variables'=> ['personalAccessToken' => config('GITHUB_ACCESS_TOKEN')]
                        ]
                    );
                // Catches Exceptions so process can continue when errors occurs.
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    $response = $e->getResponse();
                    // If username is not found in GitHub, which triggered the  404 not found status,
                    // will log that user was not found.
                    if ($response && $response->getStatusCode() == 404) {
                        Log::notice($gitHubUser.' does not have a Github account.');
                    } else {
                        // Something is wrong
                        return Error::createLocatedError('Something went wrong. Please check `laravel.log` for more details.');
                    }
                }
                $statusCode = $response->getStatusCode();
                // Should ensure that page is found or user exists befor adding to the list 
                // return and caching to Redis.
                if($statusCode == 200) {
                    // Gets the response bod text
                    $response = $response->getBody();
                    // Adds to Redis cache an should be kept for exactly 2 minutes.
                    Redis::setex($currentUser->id . "." . $gitHubUser, 120, $response);    
                    // sets the user data that is intended to be added to the return list.
                    $userData = json_decode($response, true);
                } 
            }
            // Checks if userData was found before adding to the return data
            if(isset($userData)) {
                $gitHubUsers[] = $userData;
                unset($userData);
            }
        }

        return $gitHubUsers;
    }
}