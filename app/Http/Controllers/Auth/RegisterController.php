<?php

namespace App\Http\Controllers\Auth;

use App\Entities\User;
use App\Support\AppController;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Ramsey\Uuid\Uuid;

/**
 * Class RegisterController
 *
 * @package    App\Http\Controllers\Auth
 * @subpackage App\Http\Controllers\Auth\RegisterController
 */
class RegisterController extends AppController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Create a new controller instance.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->middleware('guest');

        $this->em = $em;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data, [
            'name'     => 'required|max:255',
            'email'    => sprintf('required|email|max:255|unique:%s,email', User::class),
            'password' => 'required|min:8|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User(Uuid::uuid4(), $data['name'], $data['email'], bcrypt($data['password']));
        $user->activate();

        $this->em->persist($user);
        $this->em->flush($user);

        return $user;
    }
}
