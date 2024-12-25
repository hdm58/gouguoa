{extend name="../../base/view/common/base" /}
<!-- 主体 -->
{block name="body"}
<div class="p-page">
	<h3 class="pb-3"><name>详情</h3>
	<table class="layui-table layui-table-form">
		<tr>
			<td class="layui-td-gray"><name>名称</td>
			<td>{$detail.title|default=''}"></td>
			<td class="layui-td-gray">父级分类</td>
			<td>
				{volist name=":set_recursion(get_<model>())" id="v"}
					{eq name="$detail.pid" value="$v.id" }{$v.title}{/eq}
				{/volist}
			</td>
			<td class="layui-td-gray">排序</td>
			<td>{$detail.sort|default=0}</td>
		</tr>
		<tr>
			<td class="layui-td-gray"><name>描述</td>
			<td colspan="5">{$detail.desc|default=''}</td>
		</tr>
	</table>
</div>
{/block}
<!-- /主体 -->