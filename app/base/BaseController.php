<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\base;

use think\App;
use think\exception\HttpResponseException;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->module = strtolower(app('http')->getName());
        $this->controller = strtolower($this->request->controller());
        $this->action = strtolower($this->request->action());
        $this->uid = 0;
        // 控制器初始化
        $this->initialize();
    }
    // 初始化
    protected function initialize()
    {
        // 检测权限
        $this->checkLogin();
        $this->param = $this->request->param();
    }

    /**
     *验证用户登录
     */
    protected function checkLogin()
    {
        if ($this->controller !== 'login' && $this->controller !== 'captcha') {
            $session_admin = get_config('app.session_admin');
            if (!Session::has($session_admin)) {
                if ($this->request->isAjax()) {
                    return to_assign(404, '请先登录');
                } else {
                    redirect('/home/login/index.html')->send();
                    exit;
                }
            } else {
                $this->uid = Session::get($session_admin)['id'];
                View::assign('login_user', $this->uid);
                // 验证用户访问权限
                if (($this->module == 'api') || ($this->module == 'home' && $this->controller == 'index')) {
					return true;
				}
				else{
					$reg_pwd = Db::name('Admin')->where(['id' => $this->uid])->value('reg_pwd');
					if($reg_pwd!==''){
						redirect('/api/index/edit_password.html')->send();
						exit;
					}
                    if (!$this->checkAuth()) {
                        if ($this->request->isAjax()) {
                            return to_assign(202, '你没有权限,请联系管理员或者人事部');
                        } else {
                            echo '<div style="text-align:center;color:red;margin-top:20%;">你没有权限,请联系管理员或者人事部</div>';exit;
                        }
                    }
                }
            }
        }
    }

    /**
     * 验证用户访问权限
     * @DateTime 2020-12-21
     * @param    string $controller 当前访问控制器
     * @param    string $action 当前访问方法
     * @return   [type]
     */
    protected function checkAuth()
    {
        //Cache::delete('RulesSrc' . $uid);
        $uid = $this->uid;
        if (!Cache::get('RulesSrc' . $uid) || !Cache::get('RulesSrc0')) {
            //用户所在权限组及所拥有的权限
            // 执行查询
            $groups = [];
            $position_id = Db::name('Admin')->where('id', $uid)->value('position_id');
            $groups = Db::name('PositionGroup')
                ->alias('a')
                ->join("AdminGroup g", "a.group_id=g.id", 'LEFT')
                ->where([['a.pid', '=', $position_id], ['g.status', '=', 1]])
                ->select()
                ->toArray();
            //保存用户所属用户组设置的所有权限规则id
            $ids = [];
            foreach ($groups as $g) {
                $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
            }
            $ids = array_unique($ids);
            //读取所有权限规则
            $rules_all = Db::name('AdminRule')->field('src')->select()->toArray();
            //读取用户组所有权限规则
            $rules = Db::name('AdminRule')->where('id', 'in', $ids)->field('src')->select()->toArray();
            //循环规则，判断结果。
            $auth_list_all = [];
            $auth_list = [];
            foreach ($rules_all as $rule_all) {
                $auth_list_all[] = strtolower($rule_all['src']);
            }
            foreach ($rules as $rule) {
                $auth_list[] = strtolower($rule['src']);
            }
            //规则列表结果保存到Cache
            Cache::tag('adminRules')->set('RulesSrc0', $auth_list_all, 36000);
            Cache::tag('adminRules')->set('RulesSrc' . $uid, $auth_list, 36000);
        } else {
            $auth_list_all = Cache::get('RulesSrc0');
            $auth_list = Cache::get('RulesSrc' . $uid);
        }
        $pathUrl = $this->module . '/' . $this->controller . '/' . $this->action;
        if (!in_array($pathUrl, $auth_list)) {
            return false;
        } else {
            return true;
        }
    }

    //
    // 以下为新增，为了使用旧版TP的 success error redirect 跳转  start
    //

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function success($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url);
        }

        $result = [
            'code' => 0,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ($type == 'html') {
            $response = view($this->app->config->get('app.dispatch_success_tmpl'), $result);
        } else if ($type == 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function error($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
    {
        if (is_null($url)) {
            $url = $this->request->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : $this->app->route->buildUrl($url);
        }

        $result = [
            'code' => 1,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ($type == 'html') {
            $response = view($this->app->config->get('app.dispatch_error_tmpl'), $result);
        } else if ($type == 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }
    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        return $this->request->isJson() || $this->request->isAjax() ? 'json' : 'html';
    }

    //
    // 以上为新增，为了使用旧版的 success error redirect 跳转  end
    //
}
