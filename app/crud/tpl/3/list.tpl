{extend name="../../base/view/common/base" /}
<!-- 主体 -->
{block name="body"}

<div class="p-page">
	<form class="layui-form gg-form-bar border-t border-x" lay-filter="barsearchform">
		<div class="layui-input-inline" style="width:300px">
			<input type="text" name="keywords" placeholder="关键字" class="layui-input" autocomplete="off" />
		</div>
		<div class="layui-input-inline" style="width:150px">
			<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="table-search"><i class="layui-icon layui-icon-search mr-1"></i>搜索</button>
			<button type="reset" class="layui-btn layui-btn-reset" lay-filter="table-reset">清空</button>
		</div>
	</form>
	<table class="layui-hide" id="table_<model>" lay-filter="table_<model>"></table>
</div>

<script type="text/html" id="toolbarDemo">
  <div class="layui-btn-group">
  	<button class="layui-btn layui-btn-sm tool-add" type="button" data-href="/<module>/<controller>/add">+ 添加<name></button>
  </div>
</script>

{/block}
<!-- /主体 -->

<!-- 脚本 -->
{block name="script"}
<script>
	const moduleInit = ['tool','tablePlus'];
	function gouguInit() {
		var table = layui.tablePlus, tool = layui.tool;
		
		layui.pageTable = table.render({
			elem: "#table_<model>"
			,title: '<name>列表'
			,toolbar: "#toolbarDemo"
			,url: "/<module>/<controller>/datalist"
			,page: true
			,limit: 20
			,cellMinWidth: 80
			,height: 'full-112'
			,cols: [[
				{field:'id',width:80, title: 'ID号', align:'center'}
				,{field:'title',title: '名称'}
				,{field:'admin_name',title: '创建人',width:90,align:'center'}
				,{field:'create_time', title: '创建时间',width:150,align:'center'}
				,{width:120,fixed:'right',title: '操作', align:'center',ignoreExport:true,templet: function(d){
					var html='';
					var btn1='<a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="view">详细</a>';
					var btn2='<a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>';
					var btn3='<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>';
					html = '<div class="layui-btn-group">'+btn1+btn2+btn3+'</div>';
					return html;
				}}
			]]
		});
			
		table.on('tool(table_<model>)',function (obj) {
			var checkStatus = table.checkStatus(obj.config.id); //获取选中行状态
			var data = obj.data;
			if (obj.event === 'view') {
				tool.side("/<module>/<controller>/view?id="+data.id);
				return;
			}
			if (obj.event === 'edit') {
				tool.side("/<module>/<controller>/add?id="+data.id);
				return;
			}
			if (obj.event === 'del') {
				layer.confirm('确定要删除该内容吗?', { icon: 3, title: '提示' }, function (index) {
					let callback = function (e) {
						layer.msg(e.msg);
						if (e.code == 0) {
							obj.del();
						}
					}
					tool.delete("/<module>/<controller>/del", { id: data.id }, callback);
					layer.close(index);
				});
				return;
			}
		});
	}
</script>
{/block}
<!-- /脚本 -->