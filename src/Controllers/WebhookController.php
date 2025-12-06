<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Helpers\MockDataHelper;
use App\Helpers\Logger;
use App\Core\Request;
use App\Core\Response;

/**
 * Webhook Controller for handling external webhook events.
 */
class WebhookController
{
  /**
   * Handle face recognition webhook.
   *
   * @param Request $req
   * @param Response $res
   */
  public function faceRecognition(Request $req, Response $res): void
  {
    $data = $req->json();
    if (!$data) {
      $res->status(400)->json([
        'code' => 'error',
        'message' => 'Invalid JSON payload'
      ]);
      return;
    }

    // Validate required fields
    if (!isset($data['event']) || !isset($data['data'])) {
      $res->status(400)->json([
        'code' => 'error',
        'message' => 'Missing required fields: event, data'
      ]);
      return;
    }

    $event = $data['event'];
    $webhookData = $data['data'];

        // Log webhook event
        Logger::webhook($event, $webhookData);    // Handle different event types
    switch ($event) {
      case 'face.matched':
        $this->handleFaceMatched($webhookData, $res);
        break;
      case 'face.registered':
        $this->handleFaceRegistered($webhookData, $res);
        break;
      default:
        $res->status(400)->json([
          'code' => 'error',
          'message' => 'Unknown event type: ' . $event
        ]);
        return;
    }
  }

  /**
   * Handle generic webhook endpoint.
   *
   * @param Request $req
   * @param Response $res
   */
  public function generic(Request $req, Response $res): void
  {
    $data = $req->json();
    if (!$data) {
      $res->status(400)->json([
        'code' => 'error',
        'message' => 'Invalid JSON payload'
      ]);
      return;
    }

        // Log generic webhook
        Logger::webhook('generic', $data);    $res->json(MockDataHelper::apiResponse([
      'received' => true,
      'timestamp' => date('Y-m-d H:i:s'),
      'data' => $data
    ], 'Webhook received successfully'));
  }

  /**
   * Handle face matched event.
   *
   * @param array $data
   * @param Response $res
   */
  private function handleFaceMatched(array $data, Response $res): void
  {
    $code = $data['code'];
    $type = $data['type'];
    $confidence = $data['confidence'];

    // Check if user exists
    $user = MockDataHelper::getUserByCode($code, $type);
    if (!$user) {
      $res->status(404)->json([
        'code' => 'error',
        'message' => 'User not found'
      ]);
      return;
    }

    // Process face match
    $result = [
      'user' => $user,
      'confidence' => $confidence
    ];

    $res->json(MockDataHelper::apiResponse($result, 'Face matched successfully'));
  }

  /**
   * Handle face registered event.
   *
   * @param array $data
   * @param Response $res
   */
  private function handleFaceRegistered(array $data, Response $res): void
  {
    $code = $data['code'];
    $type = $data['type'];

    // Check if user exists
    $user = MockDataHelper::getUserByCode($code, $type);
    if (!$user) {
      $res->status(404)->json([
        'code' => 'error',
        'message' => 'User not found'
      ]);
      return;
    }

    // Process face registration
    $result = [
      'user' => $user,
      'status' => 'registered'
    ];

    $res->json(MockDataHelper::apiResponse($result, 'Face registered successfully'));
  }
}
