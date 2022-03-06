<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\oa\model;

use think\Model;
use think\facade\Db;

class WorkRecord extends Model
{
    const ZERO = 0;
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;

    public static $Type = [
        self::ZERO => '-',
        self::ONE => '日报',
        self::TWO => '周报',
        self::THREE => '月报',
    ];
}
