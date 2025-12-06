<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Helpers\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\FaceApiHelper;
use stdClass;

/**
 * Auth Controller for handling authentication routes.
 */
class AuthController
{
  /**
   * Handle user login.
   *
   * @param Request $req
   * @param Response $res
   */
  public function login(Request $req, Response $res): void
  {
    $data = $req->json();
    if (!$data || !isset($data['username']) || !isset($data['password'])) {
      $res->status(400)->json([
        'code' => 'error',
        'message' => 'Username and password are required'
      ]);
      return;
    }

    $username = $data['username'];
    $password = $data['password'];

    // Dummy validation (replace with real auth logic)
    if ($username === 'admin' && $password === 'password') {
      // Get static auth user data
      $user = \App\Helpers\MockDataHelper::getAuthUser();

      // Generate tokens
      $tokens = TokenHelper::generate($user);

      $faceToken = FaceApiHelper::generateToken();

      $res->json([
        'code' => 'success',
        'message' => 'Login successful',
        'result' => [
          'tokens' => [
            'access' => $tokens['access'],
            'refresh' => $tokens['refresh'],
            "face" => $faceToken
          ],
          'user' => $user,
          'permissions' => [
            "attendance",
            "register"
          ]
        ]
      ]);
    } else {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid credentials'
      ]);
    }
  }
  /**
   * Generate new access token using refresh token.
   * @param Request $req
   * @param Response $res
   */
  public function token(Request $req, Response $res): void
  {
    $data = $req->json();
    if (!$data || !isset($data['refreshToken'])) {
      $res->status(400)->json([
        'code' => 'error',
        'message' => 'Refresh token is required'
      ]);
      return;
    }

    $refreshToken = $data['refreshToken'];
    $refreshData = TokenHelper::decode($refreshToken);

    if (!is_object($refreshData) || !isset($refreshData->accessToken)) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid refresh token'
      ]);
      return;
    }

    $accessToken = $refreshData->accessToken;
    if ($data["accessToken"] !== $accessToken) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Access token does not match refresh token'
      ]);
      return;
    }
    // Generate new tokens with static auth user data
    $user = \App\Helpers\MockDataHelper::getAuthUser();
    $tokens = TokenHelper::generate($user);

    //get new face token
    $faceToken = FaceApiHelper::generateToken();

    $res->json([
      'code' => 'success',
      'message' => 'Token refreshed successfully',
      'result' => [
        'tokens' => [
          'access' => $tokens['access'],
          'refresh' => $tokens['refresh'],
          "face" => $faceToken
        ],
      ]
    ]);
  }
  /**
   * Verify access token and return user info with permissions.
   *
   * @param Request $req
   * @param Response $res
   */
  public function verify(Request $req, Response $res): void
  {
    $user = $req->auth;
    if ($user === null) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Unauthorized'
      ]);
      return;
    }
    if (!$user) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid token'
      ]);
      return;
    }

    $res->json([
      'code' => 'success',
      'message' => 'Token verified successfully',
      'result' => [
        'user' => $user,
        'permissions' => [
          "attendance",
          "register"
        ]
      ]
    ]);
  }
  /**
   * Validate user password with token.
   *
   * @param Request $req
   * @param Response $res
   */
  public function validatePassword(Request $req, Response $res): void
  {
    $data = $req->json();
    if (!$data || !isset($data['password'])) {
      $res->status(400)->json([
        'code' => 'error',
        'message' => 'Password is required'
      ]);
      return;
    }

    $password = $data['password'];

    // Dummy password validation (e.g., check against user's stored password)
    if ($password === '12345678') {
      $res->json([
        'code' => 'success',
        'message' => 'User is valid'
      ]);
    } else {
      $res->status(400)->json([
        'code' => 'error',
        'message' => 'Invalid password'
      ]);
    }
  }
}
