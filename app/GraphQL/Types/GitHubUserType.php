<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class GitHubUserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'GitHubUser',
        'description' => 'Type for GitHub Users'
    ];

    public function fields(): array
    {
        return [
            'Name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name of the User',
                'resolve' => function($root, $args, $context) {
                    return $root['name'];
                }
            ],
            'Login' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Login/User Name of the User',
                'resolve' => function($root, $args, $context) {
                    return $root['login'];
                }
            ],
            'Company' => [
                'type' => Type::string(),
                'description' => 'Company where the User belongs',
                'resolve' => function($root, $args, $context) {
                    return $root['company'];
                }
            ],
            'NumFollowers' => [
                'type' => Type::int(),
                'description' => 'Number of Followers',
                'resolve' => function($root, $args, $context) {
                    return $root['followers'];
                }
            ],
            'NumPublicRepo' => [
                'type' => Type::int(),
                'description' => 'Number of Public Repositories',
                'resolve' => function($root, $args, $context) {
                    return $root['public_repos'];
                }
            ],
            'AvgFollowersPerRepo' => [
                'type' => Type::int(),
                'description' => 'Average Followers per Repository',
                'resolve' => function($root, $args, $context) {
                    return (int) ($root['followers']/$root['public_repos']);
                }
            ]
        ];
    }
}