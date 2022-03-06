<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\article\controller;

use app\base\BaseController;
use app\article\model\Article as ArticleList;
use app\article\validate\ArticleCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.title|a.keywords|a.desc|a.content|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['article_cate_id'])) {
                $where[] = ['a.article_cate_id', '=', $param['article_cate_id']];
            }
            $where[] = ['a.status', '>=', 0];
            $where[] = ['a.is_share', '=', 1];
            $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $content = ArticleList::where($where)
                ->field('a.*,c.id as cate_id,a.id as id,c.title as cate_title,a.title as title,d.title as department,u.name as user')
                ->alias('a')
                ->join('article_cate c', 'a.article_cate_id = c.id')
                ->join('admin u', 'a.uid = u.id','LEFT')
                ->join('department d', 'a.did = d.id','LEFT')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param]);
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }

    public function list()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.title|a.keywords|a.desc|a.content|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['article_cate_id'])) {
                $where[] = ['a.article_cate_id', '=', $param['article_cate_id']];
            }
            $where[] = ['a.status', '>=', 0];
            $where[] = ['a.uid', '=', $this->uid];
            $rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
            $content = ArticleList::where($where)
                ->field('a.*,c.id as cate_id,a.id as id,c.title as cate_title,a.title as title')
                ->alias('a')
                ->join('article_cate c', 'a.article_cate_id = c.id')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param]);
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }

    //文章添加&&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $DbRes = false;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ArticleCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                Db::startTrans();
                try {
                    $res = ArticleList::strict(false)->field(true)->update($param);
                    $aid = $param['id'];
                    if ($res) {
                        //关联关键字
                        if (isset($param['keyword_names']) && $param['keyword_names']) {
                            Db::name('ArticleKeywords')->where(['aid' => $aid])->delete();
                            $keywordArray = explode(',', $param['keyword_names']);
                            $res_keyword = (new ArticleList())->insertKeyword($keywordArray, $aid);
                        } else {
                            $res_keyword == true;
                        }
                        if ($res_keyword !== false) {
                            add_log('edit', $param['id'], $param);
                            Db::commit();
                            $DbRes = true;
                        }
                    } else {
                        Db::rollback();
                    }
                } catch (\Exception $e) { ##这里参数不能删除($e：错误信息)
                Db::rollback();
                }
            } else {
                try {
                    validate(ArticleCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['uid'] = $this->uid;
                $param['did'] = get_login_admin('did');
                Db::startTrans();
                try {
                    if (empty($param['desc'])) {
                        $param['desc'] = get_desc_content($param['content'], 100);
                    }
                    $aid = ArticleList::strict(false)->field(true)->insertGetId($param);
                    if ($aid) {
                        //关联关键字
                        if (isset($param['keyword_names']) && $param['keyword_names']) {
                            $keywordArray = explode(',', $param['keyword_names']);
                            $res_keyword = (new ArticleList())->insertKeyword($keywordArray, $aid);
                        } else {
                            $res_keyword == true;
                        }
                        if ($res_keyword !== false) {
                            add_log('add', $aid, $param);
                            Db::commit();
                            $DbRes = true;
                        }
                    } else {
                        Db::rollback();
                    }
                } catch (\Exception $e) { ##这里参数不能删除($e：错误信息)
                Db::rollback();
                }
            }
            if ($DbRes) {
                return to_assign();
            } else {
                return to_assign(1, '操作失败');
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            View::assign('id', $id);
            if ($id > 0) {
                $article = (new ArticleList())->detail($id);
                View::assign('article', $article);
                return view('edit');
            }
            return view();
        }
    }

    //查看文章
    public function view()
    {
        $id = get_params("id");
        $detail = (new ArticleList())->detail($id);
        // read 字段加 1
        Db::name('article')->where('id', $id)->inc('read')->update();
        View::assign('detail', $detail);
        return view();
    }
    //删除文章
    public function delete()
    {
        $id = get_params("id");
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('Article')->update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
}
