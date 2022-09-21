<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

trait DefaultJsonResponse
{
    /**
     * Status code groups
     *
     * @var array
     */
    private $_statusCodes = [
        '1' => 'Informational',
        '2' => 'Success',
        '3' => 'Redirection',
        '4' => 'Client Error',
        '5' => 'Server Error',
    ];

    /**
     * Return a assertive JSON response
     *
     * @param string                 $message Return message
     * @param integer                $status  Response status type
     * @param array|Collection|Model $data    Data to return
     *
     * @return JsonResponse
     */
    public function successResponse(
        string $message = 'Success',
        int $status = 200,
        array|Collection|Model $data = []
    ) : JsonResponse {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        
        return response()->json(
            [
                'message' => $message,
                'status' => $this->_getStatusGroup($status),
                'data' => $data,
            ],
            $status
        );
    }

    /**
     * Return a error JSON response
     *
     * @param string  $message Return message
     * @param integer $status  Response status type
     *
     * @return JsonResponse
     */
    public function failureResponse(string $message, int $status = 400)
    : JsonResponse
    {
        return response()->json(
            [
                'message' => $message,
                'status' => $this->_getStatusGroup($status),
            ],
            $status
        );
    }

    /**
     * Get status group type from status code
     *
     * @param integer $status HTTP status code
     *
     * @return string
     */
    private function _getStatusGroup(int $status) : string
    {
        return $this->_statusCodes[intdiv($status, 100)];
    }
}