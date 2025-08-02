/**
 * tool 工具组件
 */
mbui.define([], function (exports) {
	"use strict";
	var tool = {
		ajax: function (options, callback) {
			let format = 'json';
			if (options.hasOwnProperty('data')) {
				format = options.data.hasOwnProperty('format') ? options.data.format : 'json';
			}
			callback = callback || options.success;
			callback && delete options.success;
			let optsetting = { timeout: 10000 };
			if (format == 'jsonp') {
				optsetting = { timeout: 10000, dataType: 'jsonp', jsonp: 'callback' }
			}
			let opts = $.extend({}, optsetting, {
				success: function (res) {
					if (callback && typeof callback === 'function') {
						callback(res);
					}
				}
			}, options);
			$.ajax(opts);
		},
		get: function (url, data, callback) {
			this.ajax({ url: url, type: "GET", data: data }, callback);
		},
		post: function (url, data, callback) {
			this.ajax({ url: url, type: "POST", data: data }, callback);
		},
		put: function (url, data, callback) {
			this.ajax({ url: url, type: "PUT", data: data }, callback);
		},
		delete: function (url, data, callback) {
			this.ajax({ url: url, type: "DELETE", data: data }, callback);
		},
		reload: function (delay) {
			//延迟刷新，一般是在编辑完页面数据后需要自动关闭页面用到
			if(delay && delay>0){
				setTimeout(function () {
					location.reload();
				}, delay);
			}else{
				location.reload();
			}
		},
		replace: function (url,delay) {
			//延迟刷新，一般是在编辑完页面数据后需要自动关闭页面用到
			if(delay && delay>0){
				setTimeout(function () {
					window.location.replace(url);
				}, delay);
			}else{
				window.location.replace(url);
			}
		},
		//格式化文件大小
		renderSize:function(val){
			if(null==val||val==''){
				 return "0 Bytes";
			}
			var unitArr = new Array("Bytes","KB","MB","GB","TB","PB","EB","ZB","YB");
			var index=0;
			var srcsize = parseFloat(val);
			index=Math.floor(Math.log(srcsize)/Math.log(1024));
			var size =srcsize/Math.pow(1024,index);
			size=size.toFixed(2);//保留的小数位数
			return size+unitArr[index];
		},
		// 格式化文件
		file_item:function(file,view=''){
			let image=['jpg','jpeg','png','gif'];
			let fileshow='<div class="mbui-file-icon"><i class="iconfont icon-weizhigeshi"></i></div>';
			if(file['fileext'] == 'pdf'){

			}
			if(image.includes(file['fileext'])){
				fileshow='<div class="mbui-file-icon file-img"><img src="'+file['filepath']+'" alt="'+file['name']+'"></div>';
			}
			let file_del='';
			if(view == ''){
				file_del = '<div class="mbui-file-del"><i class="iconfont icon-cuowukongxin"></i></div>';
			}
			let filesize = this.renderSize(file['filesize']);
			let filedate = this.formatDate(file['create_time']);
			let item = '<div class="mbui-file-div"data-id="'+file['id']+'">\
						'+fileshow+'\
						<div class="mbui-file-info">\
							<div class="mbui-file-name line-limit-1">'+file['name']+'</div>\
							<div class="mbui-file-size">'+filesize+'，'+filedate+'</div>\
						</div>\
						'+file_del+'\
					</div>';
			return item;
		},
		// 倒计时
		countdown: function (options) {
			var that = this;
			// 默认可选项
			options = $.extend(true, {
				date: new Date(),
				now: new Date()
			}, options);

			// 兼容旧版参数
			var args = arguments;
			if (args.length > 1) {
				options.date = new Date(args[0]);
				options.now = new Date(args[1]);
				options.clock = args[2];
			}
			// 实例对象
			var inst = {
				options: options,
				clear: function () { // 清除计时器
					clearTimeout(inst.timer);
				},
				reload: function (opts) { // 重置倒计时
					this.clear();
					$.extend(true, this.options, {
						now: new Date()
					}, opts);
					count();
				}
			};
			typeof options.ready === 'function' && options.ready();
			// 计算倒计时
			var count = (function fn() {
				var date = new Date(options.date);
				var now = new Date(options.now);
				var countTime = function (time) {
					return time > 0 ? time : 0;
				}(date.getTime() - now.getTime());
				var result = {
					d: Math.floor(countTime / (1000 * 60 * 60 * 24)), // 天
					h: Math.floor(countTime / (1000 * 60 * 60)) % 24, // 时
					m: Math.floor(countTime / (1000 * 60)) % 60, // 分
					s: Math.floor(countTime / 1000) % 60 // 秒
				};
				var next = function () {
					now.setTime(now.getTime() + 1000);
					options.now = now;
					count();
				};
				// 计时 - 以秒间隔
				inst.timer = setTimeout(next, 1000);
				typeof options.clock === 'function' && options.clock(result, inst);

				// 计时完成
				if (countTime <= 0) {
					clearTimeout(inst.timer);
					typeof options.done === 'function' && options.done(result, inst);
				}
				return fn;
			})();
			return inst;
		},
		// 某个时间在当前时间的多久前
		timeAgo: function (time, onlyDate) {
			var that = this;
			var arr = [[], []];
			var stamp = new Date().getTime() - new Date(time).getTime();

			// 返回具体日期
			if (stamp > 1000 * 60 * 60 * 24 * 31) {
				stamp = new Date(time);
				arr[0][0] = that.digit(stamp.getFullYear(), 4);
				arr[0][1] = that.digit(stamp.getMonth() + 1);
				arr[0][2] = that.digit(stamp.getDate());

				// 是否输出时间
				if (!onlyDate) {
					arr[1][0] = that.digit(stamp.getHours());
					arr[1][1] = that.digit(stamp.getMinutes());
					arr[1][2] = that.digit(stamp.getSeconds());
				}
				return arr[0].join('-') + ' ' + arr[1].join(':');
			}

			// 30 天以内，返回「多久前」
			if (stamp >= 1000 * 60 * 60 * 24) {
				return ((stamp / 1000 / 60 / 60 / 24) | 0) + ' 天前';
			} else if (stamp >= 1000 * 60 * 60) {
				return ((stamp / 1000 / 60 / 60) | 0) + ' 小时前';
			} else if (stamp >= 1000 * 60 * 3) { // 3 分钟以内为：刚刚
				return ((stamp / 1000 / 60) | 0) + ' 分钟前';
			} else if (stamp < 0) {
				return '未来';
			} else {
				return '刚刚';
			}
		},
		/**
		 * 格式化时间戳为指定格式
		 * @param {number} timestamp - 时间戳（毫秒或秒）
		 * @param {string} formatStr - 格式字符串，例如 'YYYY-MM-DD HH:mm:ss'
		 * @returns {string}
		 */
		formatDate:function(timestamp, formatStr = 'YYYY-MM-DD HH:mm:ss') {
		  const date = new Date(timestamp);

		  // 如果是秒级时间戳，转为毫秒
		  if (timestamp.toString().length === 10) {
			date.setTime(timestamp * 1000);
		  }

		  const padZero = (num) => String(num).padStart(2, '0');

		  const replacements = {
			YYYY: date.getFullYear(),
			MM: padZero(date.getMonth() + 1),   // 月份从 0 开始
			DD: padZero(date.getDate()),
			HH: padZero(date.getHours()),
			mm: padZero(date.getMinutes()),
			ss: padZero(date.getSeconds())
		  };

		  return Object.entries(replacements).reduce((str, [key, value]) => {
			return str.replace(key, value);
		  }, formatStr);
		},
		//返回10位时间戳
		time: function () {
			return Math.floor((new Date).getTime() / 1e3);
		},
		/**
		 *
		 * @param n 格式化样式Y-m-d H:i:s返回带0 Y-n-j不带0 z一年中第几天要+1才正确 w星期0是周日 N 7是周日D三个字母星期 l英语星期 W周 F月份英语 L闰年判断 c带时区日期时间 r英文形式
		 * @param t 时间戳
		 * @returns 格式化时间
		 */
		date: function (n, t) {
			var e, r, u = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], o = /\\?(.?)/gi, i = function (n, t) { return r[n] ? r[n]() : t }, c = function (n, t) { for (n = String(n); n.length < t;)n = "0" + n; return n }; r = { d: function () { return c(r.j(), 2) }, D: function () { return r.l().slice(0, 3) }, j: function () { return e.getDate() }, l: function () { return u[r.w()] + "day" }, N: function () { return r.w() || 7 }, S: function () { var n = r.j(), t = n % 10; return t <= 3 && 1 === parseInt(n % 100 / 10, 10) && (t = 0), ["st", "nd", "rd"][t - 1] || "th" }, w: function () { return e.getDay() }, z: function () { var n = new Date(r.Y(), r.n() - 1, r.j()), t = new Date(r.Y(), 0, 1); return Math.round((n - t) / 864e5) }, W: function () { var n = new Date(r.Y(), r.n() - 1, r.j() - r.N() + 3), t = new Date(n.getFullYear(), 0, 4); return c(1 + Math.round((n - t) / 864e5 / 7), 2) }, F: function () { return u[6 + r.n()] }, m: function () { return c(r.n(), 2) }, M: function () { return r.F().slice(0, 3) }, n: function () { return e.getMonth() + 1 }, t: function () { return new Date(r.Y(), r.n(), 0).getDate() }, L: function () { var n = r.Y(); return n % 4 == 0 & n % 100 != 0 | n % 400 == 0 }, o: function () { var n = r.n(), t = r.W(); return r.Y() + (12 === n && t < 9 ? 1 : 1 === n && t > 9 ? -1 : 0) }, Y: function () { return e.getFullYear() }, y: function () { return r.Y().toString().slice(-2) }, a: function () { return e.getHours() > 11 ? "pm" : "am" }, A: function () { return r.a().toUpperCase() }, B: function () { var n = 3600 * e.getUTCHours(), t = 60 * e.getUTCMinutes(), r = e.getUTCSeconds(); return c(Math.floor((n + t + r + 3600) / 86.4) % 1e3, 3) }, g: function () { return r.G() % 12 || 12 }, G: function () { return e.getHours() }, h: function () { return c(r.g(), 2) }, H: function () { return c(r.G(), 2) }, i: function () { return c(e.getMinutes(), 2) }, s: function () { return c(e.getSeconds(), 2) }, u: function () { return c(1e3 * e.getMilliseconds(), 6) }, e: function () { throw new Error("Not supported (see source code of date() for timezone on how to add support)") }, I: function () { return new Date(r.Y(), 0) - Date.UTC(r.Y(), 0) != new Date(r.Y(), 6) - Date.UTC(r.Y(), 6) ? 1 : 0 }, O: function () { var n = e.getTimezoneOffset(), t = Math.abs(n); return (n > 0 ? "-" : "+") + c(100 * Math.floor(t / 60) + t % 60, 4) }, P: function () { var n = r.O(); return n.substr(0, 3) + ":" + n.substr(3, 2) }, T: function () { return "UTC" }, Z: function () { return 60 * -e.getTimezoneOffset() }, c: function () { return "Y-m-d\\TH:i:sP".replace(o, i) }, r: function () { return "D, d M Y H:i:s O".replace(o, i) }, U: function () { return e / 1e3 | 0 } }; return function (n, t) { return e = void 0 === t ? new Date : t instanceof Date ? new Date(t) : new Date(1e3 * t), n.replace(o, i) }(n, t)
		},
		/**
		 *
		 * @param text 转换参数now +-1 day +- 2 days +-1 week
		 * @param now 默认当前时间戳
		 * @returns 字符串转换时间戳
		 */
		strtotime: function (text, now) {
			var parsed, match, today, year, date, days, ranges, len, times, regex, i, fail = false; if (!text) { return fail } text = text.replace(/^\s+|\s+$/g, "").replace(/\s{2,}/g, " ").replace(/[\t\r\n]/g, "").toLowerCase(); match = text.match(/^(\d{1,4})([\-\.\/\:])(\d{1,2})([\-\.\/\:])(\d{1,4})(?:\s(\d{1,2}):(\d{2})?:?(\d{2})?)?(?:\s([A-Z]+)?)?$/); if (match && match[2] === match[4]) { if (match[1] > 1901) { switch (match[2]) { case "-": if (match[3] > 12 || match[5] > 31) { return fail } return new Date(match[1], parseInt(match[3], 10) - 1, match[5], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000; case ".": return fail; case "/": if (match[3] > 12 || match[5] > 31) { return fail } return new Date(match[1], parseInt(match[3], 10) - 1, match[5], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000 } } else { if (match[5] > 1901) { switch (match[2]) { case "-": if (match[3] > 12 || match[1] > 31) { return fail } return new Date(match[5], parseInt(match[3], 10) - 1, match[1], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000; case ".": if (match[3] > 12 || match[1] > 31) { return fail } return new Date(match[5], parseInt(match[3], 10) - 1, match[1], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000; case "/": if (match[1] > 12 || match[3] > 31) { return fail } return new Date(match[5], parseInt(match[1], 10) - 1, match[3], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000 } } else { switch (match[2]) { case "-": if (match[3] > 12 || match[5] > 31 || (match[1] < 70 && match[1] > 38)) { return fail } year = match[1] >= 0 && match[1] <= 38 ? +match[1] + 2000 : match[1]; return new Date(year, parseInt(match[3], 10) - 1, match[5], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000; case ".": if (match[5] >= 70) { if (match[3] > 12 || match[1] > 31) { return fail } return new Date(match[5], parseInt(match[3], 10) - 1, match[1], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000 } if (match[5] < 60 && !match[6]) { if (match[1] > 23 || match[3] > 59) { return fail } today = new Date(); return new Date(today.getFullYear(), today.getMonth(), today.getDate(), match[1] || 0, match[3] || 0, match[5] || 0, match[9] || 0) / 1000 } return fail; case "/": if (match[1] > 12 || match[3] > 31 || (match[5] < 70 && match[5] > 38)) { return fail } year = match[5] >= 0 && match[5] <= 38 ? +match[5] + 2000 : match[5]; return new Date(year, parseInt(match[1], 10) - 1, match[3], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000; case ":": if (match[1] > 23 || match[3] > 59 || match[5] > 59) { return fail } today = new Date(); return new Date(today.getFullYear(), today.getMonth(), today.getDate(), match[1] || 0, match[3] || 0, match[5] || 0) / 1000 } } } } if (text === "now") { return now === null || isNaN(now) ? new Date().getTime() / 1000 | 0 : now | 0 } if (!isNaN(parsed = Date.parse(text))) { return parsed / 1000 | 0 } date = now ? new Date(now * 1000) : new Date(); days = { "sun": 0, "mon": 1, "tue": 2, "wed": 3, "thu": 4, "fri": 5, "sat": 6 }; ranges = { "yea": "FullYear", "mon": "Month", "day": "Date", "hou": "Hours", "min": "Minutes", "sec": "Seconds" }; function lastNext(type, range, modifier) { var diff, day = days[range]; if (typeof day !== "undefined") { diff = day - date.getDay(); if (diff === 0) { diff = 7 * modifier } else { if (diff > 0 && type === "last") { diff -= 7 } else { if (diff < 0 && type === "next") { diff += 7 } } } date.setDate(date.getDate() + diff) } } function process(val) { var splt = val.split(" "), type = splt[0], range = splt[1].substring(0, 3), typeIsNumber = /\d+/.test(type), ago = splt[2] === "ago", num = (type === "last" ? -1 : 1) * (ago ? -1 : 1); if (typeIsNumber) { num *= parseInt(type, 10) } if (ranges.hasOwnProperty(range) && !splt[1].match(/^mon(day|\.)?$/i)) { return date["set" + ranges[range]](date["get" + ranges[range]]() + num) } if (range === "wee") { return date.setDate(date.getDate() + (num * 7)) } if (type === "next" || type === "last") { lastNext(type, range, num) } else { if (!typeIsNumber) { return false } } return true } times = "(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec" + "|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?" + "|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)"; regex = "([+-]?\\d+\\s" + times + "|" + "(last|next)\\s" + times + ")(\\sago)?"; match = text.match(new RegExp(regex, "gi")); if (!match) { return fail } for (i = 0, len = match.length; i < len; i++) { if (!process(match[i])) { return fail } } return (date.getTime() / 1000)
		},
		/**
		 * date_eq('2019-10-22','2019-10-21') true
		 * @param 前日期
		 * @param 后日期
		 * @returns 判断日期是否前边大于后边
		 */
		date_eq: function (strDate1, strDate2) {
			var date1 = new Date(strDate1.replace(/\-/g, "\/")); var date2 = new Date(strDate2.replace(/\-/g, "\/")); if ((date1 - date2) >= 0) { return true; } else { return false; }
		},
		/**
		 * @param 时间戳10位
		 * @returns 格式化时间
		 */
		timeline: function (tt) {
			var today = new Date(); var d = new Date(tt * 1000); var m = today.getTime() - d.getTime(); if (m <= 0) { m = 1000 } if (m < 60 * 1000) { return Math.floor(m / 1000) + "秒前" } else { if (m < 60 * 60 * 1000) { return Math.floor(m / (1000 * 60)) + "分钟前" } else { if (m < 60 * 60 * 1000 * 24) { return Math.floor(m / (1000 * 60 * 60)) + "小时前" } else { if (m < 60 * 60 * 1000 * 24 * 7) { return Math.floor(m / (1000 * 60 * 60 * 24)) + "天前" } else { if (m < 60 * 60 * 1000 * 24 * 7 * 56) { return Math.floor(m / (1000 * 60 * 60 * 24 * 7)) + "周前" } else { return Math.floor(m / (1000 * 60 * 60 * 24 * 7 * 52)) + "年前" } } } } }
		},
		/**
		 * 获取某个月份的第一天、最后一天
		 * @param nowDate 时间
		 * @return {(string|string)[]}
		 */
		getFirstNowLastDay: function (nowDate) {
			var now = nowDate ? new Date(nowDate) : new Date();
			var strLink = "-";
			var year = now.getFullYear();
			var month = now.getMonth() + 1;
			var day = now.getDate();
			if (month >= 1 && month <= 9) {
				month = "0" + month;
			}
			if (day >= 1 && day <= 9) {
				day = "0" + day;
			}
			var yearMonth = year + strLink + month;
			var firstDate = year + strLink + month + strLink + '01';
			var sysDate = year + strLink + month + strLink + day;
			var lastDay = this.getLastDay(year, month);
			var lastDate = year + strLink + month + strLink + lastDay;
			//以数组形式返回
			return [firstDate, sysDate, lastDate, yearMonth];
		},
		/**
		 * 获取当月的最后一天
		 * @param year 年份
		 * @param month 月份
		 **/
		getLastDay: function (year, month) {
			var new_year = year;
			var new_month = month++;//取下一个月的第一天，方便计算（最后一天不固定）
			if (month > 12) {//如果当前大于12月，则年份转到下一年
				new_month -= 12;//月份减
				new_year++;//年份增
			}
			// 取当年当月对应的下个月的前一天，即当前月的最后一天
			return new Date(new_year, new_month, 0).getDate();
		},
		/**
		 *array_keys( {firstname: 'Kevin', surname: 'van Zonneveld'} );
		 * @param 数组
		 * @returns 返回数组中key
		 */
		array_keys: function (r, n, a) {
			var e = void 0 !== n, o = [], t = !!a, i = !0, y = ""; for (y in r) r.hasOwnProperty(y) && (i = !0, e && (t && r[y] !== n ? i = !1 : r[y] !== n && (i = !1)), i && (o[o.length] = y)); return o
		},
		/**
		 * array_values( {firstname: 'Kevin', surname: 'van Zonneveld'} );
		 * @param 数组
		 * @returns 返回数组中值
		 */
		array_values: function (r) {
			var n = [], a = ""; for (a in r) n[n.length] = r[a]; return n
		},
		/**
		 *array_unique({'a': 'green', 0: 'red', 'b': 'green', 1: 'blue', 2: 'red'})
		 * @param 数组
		 * @returns 去除数组重复
		 */
		array_unique: function (r) {
			var n = "", t = {}, u = ""; for (n in r) r.hasOwnProperty(n) && (u = r[n], !1 === function (r, n) { var t = ""; for (t in n) if (n.hasOwnProperty(t) && n[t] + "" == r + "") return t; return !1 }(u, t) && (t[n] = u)); return t
		},
		/**
		 * array_slice(["a", "b", "c", "d", "e"], 2, -1, true);
		 * @param 数组
		 * @param 开始位置
		 * @param 取出个数
		 * @param 是否重排
		 * @returns 截取数组
		 */
		array_slice: function (r, i, e, t) {
			var o = ""; if ("[object Array]" !== Object.prototype.toString.call(r) || t && 0 !== i) { var a = 0, c = {}; for (o in r) a += 1, c[o] = r[o]; r = c, i = i < 0 ? a + i : i, e = void 0 === e ? a : e < 0 ? a + e - i : e; var n = {}, l = !1, f = -1, s = 0, v = 0; for (o in r) { if (++f, s >= e) break; f === i && (l = !0), l && (++s, is_int(o) && !t ? n[v++] = r[o] : n[o] = r[o]) } return n } return void 0 === e ? r.slice(i) : e >= 0 ? r.slice(i, i + e) : r.slice(i, e)
		},
		/**
		 *array_search('e',["a", "b", "c", "d", "e"])
		 * @param 要查找的值
		 * @param 数组
		 * @returns 数组中搜索指定值返回键
		 */
		array_search: function (r, e, t) {
			var n = !!t, i = ""; if ("object" == typeof r && r.exec) { if (!n) { var o = "i" + (r.global ? "g" : "") + (r.multiline ? "m" : "") + (r.sticky ? "y" : ""); r = new RegExp(r.source, o) } for (i in e) if (e.hasOwnProperty(i) && r.test(e[i])) return i; return !1 } for (i in e) if (e.hasOwnProperty(i) && (n && e[i] === r || !n && e[i] == r)) return i; return !1
		},
		/**
		 *
		 * @param 变量
		 * @returns 是否数组判断
		 */
		is_array: function (t) {
			if (!t || "object" != typeof t) return !1; if (function (t) { if (!t || "object" != typeof t || "number" != typeof t.length) return !1; var e = t.length; return t[t.length] = "bogus", e !== t.length ? (t.length -= 1, !0) : (delete t[t.length], !1) }(t)) return !0; var e = Object.prototype.toString.call(t), n = function (t) { var e = /\W*function\s+([\w$]+)\s*\(/.exec(t); return e ? e[1] : "(Anonymous)" }(t.constructor); return "[object Object]" === e && "Object" === n
		},
		/**
		 *
		 * @param 值
		 * @param 数组
		 * @returns 判断元素是否在数组中bool
		 */
		in_array: function (r, n, i) {
			var f = ""; if (!i) { for (f in n) if (n[f] == r) return !0 } else for (f in n) if (n[f] === r) return !0; return !1
		},
		/**
		 *sort(["a","b"],"SORT_STRING",true)
		 * @param 数组
		 * @param SORT_STRING|SORT_NUMERIC 排序类型
		 * @param 默认true只排序不输出,false输出
		 * @returns 数组排序
		 */
		sort: function (inputArr, sort_flags, strictForIn = true) {
			var valArr = [], keyArr = [], k = '', i = 0, sorter = false, populateArr = []; switch (sort_flags) { case 'SORT_STRING': sorter = function (a, b) { return strnatcmp(a, b); }; break; case 'SORT_NUMERIC': sorter = function (a, b) { return (a - b); }; break; default: sorter = function (a, b) { var aFloat = parseFloat(a), bFloat = parseFloat(b), aNumeric = aFloat + '' === a, bNumeric = bFloat + '' === b; if (aNumeric && bNumeric) { return aFloat > bFloat ? 1 : aFloat < bFloat ? -1 : 0; } else if (aNumeric && !bNumeric) { return 1; } else if (!aNumeric && bNumeric) { return -1; } return a > b ? 1 : a < b ? -1 : 0; }; break; }populateArr = strictForIn ? inputArr : populateArr; for (k in inputArr) { if (inputArr.hasOwnProperty(k)) { valArr.push(inputArr[k]); if (strictForIn) { delete inputArr[k]; } } } valArr.sort(sorter); for (i = 0; i < valArr.length; i++) { populateArr[i] = valArr[i]; } return strictForIn || populateArr;
		},
		/**
		 *
		 * @param 字符
		 * @returns 是否整数判断
		 */
		is_int: function (i) {
			return i === +i && isFinite(i) && !(i % 1)
		},
		/**
		 *
		 * @param 字符
		 * @returns 是否浮点数判断
		 */
		is_float: function (i) {
			return !(+i !== i || isFinite(i) && !(i % 1))
		},
		/**
		 *
		 * @param 变量
		 * @returns 是否对象判断
		 */
		is_object: function (t) {
			return "[object Array]" !== Object.prototype.toString.call(t) && (null !== t && "object" == typeof t)
		},
		/**
		 *  @param 函数名字符串
		 * @returns 判断函数是否存在
		 */
		function_exists: function (n) {
			var o = "undefined" != typeof window ? window : global; return "string" == typeof n && (n = o[n]), "function" == typeof n
		},
		/**
		 *
		 * @param 数组或对象
		 * @returns 序列化
		 */
		serialize: function (r) {
			var e, t, a, n = "", o = 0, i = function (r) { var e, t, a, n, o = typeof r; if ("object" === o && !r) return "null"; if ("object" === o) { if (!r.constructor) return "object"; a = r.constructor.toString(), e = a.match(/(\w+)\(/), e && (a = e[1].toLowerCase()), n = ["boolean", "number", "string", "array"]; for (t in n) if (a == n[t]) { o = n[t]; break } } return o }, c = i(r); switch (c) { case "function": e = ""; break; case "boolean": e = "b:" + (r ? "1" : "0"); break; case "number": e = (Math.round(r) == r ? "i" : "d") + ":" + r; break; case "string": e = "s:" + function (r) { var e = 0, t = 0, a = r.length, n = ""; for (t = 0; t < a; t++)n = r.charCodeAt(t), e += n < 128 ? 1 : n < 2048 ? 2 : 3; return e }(r) + ':"' + r + '"'; break; case "array": case "object": e = "a"; for (t in r) if (r.hasOwnProperty(t)) { if ("function" === i(r[t])) continue; a = t.match(/^[0-9]+$/) ? parseInt(t, 10) : t, n += this.serialize(a) + this.serialize(r[t]), o++ } e += ":" + o + ":{" + n + "}"; break; case "undefined": default: e = "N" }return "object" !== c && "array" !== c && (e += ";"), e
		},
		/**
		 *
		 * @param 字符串
		 * @returns 解码序列化
		 */
		unserialize: function (r) {
			var e = this, n = function (r) { var e = r.charCodeAt(0); return e < 128 ? 0 : e < 2048 ? 1 : 2 }; return error = function (r, n, a, t) { throw new e.window[r](n, a, t) }, read_until = function (r, e, n) { for (var a = 2, t = [], i = r.slice(e, e + 1); i != n;)a + e > r.length && error("Error", "Invalid"), t.push(i), i = r.slice(e + (a - 1), e + a), a += 1; return [t.length, t.join("")] }, read_chrs = function (r, e, a) { var t, i, u; for (u = [], t = 0; t < a; t++)i = r.slice(e + (t - 1), e + t), u.push(i), a -= n(i); return [u.length, u.join("")] }, _unserialize = function (r, e) { var n, a, t, i, u, s, o, l, c, d, f, h, _, p, w, b, k, g, v = 0, I = function (r) { return r }; switch (e || (e = 0), n = r.slice(e, e + 1).toLowerCase(), a = e + 2, n) { case "i": I = function (r) { return parseInt(r, 10) }, c = read_until(r, a, ";"), v = c[0], l = c[1], a += v + 1; break; case "b": I = function (r) { return 0 !== parseInt(r, 10) }, c = read_until(r, a, ";"), v = c[0], l = c[1], a += v + 1; break; case "d": I = function (r) { return parseFloat(r) }, c = read_until(r, a, ";"), v = c[0], l = c[1], a += v + 1; break; case "n": l = null; break; case "s": d = read_until(r, a, ":"), v = d[0], f = d[1], a += v + 2, c = read_chrs(r, a + 1, parseInt(f, 10)), v = c[0], l = c[1], a += v + 2, v != parseInt(f, 10) && v != l.length && error("SyntaxError", "String length mismatch"); break; case "a": for (l = {}, t = read_until(r, a, ":"), v = t[0], i = t[1], a += v + 2, s = parseInt(i, 10), u = !0, h = 0; h < s; h++)p = _unserialize(r, a), w = p[1], _ = p[2], a += w, b = _unserialize(r, a), k = b[1], g = b[2], a += k, _ !== h && (u = !1), l[_] = g; if (u) { for (o = new Array(s), h = 0; h < s; h++)o[h] = l[h]; l = o } a += 1; break; default: error("SyntaxError", "Unknown / Unhandled data type(s): " + n) }return [n, a - e, I(l)] }, _unserialize(r + "", 0)[2]
		},
		/**
		 * uniqid('',true)长度23 默认13
		 * @param 前缀
		 * @param 是否增加长度
		 * @returns 生成唯一id
		 */
		uniqid: function (n, e) {
			void 0 === n && (n = ""); var t, i = function (n, e) { return n = parseInt(n, 10).toString(16), e < n.length ? n.slice(n.length - e) : e > n.length ? Array(e - n.length + 1).join("0") + n : n }, o = "undefined" != typeof window ? window : global; o.$locutus = o.$locutus || {}; var d = o.$locutus; return d.php = d.php || {}, d.php.uniqidSeed || (d.php.uniqidSeed = Math.floor(123456789 * Math.random())), d.php.uniqidSeed++, t = n, t += i(parseInt((new Date).getTime() / 1e3, 10), 8), t += i(d.php.uniqidSeed, 5), e && (t += (10 * Math.random()).toFixed(8).toString()), t
		},
		/**
		 *
		 * @param 变量
		 * @param 进制,默认十进制
		 * @returns 转换成整型
		 */
		intval: function (i, t) {
			var n, a, e = typeof i; return "boolean" === e ? +i : "string" === e ? (0 === t && (a = i.match(/^\s*0(x?)/i), t = a ? a[1] ? 16 : 8 : 10), n = parseInt(i, t || 10), isNaN(n) || !isFinite(n) ? 0 : n) : "number" === e && isFinite(i) ? i < 0 ? Math.ceil(i) : Math.floor(i) : 0
		},
		//转换成浮点型
		floatval: function (a) {
			return parseFloat(a) || 0
		},
		//打印变量
		log: function (arr) {
			console.log(arr);
		},
		/**
		 *
		 * @param 字符串
		 * @param 要去除的字符,默认为空格
		 * @returns 去除两边空格
		 */
		trim: function (r, n) {
			var t = [" ", "\n", "\r", "\t", "\f", "\v", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "​", "\u2028", "\u2029", "　"].join(""), e = 0, i = 0; for (r += "", n && (t = (n + "").replace(/([[\]().?\/*{}+$^:])/g, "$1")), e = r.length, i = 0; i < e; i++)if (-1 === t.indexOf(r.charAt(i))) { r = r.substring(i); break } for (e = r.length, i = e - 1; i >= 0; i--)if (-1 === t.indexOf(r.charAt(i))) { r = r.substring(0, i + 1); break } return -1 === t.indexOf(r.charAt(0)) ? r : ""
		},
		/**
		 *
		 * @param 字符串
		 * @param 要去除的字符
		 * @returns 去除右边空格
		 */
		rtrim: function (e, r) {
			return r = r ? (r + "").replace(/([[\]().?\/*{}+$^:])/g, "\\$1") : " \\s ", (e + "").replace(new RegExp("[" + r + "]+$", "g"), "")
		},
		/**
		 *
		 * @param 字符串
		 * @param 要去除的字符
		 * @returns 去除左边空格
		 */
		ltrim: function (e, r) {
			return r = r ? (r + "").replace(/([[\]().?\/*{}+$^:])/g, "$1") : " \\s ", (e + "").replace(new RegExp("^[" + r + "]+", "g"), "")
		},
		//字符串删除多个空格只保留一个
		strtrim: function (a) {
			return a.replace(/\s+/g, " ");
		},
		/**
		 *str_replace("a","我","来自于a")
		 * @param 要查找的字符
		 * @param 要替换的字符
		 * @param 字符串
		 * @returns 字符串替换
		 */
		str_replace: function (t, o, e, c) {
			var r = 0, l = 0, n = "", a = "", i = 0, p = 0, u = [].concat(t), f = [].concat(o), g = e, y = "[object Array]" === Object.prototype.toString.call(f), b = "[object Array]" === Object.prototype.toString.call(g); g = [].concat(g); var j = "undefined" != typeof window ? window : global; j.$locutus = j.$locutus || {}; var v = j.$locutus; if (v.php = v.php || {}, "object" == typeof t && "string" == typeof o) { for (n = o, o = [], r = 0; r < t.length; r += 1)o[r] = n; n = "", f = [].concat(o), y = "[object Array]" === Object.prototype.toString.call(f) } for (void 0 !== c && (c.value = 0), r = 0, i = g.length; r < i; r++)if ("" !== g[r]) for (l = 0, p = u.length; l < p; l++)n = g[r] + "", a = y ? void 0 !== f[l] ? f[l] : "" : f[0], g[r] = n.split(u[l]).join(a), void 0 !== c && (c.value += n.split(u[l]).length - 1); return b ? g : g[0]
		},
		/**
		 *
		 * @param 字符串
		 * @param 保留的标签
		 * @returns 过滤去除html标签
		 */
		strip_tags: function (input, allowed) {
			allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(""); var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi; return input.replace(commentsAndPhpTags, "").replace(tags, function ($0, $1) { return allowed.indexOf("<" + $1.toLowerCase() + ">") > -1 ? $0 : "" })
		},
		//字符串转换首字母大写
		ucfirst: function (t) {
			return t += "", t.charAt(0).toUpperCase() + t.substr(1)
		},
		//html转换实体
		htmlencode: function (sStr) {
			var htmlspecialchars = function (e, E, T, _) { var r = 0, t = 0, a = !1; ("undefined" == typeof E || null === E) && (E = 2), e = e.toString(), _ !== !1 && (e = e.replace(/&/g, "&amp;")), e = e.replace(/</g, "&lt;").replace(/>/g, "&gt;"); var N = { ENT_NOQUOTES: 0, ENT_HTML_QUOTE_SINGLE: 1, ENT_HTML_QUOTE_DOUBLE: 2, ENT_COMPAT: 2, ENT_QUOTES: 3, ENT_IGNORE: 4 }; if (0 === E && (a = !0), "number" != typeof E) { for (E = [].concat(E), t = 0; t < E.length; t++)0 === N[E[t]] ? a = !0 : N[E[t]] && (r |= N[E[t]]); E = r } return E & N.ENT_HTML_QUOTE_SINGLE && (e = e.replace(/'/g, "&#039;")), a || (e = e.replace(/"/g, "&quot;")), e }; return htmlspecialchars(sStr);
		},
		//html实体还原
		htmldecode: function (sStr) {
			var htmlspecialchars_decode = function (e, E) { var T = 0, _ = 0, r = !1; "undefined" == typeof E && (E = 2), e = e.toString().replace(/&lt;/g, "<").replace(/&gt;/g, ">"); var t = { ENT_NOQUOTES: 0, ENT_HTML_QUOTE_SINGLE: 1, ENT_HTML_QUOTE_DOUBLE: 2, ENT_COMPAT: 2, ENT_QUOTES: 3, ENT_IGNORE: 4 }; if (0 === E && (r = !0), "number" != typeof E) { for (E = [].concat(E), _ = 0; _ < E.length; _++)0 === t[E[_]] ? r = !0 : t[E[_]] && (T |= t[E[_]]); E = T } return E & t.ENT_HTML_QUOTE_SINGLE && (e = e.replace(/&#0*39;/g, "'")), r || (e = e.replace(/&quot;/g, '"')), e = e.replace(/&amp;/g, "&") }; return htmlspecialchars_decode(sStr)
		},
		// 转义 html
		escape: function (html) {
			var exp = /[<"'>]|&(?=#[a-zA-Z0-9]+)/g;
			if (html === undefined || html === null) return '';
			html += '';
			if (!exp.test(html)) return html;
			return html.replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
				.replace(/</g, '&lt;').replace(/>/g, '&gt;')
				.replace(/'/g, '&#39;').replace(/"/g, '&quot;');
		},
		// 还原转义的 html
		unescape: function (html) {
			if (html === undefined || html === null) html = '';
			html += '';
			return html.replace(/\&amp;/g, '&')
				.replace(/\&lt;/g, '<').replace(/\&gt;/g, '>')
				.replace(/\&#39;/g, '\'').replace(/\&quot;/g, '"');
		},
		//base64还原
		base64_decode: function (n) {
			var r = function (n) { return decodeURIComponent(n.split("").map(function (n) { return "%" + ("00" + n.charCodeAt(0).toString(16)).slice(-2) }).join("")) }; if ("undefined" == typeof window) return new Buffer(n, "base64").toString("utf-8"); if (void 0 !== window.atob) return r(window.atob(n)); var e, t, o, i, d, f, a, c, u = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", h = 0, w = 0, C = "", g = []; if (!n) return n; n += ""; do { i = u.indexOf(n.charAt(h++)), d = u.indexOf(n.charAt(h++)), f = u.indexOf(n.charAt(h++)), a = u.indexOf(n.charAt(h++)), c = i << 18 | d << 12 | f << 6 | a, e = c >> 16 & 255, t = c >> 8 & 255, o = 255 & c, g[w++] = 64 === f ? String.fromCharCode(e) : 64 === a ? String.fromCharCode(e, t) : String.fromCharCode(e, t, o) } while (h < n.length); return C = g.join(""), r(C.replace(/\0+$/, ""))
		},
		// base64编码
		base64_encode: function (e) {
			var r = function (e) { return encodeURIComponent(e).replace(/%([0-9A-F]{2})/g, function (e, r) { return String.fromCharCode("0x" + r) }) }; if ("undefined" == typeof window) return new Buffer(e).toString("base64"); if (void 0 !== window.btoa) return window.btoa(r(e)); var n, t, o, i, a, c, d, f, h = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", u = 0, w = 0, A = "", l = []; if (!e) return e; e = r(e); do { n = e.charCodeAt(u++), t = e.charCodeAt(u++), o = e.charCodeAt(u++), f = n << 16 | t << 8 | o, i = f >> 18 & 63, a = f >> 12 & 63, c = f >> 6 & 63, d = 63 & f, l[w++] = h.charAt(i) + h.charAt(a) + h.charAt(c) + h.charAt(d) } while (u < e.length); A = l.join(""); var C = e.length % 3; return (C ? A.slice(0, C - 3) : A) + "===".slice(C || 3)
		},
		//字符串URL编码
		urlencode: function (e) {
			return e += "", encodeURIComponent(e).replace(/!/g, "%21").replace(/'/g, "%27").replace(/\(/g, "%28").replace(/\)/g, "%29").replace(/\*/g, "%2A").replace(/~/g, "%7E").replace(/%20/g, "+")
		},
		//字符串URL解码
		urldecode: function (e) {
			return decodeURIComponent((e + "").replace(/%(?![\da-f]{2})/gi, function () { return "%25" }).replace(/\+/g, "%20"))
		},
		//unicode解码表情中文
		unicode_decode: function (e) {
			return e = e.replace(/\\/g, "%"), unescape(e)
		},
		/**
		 * @param 字符串
		 * @param 是否中文也编码
		 * @returns unicode编码中文表情
		 */
		unicode_encode: function (n) {
			if (1 == (arguments.length > 1 && void 0 !== arguments[1] && arguments[1])) { for (var r = [], t = 0; t < n.length; t++)r[t] = ("00" + n.charCodeAt(t).toString(16)).slice(-4); return "\\u" + r.join("\\u") } var e = function (n) { for (var r = [], t = 0; t < n.length; t++)r[t] = ("00" + n.charCodeAt(t).toString(16)).slice(-4); return "\\u" + r.join("\\u") }, u = /[\ud800-\udbff][\udc00-\udfff]/g; return n = n.replace(u, function (n) { return 2 === n.length ? e(n) : n })
		},
		//获取url变量值
		get_params: function (c) {
			var e = c + "=", b = window.location.href, h = b.indexOf("?"), b = b.slice(h + 1), a = b.split("&"), d = 0, f = "", g = a.length; for (d = 0; d < g; d++) { var f = a[d]; if (f.indexOf(e) === 0) { return decodeURIComponent(f.slice(e.length).replace(/\+/g, "%20")) } } return null
		},
		/**
		 * 可以不要参数
		 * @param 最小
		 * @param 最大
		 * @returns 生成更好随机数
		 */
		mt_rand: function (r, e) {
			var n = arguments.length; if (0 === n) r = 0, e = 2147483647; else { if (1 === n) throw new Error("Warning: mt_rand() expects exactly 2 parameters, 1 given"); r = parseInt(r, 10), e = parseInt(e, 10) } return Math.floor(Math.random() * (e - r + 1)) + r
		},
		/**
		 * 可以不要参数
		 * @param 最小
		 * @param 最大
		 * @returns 生成随机数
		 */
		rand: function (r, e) {
			var n = arguments.length; if (0 === n) r = 0, e = 2147483647; else if (1 === n) throw new Error("Warning: rand() expects exactly 2 parameters, 1 given"); return Math.floor(Math.random() * (e - r + 1)) + r
		},
		/**
		 *strcut("我爱中国人",4,"...")
		 * @param 字符串
		 * @param 长度中文汉字算两个
		 * @param 显示符号
		 * @returns 字符串截取
		 */
		strcut: function (str, iMaxBytes, sSuffix) {
			if (isNaN(iMaxBytes)) { return str } if (strlen(str) <= iMaxBytes) { return str } var i = 0, bytes = 0; for (; i < str.length && bytes < iMaxBytes; ++i, ++bytes) { if (str.charCodeAt(i) > 255) { ++bytes } } sSuffix = sSuffix || ""; return (bytes - iMaxBytes == 1 ? str.substr(0, i - 1) : str.substr(0, i)) + sSuffix
		},
		/**
		 * @param 字符串
		 * @param 子字符串
		 * @returns 查找字符串
		 */
		strfind: function (string, find) {
			return !(string.indexOf(find) === -1)
		},
		//判断是否数字
		is_num: function (num) {
			var reg = new RegExp("^[0-9]*$"); return reg.test(num)
		},
		//判断是否手机号
		is_mobile: function (num) {
			var reg = /^1\d{10}$/; return reg.test(num)
		},
		//判断是否QQ
		is_qq: function (num) {
			var reg = /^[1-utf8_decode]{1}\d{4,11}$/; return reg.test(num)
		},
		//判断是否邮箱
		is_email: function (str) {
			var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/; return reg.test(str)
		},
		//判断是否中文
		is_chinese: function (num) {
			var reg = /[\u4e00-\u9fa5]/g; return reg.test(num)
		},
		//判断是否符合注册用户名(数字字母组成,下划钱字母开头)
		is_reg: function (num) {
			var reg = /^([a-zA-z_]{1})([\w]*)$/g; return reg.test(num)
		},
		//判断是否电话
		is_tel: function (str) {
			var reg = /^[+]{0,1}(\d){1,3}[ ]?([-]?((\d)|[ ]){1,12})+$/; return reg.test(str)
		},
		//判断是否是IP
		is_ip: function (strIP) {
			if (isNull(strIP)) { return false } var re = /^(\d+)\.(\d+)\.(\d+)\.(\d+)$/g; if (re.test(strIP)) { if (RegExp.$1 < 256 && RegExp.$2 < 256 && RegExp.$3 < 256 && RegExp.$4 < 256) { return true } } return false
		},
		//判断是否邮编
		is_zipcode: function (str) {
			var reg = /^(\d){6}$/; return reg.test(str)
		},
		//判断是否英文
		is_english: function (str) {
			var reg = /^[A-Za-z]+$/; return reg.test(str)
		},
		//判断是否是URL
		is_url: function (url) {
			var strRegex = /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/; var re = new RegExp(strRegex); return re.test(url);
		},
		//判断是否http
		is_http: function (url) {
			if (url.indexOf("http://") === -1 && url.indexOf("https://") === -1) { return false } return true
		},
		//判断是否金额
		is_money: function (n) {
			return !!/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/.test(n)
		},
		//是否是银行卡号
		is_CardNumber: function (num) {
			return num.toString().replace(/\s/g, '').replace(/(.{4})/g, "$1 ");
		},
		//是否是身份证号
		is_IDCard: function (idcode) {
			var weight_factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
			var check_code = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2', 'x'];
			var code = idcode + "";
			var last = idcode[17];//最后一位
			var seventeen = code.substring(0, 17);
			var arr = seventeen.split("");
			var len = arr.length;
			var num = 0;
			for (var i = 0; i < len; i++) {
				num = num + arr[i] * weight_factor[i];
			}
			var resisue = num % 11;
			var last_no = check_code[resisue];
			var idcard_patter = /^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;
			var format = idcard_patter.test(idcode);
			return last === last_no && format;
		},
		/**
		 *
		 * @param 整数
		 * @param 最小
		 * @param 最大
		 * @returns 判断是否在两个整数内
		 */
		in_int: function (n, iMin, iMax) {
			if (!isFinite(n)) { return false } if (!/^[+-]?\d+$/.test(n)) { return false } if (iMin != undefined && parseInt(n) < parseInt(iMin)) { return false } if (iMax != undefined && parseInt(n) > parseInt(iMax)) { return false } return true
		},
		/**
		 *
		 * @param 浮点数
		 * @param 最小
		 * @param 最大
		 * @returns 判断是否在两个浮点数之内
		 */
		in_float: function (n, fMin, fMax) {
			if (!isFinite(n)) { return false } if (fMin != undefined && parseFloat(n) < parseFloat(fMin)) { return false } if (fMax != undefined && parseFloat(n) > parseFloat(fMax)) { return false } return true
		},
		/**
		 *number_format('150.456', 2, '.', ',')
		 * @param 数字
		 * @param 保留小数位数
		 * @param 小数点显示符号
		 * @param 千分位符号
		 * @returns 格式化数字
		 */
		number_format: function (e, n, t, i) {
			e = (e + "").replace(/[^0-9+\-Ee.]/g, ""); var r = isFinite(+e) ? +e : 0, o = isFinite(+n) ? Math.abs(n) : 0, a = void 0 === i ? "," : i, d = void 0 === t ? "." : t, u = ""; return u = (o ? function (e, n) { if (-1 === ("" + e).indexOf("e")) return +(Math.round(e + "e+" + n) + "e-" + n); var t = ("" + e).split("e"), i = ""; return +t[1] + n > 0 && (i = "+"), (+(Math.round(+t[0] + "e" + i + (+t[1] + n)) + "e-" + n)).toFixed(n) }(r, o).toString() : "" + Math.round(r)).split("."), u[0].length > 3 && (u[0] = u[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, a)), (u[1] || "").length < o && (u[1] = u[1] || "", u[1] += new Array(o - u[1].length + 1).join("0")), u.join(d)
		},
		// 数字前置补零
		digit: function (num, length) {
			var str = '';
			num = String(num);
			length = length || 2;
			for (var i = num.length; i < length; i++) {
				str += '0';
			}
			return num < Math.pow(10, length) ? str + (num | 0) : num;
		},
		/**
		* 金额数字转大写
		* @param num number
		* @return {string}
		*/
		upDigit: function (num, is_head = false) {
			var fraction = ['角', '分', '厘'];
			var digit = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
			var unit = [
				['元', '万', '亿'],
				['', '拾', '佰', '仟']
			];
			var head = num < 0 ? '欠人民币' : '人民币';
			num = Math.abs(num);
			var s = '', i;
			for (i = 0; i < fraction.length; i++) {
				s += (digit[Math.floor(num * 10 * Math.pow(10, i)) % 10] + fraction[i]).replace(/零./, '');
			}
			s = s || '整';
			num = Math.floor(num);
			for (i = 0; i < unit[0].length && num > 0; i++) {
				var p = '';
				for (var j = 0; j < unit[1].length && num > 0; j++) {
					p = digit[num % 10] + unit[1][j] + p;
					num = Math.floor(num / 10);
				}
				s = p.replace(/(零.)*零$/, '').replace(/^$/, '零') + unit[0][i] + s;
			}
			if (is_head == true) {
				return head + s.replace(/(零.)*零元/, '元').replace(/(零.)+/g, '零').replace(/^整$/, '零元整');
			}
			else {
				return s.replace(/(零.)*零元/, '元').replace(/(零.)+/g, '零').replace(/^整$/, '零元整');
			}

		},
		//隐藏银行号码
		hidebank: function (s = "6217995510035399947") {
			return s.replace(/^(\d{8})\d+(\d{4})$/, "$1*******$2");
		},
		// 隐藏手机号中间四位
		hidemobile: function (s = "18291447788") {
			return s.replace(/^(\d{3})\d+(\d{4})$/, "$1****$2");
		},
		/**
		 *
		 * @param css代码
		 * @returns 添加css代码
		 */
		addcss: function (e) {
			var t = document.createElement("style"), d = document.head || document.getElementsByTagName("head")[0]; if (t.type = "text/css", t.styleSheet) { var a = function () { try { t.styleSheet.cssText = e } catch (e) { } }; t.styleSheet.disabled ? setTimeout(a, 10) : a() } else { var s = document.createTextNode(e); t.appendChild(s) } d.appendChild(t)
		},
		/**
		 * @param js代码
		 * @returns 添加js代码
		 */
		addjs: function (t) {
			var e = document.createElement("script"); e.type = "text/javascript"; try { e.appendChild(document.createTextNode(t)) } catch (d) { e.text = t } document.head.appendChild(e)
		},
		/**
		 *
		 * @param js文件路径
		 * @param 回调函数
		 * @returns 加载js支持回调
		 */
		loadjs: function (e, a) {
			var t = document.createElement("script"); t.src = e, t.onload = function () { var e = t.readyState && "complete" != t.readyState && "loaded" != t.readyState; a && a(!e) }, document.head.appendChild(t)
		},
		/**
		 *
		 * @param css路径
		 * @param 回调函数
		 * @returns 加载css支持回调
		 */
		loadcss: function (e, n) {
			var t = document.createElement("link"); t.rel = "stylesheet", t.type = "text/css", t.onerror = function () { n(!1) }, t.onload = function () { n(!0) }, t.href = e, document.head.appendChild(t)
		},
		//获取当前域名主机
		gethost: function () {
			return window.location.protocol + "//" + window.location.host;
		},
		/**
		* 由经纬度计算两点之间的距离
		* @param latlngFirst
		* @param latlngLast
		* @return {string}
		*/
		distance: function (latlngFirst, latlngLast) {
			var La1 = latlngFirst.lat * Math.PI / 180.0;
			var La2 = latlngLast.lat * Math.PI / 180.0;
			var La3 = La1 - La2;
			var Lb3 = latlngFirst.lng * Math.PI / 180.0 - latlngLast.lng * Math.PI / 180.0;
			var dis = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(La3 / 2), 2) + Math.cos(La1) * Math.cos(La2) * Math.pow(Math.sin(Lb3 / 2), 2)));
			dis = dis * 6378.137;
			dis = Math.round(dis * 10000) / 10000;
			return dis.toFixed(2);
		},
		/**
		 * 设置cookie
		 * @param name
		 * @param value
		 * @param iDay
		 */
		setCookie: function (name, value, iDay) {
			var oDate = new Date();
			oDate.setDate(oDate.getDate() + iDay);
			document.cookie = name + '=' + value + ';expires=' + oDate;
		},
		/**
		 * 获取cookie
		 * @param name
		 * @return {string}
		 */
		getCookie: function (name) {
			var arr = document.cookie.split('; ');
			for (var i = 0; i < arr.length; i++) {
				var arr2 = arr[i].split('=');
				if (arr2[0] == name) {
					return arr2[1];
				}
			}
			return '';
		},
		/**
		 * 删除Cookie
		 * @param name
		 */
		removeCookie: function (name) {
			this.setCookie(name, 1, -1);
		},
		/**
		 * 运算百分制
		 * @param thisNum
		 * @param totalNum
		 * @return {number}
		 */
		computeProgres: function (thisNum, totalNum) {
			var num = parseFloat(thisNum) || 0.00, total = parseFloat(totalNum) || 0.00;
			return Math.round(num / total * 10000) / 100;
		},
		/**
		 * 生成随着大小写token
		 * @param length 长度，默认32
		 * @return {string}
		 */
		token: function (length) {
			length = length || 32;
			var char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			var result = '';
			for (var i = 0; i < length; i++) {
				result += char.charAt(Math.floor(Math.random() * char.length));
			}
			return result;
		}
	};
	$('body').on('click', '.link-a', function () {
		let url = $(this).data('href');
		if (url && url !== '') {
			window.location.replace(url);
		}
		return false;
	});
	// 输出接口
	exports('tool', tool);
});
