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
}