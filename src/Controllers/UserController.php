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
                "value" => "staff",
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
        if ($type == "staff") {
            $admin = DbHelper::selectOne("SELECT name, adminId, username, branch_code, adminType FROM admin WHERE adminId=? LIMIT 1", [$code]);
            if ($admin != null) {
                $branch = $this->getBranchByCode($admin['branch_code']);
                $dynamicFields = [];
                $dynamicFields[] = ["label" => "Admin ID", "value" => (string) $admin["adminId"]];
                $dynamicFields[] = ["label" => "Name", "value" => $admin["name"]];
                $dynamicFields[] = ["label" => "Type", "value" => $admin["adminType"] ?? "N/A"];
                $dynamicFields[] = ["label" => "Username", "value" => $admin["username"]];
                $dynamicFields[] = ["label" => "Branch", "value" => $branch["branch_name"] ?? $admin["branch_code"]];
                $user = [
                    'code' => (string) $admin['adminId'],
                    'name' => $admin["name"],
                    "facePayload" => [
                        "code" => (string) $admin["adminId"],
                        "type" => "admin",
                        "branch" => $admin["branch_code"],
                    ],
                    "preview" => $dynamicFields
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
                    $dynamicFields = [];

                    $dynamicFields[] = ["label" => "Register no", "value" => $code];
                    $dynamicFields[] = ["label" => "Branch", "value" => $branch_details["branch_name"] ?? $branch];
                    $dynamicFields[] = ["label" => "Session", "value" => $session];
                    $dynamicFields[] = ["label" => "Class", "value" => $class];
                    $dynamicFields[] = ["label" => "Board", "value" => $board];

                    $user = [
                        'code' => $code,
                        'name' => $student["name"],
                        "facePayload" => [
                            "code" => $code,
                            "type" => "student",
                            "branch" => $branch,
                            "session" => $session,
                            "class" => $class
                        ],
                        "preview" => $dynamicFields,
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
