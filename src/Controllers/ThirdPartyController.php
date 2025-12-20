<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use GuzzleHttp\Client;
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

    // Make HTTP request to get student data using Guzzle
    $client = new Client(['verify' => false]);
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
          'headers' => [
            'User-Agent' => 'Mozilla/5.0 (compatible; Al-Ameen-Face/1.0)',
            'Accept' => 'application/json, text/plain, */*'
          ],
          'query' => [
            'action' => 'get_students_by_form_no',
            'controll_session' => $control_session,
            'form_no' => $form_no
          ],
        ]
      );

      $status = $response->getStatusCode();
      $body = $response->getBody()->getContents();
      $decoded = json_decode($body, true);

      if ($status !== 200) {
        $res->status($status)->json(['code' => 'error', 'message' => 'API request failed']);
        return;
      }

      $student = null;

      // Log the raw response for debugging
      \App\Helpers\Logger::info('External API Response Status: ' . $status, [], 'API');
      \App\Helpers\Logger::info('External API Response Preview: ' . substr($body, 0, 200), [], 'API');

      $result = @$decoded['data'];
      $student = @$result[0];

      if ($student == null || !$student) {
        $res->status(404)->json(['code' => 'error', 'message' => 'Student not found']);
        return;
      }

      $faceToken = FaceApiHelper::generateToken();
      $res->status(200)->json([
        'code' => 'success',
        'message' => 'Student data retrieved successfully',
        'result' => [
          'student' => [
            "form_no" => $student["form_no"],
            "student_name" => $student["student_name"],
            "class_name" => $student["class_name"],
            "image" => $student["image"]
          ],
          "admission_exam_session_id" => $admission_exam_session_id,
          "url" => 'https://api.idexa.app/v1/edge',
          "token" => $faceToken,
          "query" => [
            "type" => "admission",
            "admission_exam_session_id" => (string) $admission_exam_session_id,
          ],
          "uquery" => [
            "type" => "admission",
            "admission_exam_session_id" => (string) $admission_exam_session_id,
            "code" => (string) $student["form_no"],
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
