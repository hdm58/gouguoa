<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\article\validate;

use think\Validate;

class ArticleCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:article',
        'content' => 'require',
        'id' => 'require',
        'article_cate_id' => 'require',
        'status' => 'require',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'title.unique' => '同样的文章标题已经存在',
        'article_cate_id.require' => '所属分类为必选',
        'id.require' => '缺少更新条件',
        'status.require' => '状态为必选',
    ];

    protected $scene = [
        'add' => ['title', 'article_cate_id', 'content', 'status'],
        'edit' => ['title', 'article_cate_id', 'content', 'id', 'status'],
    ];
}
