<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Helpers\MockDataHelper;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\DbHelper;

/**
 * User Controller for handling user-related routes.
 */
class UserController
{
    /**
     * Get user types.
     * @param Request $req
     * @param Response $res
     */
    public function types(Request $request, Response $res): void
    {
        $types = MockDataHelper::getUserTypes();

        $res->json(MockDataHelper::apiResponse(["types" => $types], 'User types retrieved successfully'));
    }
    /**
     * Lookup user by type and id.
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

        //for admin call database
        $user = null;
        if ($type == "staff") {
            $admin = DbHelper::selectOne("SELECT * FROM admin WHERE adminId=?", [$code]);
            if ($admin != null) {
                $user = [
                    'code' => (string) $admin['adminId'],
                    'name' => $admin["name"],
                    "description" => $admin["adminType"],
                    "facePayload" => [
                        "code" => (string) $admin["adminId"],
                        "type" => "admin",
                        "branch" => $admin["branch_code"],
                    ]
                ];
            }
        } else if ($type == "student") {
            $student = DbHelper::selectOne("SELECT * FROM student WHERE registerNo=? LIMIT 1", [$code]);
            if ($student != null) {
                $history = DbHelper::selectOne("SELECT * FROM history WHERE studentId=? ORDER BY asession DESC LIMIT 1", [$student['studentId']]);
                if ($history != null) {
                    $code = (string) $student['registerNo'];
                    $branch = (string) $student['branch'];
                    $session = (string) $history['asession'];
                    $class = (string) $history['class'];
                    $board = (string) $history['board'];
                    $description = "Student from branch {$branch}, session {$session}, class {$class}, board {$board}";
                    $user = [
                        'code' => $code,
                        'name' => $student["name"],
                        "description" => $description,
                        "facePayload" => [
                            "code" => $code,
                            "type" => "student",
                            "branch" => $branch,
                            "session" => $session,
                            "class" => $class
                        ]
                    ];
                }
            }
        } else {
            //TODO: fetch from real database
            $user = MockDataHelper::getUserByCode($code, $type);
            if (!$user) {
                $res->status(404)->json([
                    'code' => 'error',
                    'message' => 'User not found'
                ]);
                return;
            }
        }

        if (!$user) {
            $res->status(404)->json([
                'code' => 'error',
                'message' => 'User not found'
            ]);
            return;
        }

        $res->json(MockDataHelper::apiResponse([
            'user' => $user
        ], 'User retrieved successfully'),);
    }
}
