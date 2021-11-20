<?php
namespace App\Traits;

use Illuminate\Support\Facades\Response;

trait ApiResponse
{
    protected $statusCode;
    protected $httpCode = 200;          // 默认HTTP状态码
    protected $successStatusCode = 1; // 默认成功逻辑码
    protected $errorStatusCode = 0;   // 默认失败逻辑码
    protected $successMessage = 'success';
    protected $errorMessage = 'error';
    protected $successData = [];
    protected $errorData = [];

    protected function result($data, int $code, string $msg = '', int $status = 200)
    {
        $result = [
            'data' => $data,
            'code' => $code,
            'message'  => $msg,
        ];

        return response()->json($result, $status);
    }


    protected function success($data = null, $message = null)
    {
        $code = $this->statusCode ?? $this->successStatusCode;
        $message = $message ?? $this->successMessage;
//        if (is_string($data)) {
//            $message = $data;
//            $data = [];
//        }
        $data = $data ?? $this->successData;

        return $this->response($data, $code, $message);
    }

    protected function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    protected function error($data = null, $message = null)
    {
        $code = $this->statusCode ?? $this->errorStatusCode;
        $message = $message ?? $this->errorMessage;
        $data = $data ?? $this->errorData;
//        if (is_string($data)) {
//            $message = $data;
//            $data = [];
//        }

        return $this->response($data, $code, $message);
    }

    protected function response($data, $code, $message)
    {
        $data = compact('data', 'code', 'message');

        return Response::json($data, $this->httpCode);
    }

    public function setHttpCode($code)
    {
        $this->httpCode = $code;
        return $this;
    }

    /***
     * use examples
     *
    1、返回正确或错误资源，并用默认提示语
    $user = User::first();
    return $this->success($user);
    return $this->error($user);
    2、返回正确或错误资源信息并设置提示语
    $user = User::first();
    return $this->success($user, '修改成功');
    return $this->fail($user, '修改失败');
    3、返回正确或错误信息
    return $this->success('修改成功');
    return $this->fail('修改失败');
    4、设置自定义逻辑状态码
    return $this->setStatusCode(1)->success('修改成功');
     */
}
