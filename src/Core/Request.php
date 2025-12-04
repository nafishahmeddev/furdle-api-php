<?php
declare(strict_types=1);

namespace App\Core;

use App\Helpers\TokenHelper;

/**
 * Request class to encapsulate HTTP request data.
 */
class Request
{
    public string $method;
    public string $uri;
    public string $path;
    /** @var array<string, string> */
    public array $query;
    public string $body;
    /** @var array<string, string> */
    public array $headers;
    /** @var array<string, array> */
    public array $files;
    /** @var array<string, string|null> */
    public array $params = [];
    /** @var array<string, mixed> */
    public mixed $auth = null;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path = parse_url($this->uri, PHP_URL_PATH) ?: '/';
        $this->query = $_GET;
        $this->body = file_get_contents('php://input');
        $this->headers = function_exists('getallheaders') ? getallheaders() : [];
        $this->files = $_FILES;
    }

    /**
     * Get a route parameter by name.
     *
     * @param string $name
     * @return string|null
     */
    public function param(string $name): ?string
    {
        return $this->params[$name] ?? null;
    }

    /**
     * Get a query parameter by name.
     *
     * @param string $name
     * @return string|null
     */
    public function query(string $name): ?string
    {
        return $this->query[$name] ?? null;
    }

    /**
     * Get a header by name.
     *
     * @param string $name
     * @return string|null
     */
    public function header(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * Get the request body as JSON.
     *
     * @return array|null
     */
    public function json(): ?array
    {
        $data = json_decode($this->body, true);
        return json_last_error() === JSON_ERROR_NONE ? $data : null;
    }

    /**
     * Get an uploaded file by name.
     *
     * @param string $name
     * @return array|null
     */
    public function file(string $name): ?array
    {
        return $this->files[$name] ?? null;
    }

    /**
     * Check if a file was uploaded.
     *
     * @param string $name
     * @return bool
     */
    public function hasFile(string $name): bool
    {
        return isset($this->files[$name]) && $this->files[$name]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Get all uploaded files.
     *
     * @return array
     */
    public function allFiles(): array
    {
        return $this->files;
    }
}