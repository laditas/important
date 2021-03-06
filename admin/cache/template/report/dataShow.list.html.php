 __layout|public:mini_layout|layout__
 <style type="text/css">
	.customer{
		width: 600px;
		float: left;
		margin-right: 100px;
		overflow-y:scroll; 
		max-height: 165px;
		clear: left;
		margin-bottom: 30px;
	}
	.day{
		float: left;
		margin-bottom: 30px;
		margin-left: 10px;
	}
	.customer td{
		width: 170px;
		text-align: center;
	}
	.day td{
		width: 110px;
		text-align: center;
	}
	.notice{
		float: left;
		text-align: center;
		clear: left;
		width: 100%;
	}
	.notice_title{
		width: 520px;
	}
	.title{
		border: 1px solid black;
		width: 515px;
		text-align: center;
		background-color: red;
		font-weight: bold;
	}
	.span{
		width: 520px;
		float: left;
	}
	.span div{
		width: 171px;
		float: left;
		clear: right;
		text-align: center;
		border: 1px solid black;
	}
	.title2{
		float: left;
		border: 1px solid red;
		clear: left;
		width: 515px;
		text-align: center;
		background-color: red;
		font-weight: bold;
	}
	.span2{
		width: 520px;
		float: left;
		clear: left;
	}
	.span2 div{
		width: 171px;
		float: left;
		clear: right;
		text-align: center;
		border: 1px solid black;
	}
 </style>
 	<div class="notice_title">
 		<div class="title">私海客户强开提前提醒<a href="javascript:void(0)" onclick="privateCustomers()">(<?php echo $this->_var['UnCooperationCustomerRemind']['num']; ?>)</a></div>
 		<div class="span"><div>客户名称</div><div>当前状态</div><div>距强开天数</div></div>
 	</div>

 <div class="customer">
 	<table border="1"  cellspacing="0" cellpadding="0">
 		<!-- <th colspan="3" bgcolor="#FF0000">私海客户强开提前提醒<a href="javascript:void(0)" onclick="privateCustomers()">(<?php echo $this->_var['UnCooperationCustomerRemind']['num']; ?>)</a></th> -->
 		<!-- <tr><td>客户名称</td><td>当前状态</td><td>距强开天数</td></tr> -->
 		<?php $_from = $this->_var['UnCooperationCustomerRemind']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->_push_vars('', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['v']):
?>
 		<tr><td><?php echo $this->_var['v']['c_name']; ?></td><td><?php echo $this->_var['v']['type']; ?></td><td><?php echo $this->_var['v']['remind_day']; ?>天</td></tr>
         <?php endforeach; endif; unset($_from); ?><?php $this->_pop_vars();; ?> 
 	</table>
 </div>
<div class="day">
	<table border="1"  cellspacing="0" cellpadding="0">
 		<th colspan="4" bgcolor="#FF0000">每日数据汇总</th>
 		<tr>
 			<td>今日新增客户数：</td>
 			<td><a href="javascript:void(0)" onclick="new_clients_num()"><?php echo $this->_var['dailyDataSummary']['new_clients_num']['num']; ?></a></td>
 			<td>今日销售吨数：</td>
 			<td><a href="javascript:void(0)" onclick="today_sale_num()"><?php echo $this->_var['dailyDataSummary']['today_sale_num']['num']; ?></a></td>
		</tr>
 		<tr>
 			<td>今日跟进客户数：</td>
 			<td><span class="todayFollowCus"><a href="javascript:void(0)" onclick="getTodayFollowCustomers()"><?php echo $this->_var['dailyDataSummary']['getTodayFollowCustomers']['num']; ?></a></span></td>
 			<td>今日采购吨数：</td>
 			<td><a href="javascript:void(0)" onclick="today_buy_num()"><?php echo $this->_var['dailyDataSummary']['today_buy_num']['num']; ?></a></td>
		</tr>
 		<tr>
 			<td>今日客户成交数：</td>
 			<td><a href="javascript:void(0)" onclick="new_clients_order_num()"><?php echo $this->_var['dailyDataSummary']['new_clients_order_num']['num']; ?></a></td>
 			<td>今日利润：</td>
 			<td><a href="javascript:void(0)" onclick="today_profit()"><?php echo $this->_var['dailyDataSummary']['today_profit']['profit']; ?></a></td>
		</tr>
 		<tr>
 			<td>今日电话数：</td>
 			<td><?php echo $this->_var['dailyDataSummary']['today_phone_num']; ?></td>
 			<td>目标完成率：</td>
 			<td><?php echo $this->_var['dailyDataSummary']['month_profit_rate']; ?></td></tr>
 	</table>
</div>
<div>
	<div class="title2">已合作客户强开提前提醒(<?php echo $this->_var['cooperationCustomerRemindTotal']; ?>)</div>
	<div class="span2"><div>客户名称</div><div>当前状态</div><div>距强开天数</div></div>
</div>
 <div class="customer">
 	<table border="1"  cellspacing="0" cellpadding="0">
 		<!-- <th colspan="3" bgcolor="#FF0000" >已合作客户强开提前提醒(<?php echo $this->_var['cooperationCustomerRemindTotal']; ?>)</th> -->
 		<!-- <tr><td>客户名称</td><td>当前状态</td><td>距强开天数</td></tr> -->
 		<?php $_from = $this->_var['cooperationCustomerRemind']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->_push_vars('', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['v']):
?>
 		<tr><td><?php echo $this->_var['v']['c_name']; ?></td><td><?php echo $this->_var['v']['cooperation_days']; ?></td><td><?php echo $this->_var['v']['remind_day']; ?>天</td></tr>
         <?php endforeach; endif; unset($_from); ?><?php $this->_pop_vars();; ?> 
 	</table>
 </div>
<div class="day">
	<table border="1"  cellspacing="0" cellpadding="0">
 		<th colspan="4" bgcolor="#FF0000">收、付款，进、销项提前提醒</th>
 		<tr><td>未收欠款：</td><td><a href="javascript:void(0)" onclick="sale_collection()"><?php echo $this->_var['getInvoiceData']['sale_collection']['num']; ?></a></td>
 		<tr><td>未付欠款：</td><td><a href="javascript:void(0)" onclick="pur_collection()"><?php echo $this->_var['getInvoiceData']['pur_collection']['num']; ?></a></td>
 		<tr><td>未开进项：</td><td><a href="javascript:void(0)" onclick="pur_unBilling()"><?php echo $this->_var['getInvoiceData']['pur_unBilling']['num']; ?></a></td>
 		<tr><td>未开销项：</td><td><a href="javascript:void(0)" onclick="sale_unBilling()"><?php echo $this->_var['getInvoiceData']['sale_unBilling']['num']; ?></a></td>
 	</table>
</div>
<div class="day">
	<table border="1"  cellspacing="0" cellpadding="0">
 		<th colspan="4" bgcolor="#FF0000">战队配资状况</th>
 		<tr><td>战队名称：</td><td><?php echo $this->_var['team_capital']['name']; ?></td>
 		<tr><td>总配资额度：</td><td><?php echo $this->_var['team_capital']['total_money']; ?></td>
 		<tr><td>已用额度：</td><td><?php echo $this->_var['team_capital']['used_money']; ?></td>
 		<tr><td>可用额度：</td><td class="available_money"><?php echo $this->_var['team_capital']['available_money']; ?></td>
 	</table>
</div>
<div class="notice">
	<table border="1"  cellspacing="0" cellpadding="0">
 		<th colspan="5" bgcolor="#FF0000">公告栏</th>
 		<tr><td width="80">创建日期</td><td width="100">创建者</td><td width="150">标题</td><td width="400">公告内容</td><td width="100">附件</td></tr>
 		<?php $_from = $this->_var['notice']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->_push_vars('', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['v']):
?>
 		<tr><td width="150"><?php echo $this->_var['v']['input_time']; ?></td><td><?php echo $this->_var['v']['input_admin']; ?></td><td><?php echo $this->_var['v']['title']; ?></td><td><?php echo $this->_var['v']['content']; ?></td><td><a href="" onclick="addWork(<?php echo $this->_var['v']['id']; ?>)">查看详情</a></td></tr>
         <?php endforeach; endif; unset($_from); ?><?php $this->_pop_vars();; ?>     

 	</table>
</div>
<input name="sale_unBilling" type="hidden" value="<?php echo $this->_var['getInvoiceData']['sale_unBilling']['o_id']; ?>">
<input name="pur_unBilling" type="hidden" value="<?php echo $this->_var['getInvoiceData']['pur_unBilling']['o_id']; ?>">
<input name="sale_collection" type="hidden" value="<?php echo $this->_var['getInvoiceData']['sale_collection']['o_id']; ?>">
<input name="pur_collection" type="hidden" value="<?php echo $this->_var['getInvoiceData']['pur_collection']['o_id']; ?>">
<input name="todayFollowCustomersNums" type="hidden" value="<?php echo $this->_var['todayFollowCustomersNums']; ?>">
<input name="today_sale_num" type="hidden" value="<?php echo $this->_var['dailyDataSummary']['today_sale_num']['o_id']; ?>">
<input name="today_buy_num" type="hidden" value="<?php echo $this->_var['dailyDataSummary']['today_buy_num']['o_id']; ?>">
<input name="getTodayFollowCustomers" type="hidden" value="<?php echo $this->_var['dailyDataSummary']['getTodayFollowCustomers']['id']; ?>">
<input name="new_clients_num" type="hidden" value="<?php echo $this->_var['dailyDataSummary']['new_clients_num']['c_id']; ?>">
<input name="new_clients_order_num" type="hidden" value="<?php echo $this->_var['dailyDataSummary']['new_clients_order_num']['c_id']; ?>">
<input name="today_profit" type="hidden" value="<?php echo $this->_var['dailyDataSummary']['today_profit']['o_id']; ?>">
<input name="privateCustomers" type="hidden" value="<?php echo $this->_var['UnCooperationCustomerRemind']['c_id']; ?>">

<script type="text/javascript">
$(document).ready(function(){ 	
	var todayFollowCustomersNums = $("input[name='todayFollowCustomersNums']").val();
	if($(".todayFollowCus").text() < todayFollowCustomersNums){
		$('.todayFollowCus').css('color','#ff4343');
	}
	if($(".available_money").text() <= 0){
		$('.available_money').css('color','#ff4343');
	}
});
mini.parse();
//查看销售未开票信息
function sale_unBilling(){
	var o_ids = $("input[name='sale_unBilling']").val();
	// console.log(o_ids);
	if(o_ids == "('')"){
		return;
	}
	mini.open({
	  url: "/product/order/init?moreChoice=1&o_ids="+o_ids,
	  showMaxButton:true,
	  title: "未开销项", 
	  width: 1200, height:430
	});   
}
//查看采购未开票信息
function pur_unBilling(){
	var o_ids = $("input[name='pur_unBilling']").val();
	if(o_ids == "('')"){
		return;
	}
	mini.open({
	  url: "/product/order/purchase?moreChoice=1&o_ids="+o_ids,
	  showMaxButton:true,
	  title: "未开进项", 
	  width: 1200, height:430
	});   
}
//查看销售欠款未收
function sale_collection(){
	var o_ids = $("input[name='sale_collection']").val();
	if(o_ids == "('')"){
		return;
	}
	mini.open({
	  url: "/product/order/init?moreChoice=1&o_ids="+o_ids,
	  showMaxButton:true,
	  title: "未收欠款", 
	  width: 1200, height:430
	});   
}
//查看采购欠款未付
function pur_collection(){
	var o_ids = $("input[name='pur_collection']").val();
	if(o_ids == "('')"){
		return;
	}
	mini.open({
	  url: "/product/order/purchase?moreChoice=1&o_ids="+o_ids,
	  showMaxButton:true,
	  title: "未付欠款", 
	  width: 1200, height:430
	});   
}

//查看公告详情
function addWork(id){
	// console.log(id);
	mini.open({
		url: "/report/dataShow/info?id="+id,
		showMaxButton:true,
		title: '查看详情', width: 1000, height: 700, 
		ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});	
}
//查看今日销售吨数相关的订单
function today_sale_num(){
	var o_ids = $("input[name='today_sale_num']").val();
	if(o_ids == "('')"){
		return;
	}
	mini.open({
	  url: "/product/order/init?moreChoice=1&o_ids="+o_ids,
	  showMaxButton:true,
	  title: "销售订单", 
	  width: 1200, height:430
	});   
}
//查看今日采购吨数相关的订单
function today_buy_num(){
	var o_ids = $("input[name='today_buy_num']").val();
	if(o_ids == "('')"){
		return;
	}
	mini.open({
	  url: "/product/order/purchase?moreChoice=1&o_ids="+o_ids,
	  showMaxButton:true,
	  title: "采购订单", 
	  width: 1200, height:430
	});   
}
//查看今日跟进客户列表
function getTodayFollowCustomers(){
	var ids = $("input[name='getTodayFollowCustomers']").val();
	if(ids == "('')"){
		return;
	}
	mini.open({
	  url: "/user/follow/init?moreChoice=1&ids="+ids,
	  showMaxButton:true,
	  title: "客户跟进", 
	  width: 1200, height:430
	});   
}
//查看今日新增客户
function new_clients_num(){
	var cids = $("input[name='new_clients_num']").val();
	if(cids == "('')"){
		return;
	}
	mini.open({
	  url: "/user/customer/customerList?moreChoice=1&cids="+cids,
	  showMaxButton:true,
	  title: "今日新增客户列表", 
	  width: 1200, height:430
	});   
}
//查看今日客户成交数
function new_clients_order_num(){
	var cids = $("input[name='new_clients_order_num']").val();
	// console.log(cids);return;
	if(cids == "('')"){
		return;
	}
	mini.open({
	  url: "/user/customer/customerList?moreChoice=1&cids="+cids,
	  showMaxButton:true,
	  title: "今日成交客户列表", 
	  width: 1200, height:430
	});   
}
//查看今日利润
function today_profit(){
	var o_ids = $("input[name='today_profit']").val();
	// console.log(o_ids);return;
	if(o_ids == "('')"){
		return;
	}
	mini.open({
	  url: "/product/order/init?moreChoice=1&o_ids="+o_ids,
	  showMaxButton:true,
	  title: "销售订单", 
	  width: 1200, height:430
	});   
}

//查看私海客户强开点击弹窗
function privateCustomers(){
	var cids = $("input[name='privateCustomers']").val();
	if(cids == "('')"){
		return;
	}
	mini.open({
	  url: "/user/customer/customerList?moreChoice=1&cids="+cids,
	  showMaxButton:true,
	  title: "私海客户列表", 
	  width: 1200, height:430
	});   
}
</script> 