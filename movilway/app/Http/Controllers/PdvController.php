<?php

namespace App\Http\Controllers;

use App\Models\Pdv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Pdv controller
 *
 * @package App\Http\Controllers
 */
class PdvController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        try {
            $pdvs = Pdv::active()->get();

            return $this->successResponse(data: $pdvs);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created Pdv.
     *
     * @param  Request $request Request
     *'
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'fantasy_name' => ['required', 'string'],
                    'cnpj' => [
                        'required',
                        'string',
                        'regex:/\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}/'
                    ],
                    'owner_name' => ['required', 'string'],
                    'owner_phone' => [
                        'required',
                        'string',
                        'regex:/\(\d{2}\)\s9?\d{4}\-\d{4}/'
                    ],
                    'sales_limit' => ['required', 'numeric'],
                ]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $pdv = Pdv::create($request->all());
            
            return $this->successResponse(status: 201, data: $pdv);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    /**
     * Define a new Pdv limit.
     *
     * @param Request $request Request
     * @param Pdv     $pdv     Pdv
     *
     * @return JsonResponse
     */
    public function setLimit(Request $request, Pdv $pdv) : JsonResponse
    {
        try {
            if (!$pdv->isActive()) {
                throw new \Exception('Pdv not found', 404);
            }

            $validator = Validator::make(
                $request->all(),
                [
                    'sales_limit' => ['required', 'numeric', 'min:0.01'],
                ]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $pdv = $pdv
                ->updateSalesLimit($request->get('sales_limit'));

            if (!$pdv) {
                throw new \Exception('Invalid sales limit');
            }

            return $this->successResponse(message: 'Limit updated', data: $pdv);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    /**
     * Get pdv debt.
     *
     * @param Request $request Request
     * @param Pdv     $pdv     Pdv
     *
     * @return JsonResponse
     */
    public function getDebt(Pdv $pdv) : JsonResponse
    {
        try {
            if (!$pdv->isActive()) {
                throw new \Exception('Pdv not found', 404);
            }

            $debt = $pdv->getTotalPendingSales();

            return $this->successResponse(data: array($debt));
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    /**
     * Pay a Pdv debt.
     *
     * @param Request $request Request
     * @param Pdv     $pdv     Pdv
     *
     * @return JsonResponse
     */
    public function quitDebt(Request $request, Pdv $pdv) : JsonResponse
    {
        try {
            if (!$pdv->isActive()) {
                throw new \Exception('Pdv not found', 404);
            }

            $validator = Validator::make(
                $request->all(),
                [
                    'payment_value' => ['required', 'numeric', 'min:0.01'],
                ]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $paid = $pdv
                ->paySalesLimit($request->get('payment_value'));

            if (!$paid) {
                throw new \Exception('Invalid payment value');
            }

            return $this->successResponse(message: 'Limit paid', data: $pdv);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    /**
     * Return the specified resource.
     *
     * @param int $id 
     *
     * @return JsonResponse
     */
    public function show(Pdv $pdv) : JsonResponse
    {
        try {
            if (!$pdv->isActive()) {
                throw new \Exception('Pdv not found', 404);
            }

            return $this->successResponse(data: $pdv);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, Pdv $pdv) : JsonResponse
    {
        try {
            if (!$pdv->isActive()) {
                throw new \Exception('Pdv not found', 404);
            }

            $validator = Validator::make(
                $request->all(),
                [
                    'fantasy_name' => ['string'],
                    'cnpj' => [
                        'string',
                        'regex:/\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}/'
                    ],
                    'owner_name' => ['string'],
                    'owner_phone' => [
                        'string',
                        'regex:/\(\d{2}\)\s9?\d{4}\-\d{4}/',
                    ],
                    'sales_limit' => ['numeric'],
                ]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $pdv->update($request->all());

            return $this->successResponse(message: 'Updated', data: $pdv);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(Pdv $pdv) : JsonResponse
    {
        try {
            if (!$pdv->isActive()) {
                throw new \Exception('Pdv not found', 404);
            }

            $pdv->deactivate();

            return $this->successResponse(message: 'Deleted');
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }
}
