<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\middleware;

class Install
{
    public function handle($request, \Closure $next)
    {
        if (!is_installed()) {
            return $request->isAjax() ? to_assign(1, '请先完成系统安装引导') : redirect((string) url('/install/index'));
        }

        return $next($request);
    }
}
