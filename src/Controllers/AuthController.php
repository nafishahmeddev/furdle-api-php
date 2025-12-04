<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Core\Request;
use App\Core\Response;

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
      // Generate tokens
      $tokens = TokenHelper::generate([
        'id' => '123',
        'name' => 'John Doe',
        'email' => 'john.doe@example.com'
      ]);

      $faceTokens = [
        "access" => rand(100000, 999999),
        "refresh" => rand(10000000, 99999999),
      ];

      $res->json([
        'code' => 'success',
        'message' => 'Login successful',
        'result' => [
          'tokens' => [
            'access' => $tokens['access'],
            'refresh' => $tokens['refresh']
          ],
          'faceTokens'=> $faceTokens,
          'user' => [
            'id' => '123',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
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
  public function token(Request $req, Response $res) : void
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

    if (!TokenHelper::validate($refreshToken)) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid refresh token'
      ]);
      return;
    }

    $refreshData = TokenHelper::decode($refreshToken);
    if (!$refreshData || !isset($refreshData['accessTokenId'])) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid refresh token data'
      ]);
      return;
    }

    $accessToken = $refreshData['accessTokenId'];
    $accessData = TokenHelper::decode($accessToken);
    if (!$accessData || !isset($accessData['payload'])) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid access token data'
      ]);
      return;
    }

    // Generate new tokens
    $tokens = TokenHelper::generate($accessData['payload']);

    $res->json([
      'code' => 'success',
      'message' => 'Token refreshed successfully',
      'result' => [
        'tokens' => [
          'access' => $tokens['access'],
          'refresh' => $tokens['refresh']
        ]
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
        'isAttendanceAllowed' => true,
        'isRegisterAllowed' => true
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
    $authHeader = $req->header('Authorization');
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Valid Bearer token required'
      ]);
      return;
    }

    $token = substr($authHeader, 7); // Remove 'Bearer '

    // Dummy token validation
    if (strlen($token) < 10) {
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid token'
      ]);
      return;
    }

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
      $res->status(401)->json([
        'code' => 'error',
        'message' => 'Invalid password'
      ]);
    }
  }
}
