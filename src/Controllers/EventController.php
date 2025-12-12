<?php

declare(strict_types=1);

namespace App\Controllers;

use GuzzleHttp\Client;
use App\Helpers\CacheHelper;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\DbHelper;
use App\Helpers\MockDataHelper;

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

    protected function getExamPerm($event): ?array
    {
        $exam_group_id = $event["exam_group_id"];
        $sql = "SELECT egca.asession as session, egca.class, e.branch_codes  as branches 
            FROM exam_group_class_access egca 
	            INNER JOIN exam_group eg ON eg.exam_group_id =egca.exam_group_id
	            INNER JOIN exam e ON e.examId =eg.examId 
            WHERE egca.exam_group_id=?";
        $exam_perm = DbHelper::select($sql, [$exam_group_id]);
        $branches = json_decode($exam_perm[0]["branches"] ?? "[]", true);
        $classes = array_map(function ($item) {
            return [
                "session" => $item["session"],
                "class" => $item["class"]
            ];
        }, $exam_perm);

        $result = [
            "branches" => $branches,
            "classes" => $classes
        ];
        return $result;
    }
    /**
     * Get list of events/attendance records.
     *
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res): void
    {
        //get list of events from database
        $events_data = DbHelper::select("SELECT * FROM events WHERE active_status='Active' ORDER BY addedDate DESC");
        $events = [];
        foreach ($events_data as $event) {
            $query = [];
            if ($event["event_type"] === "student") {
                $query["type"] = "student";
            } else if ($event["event_type"] === "admin") {
                $query["type"] = "admin";
            } else if ($event["event_type"] === "exam") {
                $query["type"] = "student";
            } else if ($event["event_type"] === "admission") {
                $query["type"] = "admission";
                $query["admission_exam_session_id"] = "1";
            }

            $record = [
                "code" => (string) $event["event_code"],
                "name" => (string) $event["name"],
                'description' => (string) $event["description"],
                'query' => $query,
                "payload" => [
                    "event_code" => (string) $event["event_code"],
                ],
            ];
            $events[] = $record;
        }


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
        $event_code = $data['code'];
        $type = $payload['type'];
        $code = $payload['code'];

        //find the event details from database
        $event = DbHelper::selectOne("SELECT * FROM events WHERE event_code=? LIMIT 1", [$event_code]);
        if ($event == null) {
            $res->status(404)->json([
                'code' => 'error',
                'message' => 'Event not found'
            ]);
            return;
        }
        $event_type = $event["event_type"];

        //for admin call database
        $user = null;
        if ($event_type == "admin") {
            $admin = DbHelper::selectOne("SELECT name, adminId, username, branch_code, adminType FROM admin WHERE username=? LIMIT 1", [$code]);
            if ($admin != null) {
                $branch = $this->getBranchByCode($admin['branch_code']);

                // view only for preview purpose in future
                $preview = [];
                $preview[] = ["label" => "Name", "value" => (string) $admin["name"]];
                $preview[] = ["label" => "Username", "value" => (string) $admin["username"]];
                $preview[] = ["label" => "Type", "value" => $admin["adminType"] ?? "N/A"];
                if ($branch != null && $admin["branch_code"] != null) {
                    $preview[] = ["label" => "Branch", "value" => $branch["branch_name"] ?? $admin["branch_code"]];
                }
                $user = [
                    "preview" => $preview
                ];
            }
        } else if ($event_type == "student") {
            $student = DbHelper::selectOne("
                SELECT 
                    s.name,
                    s.registerNo,
                    s.branch,
                    s.studentId,
                    h.asession,
                    h.class,
                    h.board
                FROM student AS s
                LEFT JOIN history AS h 
                    ON h.studentId = s.studentId
                WHERE s.registerNo = ?
                ORDER BY h.asession DESC
                LIMIT 1
            ", [$code]);
            if ($student != null) {
                $code = (string) $student['registerNo'];
                $branch = (string) $student['branch'];
                $session = (string) $student['asession'];
                $class = (string) $student['class'];
                $board = (string) $student['board'];
                $branch_details = $this->getBranchByCode($student['branch']);
                //dynamic filed is for displaying data in future if needed
                $preview = [];
                $preview[] = ['label' => 'Name', 'value' => (string) $student['name']];
                $preview[] = ["label" => "Register no", "value" => $code];
                $preview[] = ["label" => "Branch", "value" => $branch_details["branch_name"] ?? $branch];
                $preview[] = ["label" => "Session", "value" => $session];
                $preview[] = ["label" => "Class", "value" => $class];
                $preview[] = ["label" => "Board", "value" => $board];

                $user = [
                    "preview" => $preview,
                ];
            }
        } else if ($event_type == "admission") {
            $session_id = "1";
            //find session id in database
            $sessionDetail = DbHelper::selectOne(
                'SELECT * FROM admission_exam_session WHERE admission_exam_session_id=?',
                [$session_id]
            );
            if ($sessionDetail == null) {
                $res->status(404)->json(['message' => 'Invalid session', "code" => "error"]);
                return;
            }
            $control_session_id = $sessionDetail['control_session_id'];

            // Make HTTP request to get student data using Guzzle
            $client = new Client(['verify' => false]);

            //call api to get student details
            $response = $client->get(
                'https://aamsystem.in/alameen2023/import_student_api/api/admission.php',
                [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (compatible; Al-Ameen-Face/1.0)',
                        'Accept' => 'application/json, text/plain, */*'
                    ],
                    'query' => [
                        'action' => 'get_students_by_form_no',
                        'controll_session' => $control_session_id,
                        'form_no' => $code
                    ]
                ]
            );

            $status = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($status !== 200) {
                \App\Helpers\Logger::error('EventController API Error - Status: ' . $status . ' Body: ' . substr($body, 0, 500), [], 'API');
                $res->status($status)->json(['error' => 'API request failed', 'code' => $status]);
                return;
            }

            try {
                // Clean the response body of any potential BOM or whitespace
                $cleanBody = trim(ltrim($body, "\xEF\xBB\xBF"));

                // Check if the response looks like JSON
                if (empty($cleanBody) || (substr($cleanBody, 0, 1) !== '{' && substr($cleanBody, 0, 1) !== '[')) {
                    \App\Helpers\Logger::error('EventController Invalid JSON response: ' . substr($cleanBody, 0, 200), [], 'API');
                    $res->status(500)->json(['error' => 'Invalid response format from external API', 'code' => 'error']);
                    return;
                }

                $decoded = json_decode($cleanBody, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    \App\Helpers\Logger::error('EventController JSON decode error: ' . json_last_error_msg() . ' - Response: ' . substr($cleanBody, 0, 200), [], 'API');
                    $res->status(500)->json(['error' => 'Failed to decode JSON response', 'code' => 'error']);
                    return;
                }

                $result = @$decoded['data'];
                $student = @$result[0];
            } catch (\Exception $e) {
                \App\Helpers\Logger::error('EventController Exception: ' . $e->getMessage(), [], 'API');
                $res->status(500)->json(['error' => 'Failed to process response', 'code' => 'error']);
                return;
            }
            if ($student != null) {
                $preview = [];
                $preview[] = ['label' => 'Name', 'value' => (string) $student['student_name']];
                $preview[] = ["label" => "Form no", "value" => (string) $student['form_no']];
                $preview[] = ['label' => 'Class', 'value' => (string) $student['class_name']];

                $user = [
                    "preview" => $preview,
                ];
            }
        } else if ($event_type == "exam") {
            $exam_perm = $this->getExamPerm($event);
            $branches = $exam_perm["branches"] ?? [];
            $student = DbHelper::selectOne("
                SELECT 
                    s.name,
                    s.registerNo,
                    s.branch,
                    s.studentId,
                    h.asession,
                    h.class,
                    h.board
                FROM student AS s
                LEFT JOIN history AS h 
                    ON h.studentId = s.studentId
                WHERE s.registerNo = ?
                ORDER BY h.asession DESC
                LIMIT 1
            ", [$code]);

            if ($student != null) {
                $code = (string) $student['registerNo'];
                $branch = (string) $student['branch'];
                $session = (string) $student['asession'];
                $class = (string) $student['class'];
                $board = (string) $student['board'];
                //check access permission
                $has_branch_access = in_array($student['branch'], $branches);
                $has_class_access = false;
                foreach ($exam_perm["classes"] as $class_perm) {
                    if ($class_perm["session"] === $session && $class_perm["class"] === $class) {
                        $has_class_access = true;
                        break;
                    }
                }
                if ($has_branch_access && $has_class_access) {
                    $branch_details = $this->getBranchByCode($student['branch']);
                    //dynamic filed is for displaying data in future if needed
                    $preview = [];
                    $preview[] = ['label' => 'Name', 'value' => (string) $student['name']];
                    $preview[] = ["label" => "Register no", "value" => $code];
                    $preview[] = ["label" => "Branch", "value" => $branch_details["branch_name"] ?? $branch];
                    $preview[] = ["label" => "Session", "value" => $session];
                    $preview[] = ["label" => "Class", "value" => $class];
                    $preview[] = ["label" => "Board", "value" => $board];

                    $user = [
                        "preview" => $preview,
                    ];
                    $user["perm"] = $exam_perm;
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
