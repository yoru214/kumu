<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AuthType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Auth',
        'description' => 'Auth'
    ];

    public function fields(): array
    {
        return [
            'ID' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'ID of the User'
            ],
            'Name' => [
                'type' => Type::string(),
                'description' => 'Name of the User.'
            ],
            'Email' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Email of the User which also serves as the Username.'
            ],
            'Token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Authenticated Token.'
            ]
        ];
    }
}