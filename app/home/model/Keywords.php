<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\home\model;

use think\Model;

// 关键字模型
class Keywords extends Model
{
    // 关联关键字
    public function increase($keywords)
    {
        $is_exist = $this->where('title', $keywords)->find();
        if ($is_exist) {
            $res = $is_exist['id'];
        } else {
            $res = $this->strict(false)->field(true)->insertGetId(['title' => $keywords, 'create_time' => time()]);
        }
        return $res;
    }
}
