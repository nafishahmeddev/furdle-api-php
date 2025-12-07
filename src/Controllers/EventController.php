<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Helpers\MockDataHelper;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\DbHelper;

/**
 * Events Controller for handling event-related routes.
 */
class EventController
{
    protected function getBranchByCode(string $branchCode): ?array
    {
        $branch = DbHelper::selectOne("SELECT branch_code, branch_name FROM branch WHERE branch_code=? LIMIT 1", [$branchCode]);
        return $branch ?: null;
    }
    /**
     * Get list of events/attendance records.
     *
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res): void
    {
        $events = MockDataHelper::getEvents();

        $res->json(MockDataHelper::apiResponse([
            'records' => $events
        ], 'Attendance list retrieved successfully'));
    }

    /**
     * Lookup user by type and id.
     *
     * @param Request $req
     * @param Response $res
     */
    public function attend(Request $req, Response $res): void
    {
        $data = $req->json();
        if (!$data) {
            $res->status(400)->json([
                'code' => 'error',
                'message' => 'Type and code are required'
            ]);
            return;
        }

        $payload = $data["payload"];

        if (!$payload || !isset($payload['type']) || !isset($payload['code'])) {
            $res->status(400)->json([
                'code' => 'error',
                'message' => 'Payload is required'
            ]);
            return;
        }

        $type = $payload['type'];
        $code = $payload['code'];

        //for admin call database
        $user = null;
        if ($type == "staff") {
            $admin = DbHelper::selectOne("SELECT name, adminId, username, branch_code, adminType FROM admin WHERE username=? LIMIT 1", [$code]);
            if ($admin != null) {
                $branch = $this->getBranchByCode($admin['branch_code']);

                // view only for preview purpose in future
                $preview = [];
                $preview[] = ["label" => "Username", "value" => (string) $admin["username"]];
                $preview[] = ["label" => "Type", "value" => $admin["adminType"] ?? "N/A"];
                $preview[] = ["label" => "Branch", "value" => $branch["branch_name"] ?? $admin["branch_code"]];
                $user = [
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
