mbui.define(['form','layer','userPicker','fileupload'], function (exports) {
	const layer = mbui.layer;
	const form = mbui.form;
	const tool = mbui.tool;
	const fileupload = mbui.fileupload;
	const opts={
		"checkBox":"checkBox",//审核容器id
		"check_name": "",//审核类型标识
		"check_btn":1,//是否显示提交审核按钮
		"checking_btn":'',//待审核状态下添加的按钮
		"checked_btn":'',//审核通过后添加的按钮
		"check_ok":function(){
			//审核通过的执行函数
		},
		"check_no":function(){
			//审核拒绝的执行函数
		},
		"check_back":function(){
			//撤回的执行函数
		},
		"check_reversed":function(){
			//反确认的执行函数
		}
	};
	const obj = {
		loading:false,
		checkStatus: function (status) {
			statusArray = ['待提交审批','审批中','审批通过','审批不通过','已撤回'];
			return '<span class="check-status-color-'+status+'">'+statusArray[status]+'</span>';
		},
		statusTemplate: function (status) {
			let me = this;
			let tem =`
				<div class="mbui-list">
					<div class="mbui-list-bd">审批状态</div>
					<div class="mbui-list-ft">
						${me.checkStatus(status)}
					</div>
				</div>
			`;
			return tem;
		},
		uidsTemplate: function () {
			let tem =`
				<div class="mbui-form-line">
					<label class="mbui-form-label">审 批 人</label>
					<input type="text" name="check_uames" value="" placeholder="请选择审批人" data-verify="required" data-errortips="请选择审批人" class="mbui-form-input" readonly><input type="text" name="check_uids" value="" readonly style="display:none;">
				</div>
			`;
			return tem;
		},
		flowTemplate: function (data) {
			let flowtype='<option value="" title="">--请选择--</option>';
			if(data && data.length>0){
				for(let i=0;i<data.length;i++){
					flowtype+='<option value="'+data[i].id+'" title="'+data[i].check_type+'">'+data[i].title+'</option>';
				}				
			}
			let tem =`
				<div class="mbui-form-line">
					<label class="mbui-form-label">审批流程</label>
					<div class="mbui-form-select">
						<select name="flow_id" data-verify="required" data-errortips="请选择审批流程">
							${flowtype}
						</select>
					</div>
				</div>
			`;
			return tem;
		},
		copyTemplate: function (detail) {
			let me = this;
			let tem =`
				<div class="mbui-form-line" style="${detail.is_copy==0?'display:none':''}">
					<label class="mbui-form-label">抄 送 人</label>
					<input class="mbui-form-input" name="check_copy_unames" value="" readonly placeholder="请选择" placeholder="请选择审批通过后的抄送人" data-type="2">
					<input type="text" name="check_copy_uids" value="" readonly style="display:none;">
				</div>
			`;
			return tem;		
		},
		btnTemplate: function () {
			let me = this;
			let tem =`
			<div class="padding-16 center">
				<button class="mbui-btn mbui-btn-normal" data-event="submit">提交审批</button>
				<button type="reset" class="mbui-btn mbui-btn-primary">重置</button>
				${me.sets.checking_btn}
			</div>
			`;			
			if(me.sets.check_btn == 1){
				return tem;
			}else{
				return '';
			}			
		},
		recordTemplate: function (record) {
			let me = this;
			let tem ='';
			if(record.length>0){
				tem+='<div class="padding-12 border-top">';
				tem+='<div class="padding-12-b"><span class="f16 mbui-text-gray">审批记录</span></div>';
				tem+='<div class="mbui-steps">';
						for(let l=0;l<record.length;l++){
							tem+='<div class="mbui-steps-item delete-'+record[l].delete_time+'">'+record[l].check_time_str+'<small><span class="text-black">'+record[l].name+'</span><span class="check-status-color-'+(record[l].check_status+1)+'">『'+record[l].status_str+'』</span>了此申请。审批意见：<span class="text-black">'+record[l].content+'。</span></small>';
						
							let file_array= record[l].file_array;	
							if(file_array.length>0){
								tem+='<div style="padding:4px 0; color:#666666">审批附件</div><div class="bg-white"><div class="mbui-file-list" style="padding-bottom:0;">';
								for(let f=0;f<file_array.length;f++){
									tem+=tool.file_item(file_array[f],1);
								}
								tem+='</div></div>';
							}
							tem+='</div>';
						}
					tem+='</div>';
				tem+='</div>';
			}
			return tem;		
		},
		createTemplate: function (flow) {
			let me = this;
			let checkHtml = `
				<form class="mbui-form" id="formCheckBox">
					<div class="mbui-group-title">审批操作</div>
					<div class="bg-white">
						${me.flowTemplate(flow)}
						<div id="checkTR">${me.uidsTemplate()}</div>
						${me.copyTemplate(flow[0])}
					</div>
				</form>
			`;
			return checkHtml;
		},
		//审批待提交模版
		initTemplate: function (detail) {
			let me = this;
			let checkHtml = `
				<form class="mbui-form" id="formCheckBox">
					<div class="mbui-group-title">审批操作</div>
					<div class="bg-white">
						${me.statusTemplate(detail.check_status)}
						${me.recordTemplate(detail.check_record)}
						${me.flowTemplate(detail.flow)}
						<div id="checkTR">${me.uidsTemplate()}</div>
						${me.copyTemplate(detail)}
					</div>
					${me.btnTemplate()}
				</form>
			`;
			let viewHtml = `
				<form class="mbui-form" id="formCheckBox">
					<div class="mbui-group-title">审批操作</div>
					<div class="bg-white">
						${me.statusTemplate(detail.check_status)}
						${me.recordTemplate(detail.check_record)}
					</div>
				</form>
			`;
			if(detail.is_creater==1){
				return checkHtml;
			}
			else{
				return viewHtml;
			}
		},
		//审批中模版
		checkTemplate: function (detail) {
			let me = this,flowHtml='',list = detail.nodes;
			for(var f=0;f<list.length;f++){
				//审批流程
				let checkUser = '',
				iconRight ='<span class="iconfont icon-arrow"></span>',
				iconStatus ='<i class="mbui-icon mbui-icon-time"></i>',
				strStatus ='<div class="check-item-status">待审批</div>',
				check_types= '',
				sortClass ='';
				if(f == list.length-1){
					iconRight ='';
				}
				if(detail.check_step_sort == list[f].sort){
					sortClass ='flow-this';
					iconStatus ='<i class="mbui-icon mbui-icon-time"></i>';
					strStatus ='<div class="check-item-status">当前审批</div>';
					if(list[f].check_uids_info.length>1){
						check_types= ' <span class="mbui-badge mbui-bg-blue">会签</span>';
						if(list[f].check_types==2){
							check_types= ' <span class="mbui-badge mbui-bg-orange">或签</span>';
						}
					}
				}
				if(list[f].check_role == 0 || list[f].check_role == 5){
					checkUser=list[f].check_uids_info[0].name;
				}
				if(list[f].check_role == 1 || list[f].check_role == 2 || list[f].check_role == 3  || list[f].check_role == 4){
					checkUser=list[f].flow_name;
				}	
				if(list[f].check_list.length>0){
					let one=0,two=0,three=0;
					for(var m=0;m<list[f].check_uids_info.length;m++){
						if(list[f].check_uids_info[m].check_status == 2){
							three++
						}
						if(list[f].check_uids_info[m].check_status == 1){
							two++
						}
						if(list[f].check_uids_info[m].check_status == 0){
							one++
						}
					}
					//会签
					if(list[f].check_types==1){
						if(three > 0){
							iconStatus ='<i class="iconfont mbui-icon-close" data-no=""></i>';
							strStatus ='<div class="check-item-status">拒绝</div>';
						}
						else{
							if(one > 0){
								iconStatus ='<i class="iconfont icon-clock"></i>';
								strStatus ='<div class="check-item-status">待审批</div>';
							}
							else{
								iconStatus ='<i class="iconfont icon-kongjian" data-ok=""></i>';
								strStatus ='<div class="check-item-status">通过</div>';	
							}
						}
					}
					//或签
					if(list[f].check_types==2){
						if(two > 0){
							iconStatus ='<i class="iconfont icon-kongjian" data-ok=""></i>';
							strStatus ='<div class="check-item-status">通过</div>';
						}
						else{
							iconStatus ='<i class="iconfont mbui-icon-close" data-no=""></i>';
							strStatus ='<div class="check-item-status">拒绝</div>';
						}
					}
				}
				flowHtml+= '<div class="flow-flexbox check-item flow-flex-row '+sortClass+'" id="flow'+f+'">'+iconStatus+'<div class="check-item-name">'+check_types+checkUser+'</div>'+strStatus+iconRight+'</div>';
			}			
			
			let checkCopy=`<div class="mbui-list" style="${detail.is_copy==0?'display:none':''}">
								<div class="mbui-list-bd">抄 送 人</div>
								<div class="mbui-list-ft">
									${detail.copy_unames}
								</div>
							</div>
							`;
			
			let checkNode = `
							<div class="mbui-form-line">
								<label class="mbui-form-label">审批节点</label>
								<div class="mbui-form-radio">
									<input class="mbui-input-radio" type="radio" name="check_node" id="check_node1" value="1" /><label for="check_node1">审批结束</label>
									<input class="mbui-input-radio" type="radio" name="check_node" id="check_node2" value="2" /><label for="check_node2">下一审批人</label>
								</div>
							</div>
							<div class="mbui-form-line next-admin" style="display:none;">
								<label class="mbui-form-label">下一审批人</label>
								<input class="mbui-form-input picker-admin" type="text" name="check_uname" value="" placeholder="请选择" readonly>
								<input type="hidden" name="check_uids" value="">
							</div>
				`;
				
			let checkContent = `
								<div class="mbui-form-line">
									<label class="mbui-form-label">审批意见</label>
									<textarea rows="3" name="content" class="mbui-form-input" placeholder="请输入审批意见" data-verify="required" data-errortips="请完善审批意见"></textarea>
								</div>
								<div class="mbui-form-line" ${detail.is_file==0?'style="display:none;"':''}>
									<label class="mbui-form-label">审批附件</label>
									<div class="mbui-form-uploader">
										<div class="mbui-uploader-btn-border" id="uploadBtnCheck"><i class="iconfont icon-shangchuan"></i></div>
									</div>
									<div id="uploadBoxCheck" class="mbui-file-list" style="padding:0 12px 2px;">
										<input data-type="file" name="check_files" type="hidden" value="">
									</div>
								</div>
								`;
				
			let btnCheck='<span class="mbui-btn mbui-btn-normal btn-check" data-status="1"><i class="mbui-icon mbui-icon-ok"></i> 通过</span><span class="mbui-btn mbui-btn-danger btn-check" data-status="2"><i class="mbui-icon mbui-icon-close"></i> 拒绝</span>';
			
			if(detail.step.check_role==5){
				btnCheck='<span class="mbui-btn mbui-btn-normal btn-check" data-status="1"><i class="mbui-icon mbui-icon-ok"></i> 通过</span><span class="mbui-btn mbui-btn-danger btn-check" data-status="2"><i class="mbui-icon mbui-icon-close"></i> 退回</span>';
			}
			
			let btnBack='<span class="mbui-btn mbui-btn-primary btn-check" data-status="3"><i class="mbui-icon mbui-icon-reduce-circle"></i> 撤回</span>';
			let btnCheckBack='<span class="mbui-btn mbui-btn-danger btn-check" data-event="check" data-status="4">反确认审核</span>';
			
			let checkHtml = `
				<form class="mbui-form" id="formCheckBox">
					<div class="mbui-group-title">审批操作</div>
					<div class="mbui-list">
						<div class="mbui-list-bd">审批状态</div>
						<div class="mbui-list-ft">
							${me.checkStatus(detail.check_status)}
						</div>
					</div>
					<div class="mbui-list">
						<div class="mbui-list-bd">当前审批人</div>
						<div class="mbui-list-ft">
							${detail.check_unames}
						</div>
					</div>
					${checkCopy}					
					<div class="bg-white">
						<div class="padding-12 border-top">
							<span class="f16 mbui-text-gray">审批流</span>
						</div>
						<div class="bg-gray radius-4" style="padding:4px 8px; margin:0 12px 12px;">
							<div class="flow-flexbox check-items flow-flex-row" id="flowList">
								<div class="flow-flexbox check-item flow-flex-row">
										<i class="iconfont icon-fangkuai2" data-ok=""></i>
										<div class="check-item-name">${detail.admin_name}</div>
										<div class="check-item-status">创建</div>
										<span class="iconfont icon-arrow"></span>
								</div>
								${flowHtml}
							</div>							
						</div>
						${me.recordTemplate(detail.check_record)}
						${detail.is_checker==1 && detail.step.check_role==0?checkNode:''}
						${detail.is_checker==1?checkContent:''}
					</div>
					<div class="padding-16 center">
						<input type="hidden" name="check_role" value="${detail.step.check_role}">
						${detail.is_checker==1?btnCheck:''}
						${detail.is_creater==1 && detail.is_back==1 && (detail.check_status==1 || detail.check_status==3)?btnBack:''}
						${detail.is_reversed == 1 && typeof me.sets.check_reversed ==='function' && detail.check_status==2?btnCheckBack:''}
						${detail.check_status==2?me.sets.checked_btn:''}
					</div>
				</form>
			`;
			return checkHtml;
		},
		//审批撤回模版
		backTemplate: function (detail) {
			let me = this;
			let btnHtml ='';
			if(me.sets.check_btn == 1){
				btnHtml ='<div class="padding-16 center"><button class="mbui-btn mbui-btn-normal" data-event="submit">提交审批</button><button type="reset" class="mbui-btn mbui-btn-primary">重置</button></div>';
			}
			let checkHtml = `
				<form class="mbui-form" id="formCheckBox">
					<div class="mbui-group-title">审批操作</div>
					<div class="bg-white">
						${me.statusTemplate(detail.check_status)}
						${me.recordTemplate(detail.check_record)}
						${me.flowTemplate(detail.flow)}
						<div id="checkTR">${me.uidsTemplate()}</div>
						${me.copyTemplate(detail)}
					</div>
					${me.btnTemplate()}
				</form>
			`;
			let viewHtml = `
				<form class="mbui-form" id="formCheckBox">
					<div class="mbui-group-title">审批操作</div>
					<div class="bg-white">
						${me.statusTemplate(detail.check_status)}
						${me.recordTemplate(detail.check_record)}
					</div>
				</form>
			`;
			if(detail.is_creater==1){
				return checkHtml;
			}
			else{
				return viewHtml;
			}
		},
		submit:function(data,callback){
			tool.post("/api/check/submit_check", data, callback);
		},
		init: function (options) {
			this.sets = $.extend({}, opts, options);
			let me = this;
			let checkBox = $('#'+me.sets.checkBox);
			let action_id = checkBox.data('id');
			let check_status = checkBox.data('status');
			let check_flow_id = checkBox.data('checkflowid');
			if(action_id === undefined || check_status === undefined || check_flow_id === undefined){
				return false;
			}
			//获取审批信息
			$.ajax({
				url: "/api/check/get_flow_nodes",
				type:'get',
				data:{check_name:me.sets.check_name,action_id:action_id,flow_id:check_flow_id},
				success: function (e) {
					if (e.code == 0) {
						if(check_status==0){
							if(action_id==0){
								if(e.data.length>0){
									checkBox.append(me.createTemplate(e.data));
								}
								else{
									let none = '<div class="mbui-group-title">审批操作</div><div class="mbui-form-line">\
										<label class="mbui-form-label">审批流程</label>\
										<input type="text" name="flow_id" value="" placeholder="未设置审批流程，请联系管理员设置" data-verify="required" data-errortips="未设置审批流程，请联系管理员设置" class="mbui-form-input" readonly>\
									</div>';
									checkBox.append(none);
								}
							}
							else{
								checkBox.append(me.initTemplate(e.data));
							}							
						}
						else if(check_status==4){
							checkBox.append(me.backTemplate(e.data));
						}
						else{
							checkBox.append(me.checkTemplate(e.data));
							//附件上传
							fileupload({
								uploadBtn: "uploadBtnCheck",
								uploadBox: "uploadBoxCheck"
							});							
						}
						//提交审批
						form({
							target: '#formCheckBox',
							submit:function(data){
								let callback = function (e) {
									layer.msg(e.msg);
									if (e.code == 0) {
										tool.reload(2000);
									}
								}
								data.action_id = action_id;
								data.check_name = me.sets.check_name;
								tool.post("/api/check/submit_check", data, callback);
								return false;
							}
						});	
					}
				}
			})
			
			$('body').on('change','input[name="check_node"]',function() {  
                var val = this.value;  
                if(val==2){
					$('.next-admin').show();
				}
				else{
					$('.next-admin').hide();
				}
            }); 
			//选择审批流
			$('body').on('change','select[name="flow_id"]', function(data){
				var check_type = $('select[name="flow_id"] option:selected').attr('title');
				var selectedValue = $(this).val();
				if(selectedValue==''){
					$('#checkTR').html(me.uidsTemplate());
					checkBox.find('[name="check_copy_unames"]').val('');
					checkBox.find('[name="check_copy_uids"]').val('');
					return false;
				}
				if(check_type == 1){
					$('#checkTR').html(me.uidsTemplate());
					checkBox.find('[name="check_uames"]').addClass('picker-admin');
					checkBox.find('[name="check_copy_unames"]').addClass('picker-admin');
				}
				$.ajax({
					url: "/api/check/get_flow_users",
					type:'get',
					data:{id:selectedValue},
					success: function (e) {
						if (e.code == 0) {
							var flow_li='',flow_idx=0;
							var flow_data = e.data.flow_data;
							checkBox.find('[name="check_copy_unames"]').addClass('picker-admin');
							if(e.data.copy_uids && e.data.copy_uids !=''){
								checkBox.find('[name="check_copy_unames"]').val(e.data.copy_unames);
								checkBox.find('[name="check_copy_uids"]').val(e.data.copy_uids.split(','));
							}
							if(check_type == 2 || check_type == 3){
								for(var a=0;a<flow_data.length;a++){
									var check_types = '',check_role = '',user_li='';
									if(flow_data[a].check_types==1){
										check_types= ' <span class="mbui-badge mbui-bg-blue">会签</span>';
									}
									if(flow_data[a].check_types==2){
										check_types= ' <span class="mbui-badge mbui-bg-orange">或签</span>';
									}
									
									if(flow_data[a].check_role==1){
										check_role = '当前部门负责人';
									}
									if(flow_data[a].check_role==2){
										check_role = '上级部门负责人';
									}
									if(flow_data[a].check_role==3){
										check_role = '岗位职称:'+flow_data[a].check_position;
									}
									if(flow_data[a].check_role==4){
										check_role = '指定人员';
									}
									if(flow_data[a].check_role==5){
										check_role = flow_data[a].flow_name;
										check_types= ' <span class="mbui-badge mbui-bg-text-green">可回退</span>';
									}
									let check_uids_info=flow_data[a].check_uids_info;
									if(check_uids_info.length>0){
										flow_idx++;
										for(var b=0;b<check_uids_info.length;b++){
											user_li+= '<li style="padding:3px 0;line-height:22px"><img src="'+check_uids_info[b].thumb+'" style="width:22px; height:22px; border-radius:50%; vertical-align: bottom; margin-right:8px;" />'+check_uids_info[b].name+'</li>';
										}
										flow_li+='<div class="mbui-steps-item">第'+flow_idx+'级审批『'+check_role+'』'+check_types+'<small>'+user_li+'</small></div>';
									}
								}
								if(flow_li==''){
									flow_li='<span style="color:#999;">审批流程错误，请联系管理员设置</span>';
								}
								formHtml = '<div class="mbui-form-line">\
												<label class="mbui-form-label">审批流</label>\
												<ul id="flowList" class="mbui-form-input mbui-steps">'+flow_li+'</ul>\
											</div>';
								$('#checkTR').html(formHtml);
							}
						}
					}
				})
			});
			
			//审批操作按钮
			checkBox.on('click','.btn-check', function(data){
				let content=checkBox.find('[name="content"]').val();
				let check_status=$(this).data('status');
				let check_role = checkBox.find('input[name="check_role"]').val();
				
				let check_node=0,check_uids='',check_files='';
				if(check_role == 0 && check_status==1){
					check_node = checkBox.find('input[name="check_node"]:checked').val();
					check_uids = checkBox.find('input[name="check_uids"]').val();
					check_files = checkBox.find('input[name="check_files"]').val();
					if(!check_node){
						layer.msg('请选择下一审批节点');
						return false;
					}
					if(check_node == 2 && check_uids==''){
						layer.msg('请选择下一审批人');
						return false;
					}
				}
				if(check_status ==1 || check_status==2){
					if(content==''){
						layer.msg('请输入审批意见');
						return false;
					}
					let confirmTips='确定通过该审批？';
					if(check_status==2){
						confirmTips='确定拒绝该审批？';
					}				
					layer.confirm(confirmTips, function(index){
						$.ajax({
							url: "/api/check/flow_check",
							type:'post',
							data:{
								action_id:action_id,
								check_name:me.sets.check_name,
								check_flow_id:check_flow_id,
								check_node:check_node,
								check_uids:check_uids,
								check:check_status,
								check_files:check_files,
								content:content
							},
							success: function (e) {
								layer.msg(e.msg);
								if (e.code == 0) {
									if(e.data.check_status==2 && typeof me.sets.check_ok ==='function'){
										me.sets.check_ok(e);
									}
									if(e.data.check_status==3 && typeof me.sets.check_no ==='function'){
										me.sets.check_no(e);
									}
									tool.reload(1000);
								}
							}
						})
						layer.close(index);
					});   
				}
				else if(check_status ==3){			
					layer.prompt('请输入撤回理由', function(val, index){
						if(val==''){
							layer.msg('请输入撤回理由');
							return false;
						}
						$.ajax({
							url: "/api/check/flow_check",
							type:'post',
							data:{
								action_id:action_id,
								check_name:me.sets.check_name,
								check_flow_id:check_flow_id,
								check:check_status,
								content:val
							},
							success: function (e) {
								layer.msg(e.msg);
								if (e.code == 0) {
									if(e.data.check_status==4 && typeof me.sets.check_back ==='function'){
										me.sets.check_back(e);
									}
									tool.reload(1000);
								}
							}
						})
						layer.close(index);
					});
				}
				else if(check_status ==4){			
					layer.prompt('请输入反确认理由', function(val, index){
						if(val==''){
							layer.msg('请输入反确认理由');
							return false;
						}
						$.ajax({
							url: "/api/check/flow_check",
							type:'post',
							data:{
								action_id:action_id,
								check_name:me.sets.check_name,
								check_flow_id:check_flow_id,
								check:check_status,
								content:val
							},
							success: function (e) {
								layer.msg(e.msg);
								if (e.code == 0) {
									if(e.data.check_status==0 && typeof me.sets.check_reversed ==='function'){
										me.sets.check_reversed(e);
									}									
									tool.reload(1000);
								}
							}
						})
						layer.close(index);
					});
				}
				return false;
			});
			
		}
	};
	exports('oaCheck', obj);
});