<?php
namespace app\exception;

use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

class Http extends Handle
{
	public function render($request, Throwable $e): Response
    {
        // 参数验证错误
        if ($e instanceof ValidateException) {
            //return json($e->getError(), 422);
			to_assign(1, $e->getError(), [], '', 422);
        }

        // 请求异常
        if ($e instanceof HttpException && $request->isAjax()) {
            //return response($e->getMessage(), $e->getStatusCode());
			to_assign(1, $e->getMessage(), [], '', $e->getStatusCode());
        }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}