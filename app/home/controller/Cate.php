<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use app\home\validate\FlowTypeCheck;
use app\home\validate\ExpenseCateCheck;
use app\home\validate\CostCateCheck;
use app\home\validate\SealCateCheck;
use app\home\validate\CarCateCheck;
use app\home\validate\NoteCateCheck;
use app\home\validate\ArticleCateCheck;
use app\home\validate\InvoiceSubjectCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Cate extends BaseController
{
	
	//审批类型
    public function flow_type()
    {
        if (request()->isAjax()) {
            $cate = Db::name('FlowType')->order('id asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }
	
    //审批类型添加
    public function flow_type_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(FlowTypeCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $data['update_time'] = time();
                $res = Db::name('FlowType')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(FlowTypeCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('FlowType')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
		else {
			$id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = Db::name('FlowType')->where(['id' => $id])->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            return view();
        }
    }
	
	//审批类型设置
    public function flow_type_check()
    {
		$param = get_params();
        $res = Db::name('FlowType')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }	
	
	//费用类别
    public function cost_cate()
    {
        if (request()->isAjax()) {
            $cate = Db::name('CostCate')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }
    //费用类别添加
    public function cost_cate_add()
    {
        if (request()->isAjax()) {
            $param = get_params();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(CostCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $data['update_time'] = time();
                $res = Db::name('CostCate')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(CostCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('CostCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
    }
	
    //费用类别设置
    public function cost_cate_check()
    {
		$param = get_params();
        $res = Db::name('CostCate')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }
	
	//印章类别
    public function seal_cate()
    {
        if (request()->isAjax()) {
            $cate = Db::name('SealCate')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }
    //印章类别添加
    public function seal_cate_add()
    {
        if (request()->isAjax()) {
            $param = get_params();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(SealCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $data['update_time'] = time();
                $res = Db::name('SealCate')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(SealCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('SealCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
    }
	
    //印章类别设置
    public function seal_cate_check()
    {
		$param = get_params();
        $res = Db::name('SealCate')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }
	
	//车辆类型
    public function car_cate()
    {
        if (request()->isAjax()) {
            $cate = Db::name('CarCate')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }
    //车辆类型添加
    public function car_cate_add()
    {
        if (request()->isAjax()) {
            $param = get_params();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(CarCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $data['update_time'] = time();
                $res = Db::name('CarCate')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(CarCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('CarCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
    }
	
    //车辆类型设置
    public function car_cate_check()
    {
		$param = get_params();
        $res = Db::name('CarCate')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }
	
	//报销类别
    public function expense_cate()
    {
        if (request()->isAjax()) {
            $cate = Db::name('ExpenseCate')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }
    //报销类别添加
    public function expense_cate_add()
    {
        if (request()->isAjax()) {
            $param = get_params();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ExpenseCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $data['update_time'] = time();
                $res = Db::name('ExpenseCate')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(ExpenseCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('ExpenseCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
    }
	
    //报销类别设置
    public function expense_cate_check()
    {
		$param = get_params();
        $res = Db::name('ExpenseCate')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }
	
    //发票主体
	public function subject()
    {
        if (request()->isAjax()) {
            $subject = Db::name('InvoiceSubject')->order('create_time asc')->select();
            return to_assign(0, '', $subject);
        } else {
            return view();
        }
    }
    //发票主体新建编辑
    public function subject_add()
    {
        if (request()->isAjax()) {
            $param = get_params();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(InvoiceSubjectCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = Db::name('InvoiceSubject')->strict(false)->field('title,id,update_time')->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(InvoiceSubjectCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('InvoiceSubject')->strict(false)->field('title,id,create_time')->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
    }
	
    //发票主体设置
    public function subject_check()
    {
		$param = get_params();
        $res = Db::name('InvoiceSubject')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    }

	//公告类别
    public function note_cate()
    {
        if (request()->isAjax()) {
            $cate = Db::name('NoteCate')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }

    //公告类别添加
    public function note_cate_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(NoteCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $note_array = admin_note_cate_son($param['id']);
                if (in_array($param['pid'], $note_array)) {
                    return to_assign(1, '父级分类不能是该分类本身或其子分类');
                } else {
                    $param['update_time'] = time();
                    $res = Db::name('NoteCate')->strict(false)->field(true)->update($param);
                    if ($res) {
                        add_log('edit', $param['id'], $param);
                    }
                    return to_assign();
                }
            } else {
                try {
                    validate(NoteCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('NoteCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
			$cate = $cate = Db::name('NoteCate')->order('id desc')->select()->toArray();
			$cates = set_recursion($cate);
            if ($id > 0) {
                $detail = Db::name('NoteCate')->where(['id' => $id])->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            View::assign('pid', $pid);
            View::assign('cates', $cates);
            return view();
        }
    }

    //公告类别删除
    public function note_cate_delete()
    {
        $id = get_params("id");
        $cate_count = Db::name('NoteCate')->where(["pid" => $id])->count();
        if ($cate_count > 0) {
            return to_assign(1, "该分类下还有子分类，无法删除");
        }
        $content_count = Db::name('Article')->where(["article_cate_id" => $id])->count();
        if ($content_count > 0) {
            return to_assign(1, "该分类下还有文章，无法删除");
        }
        if (Db::name('NoteCate')->delete($id) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除分类成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
	
	//文章类别
    public function article_cate()
    {
        if (request()->isAjax()) {
            $cate = Db::name('ArticleCate')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }

    //文章分类添加&编辑
    public function article_cate_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ArticleCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $note_array = admin_article_cate_son($param['id']);
                if (in_array($param['pid'], $note_array)) {
                    return to_assign(1, '父级分类不能是该分类本身或其子分类');
                } else {
                    $param['update_time'] = time();
                    $res = Db::name('ArticleCate')->strict(false)->field(true)->update($param);
                    if($res){
                        add_log('edit', $param['id'], $param);
                        return to_assign();
                    }
                }
            } else {
                try {
                    validate(ArticleCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('ArticleCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
			$cate = Db::name('ArticleCate')->order('id desc')->select()->toArray();
			$cates = set_recursion($cate);
            if ($id > 0) {
                $detail = Db::name('ArticleCate')->where(['id' => $id])->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            View::assign('pid', $pid);
            View::assign('cates', $cates);
            return view();
        }
    }

    //删除文章分类
    public function article_cate_delete()
    {
        $id = get_params("id");
        $cate_count = Db::name('ArticleCate')->where(["pid" => $id])->count();
        if ($cate_count > 0) {
            return to_assign(1, "该分类下还有子分类，无法删除");
        }
        $content_count = Db::name('Article')->where(["article_cate_id" => $id])->count();
        if ($content_count > 0) {
            return to_assign(1, "该分类下还有文章，无法删除");
        }
        if (Db::name('ArticleCate')->delete($id) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除分类成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
   
   
}
