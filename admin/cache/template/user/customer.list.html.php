__layout|public:mini_layout|layout__
<div class="mini-toolbar" style="margin:3px 3px 0;">
<style>table p{padding:0; margin:0; padding-top:3px;}</style>
	<table style="width:100%;">
		<tr>
			<td style="white-space:nowrap;">
			<form id="soform">
				<?php if ($this->_var['helper'] == 1): ?>
				<a class="mini-button" iconCls="icon-add" plain="true" onclick="send()">发送短信</a>
				<span class="separator"></span>
				<?php endif; ?>
				添加时间
				<input name="startTime" class="mini-datepicker" style="width:100px;"/> -
				<input name="endTime" class="mini-datepicker" style="width:100px;"/>
				<?php if ($this->_var['isPublic'] == '1'): ?>
				<select name="status" id="soStatus">
					<option value="" selected="selected">已审核</option>
					<?php echo $this->html_options(array('options'=>$this->_var['status'])); ?>
				</select>
				<?php endif; ?>
				<select name="identification" id="soidentification">
					<option value="" selected="selected">=认证=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['identification'])); ?>
				</select>
				<!-- <select name="level" id="soLevel">
					<option value="" selected="selected">=客户级别=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['level'])); ?>
				</select> -->
				<select name="type" id="soType">
					<option value="" selected="selected">=客户类型=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['type'])); ?>
				</select>
				<select name="invoice">
					<option value="" selected="selected">=开票资料=</option>
					<option value="1">否</option>
					<option value="2">是</option>
				</select>
				<select name="company_chanel">
					<option value="" selected="selected">=来源渠道=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['company_chanel'])); ?>
				</select>
				<select name="key_type">
					<option value="c_name" selected="selected">客户名称</option>
					<!-- <option value="c_id">客户ID</option> -->
					<!-- <option value="legal_idcard">法人身份证</option> -->
					<option value="need_product" >所需牌号</option>
					<!-- <option value="business_licence">营业执照号</option> -->
					<!-- <option value="organization_code">组织代码</option> -->
					<!-- <option value="tax_registration">税务登记号码</option> -->
					<?php if ($this->_var['isPublic'] != '1'): ?>
					<option value="customer_manager">交易员</option>
					<?php endif; ?>
				</select>
				<input name="keyword" class="mini-textbox" emptyText="请输入关键词" style="width:140px;" onenter="onKeyEnter"/>
				<a class="mini-button" iconCls="icon-search" onclick="search()">查询</a>
							<span id="searchMsg"></span>
			   </form>
			</td>
			<?php if ($this->_var['doact'] != 'search'): ?>
			<td style="float:right;">
			<?php if ($this->_var['isPublic'] != '1' && $this->_var['helper'] != 1): ?>
				<a class="mini-button" iconCls="icon-user" onclick="exchange()">批量替换交易员</a>
				<a class="mini-button" iconCls="icon-addnew" onclick="share()">共享</a>
				<a class="mini-button" iconCls="icon-add" onclick="add(0,1)">新增客户联系人</a>
				<?php if ($this->_var['cooperation'] != 1): ?>
				<a class="mini-button" iconCls="icon-add" onclick="add(0,3)">新增客户</a>
				<a class="mini-button" iconCls="icon-save" plain="true" onclick="saveTags()">保存更改</a>
				<?php endif; ?>
			<?php endif; ?>
			 </p>
			</td>
			<?php endif; ?>
		</tr>
	</table>
</div>
<div class="mini-fit" style="padding:1px 3px 3px;">
	<div id="gridCell" class="mini-datagrid" style="width:100%;height:100%;"  sizeList="[10,20,50,100]" pageSize="20" allowCellWrap="true"
		url="/user/customer/init?action=grid&do=<?php echo $this->_var['doact']; ?>&isPublic=<?php echo $this->_var['isPublic']; ?><?php if ($this->_var['isSecurity'] == '1'): ?>&isSecurity=<?php echo $this->_var['isSecurity']; ?><?php endif; ?>&pt=<?php echo $this->_var['pt']; ?>&cooperation=<?php echo $this->_var['cooperation']; ?>&supplier=<?php echo $this->_var['supplier']; ?>&privated=<?php echo $this->_var['privated']; ?>&cids=<?php echo $this->_var['cids']; ?>&moreChoice=<?php echo $this->_var['moreChoice']; ?>" idField="user_id"  onrowdblclick="onRowDblClick" <?php if ($this->_var['choose'] == '1'): ?>multiSelect="false"<?php endif; ?>
		multiSelect="true" <?php if ($this->_var['doact'] != 'search'): ?>allowCellEdit="true" allowCellSelect="true"<?php endif; ?>
		>
		<div property="columns">
			<div type="checkcolumn"></div>
			<div field="c_name" width="70" headerAlign="center" allowSort="true" align="center" renderer="onLoadHandle">客户名字</div>
			<div field="chanel" width="35" headerAlign="center" align="center">渠道</div>
			<div field="type" width="35" headerAlign="center" align="center">客户类型</div>
<!-- 			<div field="level" width="40" headerAlign="center" align="center">级别</div> -->
			<div field="need_product_adm" width="50" headerAlign="center" align="center">所需牌号</div>
			<div field="need_product" width="50" headerAlign="center" align="center">主营产品</div>
			<div field="month_consum" width="50" headerAlign="center" align="center">月用量</div>
			<!-- <div field="legal_person" width="30" headerAlign="center" align="center" >法人姓名</div>
			<div field="legal_idcard" width="60" headerAlign="center"  align="center">法人身份证</div> -->
			<div field="invoice" width="30" headerAlign="center"  align="center">开票资料</div>
			<div field="name" width="30" headerAlign="center" align="center" >联系人姓名</div>
			<div field="tel" width="60" headerAlign="center"  align="center">联系人电话</div>
			<div field="mobile" width="60" headerAlign="center"  align="center">联系人手机</div>
			<div field="remark" width="60" headerAlign="center"  align="center">最新跟进</div>
			<div field="credit_limit" width="50" headerAlign="center"  align="center">信用额度</div>
			<div field="available_credit_limit" width="50" headerAlign="center"  align="center">可用额度</div>

			<div field="input_time" width="50" headerAlign="center" dateFormat="yy-MM-dd HH:mm" align="center">添加时间</div>
			<div field="update_time" width="50" headerAlign="center" dateFormat="yy-MM-dd HH:mm" align="center">更新时间</div>
			<div field="customer_manager" width="30" headerAlign="center"  align="center">交易员</div>
			<div field="depart" width="30" headerAlign="center"  align="center">部门</div>
			<?php if ($this->_var['isPublic'] == '1'): ?>
			<div field="identification" width="40" headerAlign="center" align="center" renderer="identificationStatus">认证</div>
			<div field="status" width="40" headerAlign="center"  align="center" renderer="LoadStatus" >状态</div>
			<?php else: ?>
			<div field="identification" width="40" headerAlign="center" type="comboboxcolumn" autoShowPopup="true" align="center">认证
				<input property="editor" class="mini-combobox" style="width:100%;" textfield="name" valuefield="id"  data='<?php echo setMiniConfig($this->_var['identification']); ?>' />
			</div>
			<?php endif; ?>
			<?php if ($this->_var['helper'] != 1): ?>
			<div field="" width="45" headerAlign="center"  align="center" renderer="onLoadFllow">操作</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div id="allotInfo" class="mini-window" title="分配" style="width:200px;"
	showModal="true" allowResize="true" allowDrag="true"
	>
	<div id="replaceForm" class="form" >
		<table style="width:100%;">
			<input class="mini-hidden" name="id"/>
			<tr>
				<td>分配交易员:</td>
				<td>
					<input name="id" class="mini-buttonedit" onbuttonclick="allotCustomer" valueField="id"  value="" allowInput="false"  style="width:100px" id="id"/>
					<input id="c_id" class="mini-hidden" value="">
				</td>

			</tr>

			<tr>
			<td style="text-align:right;padding-top:5px;padding-right:20px;" colspan="2">
				<a class="mini-button" iconCls="icon-save" plain="true" href="javascript:submitAllot()">保存</a>
			</td>
			</tr>
		</table>
	</div>
</div>
<?php if ($this->_var['doact'] == 'search'): ?>
<div class="mini-toolbar" style="text-align:center;padding-top:8px;padding-bottom:8px;" borderStyle="border:0;">
		<a class="mini-button" style="width:60px;" onClick="onComfirm()">确定</a>
		<span style="display:inline-block;width:25px;"></span>
		<a class="mini-button" style="width:60px;" onClick="onCancel()">取消</a>
</div>
<?php endif; ?>

<script type="text/javascript">
mini.parse();
var grid = mini.get("gridCell");
grid.load();
var supplier = parseInt(<?php echo $this->_var['supplier']; ?>);
var cooperation = parseInt(<?php echo $this->_var['cooperation']; ?>);
//
grid.on("drawcell", function (e) {

    var record = e.record,
        column = e.column,
        field = e.field,
        value = e.value;
    console.log(record);
//    console.log(column);
    if (column.field == "available_credit_limit") {
        //console.log(record);
        if (e.value<0) {
            //e.rowStyle = "background:#FF00BA";
            e.cellStyle = "background:#FF00BA"
        } else {
            e.rowStyle = "";
        }
    }
}
);


function search() {
	grid.load($("#soform").serializeObject(),function(e){
		$("#searchMsg").html(e.msg);
	});
}
function onKeyEnter(e) {
	search();
}
function identificationStatus(e) {
	var record = e.record.identification; //record.id
	return $("#soidentification").find("option[value="+record+"]").text();
}
function onRowDblClick(e) {
	onEdit();
}
//分配客户
function allotView(e){
	 var record = e.record,s='', cid = record.c_id;

	 return s;
}



//分配客户
var allotInfo = mini.get("allotInfo");
var form = new mini.Form("#replaceForm");
function allotO(cid) {
mini.get(c_id).setValue(cid)
	allotInfo.show();
}

//编辑联系人
function onLoadHandle(e) {
	var record = e.record,uid=record.c_id, c_name=record.c_name, s='';
	s += '<a href="javascript:viewCinfo('+uid+')">'+c_name+'</a> ';
	return s;
}

//跟进客户
function onLoadFllow(e) {
	var record = e.record,cid=record.c_id, status=record.status,s='',customer_manager=record.customer_manager,bli=record.bli;
	if(status == 2 && customer_manager ==''){
		s += '<a href="javascript:allotO('+cid+')">分配</a>|<a href="javascript:Retrieve('+cid+')">捡回</a>';

	}else if(status == 1 && customer_manager ==''){
		s += '<a href="javascript:chk('+cid+')">审核</a>';
		s += '|<a href="javascript:removeRow('+cid+')">黑名单</a>';
	}else{
		if(customer_manager !=''){
			s += '<a href="javascript:viewFinfo('+cid+')">跟</a>|<a href="javascript:removeRow('+cid+')">黑名单</a>';
			if(supplier==0){
				s +='|<a href="javascript:yellow('+cid+')">黄名单</a>|<a href="javascript:setSea('+cid+')">公海</a>';
			}
			s += '|<a href="javascript:onEdit()">改</a>|<a href="javascript:allotO('+cid+')">换交易员</a>';
		}
		if(bli>0){
			s +='|<a href="javascript:changeMessage('+cid+')">票</a>';
		}
	}
	return s;
}

//捡回客户
function Retrieve(cid) {
    $.post('/user/customer/retrieve',{cid:cid},
		function(data){
            if(data.err!='0'){
                grid.unmask();
                alert(data.msg);
                return false;
            }else{
                grid.reload();
                allotInfo.hide();
            }

    },'json');
}


//删除数据
function removeRow(cid) {
	mini.confirm("确定加入黑名单？", "提示",	function(action){
			if(action!='ok') return;
			var callback=function(data){
				if(data.err!='0'){
					alert(data.msg);
					return false;
				}else{
					grid.reload();
				}
			}
			if(supplier == 1){
				utils.ajax('/user/customer/remove_supplier',{ids:cid},callback,"POST","json");
			}else if(cooperation == 1){
				utils.ajax('/user/customer/remove_cooperation',{ids:cid},callback,"POST","json");
			}else{
				utils.ajax('/user/customer/remove',{ids:cid},callback,"POST","json");
			}

		}
	);
}
//标示黄名单
function yellow(cid) {
	mini.confirm("确定标记为黄名单？", "提示",	function(action){
			if(action!='ok') return;
			var callback=function(data){
				if(data.err!='0'){
					alert(data.msg);
					return false;
				}else{
					grid.reload();
				}
			}
			utils.ajax('/user/customer/yellow',{ids:cid},callback,"POST","json");
		}
	);
}
//审核用户
function chk(cid){
	mini.open({
		url: "/user/customer/chkpage?id="+cid,
		showMaxButton:true,
		title: "审核用户",
		width: 190, height:160,
		ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});
}
//开票资料显示
function changeMessage(id) {
	var btn = this;
	mini.open({
	url: "/user/customer_billing/billingInfo?id="+id,
	title: "显示开票资料",
	width: 377,
	height: 310,
	onload: function () {
		var iframe = this.getIFrameEl();
	},
	ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});
}
//会员回收
function setSea(cid) {
	mini.confirm("确定回收该用户？", "提示",	function(action){
			if(action!='ok') return;
			var callback=function(data){
				if(data.err!='0'){
					alert(data.msg);
					return false;
				}else{
					grid.reload();
				}
			}
			utils.ajax('/user/customer/setsea',{ids:cid},callback,"POST","json");
		}
	);
}
//发送短信
function send(){
	var rows = grid.getSelecteds(),_ids=new Array();
	if(rows.length<1) return;
	for(var i=0;i<rows.length;i++){
		var _id=parseInt(rows[i].c_id);
		if(_id<1){
			grid.removeRow(rows[i],false);
		}else{
			_ids.push(_id);
		}
	}
	var ids=_ids.join(',');
	if(ids=='') return;
	mini.open({
	url: "/product/interface/customer_send?ids="+ids,
	title: '给客户发送短信', width: 550, height:400,
	ondestroy: function (action) {
	  if(action=='save'){ //重新加载
		grid.reload();
	  }
	}
	});
}
//打开跟进客户信息页面
function viewFinfo(c_id){
	mini.open({
		url: "/user/follow/info?id="+c_id,
		showMaxButton:true,
		title: "新增客户跟进",
		width: 300, height:320
	});
}

//查看编辑用户信息
function add(id,ctype){
	 var c_id = 0;
	if(ctype==1){
		var width=525,height=340;
		title='新增公司联系人';
		if(grid.getSelected() != null){
			c_id = grid.getSelected().c_id;
		}
	}else if(ctype==3){
		var width=740,height=700;
		title='新增公司';
	}
	mini.open({
		url: "/user/customer/info?id="+id+"&ctype="+ctype+"&supplier=<?php echo $this->_var['supplier']; ?>&u_add="+c_id,
		showMaxButton:true,
		title: title, width: width, height:height,
		ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});
}
//共享客户联系人
function share(){
	var row = grid.getSelected();
	var width=225,height=130;
	title='添加共享联系人';
	mini.open({
		url: "/user/customer/share?id="+row.c_id,
		showMaxButton:true,
		title: title, width: width, height:height,
		ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});
}
//批量替换交易员
function exchange(){
	var width=225,height=130;
	title='批量替换交易员';
	var rows = grid.getSelecteds(),_ids=new Array();
	if(rows.length<1) return;
	for(var i=0;i<rows.length;i++){
		var _id=parseInt(rows[i].c_id);
		if(_id<1){
			grid.removeRow(rows[i],false);
		}else{
			_ids.push(_id);
		}
	}
	var ids=_ids.join(',');
	console.log(ids);
	if(ids=='') return;
		mini.open({
			url: "/user/customer/exchange?ids="+ids,
			showMaxButton:true,
			title: title, width: width, height:height,
			ondestroy: function (action) {
				if(action=='save'){ //重新加载
					grid.reload();
				}
			}
		});

}

//行内编辑后保存数据
function saveTags(){
	var data = grid.getChanges();
	var json = mini.encode(data);
	if(json.length<10) return false;
	grid.loading("保存中，请稍后......");
	var callback=function(data){
		if(data.error>0){
			alert(data.content);
			return false;
		}else{
			grid.reload();
		}
	}
	utils.ajax('/user/customer/saveTags',{data:json},callback,"POST","json");
}
//筛选数据
function CloseWindow(action) {
	if (window.CloseOwnerWindow) return window.CloseOwnerWindow(action);
	else window.close();
}
function onComfirm() {
	CloseWindow("ok");
}
function onCancel() {
	CloseWindow("cancel");
}
function GetData() {
	var row = grid.getSelected();
	return row;
}

function onEdit(e) {
	var row = grid.getSelected();
	if (row) {
		var width=740,height=700,title='编辑客户信息';
		urlStr="/user/customer/edit?id="+row.c_id+'&ctype=3';
		mini.open({
			url: urlStr,
			title: title, width: width, height:height,
			ondestroy: function (action) {
				if(action=='save'){ //重新加载
					grid.reload();
				}
			}
		});
	}
}
//分配管理员列表
function allotCustomer(){
		var btn = this;
		mini.open({
		url: "rbac/adm/init?isPublic=1&do=search",
		title: "分配客户",
		width: 1200,
		height: 550,
		onload: function () {
			var iframe = this.getIFrameEl();
			iframe.contentWindow.SetData();
		},
		ondestroy: function (action) {
			if (action == "ok") {
				var iframe = this.getIFrameEl();
				var data = iframe.contentWindow.GetData();
				data = mini.clone(data);    //必须
				if (data) {
					btn.setValue(data.admin_id);
					btn.setText(data.name);
					// $("#"+btn.id+"\\$value").val(data.name);
				}
			}
		}
	});
}
//提交分配
function submitAllot(){
	var cid = mini.get(c_id).getValue();
	form.validate();
	if (form.isValid() == false) return;
	var o = form.getData();
	grid.loading("数据提交中，请稍后......");
	var json = mini.encode(o);
	var callback=function(data){
		if(data.err!='0'){
			grid.unmask();
			alert(data.msg);
			return false;
		}else{
			grid.reload();
			allotInfo.hide();
		}
	}
	utils.ajax('/user/customer/allotCustomer',{data:json,cid:cid},callback,"POST","json");
}
//选择框根据传入值选择
top['win']=window;
function setDvalue(a){
	console.log(a);
   var row=grid.findRow(function(row){
	 if(row.c_id==a) return true;
   })
   grid.select(row);
}

</script>
