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
    protected function getBranchByCode(string $branchCode): ?array
    {
        $branch = DbHelper::selectOne("SELECT branch_code, branch_name FROM branch WHERE branch_code=? LIMIT 1", [$branchCode]);
        return $branch ?: null;
    }
    /**
     * Get user types.
     * @param Request $req
     * @param Response $res
     */
    public function types(Request $request, Response $res): void
    {
        $types = [
            [
                "value" => "student",
                "label" => "Student"
            ],
            [
                "value" => "admin",
                "label" => "Employee"
            ]
        ];

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
        if ($type == "admin") {
            $admin = DbHelper::selectOne("SELECT name, adminId, username, branch_code, adminType FROM admin WHERE username=? LIMIT 1", [$code]);
            if ($admin != null) {
                $branch = $this->getBranchByCode($admin['branch_code']);

                // view only for preview purpose in future
                $preview = [];
                $preview[] = ["label" => "Username", "value" => (string) $admin["username"]];
                $preview[] = ["label" => "Type", "value" => $admin["adminType"] ?? "N/A"];
                $preview[] = ["label" => "Branch", "value" => $branch["branch_name"] ?? $admin["branch_code"]];
                $user = [
                    'code' => (string) $admin['username'],
                    'name' => $admin["name"],
                    "query" => [
                        "type" => "admin",
                    ],
                    "payload" => [
                        "code" => (string) $admin["username"],
                        "type" => "admin",
                        "branch" => $admin["branch_code"],
                    ],
                    "preview" => $preview
                ];
            }
        } else if ($type == "student") {
            $student = DbHelper::selectOne("SELECT name, registerNo, branch, studentId FROM student WHERE registerNo=? LIMIT 1", [$code]);
            if ($student != null) {
                $history = DbHelper::selectOne("SELECT asession, class, board FROM history WHERE studentId=? ORDER BY asession DESC LIMIT 1", [$student['studentId']]);
                if ($history != null) {

                    $code = (string) $student['registerNo'];
                    $branch = (string) $student['branch'];
                    $session = (string) $history['asession'];
                    $class = (string) $history['class'];
                    $board = (string) $history['board'];
                    $branch_details = $this->getBranchByCode($student['branch']);
                    //dynamic filed is for displaying data in future if needed
                    $preview = [];

                    $preview[] = ["label" => "Register no", "value" => $code];
                    $preview[] = ["label" => "Branch", "value" => $branch_details["branch_name"] ?? $branch];
                    $preview[] = ["label" => "Session", "value" => $session];
                    $preview[] = ["label" => "Class", "value" => $class];
                    $preview[] = ["label" => "Board", "value" => $board];

                    $user = [
                        'code' => $code,
                        'name' => $student["name"],
                        "query" => [
                            "type" => "student",
                        ],
                        "payload" => [
                            "code" => $code,
                            "type" => "student",
                            "branch" => $branch,
                            "session" => $session,
                            "class" => $class
                        ],
                        "preview" => $preview,
                    ];
                }
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
