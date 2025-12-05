<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Helpers\MockDataHelper;
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
        $events = MockDataHelper::getEvents();

        $res->json(MockDataHelper::apiResponse([
            'records' => $events
        ], 'Attendance list retrieved successfully'));
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

        // Check if user exists in static data
        $user = MockDataHelper::getUserById($studentId, "student");

        if (!$user) {
            $res->status(404)->json([
                'code' => 'error',
                'message' => 'Student not found'
            ]);
            return;
        }

        $res->json(MockDataHelper::apiResponse(null, 'Attendance marked successfully'));
    }
}