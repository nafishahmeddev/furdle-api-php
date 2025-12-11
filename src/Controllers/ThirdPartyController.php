<?php

declare(strict_types=1);

namespace App\Controllers;

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
  public function lookup(Request $request): void
  {
    //
  }
  /**
   * Handle render ui.
   *
   * @param Request $req
   * @param Response $res
   */
  public function index(Request $req, Response $res): void
  {
    $body = $req->json();

    $form_no = $body['form_no'];
    $control_session = $body['session'];

    // Make HTTP request to get student data using HttpClient
    $client = new HttpClient();
    $client->setVerifySSL(false);

    try {
      //find session id in database
      $sessionDetail = DbHelper::selectOne(
        'SELECT * FROM admission_exam_session WHERE FIND_IN_SET(?, control_session_id)',
        [$control_session]
      );
      if ($sessionDetail == null) {
        $res->status(404)->json(['code' => 'error', 'message' => 'Invalid session']);
        return;
      }
      $admission_exam_session_id = $sessionDetail['admission_exam_session_id'];
      $response = $client->get(
        'https://aamsystem.in/alameen2023/import_student_api/api/admission.php',
        [
          'action' => 'get_students_by_form_no',
          'controll_session' => $control_session,
          'form_no' => $form_no
        ]
      );

      if ($response['status'] !== 200) {
        $res->status($response['status'])->json(['code' => 'error', 'message' => 'API request failed']);
        return;
      }

      $student = null;

      try {
        $decoded = @$client->decodeJson($response);
        $result = @$decoded['data'];
        $student = @$result[0];
      } catch (\Exception $e) {
        $res->status(500)->json(['code' => 'error', 'message' => 'Failed to decode student data', 'details' => $e->getMessage()]);
        return;
      }
      if ($student == null || !$student) {
        $res->status(404)->json(['code' => 'error', 'message' => 'Student not found']);
        return;
      }

      $faceToken = FaceApiHelper::generateToken();
      $res->status(200)->json([
        'code' => 'success',
        'message' => 'Student data retrieved successfully',
        'result' => [
          'student' => $student,
          "admission_exam_session_id" => $admission_exam_session_id,
          "url" => 'https://face.nafish.me/api/edge',
          "token" => $faceToken,
          "query" => [
            "type" => "admission",
            "admission_exam_session_id" => (string) $admission_exam_session_id,
          ],
          "payload" => [
            "code" => (string) $student["form_no"],
            "type" => "admission",
            "admission_exam_session_id" => (string) $admission_exam_session_id,
          ],
        ]
      ]);
    } catch (\Exception $e) {
      $res->status(500)->json(['code' => 'error', 'message' => 'Failed to fetch student data', 'details' => $e->getMessage()]);
    }
  }
}
