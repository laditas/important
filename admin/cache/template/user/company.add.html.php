__layout|public:mini_layout|layout__
<style type="text/css">
.hidden {display:none;}
</style>
<div style="padding:5px;">
	<div id="infoForm" class="form">
		<div class="mini-tabs" activeIndex="0" plain="false">
			<div title="基本信息">
				<input class="mini-hidden" name="ctype" value="<?php echo $this->_var['ctype']; ?>"/>
				<input class="mini-hidden" name="user_id" value="<?php echo $this->_var['user']['user_id']; ?>"/>
				<input class="mini-hidden" name="is_pur" value="<?php echo $this->_var['is_pur']; ?>"/>
				<input class="mini-hidden" id="merge_three" name ="merge_three" value="1">
				<table width="100%">
					<caption><strong>基本信息</strong></caption>
					<tr>
						<td style="width:100px;"> 客户名称：<font style="color:red;">*</font></td>
						<td style="width:200px;">
							<input id="c_name" name="c_name" class="mini-textbox"  maxlength="50"  style="width:200px;" required="true" value="<?php echo $this->_var['user']['c_name']; ?>"/>
						</td>
						<td style="width:50px;"><button onClick="checkUnique()">查重</button></td>
						<td><span id="chkMsg" style="padding-left:5px; color:#F00;"></span></td>
					</tr>
					<tr>
						<td>法人姓名：</td>
						<td><input name="legal_person" class="mini-textbox" style="width:70px;" maxlength="6" value="<?php echo $this->_var['user']['legal_person']; ?>"/></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>法人身份证：</td>
						<td><input name="legal_idcard" class="mini-textbox" style="width:250px;" value="<?php echo $this->_var['user']['legal_idcard']; ?>"/></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>身份证照片：</td>
						<td colspan="3">
							<input id="img_url5"  name="legal_idcard_pic" class="mini-textbox" value="<?php echo $this->_var['user']['legal_idcard_pic']; ?>" style="width:200px"/>
							<input id="upfileId5" type="file" name="upFile" onChange="fileUpload(5);">
						</td>
					</tr>
					<tr>
						<td>所需牌号：</td>
						<td><input name="need_product" class="mini-textbox" style="width:250px;"  value="<?php echo $this->_var['user']['need_product']; ?>"/></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>所在省市</td>
						<td><input name="company_province" id="c_1_0" style="width:80px;"  class="mini-combobox" textField="name" valueField="id" onvaluechanged="setReg('c')" data='<?php echo setMiniConfig($this->_var['regionList']); ?>' value="<?php echo $this->_var['user']['infoExt']['company_province']; ?>" showNullItem="true"/>
							<input name="company_city" id="c_2_0" class="mini-combobox" url="<?php if ($this->_var['user']['infoExt']['company_province']): ?>/user/user/getRegion?pid=<?php echo $this->_var['user']['infoExt']['company_province']; ?><?php endif; ?>" value="<?php echo $this->_var['user']['infoExt']['company_city']; ?>" style="width:80px;" textField="name" valueField="id" />
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<!-- <tr>
						<td>邮政编码：</td>
						<td>
							<input name="zip" class="mini-hidden" value="<?php echo $this->_var['user']['zip']; ?>" style="width:100px;" maxlength="6" vtype="zip"/>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr> -->
					<tr>
						<td>成立日期：</td>
						<td><input  class="mini-datepicker"  name="fund_date" value="<?php echo $this->_var['user']['fund_date']; ?>"/>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>注册资本：</td>
						<td>
							<input name="registered_capital" class="mini-textbox" style="width:100px;"  value="<?php echo empty($this->_var['user']['registered_capital']) ? '' : $this->_var['user']['registered_capital']; ?>"/>
						万
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>公司地址：<font style="color:red;">*</font></td>
						<td><input name="address" class="mini-textbox" style="width:250px;" required="true" value="<?php echo $this->_var['user']['address']; ?>"/></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				<table>
					<caption><strong>扩展信息</strong></caption>
					<tr>
						<td>客户类型：<font style="color:red;">*</font></td>
						<td><input name="type" class="mini-combobox" style="width:150px;" value="<?php echo $this->_var['user']['type']; ?>" data='<?php echo setMiniConfig($this->_var['type']); ?>' required="true" textfield="name" valuefield="id"/></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<!-- <tr>
						<td>客户级别：</td>
						<td><input name="level" class="mini-combobox" style="width:150px;" value="<?php echo $this->_var['user']['level']; ?>" data='<?php echo setMiniConfig($this->_var['level']); ?>'  textfield="name" valuefield="id"/></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr> -->
					<tr>
						<td>客户渠道：<font style="color:red;">*</font></td>
						<td><input name="chanel" class="mini-combobox" style="width:150px;" value="<?php echo $this->_var['user']['chanel']; ?>" data='<?php echo setMiniConfig($this->_var['chanel']); ?>' required="true" textfield="name" valuefield="id"/></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>是否三证合一：<font style="color:red;">*</font></td>
						<td><input id="cards" name="cards" onvaluechanged="changeEvent()" class="mini-combobox" style="width:150px;" value="1" data='<?php echo setMiniConfig(array(0=>'否',1=>'是')); ?>' required="true" textfield="name" valuefield="id"/></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="three hidden">
						<td>营业执照号：</td>
						<td><input class="mini-textbox" style="width:200px;" name="business_licence" id="a" value="<?php echo $this->_var['user']['business_licence']; ?>"/>
						</td>
						<td>执照照片：</td>
						<td>
							<input id="img_url1"  name="business_licence_pic" class="mini-textbox" value="<?php echo $this->_var['user']['business_licence_pic']; ?>" style="width:70px"/>
							<input id="upfileId1" type="file" name="upFile" onChange="fileUpload(1);">
						</td>
					</tr>
					<tr class="three hidden">
						<td>组织代码：</td>
						<td><input class="mini-textbox" style="width:200px;" name="organization_code" id="b" value="<?php echo $this->_var['user']['organization_code']; ?>"/>
						</td>
						<td>组织图片：</td>
						<td>
							<input id="img_url2"  name="organization_pic" class="mini-textbox" value="<?php echo $this->_var['user']['organization_pic']; ?>" style="width:70px"/>
							<input id="upfileId2" type="file" name="upFile" onChange="fileUpload(2);">
						</td>
					</tr>
					<tr class="three hidden">
						<td>税务登记码：</td>
						<td><input class="mini-textbox" style="width:200px;" name="tax_registration" id="c" value="<?php echo $this->_var['user']['tax_registration']; ?>"/>
						</td>
						<td>税务图片：</td>
						<td>
							<input id="img_url3"  name="tax_registration_pic" class="mini-textbox" value="<?php echo $this->_var['user']['tax_registration_pic']; ?>" style="width:70px"/>
							<input id="upfileId3" type="file" name="upFile" onChange="fileUpload(3);">
						</td>
					</tr>
					<tr id="one"  >
						<td>统一社会信用代码：</td>
							<td><input class="mini-textbox" style="width:200px;" name="business_licence1" id="d" value="<?php echo $this->_var['user']['business_licence']; ?>"/>
						</td>
						<td>证件图片：</td>
						<td>
							<input id="img_url4"  name="business_licence_pic1" class="mini-textbox" value="<?php echo $this->_var['user']['business_licence_pic']; ?>" style="width:70px"/>
							<input id="upfileId4" type="file" name="upFile" onChange="fileUpload(4);">
						</td>
					</tr>
					<tr>
						<td>信用等级：<font style="color:red;">*</font></td>
						<td><input class="mini-combobox" name="credit_level" data='<?php echo setMiniConfig($this->_var['credit_level']); ?>' required="true" textfield="name" valuefield="id" value="<?php echo $this->_var['user']['credit_level']; ?>"/>
						</td>
						<td>附件地址：</td>	
						<td>
							<input id="img_url0"  name="file_url" class="mini-textbox" value="<?php echo $this->_var['user']['file_url']; ?>" style="width:70px"/>
							<input id="upfileId0" type="file" name="upFile" onChange="fileUpload(0);">
						</td>
					</tr>
					<tr>
						<td>状态：</td>
						<td><input class="mini-combobox" name="status" style="width:100px;" data='<?php echo setMiniConfig($this->_var['status']); ?>' textfield="name" valuefield="id" value="<?php echo empty($this->_var['user']['status']) ? '1' : $this->_var['user']['status']; ?>"/></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
			<div title="联系人基本信息">
			 	<div style="height:385px">
					<table border="0" cellpadding="1" cellspacing="2">
								<tr>
									<td >姓名：<font style="color:red;">*</font></td>
									<td><input name="info_name" class="mini-textbox" style="width:150px;" required="true" vtype="rangeLength:2,30"/></td>
									<td>性别：<font style="color:red;">*</font></td>
									<td><input class="mini-combobox" name="info_sex" style="width:100px;" data='<?php echo setMiniConfig($this->_var['sex']); ?>' required="true" textField="name" valueField="id" value="0"/></td>
								</tr>
								<tr>
									<td style="width:80px;">手机：<font style="color:red;">*</font></td>
									<td style="width:210px;"><input name="info_mobile" class="mini-textbox" maxlength="11" value="" style="width:150px;"/></td>
									<td>电话：<font style="color:red;">*</font></td>
									<td><input name="info_tel" class="mini-textbox" style="width:150px;"  vtype="rangeLength:6,14"/></td>
								</tr>
								<tr>
									<td style="width:80px;">QQ号：</td>
									<td style="width:210px;"><input name="info_qq" class="mini-textbox" maxlength="12" value="" style="width:150px;"/></td>
									<td>传真：</td>
									<td><input name="info_fax" class="mini-textbox" style="width:150px;"  vtype="rangeLength:6,14"/></td>
								</tr>
								<tr>
									<td>邮件：</td>
									<td><input name="info_email" class="mini-textbox" style="width:150px;"  vtype="email"/></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>备注：</td>
									<td colspan="3">
										<input name="info_remark" class="mini-textarea" style="width:403px; height:50px;"/>
									</td>
								</tr>
								<tr>
									<td>状态：</td>
									<td><input class="mini-combobox" name="info_status" style="width:100px;" data='<?php echo setMiniConfig($this->_var['status']); ?>' required="true" textField="name" valueField="id" value="1"/></td>
								</tr>
							</table>
				</div>
			</div>
		</div><input name="utype" class="mini-textbox" style="display:none" value="3" vtype="utype"/>
		<div align="center" style="margin-top:10px;">
			<a class="mini-button" iconcls="icon-ok" onclick="submitForm">确定</a>
			<a class="mini-button" iconcls="icon-cancel" onclick="onCancel">关闭</a><span id="returnMsg" style="padding-left:5px; color:#F00;"></span>
		</div>
	</div>
</div>
<script src="__JS__/jquery/jquery.upload.js" type="text/javascript"></script>
<script charset="utf-8" src="__JS__/kindeditor/kindeditor.js"></script>
<script charset="utf-8" src="__JS__/kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript">
mini.parse();
var form = new mini.Form("#infoForm");
function changeEvent(){
	var val = mini.get('cards').getValue();
	if(val == 1){
		$("#one").removeClass('hidden');
		$(".three").addClass('hidden');
		mini.get('merge_three').setValue('1');
		mini.get('a').setValue('');
		mini.get('b').setValue('');
		mini.get('c').setValue('');
		mini.get('img_url1').setValue('');
		mini.get('img_url2').setValue('');
		mini.get('img_url3').setValue('');
		
	}else{
		$(".three").removeClass('hidden');
		$("#one").addClass('hidden');
		mini.get('merge_three').setValue('0');
		mini.get('d').setValue('');
		mini.get('img_url4').setValue('');
		mini.get('img_url4').setValue('');
	}

}

function submitForm(){
	form.validate();
	if(form.isValid() == false) return;
	
	//提交数据
	var o = form.getData();
	var json = mini.encode(o);
	$("#returnMsg").text('');
	form.loading("数据提交中，请稍后......");
	<?php if ($this->_var['user']['user_id'] == ''): ?>
	var urlstr = '/user/customer/addSubmit';
	<?php else: ?>
	var urlstr = '/user/customer/editSubmit';
	<?php endif; ?>
	$.post(urlstr,{data:json},function(data){
		form.unmask();
		$("#returnMsg").text(data.msg);
		if(data.err=='0'){
			CloseWindow("save");
		}else{
			return false;
		}
	},'json');
}

function CloseWindow(action) {            
	if (window.CloseOwnerWindow) return window.CloseOwnerWindow(action);
	else window.close();            
}
function onCancel(e) {
	CloseWindow("cancel");
}

function onValidMobile(e){
	 if (e.isValid) {
		 if(!utils.isMobile(e.value)){
			 e.errorText = "错误的手机号";
				e.isValid = false;
		 }
	 }
}
function onValidZip(e){
	 if (e.isValid) {
		 if(!utils.isZip(e.value)){
			 e.errorText = "错误的邮编";
				e.isValid = false;
		 }
	 }
}
//验证身份证
function onIDCardsValidation(e) {
           if (e.isValid) {
               var pattern = /\d*/;
               if (e.value.length < 15 || e.value.length > 18 || pattern.test(e.value) == false) {
                   e.errorText = "必须输入15~18位数字";
                   e.isValid = false;
               }
           }
       }
function setReg(name,val){
	var deptCombo = mini.get(name+"_1_0");
	var positionCombo = mini.get(name+"_2_0");
	onDeptChanged(deptCombo,positionCombo,val);
}
function onDeptChanged(deptCombo,positionCombo,val) {
	var id = deptCombo.getValue();

	positionCombo.setValue("");
	var url = "/user/user/getRegion?pid=" + id
	positionCombo.setUrl(url);
}
function fileUpload(id) {
	$.ajaxFileUpload({
		url:'/system/sysUpload/images?model=<?php echo $this->_var['action']; ?>',
			secureuri:false,
			fileElementId:'upfileId'+id,
			dataType: 'json',
			success: function (data, status) {
				if(data.err=='0'){
					mini.get("img_url"+id).setValue(data.msg);
				}
			},
			error: function (data, status, e){
				$("#picResult").html(e);
			}
		}
	)
	return false;
}

function checkUnique(){
	var c_name = mini.get('c_name').getValue();
	var urlstr = '/user/customer/curUnique';
	$.post(urlstr,{data:c_name},function(data){
		if(data.err=='0'){
			$("#chkMsg").text(data.msg);
		}else{
			alert(data.msg);
		}
	},'json');
}
</script>
