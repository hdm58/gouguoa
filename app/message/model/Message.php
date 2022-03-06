<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\message\model;

use think\Model;
use think\facade\Db;

class Message extends Model
{
    const ZERO = 0;
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const FOUR = 4;
    const FINE = 5;

    public static $Source = [
        self::ZERO => '无',
        self::ONE => '已发消息',
        self::TWO => '草稿消息',
        self::THREE => '已收消息',
    ];

    public static $Type = [
        self::ZERO => '系统',
        self::ONE => '同事',
        self::TWO => '部门',
        self::THREE => '岗位',
        self::FOUR => '全部',
    ];
}
