mbui.define(['layer'], function (exports) {
	function bytesToSize(bytes) {
		const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
		if (bytes === 0) return '0 Bytes';
		const i = Math.floor(Math.log(bytes) / Math.log(1024));
		return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
	}
	var layer = mbui.layer;
	var fileUpload = function () {
		this.config = {
			uploadBtn: "#uploadBtn",
			uploadBox: "#uploadBox",
			url: "/api/index/upload",
			accept: "application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,.csv,image/*,text/plain,video/*,audio/*,application/x-zip-compressed",
			onlyImage: false,
			template:function(data){
				let exts = ['jpg', 'png', 'gif', 'jpeg'];
				let filesize = bytesToSize(data.filesize);
				let fileshow='<div class="mbui-file-icon"><i class="iconfont icon-weizhigeshi"></i></div>';
				if(exts.includes(data.fileext)){
					fileshow='<div class="mbui-file-icon file-img"><img src="'+data.filepath+'" alt="'+data.name+'"></div>';
				}
				return `<div class="mbui-file-div" data-id="${data.id}">
							${fileshow}
							<div class="mbui-file-info">
								<div class="mbui-file-name line-limit-1">${data.name}</div>
								<div class="mbui-file-size">${filesize}，刚刚</div>
							</div>
							<div class="mbui-file-del"><i class="iconfont icon-cuowukongxin"></i></div>
						</div>`;
			},
			ajaxUpload:null
		};
		this.loaded = 0;
	};
	// 初始化
	fileUpload.prototype.init = function (options) {
		var that = this,timestamp = Date.now();
		$.extend(true, that.config, options);
		if(that.config.onlyImage){
			that.config.accept = "image/jpeg,image/png,image/bmp";
		}
		var uploadBtn = $(that.config.uploadBtn),uploadBox = $(that.config.uploadBox),fileInput = 'fileInput_'+timestamp;		
		uploadBox.append('<input id="'+fileInput+'" type="file" multiple="" accept="'+that.config.accept+'" style="display:none;">');
		
		//点击上传
		uploadBtn.click(function() {
			let newtimestamp = Date.now();
			$('#'+fileInput).replaceWith($('<input type="file" id="'+fileInput+'" style="display:none;" multiple accept="'+that.config.accept+'">'));
			if(that.loaded == 0){
				$('#'+fileInput).click();
			}			
		});
		
		//赋值到form表单
		function fileValue(){
			let file_array = [];
			uploadBox.find('.mbui-file-div').each(function () {
				let $id = $(this).data('id');
				file_array.push($id);
			});		
			uploadBox.find('[data-type="file"]').val(file_array.join(','));
		}
		
		//删除已上传
		uploadBox.on('click','.mbui-file-del',function() {
			$(this).parent().remove();
			fileValue();
		});
		
		//附件上传
		uploadBox.on('change','#'+fileInput,function() {
			var fileInput = $(this)[0];
			var file = fileInput.files[0];
			if (file) {
			  var formData = new FormData();
			  formData.append('file', file);      
			  // 发送文件到服务器
			  $.ajax({
				url: that.config.url,
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				beforeSend: function () {
				// 显示加载按钮
					that.loaded = 1;
					layer.loading('文件上传中...');
				},
				complete:function(){
					that.loaded = 0;
					setTimeout(function(){
						layer.closeAll();
					},500)					
				},
				success: function(res) {
					if(res.code==0){
						if(typeof(that.config.ajaxUpload) == "undefined" || that.config.ajaxUpload==null){
							var listItem = that.config.template(res.data);
							uploadBox.append(listItem);
						}
						else{
							 that.config.ajaxUpload(res);
						}
						fileValue();
					}
				},
				error: function(xhr, status, error) {
				  console.log('文件上传失败');
				  console.log('错误信息：', error);
				}
			  });
			} else {
			  console.log('请选择要上传的文件');
			}
		  });
	};

	//查看图片
	$('body').on('click','.mbui-file-icon',function(e) {
		e.preventDefault();
		let img = $(this).find('img');
		let href = img.attr('src');
		if (href!=undefined && href!='') {					
			layer.photo(href);
		}
		else {
			layer.msg('企业微信不支持下载文件附件<br>请到PC端下载查看');
		}
	});
	// 导出loadData模块
	exports('fileupload', function (options) {
		var fileupload = new fileUpload();
		fileupload.init(options);
	});
});