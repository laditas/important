__layout|public:mini_layout|layout__
<div class="mini-toolbar" style="margin:3px 3px 0;">
	<table style="width:100%;">
		<tr>
			<td style="white-space:nowrap;">
				<a class="mini-button" iconCls="icon-save" plain="true" onclick="saveTags()">保存更改</a>
				<a class="mini-button" iconCls="icon-remove" plain="true" onclick="remove()">删除</a>
			</td>
			<td style="float:right;">
				<form id="soform" method="get" action="/user/plastic/download">
					<select name="sTime">
						<option value="input_time">创建时间</option>
						<option value="update_time">更新时间</option>
					</select>
					<input name="startTime" class="mini-datepicker" style="width:100px;"/> -
					<input name="endTime" class="mini-datepicker" style="width:100px;"/>
					<select name="status" id="soStatus">
						<option value="" selected="selected">=状态=</option>
						<?php echo $this->html_options(array('options'=>$this->_var['status'])); ?>
					</select>
					<!-- <select name="is_pass" id="is_pass">
						<option value="" selected="selected">=认证=</option>
						<?php echo $this->html_options(array('options'=>$this->_var['is_pass'])); ?>
					</select> -->
					<select name="chanel" id="chanel">
						<option value="" selected="selected">=注册来源=</option>
						<?php echo $this->html_options(array('options'=>$this->_var['quan_type'])); ?>
					</select>
					<select name="key_type">
						<option value="name">联系人</option>
						<option value="customer_manager">交易员</option>
						<option value="c_id">公司</option>
						<option value="tel">电话</option>
						<option value="parent_mobile">引荐人手机</option>
						<option value="mobile">手机号</option>
						<option value="email">邮箱</option>
					</select>
					<select name="trial_type">
						<option  selected="selected">=初审状态=</option>
						<option value="0">初审</option>
						<option value="3">初审拒绝</option>
						<option value="1">转公海</option>
						<option value="2">已转公海</option>
					</select>
					<input name="keyword" class="mini-textbox" emptyText="请输入关键词" style="width:140px;" onenter="onKeyEnter"/>
					<a class="mini-button" class="output" onclick="download()" iconCls="icon-download" plain="true">导出</a>
					<a class="mini-button" iconCls="icon-search" onclick="search()">查询</a>
					<span id="searchMsg"></span>
				</form>
			</td>
		</tr>
	</table>
</div>
<div class="mini-fit" style="padding:1px 3px 3px;">
	<div id="gridCell" class="mini-datagrid" style="width:100%;height:100%;"  sizeList="[10,20,50,100]" pageSize="20" allowCellWrap="true"
		url="/user/plastic/init?action=grid&do=<?php echo $this->_var['doact']; ?>" onrowdblclick="onRowDblClick" showFilterRow="true" idField="id" multiSelect="true" allowCellSelect="true" allowCellEdit="true"  contextMenu="#gridMenu" headerContextMenu="#headerMenu">
		<div property="columns">
			<div type="checkcolumn"></div>
			<div field="user_id" width="50" headerAlign="center" renderer="onLoadHandle" align="center">联系人ID</div>
			<div field="name" width="50" headerAlign="center" align="center">联系人</div>
			<div field="c_id" width="80" headerAlign="center" align="center">公司</div>
			<div field="sex" width="30" headerAlign="center"  align="center">性别</div>
			<div field="tel" width="80" headerAlign="center" align="center">联系电话</div>
			<div field="mobile" width="80" headerAlign="center" align="center">手机号</div>
			<div field="mobile_province" width="80" headerAlign="center" align="center">归属地(省)</div>
			<div field="mobile_area" width="80" headerAlign="center" align="center">归属地(市)</div>
			<div field="parent_mobile" width="80" headerAlign="center" align="center">引荐人手机</div>
			<div field="thumbcard" width="50" headerAlign="center" allowSort="true" align="center" renderer="onLoadShowPic">证件资料
			</div>
			<div field="qq" width="80" headerAlign="center" align="center">qq号</div>
			<div field="quan_type" width="70" headerAlign="center" align="center" renderer='chanel'>来源</div>
			<div field="visit_count" width="50" headerAlign="center" align="center" allowSort="true">登录次</div>
			<div field="customer_manager" width="40" headerAlign="center" align="center">交易员</div>
			<!-- <div field="depart" width="60" headerAlign="center" align="center">部门</div> -->
			<div field="input_time" width="80" headerAlign="center" dateFormat="yyyy-MM-dd HH:mm" align="center" allowSort="true">创建时间</div>
			<!-- <div field="input_admin" width="80" headerAlign="center" align="center">创建管理员</div>
			<div field="update_time" width="80" headerAlign="center" dateFormat="yyyy-MM-dd HH:mm" align="center">更新时间</div>
			<div field="update_admin" width="60" headerAlign="center" align="center">更新人</div> -->
			<div field="status" width="40" headerAlign="center" renderer="LoadStatus" align="center">状态</div>

            <div field="is_pass" width="50" headerAlign="center" type="comboboxcolumn" autoShowPopup="true" align="center">认证
				<input property="editor" class="mini-combobox" style="width:100%;" textfield="name" valuefield="id"  data='<?php echo setMiniConfig($this->_var['is_pass']); ?>' />
			</div>

			<!-- <div field="is_pass" width="40" headerAlign="center" align="center" renderer="is_pass_status">认证</div> -->

			<!-- <?php if ($this->_var['doact'] == 'search'): ?>
			 <div name="action" width="60" headerAlign="center" align="center" cellStyle="padding:0;" renderer="deblockingview">操作</div>
			 <?php else: ?><?php endif; ?>
			 -->
			 <div name="action" width="60" headerAlign="center" align="center" cellStyle="padding:0;" renderer="allot">操作</div>
			<div name="changeusr" width="80" headerAlign="center" renderer="ftrial" align="center">转换</div>

		</div>
	</div>
	<ul id="gridMenu" class="mini-contextmenu" onbeforeopen="onBeforeOpen">
		<li name="modifyPasswd" iconCls="icon-edit" onclick="modifyPasswd">重置密码</li>
	</ul>
</div>

<div id="picShow" style="display:none; background-color: #A4C6D5; border:1px solid #333333; padding:5px;">
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
					<input id="user_id" class="mini-hidden" value="">
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
function search() {
	grid.load($("#soform").serializeObject(),function(e){
		$("#searchMsg").html(e.msg);
	});
}
search();
function onKeyEnter(e) {
	search();
}
function download() {
	$("#soform").submit();
}


//编辑联系人
function onLoadHandle(e) {
	var record = e.record,uid=record.user_id, s='';
	s += '<a href="javascript:viewContactInfo('+uid+')">'+uid+'</a> ';

	return s;
}

function onRowDblClick(e) {
	var record = e.record,uid=record.user_id, s='';
	editContact(uid);
}

function editContact(uid){
	var row = grid.getSelected();
	if (row) {
		var width=525,height=340,title='用户信息';
		urlStr="/user/user/info?id="+uid;
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

/**
 * 审核塑料圈客户信息
 * @author gsp
 * @param  {[type]} e [description]
 * @return {[type]}   [description]
 */
function ftrial(e){
	var record = e.record,uid=record.user_id,s='';
	if(record.is_trial == 0){
		s += '<a href="javascript:viewModelAndCompany('+uid+')">初审</a>';
	}else if(record.is_trial == 1){
		s += '<a href="javascript:doTrial('+uid+')">转公海</a>';
	}else if(record.is_trial == 3){
        s += '<a href="javascript:viewModelAndCompany('+uid+')">初审拒绝</a>';
	}
	return s;
}
/**
 * 塑料圈转公海
 * @author gsp
 * @param  {[type]} $userid [description]
 * @return {[type]}         [description]
 */
function doTrial(uid){
	mini.confirm("确定将此用户转到公海么？", "提示",
		function(action){
		if(action!='ok') return;
		var callback=function(data){
			if(data.err!='0'){
				alert(data.msg);
				return false;
			}else{
				grid.reload();
			}
		}
		utils.ajax('/user/user/toPublicSea',{uid:uid},callback,"POST","json");
		}
	);
}
//分配交易员按钮
function allot(e) {
	var record = e.record,user_id=record.user_id, s='';
	s +='<a href="javascript:allotO('+user_id+')">换交易员</a>';
	return s;
}

//分配交易员
var allotInfo = mini.get("allotInfo");
var form = new mini.Form("#replaceForm");
function allotO(user_id) {
	mini.get("user_id").setValue(user_id);
	allotInfo.show();
}

//会员解锁
function deblockingview(e){
	var record = e.record,uid=record.user_id, s='';
	s += '<a href="javascript:deblocking('+uid+')">'+'解锁'+'</a> ';

	return s;

}
function deblocking(uid){
	mini.confirm("确定解锁此用户？", "提示",
		function(action){
		if(action!='ok') return;
		var callback=function(data){
			if(data.err!='0'){
				alert(data.msg);
				return false;
			}else{
				grid.reload();
			}
		}
		utils.ajax('/user/user/deblocking',{uid:uid},callback,"POST","json");
		}
	);
}
function onBeforeOpen(e) {
	var grid = mini.get("gridCell");
	var menu = e.sender;

	var row = grid.getSelected();
	var rowIndex = grid.indexOf(row);
	if (!row) {
		e.cancel = true;
		//阻止浏览器默认右键菜单
		e.htmlEvent.preventDefault();
		return;
	}

}
//重置密码
function modifyPasswd(e) {
	var row = grid.getSelected();
	if (row) {
		var width=300,height=200,title='重置密码';
		urlStr="/user/user/modifyPasswd?id="+row.user_id;
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
function chanel(e) {
	var record = parseInt(e.record.quan_type)+1; //record.id
	return $("#chanel").find("option[value="+record+"]").text();
}
//提交分配
function submitAllot(){
	var user_id = mini.get("user_id").getValue();
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
	utils.ajax('/user/contact/allotCustomer',{data:json,cid:user_id},callback,"POST","json");
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
	utils.ajax('/user/plastic/saveTags',{data:json},callback,"POST","json");
}

//查看证件资料
function onLoadShowPic(e) {
	var record = e.record, uimg =record.thumbcard, s='';
	if (uimg =='-') {
		s+='-';
	}else{
		s += '<a href="javascript:;" onmouseover="showPic(\''+uimg+'\',this)" onmouseout="hidePic()" ">看资料</a>';
	}
	return s;
}
function showPic(url,obj){
	var width = $(window).innerWidth();
	var left = $(obj).offset().left;
	var top = $(obj).offset().top;
	if($('#picShow').height()+top>$(window).height()){
		$('#picShow').show().css({"top":top-280+"px","left":left+50+"px","position":"absolute"}).html('<img width="400px" src="'+url+'" />');
	}else{
		$('#picShow').show().css({"top":top+20+"px","left":left+50+"px","position":"absolute"}).html('<img width="400px" src="'+url+'" />');
	}
}

function hidePic(){
	$('#picShow').hide();
}

//删除
function remove() {
	var rows = grid.getSelecteds(),_ids=new Array(),_cids=new Array();
	// console.log(rows);
	if(rows.length<1) return;
		for(var i=0;i<rows.length;i++){
			var _cid=parseInt(rows[i].customer_id);
	if(_cid<1){
		grid.removeRow(rows[i],false);
	}else{
		_cids.push(_cid);
	}
  }
  // console.log(_cids);
	var cids=_cids.join(',');
	if(cids=='') return;
	// console.log(cids);
	mini.confirm("确定删除？", "提示", function(action){
	if(action!='ok') return;
	var callback=function(data){
		if(data.err=='0'){
			grid.reload();
			// 	if(data.result){
			// 		width=300;
			// 		title="操作完成";
			// 		iconCls = 'mini-messagebox-warning';
			// 		html = '以下产品存在订单无法删除,【ID】：';
			// 		$.each(data.result,function(k,v){
			// 			html += k+" &nbsp;";
			// 		});
			// 		html+="";
			// 	}
		}else{
			alert(data.msg)
			return false;
		}
	}
	utils.ajax('/user/plastic/init?action=remove',{cids:cids},callback,"POST","json");
  });
}
</script>