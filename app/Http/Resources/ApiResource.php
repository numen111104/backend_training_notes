<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource extends JsonResource
{
    // Properti dengan akses protected untuk memungkinkan akses dari subclass
    protected bool $success;
    protected string $message;
    protected $data;
    protected int $statusCode;
    protected string $statusMessage;
    protected array $headers;

    /**
     * ApiResource constructor.
     * 
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @param string $statusMessage
     * @param array $headers
     */
    public function __construct(bool $success, string $message, $data, int $statusCode, string $statusMessage, array $headers = [])
    {
        $this->success = $success;
        $this->message = $message;
        parent::__construct($data); // Memanggil constructor parent untuk resource data
        $this->statusCode = $statusCode;
        $this->statusMessage = $statusMessage;
        $this->headers = $headers;
    }

    /**
     * Ubah data menjadi array
     * 
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->resource // Menggunakan resource dari JsonResource
        ];
    }

    /**
     * Kustomisasi respons untuk API
     * 
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray($request), $this->statusCode, $this->headers);
    }
}
