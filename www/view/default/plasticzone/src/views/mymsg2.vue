<template>
<div class="buyWrap" style="padding: 45px 0 0 0;">
	<div style="position: fixed; top: 0; left: 0; width: 100%; z-index: 10;">
		<header id="bigCustomerHeader">
			<a class="back" href="javascript:window.history.back();"></a>
			我的消息
		</header>
	</div>

	<ul class="mymsg2ul">
		<li v-for="r in resMsg">
			<div class="myreleaseInfo">
				<div class="myreleasetxt3" style="margin: 0;">
					<div class="mymsgwrap">
						{{r.content}}
					</div>
				</div>
			</div>
		</li>
	</ul>
</div>
</template>
<script>
export default{
	data: function() {
		return {
			resMsg: []
		}
	},
	methods: {

	},
	mounted: function() {
		var _this = this;
		try {
			var piwikTracker = Piwik.getTracker("http://wa.myplas.com/piwik.php", 2);
			piwikTracker.trackPageView();
		} catch(err) {

		}
		$.ajax({
			url: version + "/myInfo/getRobotMsg",
			type: 'post',
			data: {
				page: 1,
				token: window.localStorage.getItem("token"),
				size: 100
			},
			headers: {
				'X-UA': window.localStorage.getItem("XUA")
			},
			dataType: 'JSON'
		}).then(function(res) {
			if(res.err == 0) {
				_this.resMsg = res.data;
			}
		}, function() {

		});
	}
}
</script>