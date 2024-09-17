<?php

namespace App\Services\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    /**
     * Store a newly created user in storage.
     * @param array $data
     * @return array
     */
    public function register(array $data)
    {
        try {
            $role = false;
            if (strpos($data['email'], '@admin') !== false) {
                $role = true;
            }

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
            ]);

            $user->is_admin = $role;
            $user->save();
            return ['status' => true, 'role'    =>  $role ?? false];
        } catch (Exception $e) {
            Log::error('Error registering user: ' . $e->getMessage());
            return ['status' => false, 'msg' => "Duplicated email", 'code' => 500];
        }
    }

    /**
     * Check for credentials
     * @param array $data
     * @return array
     */
    public function login(array $data)
    {
        // Validate the request parameters
        $credentials = [
            "email"         =>      $data['email'],
            "password"      =>      $data['password']
        ];

        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return ['status' => false, 'msg' => 'Username or password is incorrect', 'code'    =>  401];
        }

        // If login is successful, return the JWT token
        return ['status'    =>  true, 'token'   =>  $token];
    }

    /**
     * Generate token at long time
     * @return array
     */
    public function refreshToken()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();

            return [
                'status' => true,
                'token'  => $newToken,
            ];
        } catch (TokenInvalidException $e) {
            return [
                'status' => false,
                'msg'    => 'Token is invalid',
                'code'   => 401
            ];
        } catch (JWTException $e) {
            return [
                'status' => false,
                'msg'    => 'A token is required',
                'code'   => 400
            ];
        }
    }

    /**
     * Remove tokens for user and looged out
     * @return bool[]
     */
    public function logout()
    {
        auth()->logout();
        return ['status' => true];
    }

    /**
     * Get user profile data
     * @return array
     */
    public function show()
    {
        $user = Auth::user();
        $data = [
            "name"          =>      $user->name,
            "email"         =>      $user->email,
        ];
        return ['status'    =>      true, 'profile'     =>      $data];
    }

    /**
     * Update user profile in storage
     * @param array $data
     * @param \App\Models\User $user
     * @return array
     */
    public function updateProfile(array $data, User $user)
    {
        if (Auth::user()->is_admin == false) {
            return ['status' => false, 'msg'    =>  "This user can't access to this permission", 'code' => 400];
        }
        try {
            $filteredData = array_filter($data, function ($value) {
                return !is_null($value) && trim($value) !== '';
            });
            if (count($filteredData) < 1) {
                return ['status' => false, 'msg' => 'Not Found Data in Request!', 'code' => 404];
            }
            $user->update($filteredData);
            return ['status'    =>  true];
        } catch (Exception $e) {
            Log::error('Error update profile: ' . $e->getMessage());
            return ['status'    =>  false, 'msg'    =>  'Failed update profile for user. Try again', 'code' =>  500];
        }
    }

    /**
     * Delete user from storage.
     * @param \App\Models\User $user
     * @return array
     */
    public function deleteUser(User $user)
    {
        if (Auth::user()->is_admin == false) {
            return ['status' => false, 'msg'    =>  "This user can't access to this permission", 'code' => 400];
        }
        try {
            $user->delete();
            return ['status' => true];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    /**
     * Retrive user after deleted
     * @param array $data
     * @return array
     */
    public function restoreUser(array $data)
    {
        if (Auth::user()->is_admin == false) {
            return ['status' => false, 'msg'    =>  "This user can't access to this permission", 'code' => 400];
        }
        try {
            $user = User::withTrashed()->where('email', $data['email'])->first();
            if (!$user) {
                return [
                    'status' => false,
                    'msg' => 'User Not Found',
                    'code' => 404,
                ];
            }
            if ($user->deleted_at === null) {
                return [
                    'status' => false,
                    'msg' => "This user isn't deleted",
                    'code' => 400,
                ];
            }
            $user->restore();
            return ['status' => true];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }
}
