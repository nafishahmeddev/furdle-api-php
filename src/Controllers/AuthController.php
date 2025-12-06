<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\DbHelper;
use App\Helpers\FaceApiHelper;

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

    //find admin from database
    $admin = DbHelper::selectOne('SELECT * FROM admin WHERE username=? AND password=?', [$username, $password]);
    if ($admin == null) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid credentials'
      ]);
      return;
    }

    // Prepare user data for token
    $user  = [
      "id" => (string) $admin['adminId'],
      "name" => $admin["name"],
      "username" => $admin["username"]
    ];

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
    // fetch user data from database or other source
    $admin = DbHelper::selectOne('SELECT * FROM admin WHERE adminId=?', [$refreshData->user->id]);
    if ($admin == null) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'User not found'
      ]);
      return;
    }
    // Generate new tokens with static auth user data
    $user = [
      'id' => (string) $admin['adminId'],
      'name' => $admin["name"],
      "username" => $admin["username"]
    ];
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
    //fetch user from database
    $admin = DbHelper::selectOne('SELECT * FROM admin WHERE adminId=?', [$user->id]);
    if ($admin == null) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'User not found'
      ]);
      return;
    }

    $user = [
      'id' => (string) $admin['adminId'],
      'name' => $admin["name"],
      "username" => $admin["username"]
    ];


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

    $user = $req->auth;
    //fetch user from database
    $admin = DbHelper::selectOne('SELECT * FROM admin WHERE adminId=? AND password=?', [$user->id, $password]);
    if ($admin == null) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid password'
      ]);
      return;
    }

    $res->json([
      'code' => 'success',
      'message' => 'Password validated successfully'
    ]);
  }
}
