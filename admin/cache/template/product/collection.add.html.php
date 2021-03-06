__layout|public:mini_layout|layout__
<div id="admInfo" title="" style="padding:10px;" showModal="true" allowResize="true" allowDrag="true">
  <div id="addForm" class="form" >
		<table style="width:100%;">
		<input class="mini-hidden" value='<?php echo $_SESSION["collection_token"]?>' name="collection_token">
		<input class="mini-hidden" name="order_type"  value="<?php echo $this->_var['type']; ?>"/>
		<input class="mini-hidden" name="order_sn"  value="<?php echo $this->_var['order_sn']; ?>"/>
		<input class="mini-hidden" name="uncollected_price"  value="<?php echo $this->_var['uncollected_price']; ?>"/>
		<input class="mini-hidden" name="c_id"  value="<?php echo $this->_var['c_id']; ?>"/>
		<input class="mini-hidden" name="o_id"  value="<?php echo $this->_var['o_id']; ?>"/>
		<!-- 审核所需字段 -->
		<input class="mini-hidden" name="id"  value="<?php echo $this->_var['id']; ?>"/>
		<input class="mini-hidden" name="finance"  value="<?php echo $this->_var['finance']; ?>"/>
		<!-- $price 是保留字段，用于收付款改掉订单总价是的显示结果-->

			<tr>
				<?php if ($this->_var['type'] == '1'): ?>
				<td>收款主题</td>
				<?php else: ?>
				<td>付款主题</td>
				<?php endif; ?>
				<td><?php echo $this->_var['company_account'][$this->_var['order_name']]; ?></td>
				<input name="title" class="mini-hidden" maxlength="12" value="<?php echo $this->_var['order_name']; ?>" style="width:150px;"allowInput="false"/>
			</tr>
			<tr>
				<td>客户名称</td>
				<td><input name="" class="mini-textbox" maxlength="12" value="<?php echo $this->_var['c_name']; ?>" style="width:150px;"allowInput="false"/></td>
			</tr>

			<tr>
				<td>合同金额</td>
				<td><input name="total_price" class="mini-textbox" maxlength="12" <?php if ($this->_var['total_price']): ?>value="<?php echo $this->_var['total_price']; ?>"<?php else: ?>value="<?php echo $this->_var['price']; ?>"<?php endif; ?> style="width:150px;" allowInput="false"/></td>
			</tr>
			<?php if ($this->_var['finance']): ?>
			<tr>
				<td><?php if ($this->_var['type'] == '1'): ?>收款日期<?php else: ?>付款日期<?php endif; ?><span style="color:red">*</span></td>
				<td><input name="payment_time" class="mini-datepicker" value="<?php echo date('Y-m-d H:i:s'); ?>" style="width:150px;" format="yyyy-MM-dd" showTime="true" required="true"/></td>
			</tr>
			<tr>
				<td><?php if ($this->_var['type'] == '1'): ?>未收款金额<?php else: ?>未付款金额<?php endif; ?></td>
				<td><input  name="uncollected_price"class="mini-textbox" maxlength="12" value="<?php echo $this->_var['u_price']; ?>" style="width:150px;" allowInput="false"/></td>
			</tr>
			<tr>
				<td>申请金额<span style="color:red">*</span></td>
				<td><input name="collected_price" id="check_price" class="mini-textbox" maxlength="12" value="<?php echo $this->_var['c_price']; ?>" style="width:150px;" required="true"/></td>
			</tr>
			<?php else: ?>
			<tr>
				<td><?php if ($this->_var['type'] == '1'): ?>未收款金额<?php else: ?>未付款金额<?php endif; ?></td>
				<td><input name="un_price" class="mini-textbox" maxlength="12" <?php if ($this->_var['uncollected_price'] == ''): ?>value="<?php echo $this->_var['price']; ?>"<?php else: ?>value="<?php echo $this->_var['uncollected_price']; ?>"<?php endif; ?> style="width:150px;" allowInput="false"/></td>
			</tr>
			<tr>
				<td><?php if ($this->_var['type'] == '1'): ?>收款金额<?php else: ?>付款金额<?php endif; ?><span style="color:red">*</span></td>
				<td><input name="collected_price" class="mini-textbox" maxlength="12" <?php if ($this->_var['uncollected_price'] == ''): ?>value="<?php echo $this->_var['price']; ?>"<?php else: ?>value="<?php echo $this->_var['uncollected_price']; ?>"<?php endif; ?> style="width:150px;" required="true"/></td>
			</tr>
			<?php endif; ?>
			<tr>
				<td>交易员</td>
				<td><input name="input_admin" class="mini-textbox" maxlength="12" value="<?php echo $this->_var['input_admin']; ?>" style="width:150px;"allowInput="false"/></td>
			</tr>
			<tr>
				<td>交易方式<span style="color:red">*</span></td>
				<td><input name="pay_method" class="mini-combobox" data='<?php echo setMiniConfig($this->_var['pay_method']); ?>' textField="name" value="<?php echo $this->_var['p_method']; ?>"valueField="id" style="width:90px;" required="true"/></td>
			</tr>
			<?php if ($this->_var['finance']): ?>
			<tr>
				<td>交易账户<span style="color:red">*</span></td>
				<td><input name="account" onvaluechanged="changeaccount" class="mini-combobox" data='<?php echo setMiniConfig($this->_var['company_account']); ?>' textField="name" valueField="id" style="width:90px;" required="true"/></td>
			</tr>
			<tr id='account' >
				<td>账户余额</td>
				<td><input name="account_price" id="account_price" value="" textField="text" valueField="id" class="mini-textbox"    allowInput="false"  style="width:89px"/></td>
			</tr>

			<tr>
				<td>附件</td>
				<td>
					<input id="file_url"  name="accessory" class="mini-textbox" value="" style="width:200px"/>
					<input id="upfileId" type="file" name="upFile" style="width:105px" onChange="fileUpload();">
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td>备注</td>
				<td><input name="remark" class="mini-textarea" value="<?php echo $this->_var['remark']; ?>"style="width:300px; height:50px;"/></td>
			</tr>
			<tr>
			<td style="text-align:center;padding-top:15px;padding-right:20px; margin-top:20px;" colspan="2">
				<?php if ($this->_var['finance']): ?>
					<a class="mini-button" iconCls="icon-save" onclick="submitForm">保存</a>
				<?php else: ?>
					<a class="mini-button" iconCls="icon-ok" onclick="submitForm">提交</a>
					<a class="mini-button" iconCls="icon-cancel" onclick="onCancel">关闭</a>
				<?php endif; ?>

				<span id="returnMsg" style="padding-left:5px; color:#F00;"></span>
			</td>
			</tr>
		</table>
	</div>
</div>
<script src="__JS__/jquery/jquery.upload.js" type="text/javascript"></script>
<script type="text/javascript">

mini.parse();
//搜索或刷新
var admInfo = mini.get("admInfo");
var form = new mini.Form("#addForm");
//默认隐藏账户余额
$('#account').hide();
//增加后提交数据(保存)
function submitForm() {
	form.validate();
  	if (form.isValid() == false) return;
	//提交数据
	var o = form.getData();
	//输入的值必须大于0，小于总金额
	if(parseFloat(o.collected_price) <= 0 || parseFloat(o.collected_price) >parseFloat(o.total_price)){
		alert("请输入正确的数值");
		return;
	}
	//uncollected_price不为空，就不是第一次申请审核，输入值得小于未收付款金额
	if (parseFloat(o.uncollected_price) >'') {
		if (parseFloat(o.collected_price) >parseFloat(o.uncollected_price)) {
			alert("请输入正确的数值");return;
		}
	}
	//un_price不为空，就不是第一次申请，输入值得小于未收付款金额
	if(parseFloat(o.un_price) >''){
		if (parseFloat(o.collected_price >o.un_price)) {alert("请输入正确的数值");return;}

	}

	var type="<?php echo $this->_var['type']; ?>";
	var finance='<?php echo $this->_var['finance']; ?>';
	if (finance==1) {
		var check_price = mini.get('check_price').value;
		if (type == 2 &&(parseFloat(check_price)>parseFloat(account_price))) {
			alert('账户余额不足，不能付款');
			return false;
		}
	}
	var json = mini.encode(o);

 	utils.ajax('/product/collection/chkCollecteprice',{data:json,async:false},function(data){
        if (data.err!='0') return alert('申请付款已提交,请关闭窗口重试!');
		form.loading("数据提交中，请稍后......");
		utils.ajax('/product/collection/ajaxSave',{data:json},function(data){
			form.unmask();
			$("#returnMsg").text(data.msg);
			if(data.err=='0'){
				CloseWindow("save");
			}else{
				return false;
			}
		},"POST","json");

 	},"POST","json");

}

//选择账户时显示不同账户余额
var account_price =0;
function changeaccount(e){
	var val = e.value;//1,2
	$.post('/product/collection/changeaccount',{data:val},function(data){
		form.unmask();
		if(data.err!='0'){
			account_price = data.sum;
			mini.get('account_price').setValue(data.sum);
		}else{
			return false;
		}
	},'json');

	$('#account').show();
}


//上传附件
function fileUpload() {
	$.ajaxFileUpload({
		url:'/system/sysUpload/upload',
			secureuri:false,
			fileElementId:'upfileId',
			dataType:'json',
			success: function (data) {
				if(data.error=='0'){
					mini.get("file_url").setValue(data.url);
				}
			},
			error: function (data, status, e){
				$("#picResult").html(e);
			}
		})
	return false;
}

function GetData() {
	var row = form.getSelected();
	return row;
}

function CloseWindow(action) {
	if (window.CloseOwnerWindow) return window.CloseOwnerWindow(action);
	else window.close();
}
function onCancel(e) {
	CloseWindow("cancel");
}

</script>