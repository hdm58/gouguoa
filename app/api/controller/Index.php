<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\home\model\AdminLog;
use app\user\validate\AdminCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Session;

class Index extends BaseController
{
 //上传文件
    public function upload()
    {
        $param = get_params();
        if (request()->file('file')) {
            $file = request()->file('file');
        } else {
            return to_assign(1, '没有选择上传文件');
        }
        // dump($file);die;
        // 获取上传文件的hash散列值
        $sha1 = $file->hash('sha1');
        $md5 = $file->hash('md5');
        $rule = [
            'image' => 'jpg,png,jpeg,gif',
            'doc' => 'doc,docx,ppt,pptx,xls,xlsx,pdf',
            'file' => 'zip,gz,7z,rar,tar',
        ];
        $fileExt = $rule['image'] . ',' . $rule['doc'] . ',' . $rule['file'];
        //1M=1024*1024=1048576字节
        $fileSize = 2 * 1024 * 1024;
        if (isset($param['type']) && $param['type']) {
            $fileExt = $rule[$param['type']];
        }
        if (isset($param['size']) && $param['size']) {
            $fileSize = $param['size'];
        }
        $validate = \think\facade\Validate::rule([
            'image' => 'require|fileSize:' . $fileSize . '|fileExt:' . $fileExt,
        ]);
        $file_check['image'] = $file;
        if (!$validate->check($file_check)) {
            return to_assign(1, $validate->getError());
        }
        // 日期前綴
        $dataPath = date('Ym');
        $use = 'thumb';
        $filename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file, function () use ($md5) {
            return $md5;
        });
        if ($filename) {
            //写入到附件表
            $data = [];
            $path = get_config('filesystem.disks.public.url');
            $data['filepath'] = $path . '/' . $filename;
            $data['name'] = $file->getOriginalName();
            $data['mimetype'] = $file->getOriginalMime();
            $data['fileext'] = $file->extension();
            $data['filesize'] = $file->getSize();
            $data['filename'] = $filename;
            $data['sha1'] = $sha1;
            $data['md5'] = $md5;
            $data['module'] = \think\facade\App::initialize()->http->getName();
            $data['action'] = app('request')->action();
            $data['uploadip'] = app('request')->ip();
            $data['create_time'] = time();
            $data['user_id'] = $this->uid;
            if ($data['module'] = 'admin') {
                //通过后台上传的文件直接审核通过
                $data['status'] = 1;
                $data['admin_id'] = $data['user_id'];
                $data['audit_time'] = time();
            }
            $data['use'] = request()->has('use') ? request()->param('use') : $use; //附件用处
            $res['id'] = Db::name('file')->insertGetId($data);
            $res['filepath'] = $data['filepath'];
            $res['name'] = $data['name'];
            $res['filename'] = $data['filename'];
			$res['filesize'] = $data['filesize'];
            add_log('upload', $data['user_id'], $data);
            return to_assign(0, '上传成功', $res);
        } else {
            return to_assign(1, '上传失败，请重试');
        }
    }

    //清空缓存
    public function cache_clear()
    {
        \think\facade\Cache::clear();
        return to_assign(0, '系统缓存已清空');
    }

    //获取关键字
    public function get_keyword_cate()
    {
        $keyword = Db::name('Keywords')->where(['status' => 1])->order('id desc')->select()->toArray();
        return to_assign(0, '', $keyword);
    }

    // 测试邮件发送
    public function email_test()
    {
        $sender = get_params('email');
        //检查是否邮箱格式
        if (!is_email($sender)) {
            return to_assign(1, '测试邮箱码格式有误');
        }
        $email_config = \think\facade\Db::name('config')->where('name', 'email')->find();
        $config = unserialize($email_config['content']);
        $content = $config['template'];
        //所有项目必须填写
        if (empty($config['smtp']) || empty($config['smtp_port']) || empty($config['smtp_user']) || empty($config['smtp_pwd'])) {
            return to_assign(1, '请完善邮件配置信息！');
        }

        $send = send_email($sender, '测试邮件', $content);
        if ($send) {
            return to_assign(0, '邮件发送成功！');
        } else {
            return to_assign(1, '邮件发送失败！');
        }
    }

    //获取部门树形节点列表
    public function get_department_tree()
    {
        $department = get_department();
        $list = get_tree($department, 0, 2);
        $data['trees'] = $list;
        return json($data);
    }
	
    //获取部门树形节点列表2
    public function get_department_select()
    {
		$keyword = get_params('keyword');
		$selected = [];
		if(!empty($keyword)){
			$selected = explode(",",$keyword);
		}		
        $department = get_department();
        $list = get_select_tree($department, 0,0,$selected);
		return to_assign(0, '',$list);
    }

    //获取子部门所有员工
    public function get_employee($did = 0)
    {
        $did = get_params('did');
		if($did == 1){
			$department = $did;
		}
		else{
			$department = get_department_son($did);
		}        
        $employee = Db::name('admin')
            ->field('a.id,a.did,a.position_id,a.mobile,a.name,a.nickname,a.sex,a.status,a.thumb,a.username,d.title as department')
            ->alias('a')
            ->join('Department d', 'a.did = d.id')
            ->where(['a.status' => 1])
            ->where('a.id', ">", 1)
            ->where('a.did', "in", $department)
            ->select();
        return to_assign(0, '', $employee);
    }
	
    //获取部门所有员工
    public function get_employee_select()
    {
        $employee = Db::name('admin')
            ->field('id as value,name')
            ->where(['status' => 1])
            ->select();
        return to_assign(0, '', $employee);
    }

    //获取角色列表
    public function get_position()
    {
        $position = Db::name('Position')->field('id,title as name')->where([['status', '=', 1], ['id', '>', 1]])->select();
        return to_assign(0, '', $position);
    }

    //首页公告
    public function get_note_list()
    {
        $list = Db::name('Note')
            ->field('a.*,c.title as cate_title')
            ->alias('a')
            ->join('note_cate c', 'a.cate_id = c.id')
            ->where(['a.status' => 1])
            ->order('a.id desc')
            ->limit(10)
            ->select()->toArray();
        foreach ($list as $key => $val) {
            $list[$key]['create_time'] = date('Y-m-d :H:i', $val['create_time']);
        }
        $res['data'] = $list;
        return table_assign(0, '', $res);
    }

    //首页文章
    public function get_article_list()
    {
        $list = Db::name('Article')
            ->field('a.*,c.title as cate_title')
            ->alias('a')
            ->join('article_cate c', 'a.article_cate_id = c.id')
            ->where(['a.status' => 1])
            ->order('a.id desc')
            ->limit(10)
            ->select()->toArray();
        foreach ($list as $key => $val) {
            $list[$key]['create_time'] = date('Y-m-d :H:i', $val['create_time']);
        }
        $res['data'] = $list;
        return table_assign(0, '', $res);
    }

    //修改个人信息
    public function edit_personal()
    {
		if (request()->isAjax()) {
            $param = get_params();
            $uid = $this->uid;
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($param);
            $session_admin = get_config('app.session_admin');
            Session::set($session_admin, Db::name('admin')->find($uid));
            return to_assign();
        }
		else{
			return view('user@user/edit_personal', [
				'admin' => get_admin($this->uid),
			]);
		}
    }

    //修改密码
    public function edit_password()
    {
		if (request()->isAjax()) {
            $param = get_params();
            try {
                validate(AdminCheck::class)->scene('editPwd')->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $uid = $this->uid;
			
			$admin = Db::name('Admin')->where(['id' => $uid])->find();
			$old_psw = set_password($param['old_pwd'], $admin['salt']);
			if ($admin['pwd'] != $old_psw) {
				return to_assign(1, '旧密码错误');
			}

			$salt = set_salt(20);
			$new_pwd = set_password($param['pwd'], $salt);
			$data = [
				'reg_pwd' => '',
				'salt' => $salt,
				'pwd' => $new_pwd,
				'update_time' => time(),
			];
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($data);
            $session_admin = get_config('app.session_admin');
            Session::set($session_admin, Db::name('admin')->find($uid));
            return to_assign();
        }
		else{
			return view('user@user/edit_password', [
				'admin' => get_admin($this->uid),
			]);
		}
    }
	
    //系统操作日志
    public function log_list()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$log = new AdminLog();
			$content = $log->get_log_list($param);
			return table_assign(0, '', $content);
		}else{
			return view('home@log/log_list');
		}
    }

    //保存密码修改
    public function password_submit()
    {
        if (request()->isAjax()) {
            $param = get_params();
            try {
                validate(AdminCheck::class)->scene('editpwd')->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $admin = get_admin($this->uid);
            if (set_password($param['old_pwd'], $admin['salt']) !== $admin['pwd']) {
                return to_assign(1, '旧密码不正确!');
            }
            unset($param['username']);
            $param['salt'] = set_salt(20);
            $param['pwd'] = set_password($param['pwd'], $param['salt']);
            Db::name('Admin')->where(['id' => $admin['id'],
            ])->strict(false)->field(true)->update($param);
            $session_admin = get_config('app.session_admin');
            Session::set($session_admin, Db::name('admin')->find($admin['id']));
            return to_assign();
        }
    }
	
	
	//获取审核类型
    public function get_flow_cate($type=0)
    {
		$flows = Db::name('FlowType')->where(['type'=>$type,'status'=>1])->select()->toArray();
		return to_assign(0, '', $flows);
	}
	//获取审核步骤人员
    public function get_flow_users($id=0)
    {
        $flow = Db::name('Flow')->where(['id' => $id])->find();
        $flowData = unserialize($flow['flow_list']);
		foreach ($flowData as $key => &$val) {
            $val['user_id_info'] = Db::name('Admin')->field('id,name,thumb')->where('id','in',$val['flow_uids'])->select()->toArray();
        }
        return to_assign(0, '', $flowData);
    }
	
	//获取审核流程节点
    public function get_flow_nodes($id=0,$type=1)
    {
		$flows = Db::name('FlowStep')->where(['action_id'=>$id,'type'=>$type,'delete_time'=>0])->order('sort asc')->select()->toArray();
		foreach ($flows as $key => &$val) {
            $user_id_info = Db::name('Admin')->field('id,name,thumb')->where('id','in',$val['flow_uids'])->select()->toArray();						
			foreach ($user_id_info as $k => &$v) {
				$v['check_time'] = 0;
				$v['content'] = '';
				$v['status'] = 0;			
				$checked = Db::name('FlowRecord')->where(['check_user_id' => $v['id'],'step_id' => $val['id']])->find();
				if($checked){
					$v['check_time'] = date('Y-m-d :H:i', $checked['check_time']);
					$v['content'] = $checked['content'];
					$v['status'] = $checked['status'];	
				}
			}
			
			$check_list = Db::name('FlowRecord')
						->field('f.*,a.name,a.thumb')
						->alias('f')
						->join('Admin a', 'a.id = f.check_user_id', 'left')
						->where(['f.step_id' => $val['id']])->select()->toArray();
			foreach ($check_list as $kk => &$vv) {		
				$vv['check_time_str'] = date('Y-m-d :H:i', $vv['check_time']);
			}
			
			$val['user_id_info'] = $user_id_info;
			$val['check_list'] = $check_list;
        }
        return to_assign(0, '', $flows);
    }


    //删除报销附件
    public function del_expense_interfix()
    {
        $id = get_params("id");
        $admin_id = Db::name('ExpenseInterfix')->where('id', $id)->value('admin_id');
        if ($admin_id == $this->uid) {
            if (Db::name('ExpenseInterfix')->where('id', $id)->delete() !== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(1, "删除失败");
            }
        } else {
            return to_assign(1, "您没权限删除该报销数据");
        }
    }
	
    //删除消息附件
    public function del_message_interfix()
    {
        $id = get_params("id");
        $detail = Db::name('MessageFileInterfix')->where('id', $id)->find();
        if ($detail['admin_id'] == $this->uid) {
            if (Db::name('MessageFileInterfix')->where('id', $id)->delete() !== false) {
				$data = Db::name('MessageFileInterfix')->where('mid', $detail['mid'])->column('file_id');
                return to_assign(0, "删除成功",$data);
            } else {
                return to_assign(1, "删除失败");
            }
        } else {
            return to_assign(1, "您没权限删除该消息附件");
        }

    }

    //获取访问记录
    public function get_view_data()
    {
        $param = get_params();
        $first_time = time();
        $second_time = $first_time - 86400;
        $three_time = $first_time - 86400 * 365;
        $begin_first = strtotime(date('Y-m-d', $first_time) . " 00:00:00");
        $end_first = strtotime(date('Y-m-d', $first_time) . " 23:59:59");
        $begin_second = strtotime(date('Y-m-d', $second_time) . " 00:00:00");
        $end_second = strtotime(date('Y-m-d', $second_time) . " 23:59:59");
        $begin_three = strtotime(date('Y-m-d', $three_time) . " 00:00:00");
        $data_first = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_first,$end_first")->select();
        $data_second = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_second,$end_second")->select();
        $data_three = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_three,$end_first")->select();
        return to_assign(0, '', ['data_first' => hour_document($data_first), 'data_second' => hour_document($data_second), 'data_three' => date_document($data_three)]);
    }
}
