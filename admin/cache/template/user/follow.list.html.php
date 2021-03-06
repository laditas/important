__layout|public:mini_layout|layout__
<div class="mini-toolbar" style="margin:3px 3px 0;">
	<table style="width:100%;">
		<tr>
			<td style="white-space:nowrap;">
				<a class="mini-button" iconCls="icon-add" plain="true" onclick="add()">新增</a>
				<span class="separator"></span>
				<a class="mini-button" iconCls="icon-remove" plain="true" onclick="removeRow()">删除</a>
				<span class="separator"></span> 
			</td>
			<td style="float:right;">
			<form id="soform">
				<select name="key_type">
					<option value="c_name">客户名称</option>
					<option value="name">联系人</option>
					<option value="input_admin">添加人</option>
				</select> 
				<input name="keyword" class="mini-textbox" emptyText="请输入关键词" style="width:140px;" onenter="onKeyEnter"/>   
				<a class="mini-button" iconCls="icon-search" onclick="search()">查询</a>
				<span id="searchMsg"></span></form>
			</td>
		</tr>
	</table>
</div>
<div class="mini-fit" style="padding:1px 3px 3px;">
	<div id="gridCell" class="mini-datagrid" style="width:100%;height:100%;"url="/user/follow/init?action=grid&ids=<?php echo $this->_var['ids']; ?>&moreChoice=<?php echo $this->_var['moreChoice']; ?>"
	idField="id"
	sizeList="[10,20,50,100]" pageSize="20" multiSelect="true" showFilterRow="true" allowCellSelect="true" allowCellEdit="true" allowCellWrap="true">
		<div property="columns">
			<div type="checkcolumn"></div>
			<div field="c_name" width="40" headerAlign="center" align="center" renderer="onLoadHandle" renderer="onLoadHandle">客户名称</div>
			<div field="name" width="30" headerAlign="center" align="center">联系人</div>
			<div field="remark" width="160" headerAlign="center"  align="center">跟进内容</div> 
			<div field="follow_up_way" width="30" headerAlign="center" allowSort="true" align="center">跟进方式</div> 
			<div field="follow_time" width="40" headerAlign="center" dateFormat="yyyy-MM-dd HH:mm" allowSort="true" align="center">跟进时间</div>
			<div field="next_follow_time" width="45" headerAlign="center" dateFormat="yyyy-MM-dd HH:mm" allowSort="true" align="center">下次跟进时间</div>
			<div field="input_admin" width="55" headerAlign="center" align="center" >添加人</div>
			<div field="input_time" width="40" headerAlign="center" dateFormat="yyyy-MM-dd HH:mm" allowSort="true" align="center">添加时间</div>
			
		</div>
	</div>
</div>

<script src="__JS__/jquery/jquery.upload.js" type="text/javascript"></script>
<script type="text/javascript">
mini.parse();
//搜索或刷新
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

function add(){
	var width=300, height=320;
	title="新增客户跟进";
	mini.open({
		url: "/user/follow/info",
		showMaxButton:true,
		 title: title, width: width, height:height,
		ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});		
}

//删除数据
function removeRow() {
	var rows = grid.getSelecteds(),_ids=new Array();

	if(rows.length<1) return;
	for(var i=0;i<rows.length;i++){
		_ids[i]=parseInt(rows[i].id);
	}
	var ids=_ids.join(',');
	if(ids=='') return;
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
			utils.ajax('/user/follow/remove',{ids:ids},callback,"POST","json");
		}
	);
}

//编辑联系人
function onLoadHandle(e) {
	var record = e.record,uid=record.c_id, c_name=record.c_name, s='';
	s += '<a href="javascript:viewCinfo('+uid+')">'+c_name+'</a> ';
	return s;
}


function GetData() {
	var row = grid.getSelected();
	return row;
}


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