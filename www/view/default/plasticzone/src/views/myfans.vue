<template>
<div class="buyWrap" style="padding: 0 0 70px 0;">
	<header id="bigCustomerHeader">
		<a class="back" href="javascript:window.history.back();"></a>
		我的粉丝
	</header>
	<ul id="nameUl">
		<li v-show="condition" v-for="n in name">
			<div style="width: 55px; height: 55px; float: left; position: relative;">
				<div class="avator">
					<img v-bind:src="n.user_id.thumb">
				</div>
				<i class="iconV" v-bind:class="{'v1':n.user_id.is_pass==1,'v2':n.user_id.is_pass==0}"></i>
			</div>
			<div class="nameinfo">
				<router-link :to="{name:'personinfo',params:{id:n.user_id.user_id}}">
					<p class="first"><i class="icon wxGs"></i>{{n.user_id.c_name}}</p>
					<p class="second"><i class="icon wxName"></i>{{n.user_id.name}}<i class="icon wxMobile"></i>{{n.user_id.mobile}}</p>
					<p class="second">&nbsp;发布供给：<b style="font-weight: normal;">{{n.user_id.sale}}</b>条 发布求购：<b style="font-weight: normal;">{{n.user_id.buy}}</b>条</p>
				</router-link>
			</div>
		</li>
		<li v-show="!condition" style="text-align: center;">
			没有相关数据
		</li>
	</ul>
	<footerbar></footerbar>
</div>
</template>
<script>
import footer from "../components/footer";
export default{
	components: {
		'footerbar': footer
	},
	data: function() {
		return {
			name: [],
			page: 1,
			condition: true
		}
	},
	mounted: function() {
		var _this = this;
		try {
		    var piwikTracker = Piwik.getTracker("http://wa.myplas.com/piwik.php", 2);
		    piwikTracker.trackPageView();
		} catch( err ) {
			
		}
		
		$.ajax({
			type:"post",
			url: version+"/myInfo/getMyFuns",
			data:{
				page: _this.page,
				size: 100,
				type: 1,
				token: window.localStorage.getItem("token")				
			},
			headers: {
				'X-UA': window.localStorage.getItem("XUA")
			},
			dataType: 'JSON'
		}).done(function(res){
			if(res.err == 2) {
				_this.condition = false;
			} else if(res.err == 1) {
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
			} else {
				_this.condition = true;
				_this.name = res.data;
			}
			
		}).fail(function(res){
			
		}).always(function(){
			
		});
		
	}
}
</script>