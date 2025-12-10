<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Client for making HTTP requests.
 */
class HttpClient
{
  /** @var array */
  private $headers = [];
  
  /** @var int */
  private $timeout = 30;
  
  /** @var bool */
  private $followRedirects = true;
  
  /** @var bool */
  private $verifySSL = true;

  /**
   * Set request headers.
   *
   * @param array $headers
   * @return self
   */
  public function setHeaders(array $headers): self
  {
    $this->headers = $headers;
    return $this;
  }

  /**
   * Set request timeout in seconds.
   *
   * @param int $timeout
   * @return self
   */
  public function setTimeout(int $timeout): self
  {
    $this->timeout = $timeout;
    return $this;
  }

  /**
   * Set whether to follow redirects.
   *
   * @param bool $follow
   * @return self
   */
  public function setFollowRedirects(bool $follow): self
  {
    $this->followRedirects = $follow;
    return $this;
  }

  /**
   * Set whether to verify SSL certificates.
   *
   * @param bool $verify
   * @return self
   */
  public function setVerifySSL(bool $verify): self
  {
    $this->verifySSL = $verify;
    return $this;
  }

  /**
   * Make a GET request.
   *
   * @param string $url
   * @param array $params Query parameters
   * @return array Response with 'body', 'status', and 'headers'
   * @throws \Exception
   */
  public function get(string $url, array $params = []): array
  {
    if (!empty($params)) {
      $url .= '?' . http_build_query($params);
    }
    return $this->request('GET', $url);
  }

  /**
   * Make a POST request.
   *
   * @param string $url
   * @param mixed $data Request body (array will be JSON encoded)
   * @return array Response with 'body', 'status', and 'headers'
   * @throws \Exception
   */
  public function post(string $url, $data = null): array
  {
    return $this->request('POST', $url, $data);
  }

  /**
   * Make a PUT request.
   *
   * @param string $url
   * @param mixed $data Request body (array will be JSON encoded)
   * @return array Response with 'body', 'status', and 'headers'
   * @throws \Exception
   */
  public function put(string $url, $data = null): array
  {
    return $this->request('PUT', $url, $data);
  }

  /**
   * Make a DELETE request.
   *
   * @param string $url
   * @param mixed $data Request body (array will be JSON encoded)
   * @return array Response with 'body', 'status', and 'headers'
   * @throws \Exception
   */
  public function delete(string $url, $data = null): array
  {
    return $this->request('DELETE', $url, $data);
  }

  /**
   * Make an HTTP request.
   *
   * @param string $method HTTP method
   * @param string $url Request URL
   * @param mixed $data Request body
   * @return array Response with 'body', 'status', and 'headers'
   * @throws \Exception
   */
  private function request(string $method, string $url, $data = null): array
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->followRedirects);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySSL);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HEADER, true);

    // Set headers
    if (!empty($this->headers)) {
      $headerArray = [];
      foreach ($this->headers as $key => $value) {
        $headerArray[] = "$key: $value";
      }
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
    }

    // Set request body for POST, PUT, DELETE
    if ($data !== null && in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
      if (is_array($data)) {
        $data = json_encode($data);
        // Add Content-Type header if not already set
        if (!isset($this->headers['Content-Type'])) {
          curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            curl_getinfo($ch, CURLINFO_HEADER_OUT) ? [] : [],
            ['Content-Type: application/json']
          ));
        }
      }
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

    curl_close($ch);

    if ($error) {
      throw new \Exception("HTTP request failed: $error");
    }

    // Split headers and body
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    return [
      'body' => $body,
      'status' => $httpCode,
      'headers' => $this->parseHeaders($headers),
    ];
  }

  /**
   * Parse response headers.
   *
   * @param string $headerString
   * @return array
   */
  private function parseHeaders(string $headerString): array
  {
    $headers = [];
    $lines = explode("\r\n", $headerString);

    foreach ($lines as $line) {
      if (strpos($line, ':') !== false) {
        list($key, $value) = explode(':', $line, 2);
        $headers[trim($key)] = trim($value);
      }
    }

    return $headers;
  }

  /**
   * Decode JSON response.
   *
   * @param array $response
   * @return mixed
   * @throws \Exception
   */
  public function decodeJson(array $response)
  {
    $decoded = json_decode($response['body'], true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new \Exception('Failed to decode JSON response: ' . json_last_error_msg());
    }

    return $decoded;
  }
}
