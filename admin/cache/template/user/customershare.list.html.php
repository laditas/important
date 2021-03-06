__layout|public:mini_layout|layout__
<div class="mini-toolbar" style="margin:3px 3px 0;">
<style>table p{padding:0; margin:0; padding-top:3px;}</style>
	<table style="width:100%;">
		<tr>
			<td style="white-space:nowrap;">
			<form id="soform">
				添加时间
				<input name="startTime" class="mini-datepicker" style="width:100px;"/> -
				<input name="endTime" class="mini-datepicker" style="width:100px;"/>
				<select name="key_type">
					<option value="c_id" selected="selected">客户ID</option>
					<option value="c_name">客户名称</option>
					<option value="name">被共享人姓名</option>
				</select>
				<input name="keyword" class="mini-textbox" emptyText="请输入关键词" style="width:140px;" onenter="onKeyEnter"/>
				<a class="mini-button" iconCls="icon-search" onclick="search()">查询</a>
                 			<span id="searchMsg"></span>
			   </form>
			</td>
			<?php if ($this->_var['doact'] != 'search'): ?>
			<td style="float:right;">
				<a class="mini-button" iconCls="icon-remove" onclick="removeRow()">取消共享</a>
			</td>
			<?php endif; ?>
		</tr>
	</table>
</div>
<div class="mini-fit" style="padding:1px 3px 3px;">
	<div id="gridCell" class="mini-datagrid" style="width:100%;height:100%;"  sizeList="[10,20,50,100]" pageSize="20" allowCellWrap="true"
		url="/user/customershare/init?action=grid&do=<?php echo $this->_var['doact']; ?>" idField="user_id"  onrowdblclick="onRowDblClick" multiSelect="true" <?php if ($this->_var['doact'] != 'search'): ?>allowCellEdit="true" allowCellSelect="true"<?php endif; ?>
		>
		<div property="columns">
			<div type="checkcolumn"></div>
			<div field="c_name" width="75" headerAlign="center" allowSort="true" align="center" renderer="onLoadHandle">客户名字</div>
			<div field="chanel" width="40" headerAlign="center" align="center">渠道</div>
			<div field="type" width="30" headerAlign="center" align="center">客户类型</div>
			<div field="customer_manager" width="60" headerAlign="center"  align="center">被共享人</div>
			<div field="need_product" width="60" headerAlign="center" align="center">所需要的牌号</div>
			<div field="legal_person" width="30" headerAlign="center" align="center" >法人姓名</div>
			<div field="cm" width="30" headerAlign="center" align="center" >所属交易员</div>
			<div field="share_managername" width="30" headerAlign="center" align="center" >添加人姓名</div>
			<div field="input_time" width="50" headerAlign="center" dateFormat="yy-MM-dd HH:mm" align="center">添加时间</div>
			<!-- <div field="input_admin" width="40" headerAlign="center"  align="center">创建人</div> -->
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
function onRowDblClick(e) {
	onEdit();
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
	mini.confirm("确定取消共享该客户？", "提示",	function(action){
			if(action!='ok') return;
			var callback=function(data){
				if(data.err!='0'){
					alert(data.msg);
					return false;
				}else{
					grid.reload();
				}
			}
			utils.ajax('/user/customershare/remove',{ids:ids},callback,"POST","json");
		}
	);
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
</script>
