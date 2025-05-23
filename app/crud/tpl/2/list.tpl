{extend name="../../base/view/common/base" /}
<!-- 主体 -->
{block name="body"}

<div class="p-page">
	<table class="layui-hide" id="table_<model>" lay-filter="table_<model>"></table>
</div>

<script type="text/html" id="toolbarDemo">
  <div class="layui-btn-group">
  	<button class="layui-btn layui-btn-sm add-new" type="button">+ 添加<name></button>
  </div>
</script>

{/block}
<!-- /主体 -->

<!-- 脚本 -->
{block name="script"}
<script>
	const moduleInit = ['tool'];
	function gouguInit() {
		var table = layui.table, tool = layui.tool;
		
		layui.pageTable = table.render({
			elem: "#table_<model>"
			,title: '<name>列表'
			,toolbar: '#toolbarDemo'
			,url: "/<module>/<controller>/datalist"
			,page: false
			,limit: 999
			,cellMinWidth: 80
			,cols: [[
				{field:'id',width:80, title: 'ID号', align:'center'}
				,{field:'title',title: '类型名称'}
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
				,{width:100,title: '操作', align:'center',ignoreExport:true,templet: function(d){
					var html='';
					var btn='<a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">编辑</a>';
					var btn1='<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="disable">禁用</a>';
					var btn2='<a class="layui-btn layui-btn-xs" lay-event="open">启用</a>';
					if(d.status==1){
						html = '<div class="layui-btn-group">'+btn+btn1+'</div>';
					}
					else{
						html = '<div class="layui-btn-group">'+btn+btn2+'</div>';
					}
					return html;
				}}
			]]
		});
			
		table.on('tool(table_<model>)',function (obj) {
			if(obj.event === 'edit'){					
				add_cate(obj.data.id,obj.data.title);
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
		});
		
		$('body').on('click','.add-new',function(){
			add_cate(0,'');	
		});
		
		function add_cate(id,val){
			var title = '新增类型';
			if(id>0){
				title = '编辑类型';
			}
			layer.prompt({
				title: title,
				value: val,
				yes: function(index, layero) {
					// 获取文本框输入的值
					var value = layero.find(".layui-layer-input").val();
					if (value) {
						let callback = function (e) {
							layer.msg(e.msg);
							if (e.code == 0) {
								layui.pageTable.reload();				
							}
						}
						tool.post("/<module>/<controller>/add", {id: id,title: value}, callback);
						layer.close(index);
					} else {
						layer.msg('请填写类型名称');
					}
				}
			})
		}
	}
</script>
{/block}
<!-- /脚本 -->