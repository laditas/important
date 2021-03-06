__layout|public:mini_layout|layout__
<div class="mini-toolbar"  style="margin:3px 3px 0;">
	<table style="width:100%;">
		<tr>	
			<td style="white-space:nowrap;">
				<a class="mini-button" iconCls="icon-add" plain="true" onclick="add()">新增</a>				
				<span class="separator"></span>
				<a class="mini-button" iconCls="icon-save" onclick="saveData()" plain="true">保存</a>
			</td>
			<td style="float:right;">
			  <form id="soform">
			  	<select name="status">
			  		<option value="1">已审核</option>
					<option value="2">未审核</option>					
				</select>
				<select name="key_type">
					<option value="c_name">客户名称</option>
					<option value="invoice_tel">开票电话</option>
					<option value="invoice_account">开票帐号</option>
					<option value="admin">交易员</option>
				</select>
				<input name="keyword" class="mini-textbox" emptyText="请输入关键词" style="width:140px;" onenter="onKeyEnter"/>
				<a class="mini-button" iconCls="icon-search" onclick="search()">查询</a>
				<span id="searchMsg"></span>
			  </form>
			</td>
		</tr>
	</table>
</div>
<div class="mini-fit" style="padding:3px;">
  <div id="gridCell" class="mini-datagrid" style="width:100%;height:100%;"url="/user/customer_billing/init?action=grid"  idField="id"
	sizeList="[10,20,50,100]" pageSize="20" <?php if ($this->_var['choose'] == '1'): ?>multiSelect="false"<?php endif; ?> multiSelect="true" showFilterRow="true" allowCellSelect="true" allowCellEdit="true" allowCellWrap="true">
	<div property="columns">
		<div type="checkcolumn"></div>
		<div field="c_name" width="90" headerAlign="center" align="center" align="center">客户名称</div>
		<div field="tax_id" width="90" headerAlign="center" align="center">纳税人识别号</div>
		<div field="invoice_address" width="90" headerAlign="center" align="center">开票地址
		</div>
		<div field="ems_address" width="90" headerAlign="center" align="center">邮寄地址</div>
		<div field="invoice_tel" width="90" headerAlign="center" align="center">开票电话</div>
		<div field="invoice_bank" width="90" headerAlign="center" >开票银行</div>
		<div field="invoice_account" width="90" headerAlign="center" >开票帐号</div>
		<!-- <div field="fax" width="50" headerAlign="center" >传真</div> -->
		<!-- <div field="input_time" width="80" headerAlign="center" align="center" allowSort="true" dateFormat="yyyy-MM-dd HH:mm" >添加时间</div>
		<div field="input_admin" width="50" headerAlign="center" align="center">创建者</div> -->
		<div field="status" width="50" headerAlign="center" align="center">审核状态</div>
		<div field="username" width="50" headerAlign="center" align="center">业务员</div>
		<div field="update_time" width="50" headerAlign="center" align="center" allowSort="true" dateFormat="yyyy-MM-dd HH:mm">修改时间</div>
		<div field="update_admin" width="50" headerAlign="center" align="center">修改者</div>
		<div field="remark" width="30" headerAlign="center" align="center"><span style="color:red">*</span>标记
				<input property="editor" class="mini-textbox" style="width:100%;" minHeight="50"/>
			</div>
		<div name="action"  width="50" headerAlign="center" align="center" renderer="onLoadHandle">操作</div>
	</div>
  </div>
 </div>

<script type="text/javascript">
//搜索或刷新数据
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

//打开增加开票资料表单
function add(){
	var width=400, height=400;
	title="新增开票资料";
	mini.open({
		url: "/user/customer_billing/info",
		showMaxButton:true,
		title: title, width: width, height:height,
		ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});		
}
//追加操作按钮
function onLoadHandle(e) {
	var record = e.record, s='',id = record.id;
	  	s += '<a href="javascript:changeMessage('+id+')">修改</a>';
	 	return s;
}

//申请修改开票资料
function changeMessage(id) {
	var btn = this;
	mini.open({
	url: "/user/customer_billing/info?id="+id,
	title: "修改开票资料",
	width: 400,
	height: 370,
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

//行内编辑后保存数据
function saveData() {
	var data = grid.getChanges();
	var json = mini.encode(data);
	if(json.length<10) return false;
  
	grid.loading("保存中，请稍后......");
	var callback=function(data){
	grid.unmask();
	if(data.err=='0'){
	  grid.reload();
	}else{
	  alert(data.msg)
	  return false;
	}
  }
	utils.ajax('/user/customer_billing/save',{data:json},callback,"POST","json");
}

function GetData() {
	var row = grid.getSelected();
	return row;
}

//筛选数据
function CloseWindow(action) {
	if (window.CloseOwnerWindow) return window.CloseOwnerWindow(action);
	else window.close();
}
//确定并获取数据
function onComfirm() {
	CloseWindow("ok");
}
//取消
function onCancel() {
	CloseWindow("cancel");
}

</script>