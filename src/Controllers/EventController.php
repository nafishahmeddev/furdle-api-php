<?php

declare(strict_types=1);

namespace App\Controllers;

use GuzzleHttp\Client;
use App\Helpers\CacheHelper;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\DateTimeHelper;
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
        $auth = $req->auth;
        $admin_id = $auth->id;
        //get list of events from database
        $events_data = DbHelper::select("SELECT e.* FROM events e
            INNER JOIN event_permissions ep ON ep.event_id = e.event_id AND ep.admin_id = ?
        WHERE e.active_status='Active' ORDER BY `e`.`priority` DESC", [$admin_id]);
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
                "code" => (string) $event["event_id"],
                "name" => (string) $event["name"],
                'description' => (string) $event["description"],
                'query' => $query,
                "payload" => [
                    "event_id" => (string) $event["event_id"],
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
        if (!$data || !isset($data["payload"]['type'], $data["payload"]['code'])) {
            $res->status(400)->json([
                'code' => 'error',
                'message' => 'Type and code are required'
            ]);
            return;
        }

        $event_id = $data['code'];
        $direction = $data['direction'] ?? "entry";
        $code = (string)$data["payload"]['code'];

        // Find the event details
        $event = DbHelper::selectOne("SELECT * FROM events WHERE event_id=? LIMIT 1", [$event_id]);
        if (!$event) {
            $res->status(404)->json([
                'code' => 'error',
                'message' => 'Event not found'
            ]);
            return;
        }

        try {
            $user = $this->resolveUser($event, $code);
        } catch (\Exception $e) {
            $status = $e->getCode() && $e->getCode() >= 100 ? $e->getCode() : 500;
            $res->status($status)->json([
                'code' => 'error',
                'message' => $e->getMessage()
            ]);
            return;
        }

        // Check event settings
        $has_exit = $event["allow_exit"] === "Yes";
        $has_recurring = $event["allow_recurring"] === "Yes";
        $is_already_marked = false;
        $active_date = date('Y-m-d');
        $timestamp = date('Y-m-d H:i:s');

        // Find existing attendance
        $attendance = DbHelper::selectOne(
            "SELECT * FROM event_attendances WHERE event_id=? AND user_code=? ORDER BY dated DESC LIMIT 1", 
            [$event['event_id'], $code]
        );

        if (!$attendance) {
            if ($direction === 'exit') {
                $res->status(400)->json([
                    'code' => 'error',
                    'message' => 'No active entry found to exit'
                ]);
                return;
            }

            // Create new attendance
            $attendance = [
                'event_id' => $event['event_id'],
                'user_code' => $code,
                'entry_time' => $timestamp,
                'dated' => $active_date,
            ];
            DbHelper::insert('event_attendances', $attendance);
        } elseif ($direction === 'exit') {
            // Processing Exit
            if ($attendance["dated"] !== $active_date) {
                $res->status(400)->json([
                    'code' => 'error',
                    'message' => 'No active entry found to exit'
                ]);
                return;
            }

            if ($has_exit && $attendance['exit_time'] === null) {
                DbHelper::update(
                    'event_attendances', 
                    ['exit_time' => $timestamp], 
                    'event_attendance_id=?', 
                    [$attendance['event_attendance_id']]
                );
                $attendance['exit_time'] = $timestamp;
            } else {
                $is_already_marked = true;
            }
        } else {
            // Processing Entry (Recurring check)
            if ($attendance['dated'] !== $active_date && $has_recurring) {
                $attendance = [
                    'event_id' => $event['event_id'],
                    'user_code' => $code,
                    'entry_time' => $timestamp,
                    'dated' => $active_date,
                ];
                DbHelper::insert('event_attendances', $attendance);
            } else {
                $is_already_marked = true;
            }
        }

        // Prepare response
        $user["preview"][] = ["label" => "Entry Time", "value" => DateTimeHelper::formatHumanDateTime($attendance["entry_time"])];
        
        if (!empty($attendance['exit_time'])) {
            $user["preview"][] = ["label" => "Exit Time", "value" => DateTimeHelper::formatHumanDateTime($attendance["exit_time"])];
        }
        
        $user["preview"][] = [
            "label" => "Status", 
            "value" => $is_already_marked ? "Already Marked" : "Marked Successfully"
        ];

        $responseData = ['user' => $user];
        if ($direction === 'entry') {
            $responseData['canExit'] = $has_exit && @$attendance['exit_time'] === null;
        }

        $res->json(MockDataHelper::apiResponse($responseData, 'User retrieved successfully'));
    }

    /**
     * Resolve user details based on event type.
     * 
     * @throws \Exception
     */
    private function resolveUser(array $event, string $code): array
    {
        $type = $event["event_type"];
        
        switch ($type) {
            case 'admin':
                $admin = DbHelper::selectOne(
                    "SELECT name, adminId, username, branch_code, adminType FROM admin WHERE username=? LIMIT 1", 
                    [$code]
                );
                
                if (!$admin) {
                    throw new \Exception('User not found', 404);
                }

                $branch = $this->getBranchByCode($admin['branch_code']);
                $preview = [
                    ["label" => "Name", "value" => (string) $admin["name"]],
                    ["label" => "Username", "value" => (string) $admin["username"]],
                    ["label" => "Type", "value" => $admin["adminType"] ?? "N/A"],
                ];

                if ($branch && $admin["branch_code"]) {
                    $preview[] = ["label" => "Branch", "value" => $branch["branch_name"] ?? $admin["branch_code"]];
                }
                
                return ["preview" => $preview];

            case 'student':
                $student = DbHelper::selectOne("
                    SELECT s.name, s.registerNo, s.branch, s.studentId, h.asession, h.class, h.board
                    FROM student AS s
                    LEFT JOIN history AS h ON h.studentId = s.studentId
                    WHERE s.registerNo = ?
                    ORDER BY h.asession DESC LIMIT 1
                ", [$code]);

                if (!$student) {
                    throw new \Exception('User not found', 404);
                }

                $registerNo = (string) $student['registerNo'];
                $branchCode = (string) $student['branch'];
                $branchDetails = $this->getBranchByCode($branchCode);

                $preview = [
                    ['label' => 'Name', 'value' => (string) $student['name']],
                    ["label" => "Register no", "value" => $registerNo],
                    ["label" => "Branch", "value" => $branchDetails["branch_name"] ?? $branchCode],
                    ["label" => "Session", "value" => (string) $student['asession']],
                    ["label" => "Class", "value" => (string) $student['class']],
                    ["label" => "Board", "value" => (string) $student['board']]
                ];

                return ["preview" => $preview];

            case 'exam':
                $student = DbHelper::selectOne("
                    SELECT s.name, s.registerNo, s.branch, s.studentId, h.asession, h.class, h.board
                    FROM student AS s
                    LEFT JOIN history AS h ON h.studentId = s.studentId
                    WHERE s.registerNo = ?
                    ORDER BY h.asession DESC LIMIT 1
                ", [$code]);

                if (!$student) {
                    throw new \Exception('User not found', 404);
                }

                $registerNo = (string) $student['registerNo'];
                $branchCode = (string) $student['branch'];
                $branchDetails = $this->getBranchByCode($branchCode);

                $preview = [
                    ['label' => 'Name', 'value' => (string) $student['name']],
                    ["label" => "Register no", "value" => $registerNo],
                    ["label" => "Branch", "value" => $branchDetails["branch_name"] ?? $branchCode],
                    ["label" => "Session", "value" => (string) $student['asession']],
                    ["label" => "Class", "value" => (string) $student['class']],
                    ["label" => "Board", "value" => (string) $student['board']]
                ];

                // Exam logic
                $examPerm = $this->getExamPerm($event);
                $allowedBranches = $examPerm["branches"] ?? [];
                
                $hasBranchAccess = in_array($branchCode, $allowedBranches);
                $hasClassAccess = false;
                
                foreach ($examPerm["classes"] as $classPerm) {
                    if ($classPerm["session"] === (string)$student['asession'] && $classPerm["class"] === (string)$student['class']) {
                        $hasClassAccess = true; 
                        break;
                    }
                }

                if (!$hasBranchAccess || !$hasClassAccess) {
                    throw new \Exception('Access denied for this student', 403);
                }

                return [
                    "preview" => $preview,
                    "perm" => $examPerm
                ];

            case 'admission':
                $sessionDetail = DbHelper::selectOne(
                    'SELECT * FROM admission_exam_session WHERE admission_exam_session_id=?',
                    ["1"]
                );

                if (!$sessionDetail) {
                    throw new \Exception('Invalid session', 404);
                }

                $client = new Client(['verify' => false]);
                $response = $client->get(
                    'https://aamsystem.in/alameen2023/import_student_api/api/admission.php',
                    [
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (compatible; Al-Ameen-Face/1.0)',
                            'Accept' => 'application/json, text/plain, */*'
                        ],
                        'query' => [
                            'action' => 'get_students_by_form_no',
                            'controll_session' => $sessionDetail['control_session_id'],
                            'form_no' => $code
                        ]
                    ]
                );

                $status = $response->getStatusCode();
                if ($status !== 200) {
                     // Could log specific error here if needed
                     throw new \Exception('API request failed', $status);
                }

                $body = $response->getBody()->getContents();
                $cleanBody = trim(ltrim($body, "\xEF\xBB\xBF"));

                if (empty($cleanBody) || (substr($cleanBody, 0, 1) !== '{' && substr($cleanBody, 0, 1) !== '[')) {
                    throw new \Exception('Invalid response format from external API', 500);
                }

                $decoded = json_decode($cleanBody, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Failed to decode JSON response', 500);
                }

                $result = $decoded['data'] ?? [];
                $studentData = $result[0] ?? null;

                if (!$studentData || !isset($studentData['form_no']) || $studentData['form_no'] != $code) {
                    throw new \Exception('User not found', 404);
                }

                return [
                    "preview" => [
                        ['label' => 'Name', 'value' => (string) $studentData['student_name']],
                        ["label" => "Form no", "value" => (string) $studentData['form_no']],
                        ['label' => 'Class', 'value' => (string) $studentData['class_name']]
                    ]
                ];

            default:
                throw new \Exception('Unknown event type', 400);
        }
    }
}

