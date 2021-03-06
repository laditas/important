<?php
/** 
 * 战队配资日志记录
 * 
 */
class teamCapitalLogAction extends adminBaseAction {
	public function __init(){
		$this->debug = false;
		$this->assign('team',L('team')+array('1'=>'其他')); //战队名称
		$this->assign('team_capital_type',L('team_capital_type')); //操作类型
		$this->db=M('public:common')->model('log_team_capital');
	}
	/**
	 * 站内信息列表
	 * @access public 
	 * @return html
	 */
	public function init(){
		$action=sget('action','s');
		$this->user_id= $_SESSION['adminid'];
		if($action=='grid'){
			//分页
			$page = sget("pageIndex",'i',0); //页码
			$size = sget("pageSize",'i',20); //每页数
			$sortField = sget("sortField",'s','input_time'); //排序字段
			$sortOrder = sget("sortOrder",'s','desc'); //排序
			$where = '  1 ';
			//关键词
			$keyword=sget('keyword','s');
			if(!empty($keyword)){
				$where.=" and input_admin='$keyword' ";
			}
			$team_id=sget('team_id','i');
			if($team_id)  $where.=" and `team_id` = '$team_id' ";

			$action_type=sget('action_type','s');
			if($action_type)  $where.=" and `action_type` = '$action_type' ";
			
			$order_sn=sget('order_sn','s');
			if($order_sn)  $where.=" and `order_sn` = '$order_sn' ";
			
			//筛选时间
			$sTime = sget("sTime",'s','input_time'); //搜索时间类型
			$where.=getTimeFilter($sTime); //时间筛选
			$list=$this->db->where($where)
						->page($page+1,$size)
						->order("$sortField $sortOrder")
						->getPage();
			foreach($list['data'] as $k=>$val){
				$list['data'][$k]['order_sn'] = M('product:order')->getColByOid($val['o_id'],'order_sn');
				$list['data'][$k]['action_type'] = L('team_capital_type')[$val['action_type']];
				$list['data'][$k]['input_time']=$val['input_time']>1000 ? date("Y-m-d H:i:s",$val['input_time']) : '-';
				$list['data'][$k]['team_name']=$this->db->model('adm_role')->select('name')->where("id = {$val['team_id']}")->getOne();
				if(!$list['data'][$k]['team_name']){
					$list['data'][$k]['team_name'] = '其他';
				}
			}
			$result=array('total'=>$list['count'],'data'=>$list['data'],'msg'=>'');
			$this->json_output($result);
		}
		$this->display('teamCapitalLog.list.html');
	}
	
}