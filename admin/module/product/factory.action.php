<?php
/**
*工厂管理控制器
*/
class factoryAction extends adminBaseAction
{
	public function __init(){
		$this->db=M('public:common')->model('factory');
	}
	public function init(){
		//获取列表数据
		$action=sget('action');
		$doact=sget('do','s');
		if($action=='grid'){
			$page = sget("pageIndex",'i',0); //页码
			$size = sget("pageSize",'i',20); //每页数
			$sortField = sget("sortField",'s','fid'); //排序字段
			$sortOrder = sget("sortOrder",'s','desc'); //排序
			//搜索条件
			$where=" 1 ";
			//状态
			$status = sget("status",'s','');
			if($status!='') $where.=" and `status` = '$status' ";
			//关键词
			 $keyword=sget('keyword','s');
			if(!empty($keyword)){
					$where.=" and `f_name` like '%$keyword%' ";
			}
			$list=$this->db->where($where)
						->page($page+1,$size)
						->order("$sortField $sortOrder")
						->getPage();
			foreach($list['data'] as $k=>$v){
				$list['data'][$k]['input_time']=$v['input_time']>1000 ? date("Y-m-d H:i:s",$v['input_time']) : '-';
				$list['data'][$k]['update_time']=$v['update_time']>1000 ? date("Y-m-d H:i:s",$v['update_time']) : '-';
				$list['data'][$k]['status']=L('factory_status')[$v['status']];
			}
			$result=array('total'=>$list['count'],'data'=>$list['data'],'msg'=>'');
			$this->json_output($result);
		}
		$this->assign('doact',$doact);
		$this->assign('status',L('factory_status'));
		$this->assign('factory_status',L('factory_status')); //厂家状态
		$this->assign('page_title','厂家管理');
		$this->display('factory.list.html');
   	}


	/**
	 * 保存(新)工厂信息
	 * @access public
	 */
	public function ajaxSave(){
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //传递的参数
		if(empty($data)){
			$this->error('错误的请求');
		}
		$data = array(
			'status'=>$data['status'],
			'input_time'=>CORE_TIME,
			'remark'=> $data['remark'],
			'input_admin'=>$_SESSION['name'],
			'f_name'=>$data['f_name'],
		);
		$result = $this->db->add($data);
		if(!$result) $this->error('操作失败');
		$cache=cache::startMemcache();
		$cache->delete('factory');
		$this->success('操作成功');
	}
	//ajax获取状态，改变保存
	public function changeSave(){
		$this->is_ajax=true; //指定为Ajax输出
		$changeid = sget('changeid','i'); //传递的参数
		$tag = sget('tag','s');
		$data['status']= $tag=='正常' ? 1 : 2;
		$data['update_time'] = CORE_TIME;
		$data['update_admin'] = $_SESSION['name'];
		$res = $this->db->wherePk($changeid)->update($data);
		if($res){
			$cache=cache::startMemcache();
			$cache->delete('factory');
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
		$this->json_output($result);
	}
	/**
	 * 删除工厂数据
	 * @access public 
	 */
	public function remove(){
		$this->is_ajax=true; //指定为Ajax输出
		$ids=sget('ids','s');
		if(empty($ids)){
			$this->error('操作有误');
		}
		$ids=explode(',', $ids);
		foreach ($ids as $k => $v) {
			if(M('product:product')->getColById($v)){
				$list[$v]=M('product:product')->getColById($v);
			}else{
				$result=$this->db->where("fid = ($v)")->delete();
				continue;
			}
		}
		if($list) $this->json_output(array('err'=>0,'result'=>$list));
		if($result){
			$cache=cache::startMemcache();
			$cache->delete('factory');
			$this->success('操作成功');
		}else{
			$this->error('删除操作失败');
		}
	}
	/**
	 * 保存行内编辑工厂数据
	 * @access public 
	 * @return html
	 */
	public function save(){
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //获取UI传递的参数
		if(empty($data)){
			$this->error('错误的操作');
		}
		$sql=array();
		foreach($data as $v){
			$_id=$v['fid'];
			if($_id>0){
				$update=array(
					'f_name'=>$v['f_name'],
					'update_time'=>CORE_TIME,
					'update_admin'=>$_SESSION['name'],
					'remark'=>$v['remark'],
				);
				$sql[]=$this->db->wherePk($_id)->updateSql(saddslashes($update));
			}
		}
		if(empty($sql)){
			$this->error('操作数据为空');
		}
		$result=$this->db->commitTrans($sql);
		if($result){
			$cache=cache::startMemcache();
			$cache->delete('factory');
			$this->success('操作成功');
		}else{
			$this->error('数据处理失败');
		}
	}

	/**
	 * 根据牌号异步获取厂家名称
	 */
	public function getfaclist(){
		$this->is_ajax=true;
		$model=sget('model','s');
		$getfaclist=M('product:product')->getFacByModel($model);
		$this->json_output($getfaclist);
	}
	
	//用来查询选择厂家 
	public function query(){
	    //获取列表数据
	    $action=sget('action');
	    $doact=sget('do','s');
	    if($action=='grid'){
	        $page = sget("pageIndex",'i',0); //页码
	        $size = sget("pageSize",'i',20); //每页数
	        $sortField = sget("sortField",'s','fid'); //排序字段
	        $sortOrder = sget("sortOrder",'s','desc'); //排序
	        //搜索条件
	        $where=" 1 ";
	        //状态
// 	        $status = sget("status",'s','');
	        $status='1';//只显示正常
	        if($status!='') $where.=" and `status` = '$status' ";
	        //关键词
	        $keyword=sget('keyword','s');
	        if(!empty($keyword)){
	            $where.=" and `f_name` like '%$keyword%' ";
	        }
	        $list=$this->db->where($where)
	        ->page($page+1,$size)
	        ->order("$sortField $sortOrder")
	        ->getPage();
	        foreach($list['data'] as $k=>$v){
	            $list['data'][$k]['input_time']=$v['input_time']>1000 ? date("Y-m-d H:i:s",$v['input_time']) : '-';
	            $list['data'][$k]['update_time']=$v['update_time']>1000 ? date("Y-m-d H:i:s",$v['update_time']) : '-';
	            $list['data'][$k]['status']=L('factory_status')[$v['status']];
	        }
	        $result=array('total'=>$list['count'],'data'=>$list['data'],'msg'=>'');
	        $this->json_output($result);
	    }
	    $this->assign('doact',$doact);
	    $this->assign('status','正常');
	    $this->assign('factory_status','正常'); //厂家状态
	    $this->assign('page_title','厂家管理');
	    $this->display('factory_query.list.html');
	}
	
}