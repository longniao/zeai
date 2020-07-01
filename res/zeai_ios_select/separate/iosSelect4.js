(function() {
	iosSelectUtil = {
		isArray: function(arg1) {
			return Object.prototype.toString.call(arg1) === '[object Array]';
		},
		isFunction: function(arg1) {
			return typeof arg1 === 'function';
		},
		attrToData: function(dom, index) {
			var obj = {};
			for (var p in dom.dataset) {
				obj[p] = dom.dataset[p];
			}
			obj['dom'] = dom;
			obj['atindex'] = index;
			return obj;
		},
		attrToHtml: function(obj) {
			var html = '';
			for (var p in obj) {
				html += 'data-' + p + '="' + obj[p] + '"';
			}
			return html;
		}
	};
	// Layer
	function Layer(html, opts) {
		if (!(this instanceof Layer)) {
			return new Layer(html, opts);
		}
		this.html = html;
		this.opts = opts;
		var el = document.createElement('div');
		el.className = 'olay';
		el.id='iosMask'+Sbindbox;//supdes
		// var layer_el = $('<div class="layer"></div>');
		var layer_el = document.createElement('div');
		layer_el.className = 'layer fadeInUp';// animattime_fast fadeInDown
		this.el = el;
		this.layer_el = layer_el;
		//el.className='alpha0_100';//supdes
		this.init();
			/*supdes mask disabled*/
			el.addEventListener('touchmove', function(e) {
				e.preventDefault();
			});
			if (zeai.empty(Sbindbox) || Sbindbox=='mate' ){
				el.onclick = function(){
					el.removeClass('alpha0_100');el.addClass('alpha100_0');
					layer_el.removeClass('fadeInUp');layer_el.addClass('fadeInDown');
					setTimeout(function(){
						if (el){
							if (!zeai.empty(o('iosMask'+Sbindbox))){
								o('iosMask'+Sbindbox).remove();el = null;
								document.body.style.overflow = 'auto';//2020-03-11
								//window.scrollTo(0, self.offsetTop);//2020-03-11
							}
						}
					},150);
				}
			}
			/*supdes end*/
	}
	Layer.prototype = {
		init: function() {
			this.layer_el.innerHTML = this.html;
			if (!zeai.empty(Sbindbox)){
				o(Sbindbox).appendChild(this.el);
			}else{
				document.body.appendChild(this.el);
			}
			this.el.appendChild(this.layer_el);
			this.el.style.height = Math.max(document.documentElement.getBoundingClientRect().height, window.innerHeight);
			if (this.opts.className) {
				this.el.className += ' ' + this.opts.className;
			}
			this.bindEvent();
		},
		bindEvent: function() {
			var sureDom = this.el.querySelectorAll('.sure');
			var closeDom = this.el.querySelectorAll('.close');
			var self = this;
			for (var i = 0, len = sureDom.length; i < len; i++) {
				sureDom[i].addEventListener('click', function(e) {
					self.close();
				});
			}
			for (var i = 0, len = closeDom.length; i < len; i++) {
				closeDom[i].addEventListener('click', function(e) {
					self.close();
					
				});
			}
			/*supdes*/
			
			//this.el.addEventListener('click', function(e) {
				//self.close();按钮点击销毁
			//});
			
			document.getElementById('iosSelectTitle'+Sbindbox).addEventListener('click', function(e) {
				e.cancelBubble = true;
			});
			
			/*supdes end*/
		},
		close: function() {
			if (this.el) {
				this.el.parentNode.removeChild(this.el);
				this.el = null;
			}
		}
	}
	/*
	 level: 选择的层级 1 2 3 最多支持3层
	 data: [oneLevelArray[, twoLevelArray[, threeLevelArray]]]
	 options:
	     callback: 选择完毕后的回调函数
	     title: 选择框title
	     itemHeight: 每一项的高度，默认 35px
	     headerHeight: 组件标题栏高度 默认 44px
	     addClassName: 组件额外类名 用于自定义样式
	     oneTwoRelation: 第一列和第二列是否通过parentId关联
	     twoThreeRelation: 第二列和第三列是否通过parentId关联
	     oneLevelId: 第一级选中id
	     twoLevelId: 第二级选中id
	     threeLevelId: 第三级选中id
	 */
	function IosSelect(level, data, options) {
		if (!iosSelectUtil.isArray(data) || data.length === 0) {
			return;
		}
		this.data = data;
		this.level = level || 1;
		this.options = options;
		this.typeBox = this.level === 1 ? 'one-level-box' : (this.level === 2 ? 'two-level-box' : 'three-level-box');
		this.callback = options.callback;
		this.title = options.title || '';
		this.itemHeight = options.itemHeight || 35;
		this.headerHeight = options.headerHeight || 44;
		this.init();
	};

	IosSelect.prototype = {
		init: function() {
			this.initLayer();
			// 选中元素的信息
			this.selectOneObj = {};
			this.selectTwoObj = {};
			this.selectThreeObj = {};
			this.setOneLevel(this.options.oneLevelId, this.options.twoLevelId, this.options.threeLevelId);
		},
		initLayer: function() {
			var self = this;
			var all_html = [
				'<header class="iosselect-header">',
				'<h2 id="iosSelectTitle'+Sbindbox+'"></h2>',
				'<a href="javascript:void(0)" class="close">取消</a>',
				'<a href="javascript:void(0)" class="sure">确定</a>',
				'</header>',
				'<section class="iosselect-box">',
				'<div class="one-level-contain" id="oneLevelContain'+Sbindbox+'">',
				'<ul class="select-one-level" id="select-one-level'+Sbindbox+'">',
				'</ul>',
				'</div>',
				'<div class="two-level-contain" id="twoLevelContain'+Sbindbox+'">',
				'<ul class="select-two-level" id="select-two-level'+Sbindbox+'">',
				'</ul>',
				'</div>',
				'<div class="three-level-contain" id="threeLevelContain'+Sbindbox+'">',
				'<ul class="select-three-level" id="select-three-level'+Sbindbox+'">',
				'</ul>',
				'</div>',
				'</section>',
				'<hr class="cover-area1"/>',
				'<hr class="cover-area2"/>',
				'<button class="btn W85_ size4 HONG sure2" type=button>下一步</button>'
			].join('\r\n');
			this.iosSelectLayer = new Layer(all_html, {
				className: 'ios-select-widget-box ' + this.typeBox + (this.options.addClassName? ' ' + this.options.addClassName: '')
			});

			this.iosSelectTitleDom = document.querySelector('#iosSelectTitle'+Sbindbox);
			if (this.options.title) {
				this.iosSelectTitleDom.innerHTML = this.options.title;
			}

			if (this.options.headerHeight && this.options.itemHeight) {
				this.coverArea1Dom = document.querySelector('.cover-area1');
				this.coverArea1Dom.style.top = this.headerHeight + this.itemHeight * 3 + 'px';

				this.coverArea2Dom = document.querySelector('.cover-area2');
				this.coverArea2Dom.style.top = this.headerHeight + this.itemHeight * 4 + 'px';
			}

			this.oneLevelContainDom = document.querySelector('#oneLevelContain'+Sbindbox);
			this.twoLevelContainDom = document.querySelector('#twoLevelContain'+Sbindbox);
			this.threeLevelContainDom = document.querySelector('#threeLevelContain'+Sbindbox);



			this.oneLevelUlContainDom = document.querySelector('#select-one-level'+Sbindbox);
			this.twoLevelUlContainDom = document.querySelector('#select-two-level'+Sbindbox);
			this.threeLevelUlContainDom = document.querySelector('#select-three-level'+Sbindbox);

/*			this.oneLevelUlContainDom = document.querySelector('.select-one-level');
			this.twoLevelUlContainDom = document.querySelector('.select-two-level');
			this.threeLevelUlContainDom = document.querySelector('.select-three-level');
*/
			this.iosSelectLayer.el.querySelector('.layer').style.height = this.itemHeight * 7 + this.headerHeight + 'px';

			this.oneLevelContainDom.style.height = this.itemHeight * 7 + 'px';

			this.offsetTop = document.body.scrollTop;
			document.body.style.overflow = 'hidden';
			//window.scrollTo(0, 0);//2020-03-18

			this.scrollOne = new IScroll('#oneLevelContain'+Sbindbox, {
				probeType: 3,
				bounce: false
			});
			this.scrollOne.on('scrollStart', function() {
				Array.prototype.slice.call(self.oneLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
					if (v.classList.contains('at')) {
						v.classList.remove('at');
					} else if (v.classList.contains('side1')) {
						v.classList.remove('side1');
					} else if (v.classList.contains('side2')) {
						v.classList.remove('side2');
					}
				});
			});
			this.scrollOne.on('scroll', function() {
				var pa = Math.abs(this.y) / self.itemHeight;
				var plast = 1;

				plast = Math.round(pa) + 1;
				Array.prototype.slice.call(self.oneLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
					if (v.classList.contains('at')) {
						v.classList.remove('at');
					} else if (v.classList.contains('side1')) {
						v.classList.remove('side1');
					} else if (v.classList.contains('side2')) {
						v.classList.remove('side2');
					}
				});

				var pdom = self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 3) + ')');
				pdom.classList.add('at');

				self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 2) + ')').classList.add('side1');
				self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 1) + ')').classList.add('side2');
				self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 4) + ')').classList.add('side1');
				self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 5) + ')').classList.add('side2');
			});
			this.scrollOne.on('scrollEnd', function() {
				var pa = Math.abs(this.y) / self.itemHeight;
				var plast = 1;
				var to = 0;
				if (Math.ceil(pa) === Math.round(pa)) {
					to = Math.ceil(pa) * self.itemHeight;
					plast = Math.ceil(pa) + 1;
				} else {
					to = Math.floor(pa) * self.itemHeight;
					plast = Math.floor(pa) + 1;
				}
				self.scrollOne.scrollTo(0, -to, 0);

				var pdom = self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 3) + ')');

				Array.prototype.slice.call(self.oneLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
					if (v.classList.contains('at')) {
						v.classList.remove('at');
					} else if (v.classList.contains('side1')) {
						v.classList.remove('side1');
					} else if (v.classList.contains('side2')) {
						v.classList.remove('side2');
					}
				});

				pdom.classList.add('at');

				self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 2) + ')').classList.add('side1');
				self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 1) + ')').classList.add('side2');
				self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 4) + ')').classList.add('side1');
				self.oneLevelContainDom.querySelector('li:nth-child(' + (plast + 5) + ')').classList.add('side2');

				self.selectOneObj = iosSelectUtil.attrToData(pdom, plast);

				if (self.level > 1 && self.options.oneTwoRelation === 1) {
					self.setTwoLevel(self.selectOneObj.i);
				}
			});
			if (this.level >= 2) {
				this.twoLevelContainDom.style.height = this.itemHeight * 7 + 'px';
				this.scrollTwo = new IScroll('#twoLevelContain'+Sbindbox, {
					probeType: 3,
					bounce: false
				});
				this.scrollTwo.on('scrollStart', function() {
					Array.prototype.slice.call(self.twoLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
						if (v.classList.contains('at')) {
							v.classList.remove('at');
						} else if (v.classList.contains('side1')) {
							v.classList.remove('side1');
						} else if (v.classList.contains('side2')) {
							v.classList.remove('side2');
						}
					});
				});
				this.scrollTwo.on('scroll', function() {
					var pa = Math.abs(this.y) / self.itemHeight;
					var plast = 0;
					plast = Math.round(pa) + 1;

					var cdom = self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 3) + ')');

					Array.prototype.slice.call(self.twoLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
						if (v.classList.contains('at')) {
							v.classList.remove('at');
						} else if (v.classList.contains('side1')) {
							v.classList.remove('side1');
						} else if (v.classList.contains('side2')) {
							v.classList.remove('side2');
						}
					});

					cdom.classList.add('at');

					self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 2) + ')').classList.add('side1');
					self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 1) + ')').classList.add('side2');
					self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 4) + ')').classList.add('side1');
					self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 5) + ')').classList.add('side2');
				});
				this.scrollTwo.on('scrollEnd', function() {
					var pa = Math.abs(this.y) / self.itemHeight;
					var plast = 1;
					var to = 0;
					if (Math.ceil(pa) === Math.round(pa)) {
						to = Math.ceil(pa) * self.itemHeight;
						plast = Math.ceil(pa) + 1;
					} else {
						to = Math.floor(pa) * self.itemHeight;
						plast = Math.floor(pa) + 1;
					}
					self.scrollTwo.scrollTo(0, -to, 0);

					var cdom = self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 3) + ')');

					Array.prototype.slice.call(self.twoLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
						if (v.classList.contains('at')) {
							v.classList.remove('at');
						} else if (v.classList.contains('side1')) {
							v.classList.remove('side1');
						} else if (v.classList.contains('side2')) {
							v.classList.remove('side2');
						}
					});

					cdom.classList.add('at');

					self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 2) + ')').classList.add('side1');
					self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 1) + ')').classList.add('side2');
					self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 4) + ')').classList.add('side1');
					self.twoLevelContainDom.querySelector('li:nth-child(' + (plast + 5) + ')').classList.add('side2');

					self.selectTwoObj = iosSelectUtil.attrToData(cdom, plast);

					if (self.level === 3 && self.options.twoThreeRelation === 1) {
						self.setThreeLevel(self.selectOneObj.i, self.selectTwoObj.i);
					}

				});
			}
			if (this.level === 3) {
				this.threeLevelContainDom.style.height = this.itemHeight * 7 + 'px';
				this.scrollThree = new IScroll('#threeLevelContain'+Sbindbox, {
					probeType: 3,
					bounce: false
				});
				this.scrollThree.on('scrollStart', function() {
					Array.prototype.slice.call(self.threeLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
						if (v.classList.contains('at')) {
							v.classList.remove('at');
						} else if (v.classList.contains('side1')) {
							v.classList.remove('side1');
						} else if (v.classList.contains('side2')) {
							v.classList.remove('side2');
						}
					});
				});
				this.scrollThree.on('scroll', function() {
					var pa = Math.abs(this.y) / self.itemHeight;
					var plast = 0;
					plast = Math.round(pa) + 1;

					var ddom = self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 3) + ')');

					Array.prototype.slice.call(self.threeLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
						if (v.classList.contains('at')) {
							v.classList.remove('at');
						} else if (v.classList.contains('side1')) {
							v.classList.remove('side1');
						} else if (v.classList.contains('side2')) {
							v.classList.remove('side2');
						}
					});

					ddom.classList.add('at');

					self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 2) + ')').classList.add('side1');
					self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 1) + ')').classList.add('side2');
					self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 4) + ')').classList.add('side1');
					self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 5) + ')').classList.add('side2');
				});
				this.scrollThree.on('scrollEnd', function() {
					var pa = Math.abs(this.y) / self.itemHeight;
					var plast = 1;
					var to = 0;
					if (Math.ceil(pa) === Math.round(pa)) {
						to = Math.ceil(pa) * self.itemHeight;
						plast = Math.ceil(pa) + 1;
					} else {
						to = Math.floor(pa) * self.itemHeight;
						plast = Math.floor(pa) + 1;
					}
					self.scrollThree.scrollTo(0, -to, 0);

					var ddom = self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 3) + ')');

					Array.prototype.slice.call(self.threeLevelContainDom.querySelectorAll('li')).forEach(function(v, i, o) {
						if (v.classList.contains('at')) {
							v.classList.remove('at');
						} else if (v.classList.contains('side1')) {
							v.classList.remove('side1');
						} else if (v.classList.contains('side2')) {
							v.classList.remove('side2');
						}
					});

					ddom.classList.add('at');

					self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 2) + ')').classList.add('side1');
					self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 1) + ')').classList.add('side2');
					self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 4) + ')').classList.add('side1');
					self.threeLevelContainDom.querySelector('li:nth-child(' + (plast + 5) + ')').classList.add('side2');

					self.selectThreeObj = iosSelectUtil.attrToData(ddom, plast);
				});
			}
			this.closeBtnDom = this.iosSelectLayer.el.querySelector('.close');
			this.closeBtnDom.addEventListener('click', function(e) {
				document.body.style.overflow = 'auto';
				window.scrollTo(0, self.offsetTop);
			});

			this.selectBtnDom = this.iosSelectLayer.el.querySelector('.sure');
			this.selectBtnDom.addEventListener('click', function(e) {
				document.body.style.overflow = 'auto';
				//2020-03-18window.scrollTo(0, self.offsetTop);
				self.callback && self.callback(self.selectOneObj, self.selectTwoObj, self.selectThreeObj);
			});
			
			this.selectBtnDom2 = this.iosSelectLayer.el.querySelector('.sure2');
			this.selectBtnDom2.addEventListener('click', function(e) {
				document.body.style.overflow = 'auto';
				window.scrollTo(0, self.offsetTop);
				self.callback && self.callback(self.selectOneObj, self.selectTwoObj, self.selectThreeObj);
			});
			
			
		},
		getOneLevel: function() {
			if (iosSelectUtil.isArray(this.data[0])){
				return this.data[0];
			}
			else if (iosSelectUtil.isFunction(this.data[0])) {
				return this.data[0]();
			}
			else {
				throw new Error('data format error');
			}
		},
		setOneLevel: function(oneLevelId, twoLevelId, threeLevelId) {
			var oneLevelData = this.getOneLevel();
			if (!oneLevelId) {
				oneLevelId = oneLevelData[0]['i'];
			}
			var oneHtml = '';
			var self = this;
			var atIndex = 0;
			oneHtml += '<li></li>';
			oneHtml += '<li></li>';
			oneHtml += '<li></li>';
			oneLevelData.forEach(function(v, i, o) {
				//if (v.i === oneLevelId) {
				if (v.i == oneLevelId) {//supdes
					console.log('w－w－w.z-e-a-i.c－n');
					oneHtml += '<li ' + iosSelectUtil.attrToHtml(v) + ' class="at">' + v.v + '</li>';
					atIndex = i + 1 + 3;
				} else {
					oneHtml += '<li ' + iosSelectUtil.attrToHtml(v) + '>' + v.v + '</li>';
				}
			});
			oneHtml += '<li></li>';
			oneHtml += '<li></li>';
			oneHtml += '<li></li>';
			this.oneLevelUlContainDom.innerHTML = oneHtml;

			this.scrollOne.refresh();
			this.scrollOne.scrollToElement('li:nth-child(' + (atIndex - 3) + ')', 0);
			if (this.level >= 2) {
				this.setTwoLevel(oneLevelId, twoLevelId, threeLevelId);
			}

			var pdom = this.oneLevelContainDom.querySelector('.at');
			this.oneLevelContainDom.querySelector('li:nth-child(' + (atIndex - 1) + ')').classList.add('side1');
			this.oneLevelContainDom.querySelector('li:nth-child(' + (atIndex - 2) + ')').classList.add('side2');
			this.oneLevelContainDom.querySelector('li:nth-child(' + (atIndex + 1) + ')').classList.add('side1');
			this.oneLevelContainDom.querySelector('li:nth-child(' + (atIndex + 2) + ')').classList.add('side2');

			this.selectOneObj = iosSelectUtil.attrToData(pdom, atIndex);
		},
		getTwoLevel: function(oneLevelId) {
			var twoLevelData = [];
			if (iosSelectUtil.isArray(this.data[1])) {
				if (this.options.oneTwoRelation === 1) {
					this.data[1].forEach(function(v, i, o) {
						if (v['f'] === oneLevelId) {
							twoLevelData.push(v);
						}
					});
				} else {
					twoLevelData = this.data[1];
				}
				return twoLevelData;
			}
			else if (iosSelectUtil.isFunction(this.data[1])) {
				return this.data[1](oneLevelId);
			}
			else {
				throw new Error('data format error');
			}
		},
		setTwoLevel: function(oneLevelId, twoLevelId, threeLevelId) {
			var twoLevelData = this.getTwoLevel(oneLevelId);
			var atIndex = 0;
			if (!twoLevelId) {
				twoLevelId = twoLevelData[0]['i'];
			}
			var twoHtml = '';
			var self = this;
			twoHtml += '<li></li>';
			twoHtml += '<li></li>';
			twoHtml += '<li></li>';
			twoLevelData.forEach(function(v, i, o) {
				//if (v.i === twoLevelId)
				if (v.i == twoLevelId) {//supdes
					//console.log(1111111);
					twoHtml += '<li ' + iosSelectUtil.attrToHtml(v) + ' class="at">' + v.v + '</li>';
					atIndex = i + 1 + 3;
				} else {
					twoHtml += '<li ' + iosSelectUtil.attrToHtml(v) + '>' + v.v + '</li>';
				}
			});
			twoHtml += '<li></li>';
			twoHtml += '<li></li>';
			twoHtml += '<li></li>';
			
			this.twoLevelUlContainDom.innerHTML = twoHtml;
			this.scrollTwo.refresh();
			this.scrollTwo.scrollToElement(':nth-child(' + (atIndex - 3) + ')', 0);
			if (this.level === 3) {
				this.setThreeLevel(oneLevelId, twoLevelId, threeLevelId);
			}

			var cdom = self.twoLevelContainDom.querySelector('li:nth-child(' + atIndex + ')');
			cdom.classList.add('at');
			self.twoLevelContainDom.querySelector('li:nth-child(' + (atIndex - 1) + ')').classList.add('side1');
			self.twoLevelContainDom.querySelector('li:nth-child(' + (atIndex - 2) + ')').classList.add('side2');
			self.twoLevelContainDom.querySelector('li:nth-child(' + (atIndex + 1) + ')').classList.add('side1');
			self.twoLevelContainDom.querySelector('li:nth-child(' + (atIndex + 2) + ')').classList.add('side2');

			self.selectTwoObj = iosSelectUtil.attrToData(cdom, atIndex);
		},
		getThreeLevel: function(oneLevelId, twoLevelId) {
			var threeLevelData = [];
			if (iosSelectUtil.isArray(this.data[2])) {
				if (this.options.twoThreeRelation === 1) {
					this.data[2].forEach(function(v, i, o) {
						if (v['f'] === twoLevelId) {
							threeLevelData.push(v);
						}
					});
				} else {
					threeLevelData = this.data[2];
				}
				return threeLevelData;
			}
			else if (iosSelectUtil.isFunction(this.data[2])) {
				return this.data[2](oneLevelId, twoLevelId);
			}
			else {
				throw new Error('data format error');
			}
		},
		setThreeLevel: function(oneLevelId, twoLevelId, threeLevelId) {
			var threeLevelData = this.getThreeLevel(oneLevelId, twoLevelId);
			var atIndex = 0;
			if (!threeLevelId) {
				threeLevelId = threeLevelData[0]['i'];
			}
			var threeHtml = '';
			var self = this;
			threeHtml += '<li></li>';
			threeHtml += '<li></li>';
			threeHtml += '<li></li>';
			threeLevelData.forEach(function(v, i, o) {
				if (v.i === threeLevelId) {
					threeHtml += '<li ' + iosSelectUtil.attrToHtml(v) + ' class="at">' + v.v + '</li>';
					atIndex = i + 1 + 3;
				} else {
					threeHtml += '<li ' + iosSelectUtil.attrToHtml(v) + '>' + v.v + '</li>';
				}
			});
			threeHtml += '<li></li>';
			threeHtml += '<li></li>';
			threeHtml += '<li></li>';
			this.threeLevelUlContainDom.innerHTML = threeHtml;
			this.scrollThree.refresh();
			this.scrollThree.scrollToElement(':nth-child(' + (atIndex - 3) + ')', 0);

			var ddom = self.threeLevelContainDom.querySelector('li:nth-child(' + atIndex + ')');
			ddom.classList.add('at');
			self.threeLevelContainDom.querySelector('li:nth-child(' + (atIndex - 1) + ')').classList.add('side1');
			self.threeLevelContainDom.querySelector('li:nth-child(' + (atIndex - 2) + ')').classList.add('side2');
			self.threeLevelContainDom.querySelector('li:nth-child(' + (atIndex + 1) + ')').classList.add('side1');
			self.threeLevelContainDom.querySelector('li:nth-child(' + (atIndex + 2) + ')').classList.add('side2');

			self.selectThreeObj = iosSelectUtil.attrToData(ddom, atIndex);
		}
	}
	if (typeof module != 'undefined' && module.exports) {
		module.exports = IosSelect;
	} else if (typeof define == 'function' && define.amd) {
		define(function() {
			return IosSelect;
		});
	} else {
		window.IosSelect = IosSelect;
	}
})();