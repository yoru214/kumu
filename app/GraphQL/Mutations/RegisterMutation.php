<?php 
namespace App\GraphQL\Mutations;

use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

use App\Models\User;

class RegisterMutation extends Mutation
{
    protected $attributes = [
        'name' => 'Register',
        'description' => 'Register new User'
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
            'name' => [
                'name' => 'name',
                'type' => Type::string()
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'rules'  => function($args) {
                    return [
                        'email',
                        'required',
                        Rule::unique('users','email')->ignore($args['email'])
                    ];
                }
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::string(),
                'rules' => ['required','Min:8']
            ],
        ];
    }
    public function validationErrorMessages(array $args = []): array
    {
        return [
            'email.required' => 'email field is required.',
            'email.email' => 'email field is invalid.',
            'email.unique' => 'email/username already exists.',
            'password.required' => 'password field is required.',
            'password.min' => 'password should be atleast eight (8) characters.'
        ];
    }
    

    public function resolve($root, $args)
    {
        $user     = User::create(['name' => isset($args['name']) ? $args['name'] : '', 'email' => $args['email'], 'password' => Hash::make($args['password'])]);
        $token = $this->jwt->attempt($args);
        
        return ["ID"=>$user->id,"Email"=>$user->email,"Name"=>$user->name,"Token"=>$token];
    }
}