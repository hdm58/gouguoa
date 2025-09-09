{extend name="../../base/view/common/base" /}
<!-- 主体 -->
{block name="body"}
<form class="layui-form p-page">
	<h3 class="pb-2"><name></h3>
	<table class="layui-table layui-table-form">
		<tr>
			<td class="layui-td-gray"><name>名称<font>*</font></td>
			<td><input type="text" name="title" placeholder="请输入<name>名称" lay-verify="required" lay-reqText="请输入<name>名称" class="layui-input" value="{$detail.title|default=''}"></td>
			<td class="layui-td-gray">父级分类<font>*</font></td>
			<td>
				{empty name="$detail"}
				<select name="pid" lay-verify="required" lay-reqText="请选择父级分类">
					<option value="0">作为顶级分类</option>
					{volist name=":set_recursion(get_base_data('<model>'))" id="v"}
					<option value="{$v.id}">{$v.title}</option>
					{/volist}
				</select>
				{else/}
				<select name="pid" lay-verify="required" lay-reqText="请选择父级分类">
					<option value="0">作为顶级分类</option>
					{volist name=":set_recursion(get_base_data('<model>'))" id="v"}
					<option value="{$v.id}" {eq name="$detail.pid" value="$v.id" }selected="" {/eq}>{$v.title}</option>
					{/volist}
				</select>
				{/empty}
			</td>
			<td class="layui-td-gray">排序</td>
			<td><input type="text" name="sort" placeholder="请输入排序，数字" class="layui-input" value="{$detail.sort|default=''}"></td>
		</tr>
		<tr>
			<td class="layui-td-gray"><name>描述</td>
			<td colspan="5"><textarea name="desc" placeholder="请输入<name>描述，可空" class="layui-textarea">{$detail.desc|default=''}</textarea></td>
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
					setTimeout(function () {
						parent.location.reload();
					}, 1000);
				}
			}
			let clickbtn = $(this);
			tool.post("/<module>/<controller>/add", data.field, callback,clickbtn);
			return false;
		});
	}
</script>
{/block}
<!-- /脚本 -->