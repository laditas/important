<template>
<div class="buyWrap">
	<header id="bigCustomerHeader">
		<a class="back" href="javascript:window.history.back();"></a>
		重置密码
	</header>
	<div class="registerWrap">
		<div class="registerTitle">
			<i class="arrowLeft"></i>帐号信息
		</div>		
		<div class="registerInput2">
			<div class="registerBox2">
				<strong><span>*</span>手机号码:</strong>
				<input type="tel" maxlength="11" v-model="mobile" placeholder="请输入您的手机号码">
			</div>
			<div class="registerBox2"><strong><span>*</span>设置密码:</strong>
				<input type="password" maxlength="20" v-model="password" placeholder="请输入新密码">
			</div>
			<div class="registerBox2"><strong><span>*</span>手机验证码:</strong>
				<input maxlength="6" type="tel" v-model="code" placeholder="请输入收到的验证码">
				<button class="validCode" v-on:click="sendCode">{{validCode}}</button>
			</div>
		</div>
	</div>
	<div class="registerBtn" style="margin: 40px 0 0 0;">
		<input type="button" v-on:click="resetPwd" value="重置" />
	</div>
</div>
</template>
<script>
export default{
	data: function() {
		return {
			mobile: "",
			password: "",
			code: "",
			times: 60,
			validCode: "获取验证码"
		}
	},
	methods: {
		sendCode: function() {
			var _this = this;
			if(this.mobile) {
				$.ajax({
					url: version+'/user/sendMsg',
					type: 'post',
					data: {
						mobile: _this.mobile,
						type: 1
					},
					headers: {
						'X-UA': window.localStorage.getItem("XUA")
					},
					dataType: 'JSON'
				}).then(function(res) {
					if(res.err == 0) {
						weui.alert(res.msg, {
							title: '塑料圈通讯录',
							buttons: [{
								label: '确定',
								type: 'parimary',
								onClick: function() {

								}
							}]
						});
						var countStart = setInterval(function() {
							_this.validCode = _this.times-- + '秒后重发';
							if(_this.times < 0) {
								clearInterval(countStart);
								_this.validCode = "获取验证码";
							}
						}, 1000);
					} else if(res.err == 1) {
						weui.alert(res.msg, {
							title: '塑料圈通讯录',
							buttons: [{
								label: '确定',
								type: 'parimary',
								onClick: function() {

								}
							}]
						});
					}
				}, function() {

				});
			} else {
				weui.alert("请填写手机号和密码", {
					title: '塑料圈通讯录',
					buttons: [{
						label: '确定',
						type: 'parimary',
						onClick: function() {
	
						}
					}]
				});
			}
		},
		resetPwd: function() {
			var _this = this;
			if(this.mobile && this.password && this.code) {
				$.ajax({
					url: version+'/user/finfMyPwd',
					type: 'post',
					data: {
						mobile: _this.mobile,
						password: _this.password,
						code: _this.code
					},
					headers: {
						'X-UA': window.localStorage.getItem("XUA")
					},
					dataType: 'JSON'
				}).then(function(res) {
					if(res.err == 0) {
						weui.alert(res.msg, {
							title: '塑料圈通讯录',
							buttons: [{
								label: '确定',
								type: 'parimary',
								onClick: function() {
									_this.$router.push({
										name: 'login'
									});
								}
							}]
						});
					} else if(res.err == 1) {
						weui.alert(res.msg, {
							title: '塑料圈通讯录',
							buttons: [{
								label: '确定',
								type: 'parimary',
								onClick: function() {

								}
							}]
						});
					}
				}, function() {

				});
			} else {
				weui.alert("请填写手机号、密码和验证码", {
					title: '塑料圈通讯录',
					buttons: [{
						label: '确定',
						type: 'parimary',
						onClick: function() {

						}
					}]
				});
			}
		}
	},
	mounted: function() {
		try {
		    var piwikTracker = Piwik.getTracker("http://wa.myplas.com/piwik.php", 2);
		    piwikTracker.trackPageView();
		} catch( err ) {
			
		}
	}

}
</script>