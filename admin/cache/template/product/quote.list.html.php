__layout|public:mini_layout|layout__
<div class="mini-toolbar" style="margin:3px 3px 0;">
	<table style="width:100%;">
		<tr>
			<td style="white-space:nowrap;">
				<a class="mini-button" iconCls="icon-add" onclick="add(0,<?php echo $this->_var['ctype']; ?>)" plain="true" tooltip="增加">增加</a>
				<a class="mini-button" iconCls="icon-save" plain="true" onclick="saveData()">保存</a>
				<a href="__UPLOAD__/zip/quote.zip" class="mini-button" iconCls="icon-download" plain="true">模板下载</a>
				<a class="mini-button" iconCls="icon-upgrade" plain="true" onclick="inputExcel(<?php echo $this->_var['ctype']; ?>)">excel导入</a>
				<a class="mini-button" class="output" iconCls="icon-downgrade" plain="true" onclick="outputExcel()">excel导出</a>
				<span class="separator"></span> 
				<a class="mini-button" iconCls="icon-reload" plain="true" onclick="reload()">重新发布</a>
				<a class="mini-button" iconCls="icon-remove" plain="true" onclick="remove()">删除</a>
			</td>
			<td style="float:right;">
			<form id="soform" method="get" action="/product/quote/download">
				<input class="mini-hidden" name="ctype"  value="<?php echo $this->_var['ctype']; ?>"/>
				创建时间   
				<input name="startTime" class="mini-datepicker" style="width:100px;"/> -
				<input name="endTime" class="mini-datepicker" style="width:100px;"/>
				&nbsp;&nbsp;&nbsp;
				<select name="product_type">
					<option value="">==品种==</option>
					<?php echo $this->html_options(array('options'=>$this->_var['product_type'])); ?>
				</select>
				<select name="bargain">
					<option value="">=议价=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['bargain'])); ?>
				</select>
				<select name="status" id="soStatus">
					<option value="">=状态=</option>
					<?php echo $this->html_options(array('options'=>$this->_var['status'])); ?>
				</select>
				<select name="key_type">
					<option value="username">交易员</option>
					<option value="f_name">厂家</option>
					<option value="number">数量</option>
					<option value="unit_price">价格</option>
				</select> 
				<input name="keyword" class="mini-textbox" emptyText="请输入关键词" style="width:140px;" onenter="onKeyEnter"/>   
				<a class="mini-button" iconCls="icon-search" onclick="search()">查询</a>
				<span id="searchMsg"></span></form>
			</td>
		</tr>
	</table>           
</div>

<div class="mini-fit" style="padding:1px 3px 3px;">
	<div id="gridCell" class="mini-datagrid" style="width:100%;height:100%;"  sizeList="[10,20,50,100]" pageSize="20"
		url="/product/quote/init?action=grid&do=<?php echo $this->_var['slt']; ?>&id=<?php echo $this->_var['id']; ?>" onrowdblclick="onRowDblClick" allowCellWrap="true"
		showFilterRow="true" idField="id" multiSelect="true" allowCellSelect="true" allowCellEdit="true"
		>
		<div property="columns">    
			<div type="checkcolumn"></div>
			<div field="c_name" width="50" headerAlign="center" align="center" allowSort="true">客户名称</div>
			<div field="product_type" width="30" headerAlign="center" align="center" allowSort="true">品种</div>
			<div field="model" width="40" headerAlign="center" align="center">牌号</div>
			<div field="f_id" width="80" headerAlign="center" allowSort="true" align="center">厂家</div>                
			<div field="process_type" width="40" headerAlign="center" allowSort="true" align="center">加工级别</div>
			<div field="number" width="40" headerAlign="center" allowSort="true" align="center">数量【吨】
				<input property="editor" class="mini-textbox" style="width:100%;"/>
			</div>
			<div field="unit_price" width="45" headerAlign="center" allowSort="true" align="center">价格(元/吨)
				<input property="editor" class="mini-textbox" style="width:100%;"/>
			</div>
			<div field="supply_count" width="40" headerAlign="center" allowSort="true" align="center">采购[SUM]</div>
			<div field="provinces" width="50" headerAlign="center" allowSort="true" align="center">省份</div>
			<div field="bargain" width="35" headerAlign="center" allowSort="true" align="center">是否实价</div>
			<div field="store_house" width="50" headerAlign="center" allowSort="true" align="center">仓库</div>
			<div field="remark" width="100" headerAlign="center" allowSort="true" align="center">备注</div>
			<div field="content" width="100" headerAlign="center" allowSort="true" align="center">直接报采</div>
			<div field="input_time" width="50" headerAlign="center" dateFormat="yy-MM-dd HH:mm" allowSort="true" align="center">创建时间</div>
			<div field="update_time" width="50" headerAlign="center" dateFormat="yy-MM-dd HH:mm" allowSort="true" align="center">更新时间</div>
			<div field="input_admin" width="45" headerAlign="center" align="center">创建人</div>
			<div field="username" width="45" headerAlign="center" align="center">交易员</div>
			<div field="status" width="30" headerAlign="center" allowSort="true" align="center" renderer="LoadStatus">状态</div>
			<div action='do' width="40" renderer="viewhander">操作</div>
		</div>
	</div>
</div>
<?php if ($this->_var['ischecked'] == 'search'): ?>
	<div class="mini-toolbar" style="text-align:center;padding-top:8px;padding-bottom:8px; border:1px solid #000;" borderStyle="border:0;">
			<a class="mini-button" style="width:60px;" onClick="onComfirm()">确定</a>
			<span style="display:inline-block;width:25px;"></span>
			<a class="mini-button" style="width:60px;" onClick="onCancel()">取消</a>
	</div>
<?php endif; ?>
<script src="__JS__/jquery/jquery.upload.js" type="text/javascript"></script>
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

function onRowDblClick(e) {
	var record = e.record,id=record.id;
	<?php if ($this->_var['slt'] != 'slt'): ?>
		add(id,2);
	<?php else: ?>
		add(id,1);
	<?php endif; ?>
}

function viewhander(e){
	var record = e.record,id=record.id, status=record.status, count=record.supply_count, s='';
	if(count>0){
		s = '<a href="javascript:viewPinfo('+id+')">采购列表</a>';
	}
	if(status==1){
		s +='&nbsp;<a href="javascript:chk('+id+')">审</a>';
	}
	return s;
}


function viewPinfo(id){
	mini.open({
		url:"/product/buySale/init?purid="+id,
		showMaxButton:true,
		title:'采购单列表查看',width:1050,height:630,
		ondestroy: function(action){
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});
}
//查看编辑用户信息
function add(id,ctype){
	var width=330,height=500;
		if(ctype==1){
			title='新增现货报价';
		}else{
			title ='新增期货报价';
		}
	mini.open({
		url: "/product/quote/init?action=info&id="+id+"&ctype="+ctype,
		showMaxButton:true,
		title: title, width: width, height:height,
		ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});	
}
//弹出阶段审核窗口
function chk(id){
	mini.open({
		url:"/product/purchase/chkpage?id="+id,
		showMaxButton:true,
		title:'审核',width:190,height:210,
		ondestroy: function(action){
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
		if(data.err!='0'){
			alert(data.msg)
			return false;
		}else{
			grid.reload();
		}
	}
	utils.ajax('/product/quote/init?action=save',{data:json},callback,"POST","json");
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

function GetData() {
	var row = grid.getSelected();
	return row;
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
	utils.ajax('/product/quote/remove',{ids:ids},callback,"POST","json");
  });
}
function reload() {
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
	utils.ajax('/product/quote/init?action=republish',{data:json},callback,"POST","json");
}

jQuery.extend({
handleError: function( s, xhr, status, e ) {
// If a local callback was specified, fire it
if ( s.error )
s.error( xhr, status, e );
// If we have some XML response text (e.g. from an AJAX call) then log it in the console
else if(xhr.responseText)
console.log(xhr.responseText);
}
});
//导入excel
function inputExcel(type){
	mini.showMessageBox({
		title: "excel导入",
		buttons: ["ok", "cancel"],
		html: '<p style="margin-left:50px;">\
				<label><strong>选择Excel文件</strong> <br /><input type="file" name="check_file" id="manual_check_file" />\
			  </p>',
		callback: function (action) {
			if(action=='ok'){
				var messageid = mini.loading("正在处理...", "处理中");
				$.ajaxFileUpload({
					url:'/product/quote/inputExcel?ctype='+type,
					fileElementId:"manual_check_file",
					dataType: 'json',
					success: function (data, status) {	
						if(!data.err){
							mini.alert(data.result);
							grid.reload();
						}else{							
							var iconCls = 'mini-messagebox-info',html = "Excel导入成功。",title="处理成功",width=200;
							if(data.result){
								width=350;
								title="导入完成，有异常订单";
								iconCls = 'mini-messagebox-warning';
								html = '手动对账处理完成，异常订单如下：<br /><div style="height:250px;overflow:scroll;"><span>失败'+data.err+'条数据</span><br/>';
								$.each(data.result,function(k,v){
									html += k+" "+v+"<br />";
								});
								html+="</div>";
							}
							mini.showMessageBox({
								showHeader: false,
								width: width,
								title: title,
								buttons: ["ok"],
								html: html,
								iconCls: iconCls
							});
							grid.reload();
						}
						mini.hideMessageBox(messageid);
					},
					error: function (data, status, e){
						mini.hideMessageBox(messageid);
						alert(e);
					}
				});
			}
		}
	});
}

//导出excel
function outputExcel(){
	$("#soform").submit();
}
</script>