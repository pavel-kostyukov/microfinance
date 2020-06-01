<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Exchange1C\API;
use Illuminate\Http\Request;
use App\Services\MandarinPayService;

class MandarinController extends Controller
{
    private MandarinPayService $mandarinPayService;
    private API $api;

    public function __construct(MandarinPayService $mandarinPayService, API $api)
    {
        $this->mandarinPayService = $mandarinPayService;
        $this->api = $api;
    }

    public function identify(Request $request)
    {
        //todo: Переделать чтобы данные передавались отдельно, чтобы отлавливать когда и каких данных не хватило для идентификации
        return $this->mandarinPayService->identify($request['data']);
    }

    public function binding(Request $request)
    {
        return $this->mandarinPayService->binding($request['email'], $request['phone']);
    }

    public function checkSms(Request $request)
    {
        return $this->mandarinPayService->checkSmsCode($request['mandarinSessionId'], $request['code']);
    }

    public function getIdentifyResult(Request $request)
    {
        return $this->mandarinPayService->getIdentifyResult($request['smsId']);
    }

    public function repaymentLoan(Request $request)
    {
        return $this->mandarinPayService->payment
        ($request['orderId'], $request['price'], $request['email'],
            env('MANDARIN_URI_RETURN_REPAYMENT'), env('MANDARIN_URI_CALLBACK_REPAYMENT'));
    }

    public function callbackRepaymentLoan(Request $request)
    {
        $response = $this->api->requestReturnLoan($request['order_id'], $request['price']);
        echo 'OK';
        \Log::info('Погашение займа ' . $request['order_id'] . '. успешно выполнено');
    }

    public function paymentExtensionPercent(Request $request)
    {
        return $this->mandarinPayService->payment
        ($request['orderId'], $request['price'], $request['email'],
            env('MANDARIN_URI_RETURN_EXTENSION'), env('MANDARIN_URI_CALLBACK_REPAYMENT_EXTENSION'));
    }

    public function callbackExtensionPercent()
    {

    }
}
