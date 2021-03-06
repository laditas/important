__layout|public:mini_layout|layout__
<div title="新增出库管理" class="form" id="editForm">
		<input class="mini-hidden" name="out_no" value="<?php echo $this->_var['out_no']; ?>"/>
		<input class="mini-hidden"  id="c_id" name="c_id" value="<?php echo $this->_var['c_id']; ?>"/>
		<input class="mini-hidden" id="o_id" name="o_id" value="<?php echo $this->_var['o_id']; ?>"/>
		<input class="mini-hidden" name="doyet" value="<?php echo $this->_var['doyet']; ?>" />
		<fieldset style="border:solid 1px #aaa;margin-top:8px;position:relative;">

			<legend>出库信息</legend>
			<table width="100%" border="0" cellpadding="1" cellspacing="2">

				<tr>
					<td>出库名称<font style="color:red;"> * </font></td>
					<td><input name="sale_subject" class="mini-textbox" style="width:120px;" maxlength="20"  required="true" value="<?php echo $this->_var['order_name']; ?>" allowInput="false" textField="name" valueField="id"/>
					</td>
					<td>出库单号</td>
					<td><?php echo $this->_var['out_no']; ?></td>
				</tr>
				<tr>
					<td>出库种类<font style="color:red;"> * </font></td>
					<td><input name="out_type" class="mini-combobox" data='<?php echo setMiniConfig($this->_var['out_type']); ?>' value="<?php echo empty($this->_var['data']['out_type']) ? '1' : $this->_var['data']['out_type']; ?>" textField="name" valueField="id" style="width:120px;" required="true"/>
					</td>
					<td>物流公司<font style="color:red;"> * </font></td>
					<td><input name="ship_id" class="mini-combobox" data='<?php echo setMiniConfig($this->_var['ship_company']); ?>' value="<?php echo $this->_var['data']['ship_company']; ?>" textField="name" valueField="id" style="width:120px;" required="true"/></td>
				</tr>
				<tr>
					<td>选择仓库<font style="color:red;"> * </font></td>
					<td><input name="store_id" class="mini-buttonedit" onbuttonclick="stoChoose" value="<?php echo $this->_var['data']['store_id']; ?>" allowInput="false"  text="<?php echo $this->_var['data']['store_name']; ?>"  style="width:120px" id="usrId" required="true"/></td>
					<td>入库人<font style="color:red;"> * </font></td>
					<td><input id="name" name="store_aid" class="mini-combobox" textField="name" value="<?php echo $this->_var['info']['store_aid']; ?>" data='<?php echo setMiniConfig($this->_var['admin_list']); ?>'   valueField="id"  style="width:120px;" required="true"/></td>
				</tr>
				<tr>
					<td>运费</td>
					<td><input name="ship" class="mini-textbox" value="<?php echo empty($this->_var['data']['ship']) ? '0' : $this->_var['data']['ship']; ?>"  style="width:120px;" />
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>备注:</td>
					<td><input name="remark" class="mini-textarea" style="width:120px" value="<?php echo $this->_var['data']['remark']; ?>"></td>
				</tr>


			</table>
			
		</fieldset>
	</div>
<div class="mini-fit" style="padding:1px 3px 3px;">
<div id="gridCell" class="mini-datagrid" style="width:100%;height:150px;" url="/product/outStorage/info?action=grid&o_id=<?php echo $this->_var['o_id']; ?>" idField="id"
sizeList="[10,20,50,100]" pageSize="20" allowCellSelect="true" allowCellEdit="true" idField="id" multiSelect="true" >
	<div property="columns" >
		<div type="checkcolumn" ></div>
		<div field="id" width="35" headerAlign="center" align="center"  renderer="onLoadHandle">ID</div>
		<div field="model" width="95" headerAlign="center" align="center">牌号</div>
		<div field="f_name" width="120" headerAlign="center" align="center">厂家</div>
		<div field="store_name" width="70" headerAlign="center" align="center">仓库</div>
		<div field="unit_price" width="50" headerAlign="center" align="center">单价</div>
		<div field="number" width="50" headerAlign="center" align="center">数量</div>
		<div field="remainder" width="50" headerAlign="center"  align="center">未发数</div>
		<div field="out_number" width="50" headerAlign="center"  align="center">发货数<font  style="color:red;">*</font>
			<input property="editor" class="mini-textbox"  onvaluechanged="onValidation" style="width:100%;" minWidth="100" />
		</div>   
	</div>
</div>
	<div align="center" style="margin-top:10px;">
		<a class="mini-button" iconcls="icon-ok" onclick="submitForm">发货</a>
		<a class="mini-button" iconcls="icon-cancel" onclick="onCancel">关闭</a><span id="returnMsg" style="padding-left:5px; color:#F00;"></span>
	</div>
	
<script type="text/javascript">
mini.parse();
var rows ;
//搜索或刷新
var grid = mini.get("gridCell");
grid.on("load",function(){ //默认全选
	for(var i =0;i<grid.totalCount;i++){
		grid.select(i);
	}
});
function search() {
	grid.load($("#soform").serializeObject(),function(e){
		$("#searchMsg").html(e.msg);
	});
}
search();
function onKeyEnter(e) {
	search();
}
//追加操作按钮
function onLoadHandle(e) {
	var record = e.record,s='',id = record.id,model = record.model ;
	s += '<a href="javascript:viewOrdInfo('+id+')">'+id+'</a> ';
	return s;
}
//查看明细相关信息
function viewOrdInfo(id){
	mini.open({
		url: "/product/saleLog/info?id="+id,
		showMaxButton:true,
		title: "查看明细相关信息", 
		width: 700, height:450
	});		
}
function submitForm() {
	var isok = true;
	var form = new mini.Form("#editForm");
	rows = grid.getSelecteds()
	form.validate();
	if(form.isValid() == false) return;
	var rows = grid.getSelecteds();
	for(var i = 0;i<rows.length;i++){
		if(!rows[i].out_number){
			$("#returnMsg").text("请填写出库数量");
			isok=false;
		} 
		if(parseInt(rows[i].out_number)>parseInt(rows[i].remainder)){
			$("#returnMsg").text("请填写正确出库数量");
			isok=false;
		}
	}
	if( !isok ) return ;


	//提交数据
	var o = form.getData();
	$.extend(o, {log:rows}); 
	var json = mini.encode(o);
	$("#returnMsg").text('');
	form.loading("数据提交中，请稍后......");
	var urlstr = '/product/outStorage/addSubmit';
	$.post(urlstr,{data:json},function(data){
		form.unmask();
		$("#returnMsg").text(data.msg);
		if(data.err=='0'){
			window.location.reload();
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
//选择明细
function detailChoose(e){
	var oid=mini.get(o_id).getValue();
	var btn = this;
		mini.open({
		url: "/product/saleLog/init?do=search&oid="+oid,
		title: "选择明细",
		width: 1200,
		height: 550,
		onload: function () {
			var iframe = this.getIFrameEl();
		},
		ondestroy: function (action) {
			if (action == "ok") {
				var iframe = this.getIFrameEl();
				var data = iframe.contentWindow.GetData();
				data = mini.clone(data);    //必须
				// alert(data);
				if (data) {
					btn.setValue(data.id);
					btn.setText(data.id);
				}
			}
		}
	});
}
//强制选择归属公司
function cusChoose(e){
	var btn = this;
		mini.open({
		url: "/user/customer/init?do=search",
		title: "选择公司",
		width: 1200,
		height: 550,
		onload: function () {
			var iframe = this.getIFrameEl();
		},
		ondestroy: function (action) {
			if (action == "ok") {
				var iframe = this.getIFrameEl();
				var data = iframe.contentWindow.GetData();
				data = mini.clone(data);    //必须
				if (data) {
					btn.setValue(data.c_id);
					btn.setText(data.c_name);
				}
			}
		}
	});
}
//选择仓库
function stoChoose(e){
	var btn = this;
	var aname = mini.get("name");
		mini.open({
		url: "/product/store/init?do=search&choose=1",
		title: "选择仓库",
		width: 1200,
		height: 550,
		onload: function () {
			var iframe = this.getIFrameEl();
		},
		ondestroy: function (action) {
			if (action == "ok") {
				var iframe = this.getIFrameEl();
				var data = iframe.contentWindow.GetData();
				data = mini.clone(data);    //必须
				if (data) {
					btn.setValue(data.id);
					btn.setText(data.store_name);
					aname.load('/product/outStorage/get_store_aid?sid='+data.id);
				}
			}
		}
	});
}
//选择交易员
function admChoose(e){
	var btn = this;
		mini.open({
		url: "/rbac/adm/init?do=search",
		title: "选择交易员",
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
				}
			}
		}
	});
}
//删除
function remove() {
	var rows = grid.getSelecteds(),_ids=new Array();
	if(rows.length<1) return;
		for(var i=0;i<rows.length;i++){
	var _id=parseInt(rows[i].id);
	if(_id<1){
		grid.removeRow(rows[i],false);
	}else{
		_ids.push(_id);
	}
  }
	var ids=_ids.join(',');
	if(ids=='') return;
	mini.confirm("确定删除？", "提示", function(action){
	if(action!='ok') return;
	var callback=function(data){
	if(data.err=='0'){
		grid.reload();
	}else{
		alert(data.msg)
		return false;
	}
	}
	utils.ajax('/product/outStorage/remove',{ids:ids},callback,"POST","json");
  });
}
//行内编辑后保存数据
function saveData() {
	var data = grid.getChanges();
	var json = mini.encode(data);
	if(json.length<10) return false;
	grid.loading("保存中，请稍后......");
	var callback=function(data){
		if(data.err!='0'){
			alert(data.msg)
			return false;
		}else{
			grid.reload();
		}
	}
	utils.ajax('/product/outStorage/save',{data:json},callback,"POST","json");
}
// function onValidation(){
// 	var out_number = parseInt(this.getValue());
// 	rows = grid.getSelecteds(),_ids=new Array(),_numbers=new Array();
// 	if(out_number>parseInt(rows[0].number)){
// 		alert('不能大于订单数量,请重新输入!');
// 		this.setValue('0');
// 	 	return false;
// 	}
// }
</script>