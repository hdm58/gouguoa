{extend name="../../base/view/common/base" /}
<!-- 主体 -->
{block name="body"}
<form class="layui-form p-page">
	<h3 class="pb-3"><name></h3>
	<table class="layui-table layui-table-form">
		<tr>
			<td class="layui-td-gray"><name>名称<font>*</font></td>
			<td><input type="text" name="title" class="layui-input" placeholder="请输入<name>名称" lay-verify="required" lay-reqText="请输入<name>名称" value="{$detail.title|default=''}"></td>
			<td class="layui-td-gray">排序</td>
			<td><input type="text" name="sort" placeholder="请输入排序，数字" class="layui-input" value="{$detail.sort|default=''}"></td>
		</tr>
		<tr>
			<td class="layui-td-gray"><name>描述</td>
			<td colspan="3"><textarea name="desc" placeholder="请输入<name>描述，可空" class="layui-textarea">{$detail.desc|default=''}</textarea></td>
		</tr>
	</table>
	<div class="pt-4">
		<input type="hidden" name="id" value="{$detail.id|default=0}"/>
		<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="webform">立即提交</button>
		<button type="reset" class="layui-btn layui-btn-primary">重置</button>
	</div>
</form>
{/block}
<!-- /主体 -->

<!-- 脚本 -->
{block name="script"}
<script>
	var moduleInit = ['tool'];

	function gouguInit() {
		var form = layui.form, tool = layui.tool;
		//监听提交
		form.on('submit(webform)', function (data) {
			let callback = function (e) {
				layer.msg(e.msg);
				if (e.code == 0) {
					tool.sideClose(1000);
				}
			}
			tool.post("/<module>/<controller>/add", data.field, callback);
			return false;
		});
	}
</script>
{/block}
<!-- /脚本 -->