mbui.define(['tool','layer','userPicker','loadData'], function (exports) {
	const layer = mbui.layer, tool = mbui.tool,userPicker = mbui.userPicker,loadData = mbui.loadData;
	const obj = {
		comment_box:'comment_box',
		module:'module',
		topic_id:'topic_id',
		load: function (comment_box,module,topic_id) {
			let that = this;
			that.comment_box=comment_box;
			that.module=module;
			that.topic_id=topic_id;
			loadData({
				url:'/api/comment/datalist',
				elem:'#'+comment_box,
				where: {topic_id: topic_id, module: module},
				limit:100,
				template:function(data){
					let to_names = '', ops = '' ,ptext='';
						if (data.to_names !='') {
							to_names = `<span class="text-blue">@${data.to_names}</span>`;
						}
						if (data.admin_id == login_admin) {
							ops = `<span class="mbui-btn mbui-btn-primary xs" data-event="edit" data-id="${data.id}">编辑</span><span class="mbui-btn mbui-btn-primary xs" data-event="del" data-id="${data.id}">删除</span>`;
						}
						else{
							ops = `<span class="mbui-btn mbui-btn-primary xs" data-event="replay" data-id="${data.id}" data-uid="${data.admin_id}" data-unames="${data.name}">回复</span>`;
						}
						if(data.pid>0){
							ptext=`<div style="padding-bottom:8px;"><fieldset><legend>回复『${data.padmin}』${data.ptimes}的评论</legend>${data.pcontent}</fieldset></div>`;
						}
							
					let listItem = `
							<div id="comment_${data.id}" class="mbui-comment-box border-bottom" data-content="${data.content}">
								<div class="comment-avatar" title="${data.name}">
									<img class="comment-image" src="${data.thumb}">
								</div>
								<div class="comment-body">
									<div class="comment-meta">
										<span class="comment-name">${data.name}</span><span title="${data.create_time}">${data.create_times}${data.update_times}</span>
									</div>
									<div class="comment-content">${to_names} ${data.content}</div>
									${ptext}
									<div class="comment-btn mbui-btn-group">${ops}</div>
								</div>
							</div>
							`;
					return listItem;
				}	
			});
		},
		add: function (topic_id, module,content,to_uids,pid) {
			let that = this;
			let callback = function (res) {
				layer.closeAll();
				that.load(that.comment_box,that.module,that.topic_id);
			}
			if (content == '') {
				layer.msg('请完善评论内容');
				return false;
			}
			let postData = { module: module,topic_id: topic_id, content: content, to_uids: to_uids, pid: pid};
			tool.post("/api/comment/add", postData, callback);
		},
		edit: function (id,content) {
			let that = this;
			let callback = function (res) {
				layer.closeAll();
				that.load(that.comment_box,that.module,that.topic_id);
			}
			if (content == '') {
				layer.msg('请完善评论内容');
				return false;
			}
			let postData = {id: id, content: content};
			tool.post("/api/comment/add", postData, callback);
		},
		del: function (id) {
			let that = this;
			layer.confirm('确定删除该评论吗？', function (index) {
				let callback = function (e) {
					layer.msg(e.msg);
					if (e.code == 0) {
						that.load(that.comment_box,that.module,that.topic_id);
					}
				}
				tool.delete("/api/comment/del", { id: id }, callback);
				layer.close(index);
			});
		},
		//文本
		textarea: function (id, topic_id, module, content, to_uids, to_unames, pid) {
			let that = this;
			let usersInput='';
			if(id==0){
				usersInput='<div class="mbui-input-wrap" style="margin-bottom:5px;"><div class="mbui-input-prefix"><i class="mbui-icon mbui-icon-at"></i></div><input type="text" placeholder="@ 要提醒的员工" value="'+to_unames+'" readonly class="mbui-input picker-admin" data-type="2" /><input type="hidden" id="to_uids" value="'+to_uids+'" /></div>';
			}
			
			layer.open({
				type:5,
				title: '请输入评论内容',
				content: '<div style="padding:0 6px 6px;">'+usersInput+'<textarea class="mbui-textarea" id="editTextarea" style="width: 100%; height: 100px;">'+content+'</textarea></div>',
				btn:['确定','取消'],
				yes:function(index){
					let content = $("#editTextarea").val();
					if (content != '') {
						if(id>0){
							that.edit(id, content);
						}
						else{
							let to_uids = $("#to_uids").val();
							that.add(topic_id, module, content, to_uids, pid);
						}
					} else {
						layer.msg('请输入评论内容');
					}
				}
			});
		}
	};
	exports('oaComment', obj);
});  