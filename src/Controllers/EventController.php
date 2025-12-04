<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * Events Controller for handling event-related routes.
 */
class EventController
{
    /**
     * Get list of events/attendance records.
     *
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res): void
    {
        $authHeader = $req->header('Authorization');
        if (!$authHeader) {
            $res->status(401)->json([
                'code' => 'error',
                'message' => 'Authorization header required'
            ]);
            return;
        }

        // Dummy token validation
        if (strlen($authHeader) < 10) {
            $res->status(401)->json([
                'code' => 'error',
                'message' => 'Invalid token'
            ]);
            return;
        }

        // Dummy events data
        $records = [
            [
                'id' => '1',
                'name' => 'Mathematics',
                'description' => 'A branch of science concerned with the properties and relations of numbers and quantities and shapes.',
                'facePayload' => [
                    'type' => 'student',
                    'branch' => 'XFM1000231',
                    'session' => '2025',
                    'class' => '5'
                ]
            ],
            [
                'id' => '5',
                'name' => 'Mathematics (All Students)',
                'description' => 'The branch of science concerned with the nature and properties of matter and energy.',
                'facePayload' => [
                    'type' => 'student',
                    'session' => '2025',
                    'class' => '5'
                ]
            ],
            [
                'id' => '2',
                'name' => 'Admission 2025',
                'description' => 'Information regarding the admission process for the year 2025.',
                'facePayload' => [
                    'type' => 'admission',
                    'session' => '2025',
                    'class' => '5'
                ]
            ],
            [
                'id' => '3',
                'name' => 'Staff(Branch Only)',
                'description' => 'Information regarding the admission process for the year 2025.',
                'facePayload' => [
                    'type' => 'staff',
                    'branch' => 'XFM1000231'
                ]
            ],
            [
                'id' => '4',
                'name' => 'Staff (Global)',
                'description' => 'General announcements for all students and staff.',
                'facePayload' => [
                    'type' => 'staff'
                ]
            ]
        ];

        $res->json([
            'code' => 'success',
            'message' => 'Attendance list retrieved successfully',
            'result' => [
                'records' => $records
            ]
        ]);
    }

    /**
     * Mark attendance for an event.
     *
     * @param Request $req
     * @param Response $res
     */
    public function attend(Request $req, Response $res): void
    {
        $eventId = $req->param('id');

        $data = $req->json();
        if (!$data || !isset($data['payload']['studentId'])) {
            $res->status(400)->json([
                'code' => 'error',
                'message' => 'Student ID is required in payload'
            ]);
            return;
        }

        $studentId = $data['payload']['studentId'];

        // Dummy attendance logic (e.g., save to database)
        // Here you would validate the event exists, student exists, etc.

        $res->json([
            'code' => 'success',
            'message' => 'successful'
        ]);
    }
}