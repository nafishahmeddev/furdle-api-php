<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Mock Data Helper for generating realistic test data.
 */
class MockDataHelper
{
    private static array $users = [
        [
            'code' => '1',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'type' => 'student',
            'branch' => 'XFM1000231',
            'session' => '2025',
            'class' => '5',
            'created_at' => '2024-01-15 10:30:00'
        ],
        [
            'code' => '2',
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'type' => 'student',
            'branch' => 'XFM1000231',
            'session' => '2025',
            'class' => '5',
            'created_at' => '2024-01-16 11:45:00'
        ],
        [
            'code' => '3',
            'name' => 'Mike Johnson',
            'email' => 'mike.johnson@example.com',
            'type' => 'staff',
            'branch' => 'XFM1000231',
            'session' => '2025',
            'class' => '5',
            'created_at' => '2024-01-17 09:15:00'
        ],
        [
            'code' => '4',
            'name' => 'Sarah Wilson',
            'email' => 'sarah.wilson@example.com',
            'type' => 'student',
            'branch' => 'XFM1000232',
            'session' => '2025',
            'class' => '6',
            'created_at' => '2024-01-18 14:20:00'
        ],
        [
            'code' => '5',
            'name' => 'David Brown',
            'email' => 'david.brown@example.com',
            'type' => 'staff',
            'branch' => 'XFM1000232',
            'session' => '2025',
            'class' => '6',
            'created_at' => '2024-01-19 16:30:00'
        ]
    ];

    private static array $events = [
        [
            'code' => '1',
            'name' => 'Mathematics',
            'description' => 'A branch of science concerned with the properties and relations of numbers and quantities and shapes.',
            'facePayload' => [
                'type' => 'student',
                'branch' => 'XFM1000231',
                'session' => '2025',
                'class' => '5'
            ],
            'created_at' => '2024-11-01 08:00:00',
            'is_active' => true
        ],
        [
            'code' => '2',
            'name' => 'Admission 2025',
            'description' => 'Information regarding the admission process for the year 2025.',
            'facePayload' => [
                'type' => 'admission',
                'session' => '2025',
                'class' => '5'
            ],
            'created_at' => '2024-11-02 09:00:00',
            'is_active' => true
        ],
        [
            'code' => '3',
            'name' => 'Staff (Branch Only)',
            'description' => 'Staff meeting for XFM1000231 branch.',
            'facePayload' => [
                'type' => 'staff',
                'branch' => 'XFM1000231'
            ],
            'created_at' => '2024-11-03 10:00:00',
            'is_active' => true
        ],
        [
            'code' => '4',
            'name' => 'Staff (Global)',
            'description' => 'General announcements for all students and staff.',
            'facePayload' => [
                'type' => 'staff'
            ],
            'created_at' => '2024-11-04 11:00:00',
            'is_active' => true
        ],
        [
            'code' => '5',
            'name' => 'Mathematics (All Students)',
            'description' => 'Mathematics class for all students in session 2025.',
            'facePayload' => [
                'type' => 'student',
                'session' => '2025',
                'class' => '5'
            ],
            'created_at' => '2024-11-05 12:00:00',
            'is_active' => true
        ],
        [
            'code' => '6',
            'name' => 'Physics Class 6',
            'description' => 'Physics class for grade 6 students.',
            'facePayload' => [
                'type' => 'student',
                'branch' => 'XFM1000232',
                'session' => '2025',
                'class' => '6'
            ],
            'created_at' => '2024-11-06 13:00:00',
            'is_active' => true
        ],
        [
            'code' => '7',
            'name' => 'Chemistry Lab',
            'description' => 'Chemistry laboratory session for students.',
            'facePayload' => [
                'type' => 'student',
                'session' => '2025',
                'class' => '5'
            ],
            'created_at' => '2024-11-07 14:00:00',
            'is_active' => true
        ],
        [
            'code' => '8',
            'name' => 'Staff Meeting XFM1000232',
            'description' => 'Branch-specific staff meeting.',
            'facePayload' => [
                'type' => 'staff',
                'branch' => 'XFM1000232'
            ],
            'created_at' => '2024-11-08 15:00:00',
            'is_active' => true
        ]
    ];

    private static array $authUser = [
        'id' => '99999',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'type' => 'admin',
        'branch' => 'XFM1000231',
        'session' => '2025',
        'class' => '0',
        'created_at' => '2024-01-01 00:00:00'
    ];

    /**
     * Get all user types.
     * @return array
     */
    public static function getUserTypes(): array
    {
        return [
            [
                "value" => "student",
                "label" => "Student"
            ],
            [
                "value" => "staff",
                "label" => "Staff"
            ],
            [
                "value" => "admission",
                "label" => "Admission"
            ]
        ];
    }

    /**
     * Get all users.
     *
     * @return array
     */
    public static function getUsers(): array
    {
        return self::$users;
    }

    /**
     * Get user by ID.
     *
     * @param string $id
     * @return array|null
     */
    public static function getUserByCode(string $code, $type): ?array
    {
        foreach (self::$users as $user) {
            if ($user['code'] === $code && $user['type'] === $type) {
                return [
                    "code" => $user['code'],
                    "name" => $user['name'],
                    "description" => "{$user['type']} from branch {$user['branch']}, session {$user['session']}, class {$user['class']}",
                    "facePayload" => [
                        "type" => $user['type'],
                        "branch" => $user['branch'],
                        "session" => $user['session'],
                        "class" => $user['class']
                    ]
                ];
            }
        }
        return null;
    }

    /**
     * Get all events.
     *
     * @return array
     */
    public static function getEvents(): array
    {
        return self::$events;
    }

    /**
     * Get event by ID.
     *
     * @param string $id
     * @return array|null
     */
    public static function getEventById(string $id): ?array
    {
        foreach (self::$events as $event) {
            if ($event['id'] === $id) {
                return $event;
            }
        }
        return null;
    }

    /**
     * Get auth user (admin user for login).
     *
     * @return array
     */
    public static function getAuthUser(): array
    {
        return self::$authUser;
    }

    /**
     * Generate API response wrapper.
     *
     * @param mixed $data
     * @param string $message
     * @param string $code
     * @return array
     */
    public static function apiResponse(mixed $data, string $message = 'success', string $code = 'success'): array
    {
        return [
            'code' => $code,
            'message' => $message,
            'result' => $data
        ];
    }
}
