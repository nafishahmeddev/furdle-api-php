<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * Constants Controller for handling constant data.
 */
class ConstantsController
{
    /**
     * Get constants data.
     *
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res): void
    {
        $registerUserTypes = [
            [
                'key' => 'admission-2025-5',
                'label' => 'Admission V(2025)'
            ],
            [
                'key' => 'student',
                'label' => 'Student'
            ],
            [
                'key' => 'staff',
                'label' => 'Staff'
            ]
        ];

        $res->json([
            'code' => 'success',
            'result' => [
                'registerUserTypes' => $registerUserTypes
            ]
        ]);
    }
}