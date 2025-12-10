<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Core\Request;
use App\Core\Response;
use App\Core\HttpClient;
use App\Helpers\DbHelper;
use App\Helpers\FaceApiHelper;

/**
 * Auth Controller for handling authentication routes.
 */
class ThirdPartyController
{
  /**
   * Handle render ui.
   *
   * @param Request $req
   * @param Response $res
   */
  public function render(Request $req, Response $res): void
  {
    $data = $req->body;

    $form_no = $req->query('form_no') ?? '';
    $control_session = $req->query('session') ?? '';

    // Make HTTP request to get student data using HttpClient
    $client = new HttpClient();
    $client->setVerifySSL(false);

    try {
      $response = $client->get(
        'https://aamsystem.in/alameen2023/import_student_api/api/admission.php',
        [
          'action' => 'get_students_by_form_no',
          'controll_session' => $control_session,
          'form_no' => $form_no
        ]
      );

      if ($response['status'] !== 200) {
        $res->status($response['status'])->json(['error' => 'API request failed', 'code' => $response['status']]);
        return;
      }

      $decoded = @$client->decodeJson($response);
      $result = @$decoded['data'];
      $student = @$result[0];
      if( !$student ) {
        $res->status(404)->json(['error' => 'Student not found']);
        return;
      }
      $res->json(['student' => $student]);
    } catch (\Exception $e) {
      $res->status(500)->json(['error' => 'Failed to fetch student data', 'details' => $e->getMessage()]);
    }
  }

  /**
   * Handle register face.
   *
   * @param Request $req
   * @param Response $res
   */

  public function register(Request $req, Response $res): void
  {
    $data = $req->body;
    $faceToken = FaceApiHelper::generateToken();
  }
}
