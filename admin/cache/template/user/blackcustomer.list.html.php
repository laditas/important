__layout|public:mini_layout|layout__
<div class="mini-toolbar" style="margin:3px 3px 0;">
<style>table p{padding:0; margin:0; padding-top:3px;}</style>
	<table style="width:100%;">
		<tr>
			<!-- <td style="white-space:nowrap;"> -->
			<td style="float:right;">
			<form id="soform">
				添加时间    
				<input name="startTime" class="mini-datepicker" style="width:100px;"/> -
				<input name="endTime" class="mini-datepicker" style="width:100px;"/>

				<select name="identification" id="soidentification">
					<option value="" selected="selected">=认证=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['identification'])); ?>
				</select>
				<select name="level" id="soLevel">
					<option value="" selected="selected">=客户级别=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['level'])); ?>
				</select>
				<select name="type" id="soType">
					<option value="" selected="selected">=客户类型=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['type'])); ?>
				</select>
				<select name="key_type">
					<option value="c_id" selected="selected">客户ID</option>
					<option value="legal_person">法人姓名</option>
					<option value="legal_idcard">法人身份证</option>
					<option value="c_name">客户名称</option>
					<option value="need_product" >所需牌号</option>
					<option value="business_licence">营业执照号</option>
					<option value="organization_code">组织代码</option>
					<option value="tax_registration">税务登记号码</option>
				</select>       
				<input name="keyword" class="mini-textbox" emptyText="请输入关键词" style="width:140px;" onenter="onKeyEnter"/>
				<a class="mini-button" iconCls="icon-search" onclick="search()">查询</a>
							<span id="searchMsg"></span>
			</form>
			</td>

		</tr>
	</table>
</div>
<div class="mini-fit" style="padding:1px 3px 3px;">
	<div id="gridCell" class="mini-datagrid" style="width:100%;height:100%;"  sizeList="[10,20,50,100]" pageSize="20"
		url="/user/blackcustomer/init?action=grid" idField="user_id" multiSelect="true" allowCellEdit="true" allowCellWrap="true" allowCellSelect="true">
		<div property="columns">    
			<div type="checkcolumn"></div>
			<div field="c_name" width="95" headerAlign="center" allowSort="true" align="center" renderer="onLoadHandle">客户名字</div>
			<div field="chanel" width="40" headerAlign="center" align="center">渠道</div>
			<div field="type" width="30" headerAlign="center" align="center">客户类型</div>
			<!-- <div field="level" width="40" headerAlign="center" align="center">级别</div> -->
			<div field="need_product" width="60" headerAlign="center" align="center">所需要的牌号</div>
			<div field="legal_person" width="30" headerAlign="center" align="center" >法人姓名</div>
			<div field="legal_idcard" width="60" headerAlign="center"  align="center">法人身份证</div>
			<div field="input_time" width="50" headerAlign="center" dateFormat="yy-MM-dd HH:mm" align="center">添加时间</div>
			<div field="update_time" width="50" headerAlign="center" dateFormat="yy-MM-dd HH:mm" align="center">更新时间</div>
			<div field="customer_manager" width="40" headerAlign="center"  align="center">交易员</div>
			<div field="depart" width="30" headerAlign="center"  align="center">部门</div>
			
			<div field="identification" width="40" headerAlign="center" type="comboboxcolumn" autoShowPopup="true" align="center">认证
				<input property="editor" class="mini-combobox" style="width:100%;" textfield="name" valuefield="id"  data='<?php echo setMiniConfig($this->_var['identification']); ?>' /> 
			</div>
			
			<div field="" width="45" headerAlign="center"  align="center" renderer="onLoadFllow">操作</div>

		</div>
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

function onLoadHandle(e) {
	var record = e.record,uid=record.c_id, c_name=record.c_name, s='';
	s += '<a href="javascript:viewCinfo('+uid+')">'+c_name+'</a> ';
	return s;
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
function onLoadFllow(e){
	var record = e.record,s='',uid=record.c_id;
	return s = '<a href ="javascript:del('+uid+')">彻底删除</a>|<a href="javascript:restore('+uid+')">还原到私海</a>';
}
//删除数据
function del(cid) {
	mini.confirm("确定删除内容？", "提示",	function(action){
			if(action!='ok') return;
			var callback=function(data){
				if(data.err!='0'){
					alert(data.msg);
					return false;
				}else{
					grid.reload();
				}
			}
			utils.ajax('/user/blackcustomer/del',{ids:cid},callback,"POST","json");
		}
	);
}
//删除数据
function restore(cid) {
	mini.confirm("确定还原到私海吗？", "提示",	function(action){
			if(action!='ok') return;
			var callback=function(data){
				if(data.err!='0'){
					alert(data.msg);
					return false;
				}else{
					grid.reload();
				}
			}
			utils.ajax('/user/blackcustomer/restore',{ids:cid},callback,"POST","json");
		}
	);
}




</script>
