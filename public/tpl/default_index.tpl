<?php
namespace {$app}\{$module}{layer};

class Index{$suffix}
{
    public function index()
    {
        return '<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>系统提示</title>	
	<style type="text/css">
		html,body {width: 100%;height: 100%;background: #f7f7f7;margin: 0;padding: 0;border: 0;}
		div,p {margin: 0;padding: 0;border: 0;}
		.container {width: 100%;height: 100%;position: fixed;top: 0;left: 0;z-index: 999;overflow: hidden;}
		.info {width: 480px;height: 360px;position: absolute;top: 50%;left: 50%;margin-top: -200px;margin-left: -240px;text-align:center;}
		.info-status{width: 500px; display:none;justify-content: space-between;align-items: center;flex-wrap: nowrap;}
		.info-status div{width:120px; height:180px; line-height:180px; font-size:160px; font-weight:200; color:#F35F37}
		.info-status div.face{font-size:60px; border:9px solid #F35F37; width:120px; height:120px; line-height:118px; background-color:#fff; border-radius:50%;}
		.info-tips{font-size:20px;color:#F35F37; padding-top:32px; font-weight:600;}
		.footer {position: absolute;font-size: 12px;bottom: 28px;text-align: center;width: 100%;color: #969696;}
		.info-status.code-500{display: -webkit-flex;display: flex;flex-direction: row;}
	</style>
</head>
<body>
	<div class="container">
		<div class="info">
			<div class="info-status code-500" title="出错啦"><div>5</div><div class="face">😔</div><div class="face">😔</div></div>
			<div class="info-tips">哎呀！出错啦，请开启debug模式调试，<a href="https://blog.gougucms.com/home/book/detail/bid/3/id/77.html" target="_blank">开启debug</a></div>
		</div>
		<div class="footer">
			Copyright © 勾股OA ，Powered by GouguOPEN
		</div>
	</div>
</body>
</html>';
    }
}
