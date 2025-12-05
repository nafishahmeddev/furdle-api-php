<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Helpers\MockDataHelper;
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

        $user = MockDataHelper::getUserById($code, $type);
        if (!$user) {
            $res->status(404)->json([
                'code' => 'error',
                'message' => 'User not found'
            ]);
            return;
        }

        $res->json(MockDataHelper::apiResponse([
            'user' => $user
        ]));
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

        // Simulate registration with mock data
        $result = [
            'type' => $type,
            'faceId' => $faceId,
            'code' => $code,
            'registered_at' => date('Y-m-d H:i:s')
        ];

        $res->json(MockDataHelper::apiResponse($result, 'User registered successfully'));
    }
}