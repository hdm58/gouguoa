mbui.define(['layer'], function (exports) {
	function bytesToSize(bytes) {
		const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
		if (bytes === 0) return '0 Bytes';
		const i = Math.floor(Math.log(bytes) / Math.log(1024));
		return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
	}
	function generateUniqueID(length) {
	  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	  let id = '';
	  while (id.length < length) {
		id += chars.charAt(Math.floor(Math.random() * chars.length));
	  }
	  return id;
	}
	var layer = mbui.layer;
	var config = {
		uploadBtn: "uploadBtn",
		uploadBox: "uploadBox",
		url: "/api/index/upload",
		accept: "application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,.csv,image/*,text/plain,video/*,audio/*,application/x-zip-compressed",
		onlyImage: false,
		more:true,
		template:function(data){
			let exts = ['jpg', 'png', 'gif', 'jpeg'];
			let filesize = bytesToSize(data.filesize);
			let fileshow='<div class="mbui-file-icon" data-url="'+data.filepath+'" data-name="'+data.name+'"><i class="iconfont icon-weizhigeshi"></i></div>';
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
		ajaxUpload:null,
		loaded:0
	};
	// 初始化
	var fileUpload = function (options) {
		var timestamp = generateUniqueID(10);
		var settings = $.extend({}, config, options);
		//$.extend(true, that.config, options);
		if(settings.onlyImage){
			settings.accept = "image/jpeg,image/png,image/bmp";
		}
		var uploadBtn = $('#'+settings.uploadBtn),uploadBox = $('#'+settings.uploadBox),fileInput = 'fileInput_'+timestamp;		
		uploadBox.append('<input id="'+fileInput+'" type="file" multiple="" accept="'+settings.accept+'" style="display:none;">');
		
		//点击上传
		uploadBtn.click(function() {
			let newtimestamp = Date.now();
			$('#'+fileInput).replaceWith($('<input type="file" id="'+fileInput+'" style="display:none;" multiple="" accept="'+settings.accept+'">'));
			if(settings.loaded == 0){
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
			var files = fileInput.files;
			console.log(files);
			//return false;
			if (files) {
				// 将文件列表转为数组
				const filesArray = Array.from(files); 
				// 使用递归方式逐个上传到服务器
				function uploadNextFile(index) {
					if (index >= filesArray.length) {
						settings.loaded = 0;
						layer.closeAll();
						return;
					}
					const file = filesArray[index];
					const formData = new FormData();
					formData.append('file', file);
					$.ajax({
						url: settings.url,
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						beforeSend: function () {
						// 显示加载按钮
							settings.loaded = 1;
							layer.loading('文件上传中...');
						},
						complete:function(){
							uploadNextFile(index + 1); // 成功后上传下一个
						},
						success: function(res) {
							if(res.code==0){
								if(typeof(settings.ajaxUpload) == "undefined" || settings.ajaxUpload==null){
									var listItem = settings.template(res.data);
									console.log(index);
									if(settings.more==false){
										uploadBox.find('.mbui-file-div').remove();
									}
									uploadBox.append(listItem);
								}
								else{
									 settings.ajaxUpload(res);
								}
								fileValue();
							}
						},
						error: function(xhr, status, error) {
							console.log('文件上传失败');
							console.log('错误信息：', error);
						}
					});
				}
				// 开始上传第一个文件
				uploadNextFile(0);
			} else {
			  console.log('请选择要上传的文件');
			}
		  });
	};

	function copyCtrl(content) {
		var save = function(e){
			e.clipboardData.setData('text/plain', content);
			e.preventDefault();
		}
		document.addEventListener('copy', save);
		document.execCommand('copy');
		document.removeEventListener('copy',save);
		if (content != '') {
			layer.msg('复制成功');
		}
	}
	
	function downloadFile(url, fileName) {
		let link = document.createElement("a");
		link.href = url;
		link.download = fileName;
		link.click();
		layer.loading('文件下载中...');
	}
	
	function isWeChat() {
	  return /micromessenger/i.test(navigator.userAgent);
	}

	//查看图片
	$('body').on('click','.mbui-file-icon',function(e) {
		e.preventDefault();
		let img = $(this).find('img');
		let href = img.attr('src');
		if (href!=undefined && href!='') {					
			layer.photo(href);
		}
		else {
			let url=window.location.origin + $(this).data('url');
			let name=$(this).data('name');
			if(isWeChat()){
				layer.open({
					type: 1,
					title: '温馨提示',
					content: '微信或企业微信不支持下载文件附件<br>请复制链接到手机浏览器下载查看',
					btn: ['复制链接'],
					yes:function(index){
						copyCtrl(url);
						layer.close(index);
					}
				});
			}
			else{
				downloadFile(url,name);
			}
		}
	});
	// 导出fileUpload模块
	exports('fileupload', function (options) {
		return new fileUpload(options);
	});
});