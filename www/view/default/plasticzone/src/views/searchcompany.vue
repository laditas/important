<template>
<div class="buyWrap" style="padding: 45px 0 0 0;">
	<div style="position: fixed; top: 0; left: 0; width: 100%; z-index: 10;">
		<header id="bigCustomerHeader">
			<a class="back" href="javascript:window.history.back();"></a>
			查别人
		</header>
	</div>
	<div class="searchcompany"></div>
	<h2 style="text-align: center; font-size: 30px; color: #333333; margin: 15px 0;">精准查询</h2>
	<p style="text-align: center;">企业名称查询<br>自动关联企业相关数据</p>
	<div class="searchfname">
		<div class="searchfnameWrap" style="margin: 0;">
		<div style=" width: auto; margin-right: 80px;">
			<input type="text" v-model="fname" style=" width: 100%; line-height: 30px; float: left; border: none; padding: 5px 7px; background: none; font-size: 12px;" placeholder="请输入企业全称" />
			<div class="searchbtn" v-on:click="search">查授信额度</div>
		</div>	
		</div>
	</div>
	<ul class="searchli">
		<li v-for="c in creditli">
			<router-link :to="{name:'credit2',params:{id:c.contact_id}}">{{c.c_name}}</router-link></li>
		</li>	
	</ul>
</div>
</template>
<script>
export default{
	data: function() {
		return {
			fname:"",
			creditli:[]
		}
	},
	methods:{
		search:function(){
			var _this=this;
			$.ajax({
				type: "post",
				url: version+'/credit/creditCertificate',
				data: {
					token: window.localStorage.getItem("token"),
					type:2,
					page:1,
					fname:_this.fname
				},
				headers: {
					'X-UA': window.localStorage.getItem("XUA")
				},
				dataType: 'JSON'
			}).then(function(res) {
				if(res.err==0){
					_this.creditli=res.data;			
				}else{
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
				}
			}, function() {
	
			});
		}
	},
	mounted: function() {
		this.creditli=[];
		this.fname="";
		try {
		    var piwikTracker = Piwik.getTracker("http://wa.myplas.com/piwik.php", 2);
		    piwikTracker.trackPageView();
		} catch( err ) {
			
		}
	}
}
</script>