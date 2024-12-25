{extend name="../../base/view/common/base" /}
<!-- 主体 -->
{block name="body"}

<div class="p-page">
	<div class="gg-form-bar border-t border-x" style="padding-bottom:12px">
		<button class="layui-btn layui-btn-sm tool-add" type="button" data-href="/<module>/<controller>/add">+ 添加<name></button>
    </div>
	<table class="layui-hide" id="table_<model>" lay-filter="table_<model>"></table>
</div>
{/block}
<!-- /主体 -->

<!-- 脚本 -->
{block name="script"}
<script>
	const moduleInit = ['tool'];
	function gouguInit() {
		var treeTable = layui.treeTable, tool = layui.tool;
		
		layui.pageTable = treeTable.render({
			elem: "#table_<model>"
			,title: '<name>列表'
			,url: "/<module>/<controller>/datalist"
			,tree: {
				customName: {name:'title'},
				view: {showIcon:false},
				data: {},
				async: {},
				callback: {}
			}
			,done:function(){
				treeTable.expandAll('table_<model>', true); // 打开全部节点
			}
			,cols: [[
				{field:'id',width:80, title: 'ID号', align:'center'}
				,{field: 'sort', title: '排序',align:'center', width:80}
				,{field:'title', width:240, title: '分类名称'}
				,{field:'pid', title: '父级ID', width:80, align:'center'}
				,{field:'desc', title: '描述', }
				,{field:'status', title: '状态',width:80,align:'center',templet: function(d){
					var html1='<span class="green">正常</span>';
					var html2='<span class="yellow">禁用</span>';
					if(d.status==1){
						return html1;
					}
					else{
						return html2;
					}
				}}
				,{width:192,title: '操作', align:'center',ignoreExport:true,templet: function(d){
						var html='';
						var btn='<button class="layui-btn layui-btn-xs" lay-event="add">添加子分类</button>';
						var btn1='<button class="layui-btn layui-bg-blue layui-btn-xs" lay-event="edit">编辑</button>';
						var btn2='<a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="disable">禁用</a>';
						var btn3='<a class="layui-btn layui-btn-xs" lay-event="open">启用</a>';
						var btn4='<button class="layui-btn layui-bg-red layui-btn-xs" lay-event="del">删除</button>';
						if(d.status==1){
							html = '<div class="layui-btn-group">'+btn+btn1+btn2+btn4+'</div>';
						}
						else{
							html = '<div class="layui-btn-group">'+btn+btn1+btn3+btn4+'</div>';
						}
						return html;
					}
				}
			]]
			,page:false
		});
		
		//操作按钮
		treeTable.on('tool(table_<model>)', function (obj) {
			if (obj.event === 'add') {
				tool.side("/<module>/<controller>/add?pid="+obj.data.id);
				return;
			}
			if (obj.event === 'edit') {
				tool.side("/<module>/<controller>/add?id="+obj.data.id);
				return;
			}
			if(obj.event === 'disable'){
				layer.confirm('确定要禁用该记录吗?', {icon: 3, title:'提示'}, function(index){
					let callback = function (e) {
						layer.msg(e.msg);
						if (e.code == 0) {
							layui.pageTable.reload();
						}
					}
					tool.post("/<module>/<controller>/set", {id:obj.data.id,status: 0}, callback);
					layer.close(index);
				});
			}
			if(obj.event === 'open'){
				layer.confirm('确定要启用该记录吗?', {icon: 3, title:'提示'}, function(index){
					let callback = function (e) {
						layer.msg(e.msg);
						if (e.code == 0) {
							layui.pageTable.reload();
						}
					}
					tool.post("/<module>/<controller>/set", {id:obj.data.id,status:1}, callback);
					layer.close(index);
				});
			}
			if (obj.event === 'del') {
				layer.confirm('确定要删除吗?', { icon: 3, title: '提示' }, function (index) {
					let callback = function (e) {
						layer.msg(e.msg);
						if (e.code == 0) {
							obj.del();
						}
					}
					tool.delete("/<module>/<controller>/del", { id: obj.data.id }, callback);
					layer.close(index);
				});
			}
		});
	}
</script>
{/block}
<!-- /脚本 -->