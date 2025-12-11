<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpClient;
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
        //get list of events from database
        $events_data = DbHelper::select("SELECT * FROM events WHERE active_status='Active' ORDER BY addedDate DESC");
        $events = [];
        foreach ($events_data as $event) {
            $record = [
                "code" => (string) $event["event_code"],
                "name" => (string) $event["name"],
                'description' => (string) $event["description"],
                'query' => [
                    'type' => (string) $event["event_type"],
                ],
                "payload" => [
                    "event_code" => (string) $event["event_code"],
                ],
            ];
            $events[] = $record;
        }

        $events[] = [
            "code" => "admission:1",
            "name" => "Admission Exam Attendance",
            'description' => 'For marking attendance manually for admission exam candidates',
            'query' => [
                'admission_exam_session_id' => '1',
                "type" => "admission",
            ],
            "payload" => [
                "event_code" => "admission:1",
            ],
        ];

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

        //for admin call database
        $user = null;
        if ($type == "admin") {
            $admin = DbHelper::selectOne("SELECT name, adminId, username, branch_code, adminType FROM admin WHERE username=? LIMIT 1", [$code]);
            if ($admin != null) {
                $branch = $this->getBranchByCode($admin['branch_code']);

                // view only for preview purpose in future
                $preview = [];
                $preview[] = ["label" => "Name", "value" => (string) $admin["name"]];
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
            }
        } else if (strpos($event_code, "admission:") === 0) {

            list($type, $id) = explode(":", $event_code);

            //find session id in database
            $sessionDetail = DbHelper::selectOne(
                'SELECT * FROM admission_exam_session WHERE admission_exam_session_id=?',
                [$id]
            );
            if ($sessionDetail == null) {
                $res->status(404)->json(['message' => 'Invalid session', "code" => "error"]);
                return;
            }
            $control_session_id = $sessionDetail['control_session_id'];

            // Make HTTP request to get student data using HttpClient
            $client = new HttpClient();
            $client->setVerifySSL(false);
            $client->setHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; Al-Ameen-Face/1.0)',
                'Accept' => 'application/json, text/plain, */*'
            ]);

            //call api to get student details
            $response = $client->get(
                'https://aamsystem.in/alameen2023/import_student_api/api/admission.php',
                [
                    'action' => 'get_students_by_form_no',
                    'controll_session' => $control_session_id,
                    'form_no' => $code
                ]
            );

            if ($response['status'] !== 200) {
                \App\Helpers\Logger::error('EventController API Error - Status: ' . $response['status'] . ' Body: ' . substr($response['body'], 0, 500), [], 'API');
                $res->status($response['status'])->json(['error' => 'API request failed', 'code' => $response['status']]);
                return;
            }

            try {
                // Clean the response body of any potential BOM or whitespace
                $cleanBody = trim(ltrim($response['body'], "\xEF\xBB\xBF"));
                
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
