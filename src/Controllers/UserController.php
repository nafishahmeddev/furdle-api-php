<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Core\Request;
use App\Core\Response;

/**
 * User Controller for handling user-related routes.
 */
class UserController
{
    /**
     * Lookup user by type and code.
     *
     * @param Request $req
     * @param Response $res
     */
    public function lookup(Request $req, Response $res): void
    {

        $data = $req->json();
        if (!$data || !isset($data['type']) || !isset($data['code'])) {
            $res->status(400)->json([
                'code' => 'error',
                'message' => 'Type and code are required'
            ]);
            return;
        }

        $type = $data['type'];
        $code = $data['code'];

        // Dummy lookup logic
        $user = [
            'id' => '12345',
            'name' => 'John Doe',
            'description' => "type: {$type}, Session: 2020, class: 5",
            'facePayload' => [
                'formNo' => (int)$code,
                'session' => 2020,
                'class' => 5
            ]
        ];

        $res->json([
            'code' => 'success',
            'result' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Register user with face ID.
     *
     * @param Request $req
     * @param Response $res
     */
    public function register(Request $req, Response $res): void
    {
        $data = $req->json();
        if (!$data || !isset($data['type']) || !isset($data['code']) || !isset($data['faceId'])) {
            $res->status(400)->json([
                'code' => 'error',
                'message' => 'Type, code, and faceId are required'
            ]);
            return;
        }

        $type = $data['type'];
        $code = $data['code'];
        $faceId = $data['faceId'];

        // Dummy registration logic (e.g., save to database)
        $result = [
            'type' => $type,
            'faceId' => $faceId,
            'code' => $code
        ];

        $res->json([
            'code' => 'success',
            'message' => 'success',
            'result' => $result
        ]);
    }
  }