layui.define(['tool'], function (exports) {
	const tool = layui.tool;
	const obj = {
		load: function (box,name, action_id) {
			let page=1;
			let callback = function (res) {
				if (res.code == 0 && res.data.length > 0) {
					let itemLog = '',log_time='';
					$.each(res.data, function (index, item) {
						let detail = "";
						if(log_time != item.create_time){
							if(log_time==''){
								itemLog+='<dl><dt><span class="date-second-point"></span>'+item.create_time+'</dt>'
							}
							else{
								itemLog+='</dl><dl><dt><span class="date-second-point"></span>'+item.create_time+'</dt>'
							}
							log_time = 	item.create_time;			
						}
						detail= `
							<span class="log-content gray">将<strong>${item.field_name}</strong>从 <span class="green">${item.old_content}</span> ${item.action}为 <span class="blue">${item.new_content}</span><span class="ml-4 gray" title="${item.create_time}">${item.times}</span></span>
						`;
						itemLog+= `
							<dd><img src="${item.thumb}" class="log-thumb" /><span class="log-name">${item.admin_name}</span>${detail}</dd>
						`;
					});
					itemLog+='</dl>';
					if(res.data.length>19){
						itemLog+='<div class="py-3 log-more"><button class="layui-btn layui-btn-normal layui-btn-sm" type="button">查看更多</button></div>';
					}
					$("#"+box).html(itemLog);
				}
				else{
					if(page ==1){
						$("#"+box).html('<div class="layui-data-none">暂无记录</div>');
					}
				}
			}
			tool.get("/api/index/load_log", {name:name,action_id:action_id,page:page}, callback);
			$("#"+box).on('click','.log-more',function(){
				page++;
				tool.get("/api/index/load_log", {name:name,action_id:action_id,page:page}, callback);
			});
		}
	};
	exports('oaLog', obj);
});  