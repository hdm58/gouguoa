<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\article\model;

use app\home\model\Keywords;
use think\facade\Db;
use think\Model;

class Article extends Model
{
    // 获取文章详情
    public function detail($id)
    {
        $article = Db::name('Article')->where(['id' => $id])->find();
        if (empty($article)) {
            return $this->error('文章知识不存在');
        }
        $keywrod_array = Db::name('ArticleKeywords')
            ->field('i.aid,i.keywords_id,k.title')
            ->alias('i')
            ->join('Keywords k', 'k.id = i.keywords_id', 'LEFT')
            ->order('i.create_time asc')
            ->where(array('i.aid' => $id, 'k.status' => 1))
            ->select()->toArray();

        $article['keyword_ids'] = implode(",", array_column($keywrod_array, 'keywords_id'));
        $article['keyword_names'] = implode(',', array_column($keywrod_array, 'title'));
        $article['user'] = Db::name('Admin')->where(['id' => $article['uid']])->value('name');
        $article['department'] = Db::name('Department')->where(['id' => $article['did']])->value('title');
        return $article;
    }

    //插入关键字
    public function insertKeyword($keywordArray = [], $aid)
    {
        $insert = [];
        $time = time();
        foreach ($keywordArray as $key => $value) {
            if (!$value) {
                continue;
            }
            $keywords_id = (new Keywords())->increase($value);
            $insert[] = ['aid' => $aid,
                'keywords_id' => $keywords_id,
                'create_time' => $time,
            ];
        }
        $res = Db::name('ArticleKeywords')->strict(false)->field(true)->insertAll($insert);
        return $res;
    }
}
