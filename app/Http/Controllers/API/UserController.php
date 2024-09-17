<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginFormRequest;
use App\Http\Requests\Auth\UserRegisterFormRequest;
use App\Http\Requests\Auth\UserRestoreFormRequest;
use App\Http\Requests\Auth\UserUpdateFormRequest;
use App\Models\User;
use App\Services\User\UserService;
use App\Traits\ResponseTrait;

class UserController extends Controller
{
  use ResponseTrait;
  protected $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  /**
   * Store a newly created user in storage.
   * @param \App\Http\Requests\Auth\UserRegisterFormRequest $userRegisterFormRequest
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function store(UserRegisterFormRequest $userRegisterFormRequest)
  {
    $validated = $userRegisterFormRequest->validated();
    $response = $this->userService->register($validated);

    if ($response['status']) {
      if ($response['role']) {
        return $this->getResponse('msg', 'Created user successfully as admin', 201);
      } else {
        return $this->getResponse('msg', 'Created user successfully', 201);
      }
    } else {
      return $this->getResponse('error', $response['msg'], $response['code']);
    }
  }

  /**
   * Check for credentials
   * @param \App\Http\Requests\Auth\UserLoginFormRequest $userLoginFormRequest
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function login(UserLoginFormRequest $userLoginFormRequest)
  {
    $validated = $userLoginFormRequest->validated();
    $response = $this->userService->login($validated);
    return $response['status']
      ? $this->getResponse('token', $response['token'], 200)
      : $this->getResponse('error', $response['msg'], $response['code']);
  }

  /**
   * Remove tokens for user and looged out
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function logout()
  {
    $response = $this->userService->logout();
    return $response['status']
      ? $this->getResponse('msg', 'Logged out user successfully', 200)
      : $this->getResponse('error', 'There is error in server', 500);
  }

  /**
   * Generate token at long time
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function refresh()
  {
    $response = $this->userService->refreshToken();
    return $response['status']
      ? $this->getResponse('token', $response['token'], 200)
      : $this->getResponse('error', $response['msg'], $response['code']);
  }

  /**
   * Display the specified user info.
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function show()
  {
    $response = $this->userService->show();
    return $response['status']
      ? $this->getResponse('profile', $response['profile'], 200)
      : $this->getResponse('error', 'There is error in server', 500);
  }

  /**
   * Update the specified user info in storage
   * @param \App\Http\Requests\Auth\UserUpdateFormRequest $userUpdateFormRequest
   * @param string $id
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function update(UserUpdateFormRequest $userUpdateFormRequest, string $id)
  {
    $user = User::find($id);
    if (!$user) {
      return $this->getResponse('error', 'Not Found This User', 404);
    }
    $validated = $userUpdateFormRequest->validated();
    $response = $this->userService->updateProfile($validated, $user);
    return $response['status']
      ? $this->getResponse('msg', 'Updated user successfully', 200)
      : $this->getResponse('error', $response['msg'], $response['code']);
  }

  /**
   * Remove the specified user from storage.
   * @param string $id
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function destory(string $id)
  {
    $user = User::find($id);
    if (!$user) {
      return $this->getResponse('error', 'Not Found This User', 404);
    }
    $response = $this->userService->deleteUser($user);
    return $response['status']
      ? $this->getResponse('msg', 'Deleted user successfully', 200)
      : $this->getResponse('error', $response['msg'], $response['code']);
  }

  /**
   * Retrive user after deleted
   * @param \App\Http\Requests\Auth\UserRestoreFormRequest $userRestoreFormRequest
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function restore(UserRestoreFormRequest $userRestoreFormRequest)
  {
    $validated = $userRestoreFormRequest->validated();
    $response = $this->userService->restoreUser($validated);
    return $response['status']
      ? $this->getResponse('msg', 'Restored user successfully', 200)
      : $this->getResponse('error', $response['msg'], $response['code']);
  }
}
