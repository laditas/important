__layout|public:mini_layout|layout__
<div class="mini-toolbar" style="margin:3px 3px 0;">

	<table style="width:100%;">
		<tr>
			<td style="white-space:nowrap;"><form id="soform">
					<span>发送时间</span>
					<input name="startTime" class="mini-datepicker" style="width:100px;"/>
					-
					<input name="endTime" class="mini-datepicker" style="width:100px;"/>
					<?php if ($this->_var['uid'] == 0): ?>
					<select name="status" id="soStatus">
							<option value="">=状态=</option>
							<?php echo $this->html_options(array('options'=>$this->_var['status'])); ?>
					</select>
					<select name="stype" id="soType">
							<option value="">=类型=</option>
							<?php echo $this->html_options(array('options'=>$this->_var['stype'])); ?>
					</select>
					<?php endif; ?>
					<select name="key_type">
							<?php if ($this->_var['uid'] == 0): ?>
							<option value="mobile">手机号</option>
							<option value="user_id">会员ID</option>
							<option value="user_ip">IP</option>
							<?php endif; ?>
							<option value="msg">短信内容</option>
					</select>
					<input name="keyword" class="mini-textbox" emptyText="请输入关键词" style="width:140px;" onenter="onKeyEnter"/>
					<a class="mini-button" iconCls="icon-search" onclick="search()">查询</a> <span id="searchMsg"></span>
				</form></td>
				<?php if ($this->_var['uid'] == 0): ?>
				<td style="float:right;">
						<a class="mini-button" iconCls="icon-add" plain="true" onclick="send()">发送短信</a>
				</td>
				<?php endif; ?>
		</tr>
	</table>

</div>
<div class="mini-fit" style="padding:1px 3px 3px;">
	<div id="gridCell" class="mini-datagrid" style="width:100%;height:100%;"  sizeList="[10,20,50,100]" pageSize="20"
				url="/report/logsms/init?action=grid&uid=<?php echo $this->_var['uid']; ?>" idField="id"
				>
		<div property="columns">
			<?php if ($this->_var['uid'] == 0): ?>
			<div name="action" width="20" headerAlign="center" align="right" renderer="onLoadHandle">操作</div>
			<?php endif; ?>
			<div field="user_id" width="20" headerAlign="center" allowSort="true" align="center" renderer="onLoadUinfo">用户ID</div>
			<div field="mobile" width="30" headerAlign="center" allowSort="true" align="center">手机号</div>
			<div field="msg" width="220" headerAlign="center" allowSort="true">短信内容</div>
			<div field="user_ip" width="80" headerAlign="center" allowSort="true">IP</div>
			<div field="input_time" width="50" headerAlign="center" dateFormat="yyyy-MM-dd HH:mm:ss" allowSort="true" align="center">发送时间</div>
			<?php if ($this->_var['uid'] == 0): ?>
			<div field="stype" width="20" headerAlign="center" allowSort="true" align="center">发送类型</div>
			<div field="status" width="20" headerAlign="center" allowSort="true" align="center">是否发送</div>
			<div field="chanel" width="25" headerAlign="center" allowSort="true" align="center">短信通道</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<script type="text/javascript">
mini.parse();
var grid = mini.get("gridCell");
grid.load();

//追加查看按钮
function onLoadHandle(e) {
	var s='';
	if(e.record.status!='成功'){
		s += '<a href="javascript:resend('+e.rowIndex+')">重发</a> ';
	}
	return s;
}

function resend(rowid){
	var row = grid.getRow(rowid);
	mini.showMessageBox({
		title: "重发短信",
		buttons: ["ok", "cancel"],
		html: '<div style="padding:10px 30px">\
				<p>是否确认重新发送？</p>\
				</div>'
			 ,
		callback: function (action) {
			if(action=='ok'){
				utils.ajax('/report/logsms/resend',{logid:row.id,channel:$("#resend_channel").val()},function(data){
					if(data.err!='0'){
						alert(data.msg);
					}else{
						grid.reload();
					}
				},"POST","json");
			}
		}
	});
	$("#resend_channel option:contains("+row.chanel+")").prop("selected",true);
}

//发送短信
function send(){
	mini.open({
		url: "/report/logsms/send",
		title: '发送短信', width: 550, height:400,
		ondestroy: function (action) {
			if(action=='save'){ //重新加载
				grid.reload();
			}
		}
	});   
}
</script>
