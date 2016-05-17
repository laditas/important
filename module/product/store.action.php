<?php
/**
*仓库管理控制器
*/
class storeAction extends adminBaseAction
{
	public function __init(){
		$this->db=M('public:common')->model('store');
	}
	public function init(){
		//获取列表数据
		$action=sget('action');
		if($action=='grid'){
			$page = sget("pageIndex",'i',0); //页码
			$size = sget("pageSize",'i',20); //每页数
			$sortField = sget("sortField",'s','input_time'); //排序字段
			$sortOrder = sget("sortOrder",'s','desc'); //排序
			//搜索条件
			$where=" 1 ";
			//关键词
			$key_type=sget('key_type','s','store_name');
			$keyword=sget('keyword','s');
			if(!empty($keyword)){
					$where.=" and $key_type like '%$keyword%' ";
			}
			$list=$this->db->where($where)
						->page($page+1,$size)
						->order("$sortField $sortOrder")
						->getPage();
			foreach($list['data'] as $k=>$v){
				$list['data'][$k]['input_time']=$v['input_time']>1000 ? date("Y-m-d H:i:s",$v['input_time']) : '-';
				$list['data'][$k]['update_time']=$v['update_time']>1000 ? date("Y-m-d H:i:s",$v['update_time']) : '-';
			}
			$result=array('total'=>$list['count'],'data'=>$list['data'],'msg'=>'');
			$this->json_output($result);
		}
		$this->assign('page_title','仓库管理');
		$this->display('store.list.html');
   	}

   	/**
	 * 所有管理员
	 * @access public 
	 * @return html
	 */
	public function info(){
		//获取列表数据
		$store_id = sget('sid','i');
		if(empty($store_id)){
			$this->error('错误的操作');
		}
		$action=sget('action');
		if($action=='grid'){
			$page = sget("pageIndex",'i',0); //页码
			$size = sget("pageSize",'i',20); //每页数
			$sortField = sget("sortField",'s','input_time'); //排序字段
			$sortOrder = sget("sortOrder",'s','desc'); //排序
			
			$list=$this->db->select("sa.*,ad.name, ad.mobile, ad.username")
					->from('store_admin sa')->join('admin ad','ad.admin_id = sa.admin_id')
					->where("`store_id`=".$store_id)
					->page($page+1,$size)
					->order("$sortField $sortOrder")
					->getPage();
			foreach($list['data'] as $k=>$v){
				$list['data'][$k]['input_time']=$v['input_time']>1000 ? date("Y-m-d H:i:s",$v['input_time']) : '-';
				$list['data'][$k]['update_time']=$v['update_time']>1000 ? date("Y-m-d H:i:s",$v['update_time']) : '-';
			}
			$result=array('total'=>$list['count'],'data'=>$list['data'],'msg'=>'');
			$this->json_output($result);
		}
		$this->assign('sid',$store_id);
		$this->assign('page_title','管理员列表');
		$this->display('storeAdmin.list.html');
	}

	/**
	 * 保存(新)仓库信息
	 * @access public
	 */
	public function ajaxSave(){
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //传递的参数
		if(empty($data)){
			$this->error('错误的请求');
		}
		$data = $data+array(
			'input_time'=>CORE_TIME,
			'input_admin'=>$_SESSION['name'],
		);
		$result = $this->db->add($data);
		if(!$result) $this->error('操作失败');
		$cache=cache::startMemcache();
		$cache->delete('store');
		$this->success('操作成功');
	}

	/**
	 * 删除数据
	 * @access public
	 */
	public function remove(){
		$this->is_ajax=true; //指定为Ajax输出
		$ids=sget('ids','s');
		if(empty($ids)){
			$this->error('操作有误');
		}
		$result=$this->db->where("id in ($ids)")->delete();
		if($result){
			$cache=cache::startMemcache();
			$cache->delete('store');
			$this->success('操作成功');
		}else{
			$this->error('删除操作失败');
		}
	}

	
	/**
	 * 保存行内编辑仓库数据
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
			$_id=$v['id'];
			if($_id>0){
				$update=array(
		            'store_name'   => $v['store_name'],
		            'store_tel'    =>$v['store_tel'],
		            'store_address'=>$v['store_address'],
		            'remark'       =>$v['remark'],
					'update_time'  =>CORE_TIME,
					'update_admin' =>$_SESSION['name'],
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
	 * 选定业务员
	 */
	public function checkLock(){
		$this->is_ajax=true; //指定为Ajax输出		
		$data = sdata(); //传递的参数
		if(empty($data)) $this->error('错误的请求');

		$id=M('product:store_admin')->replaceByStoreId($data[admin_id],$data[store_id]);
		if($id) $this->error('此业务员已经绑定');

		$update=array(
				'input_time'=>CORE_TIME,
				'input_admin'=>$_SESSION['name'],
				);
		$result =$this->db->model('store_admin')->add($data+array('update_time'=>CORE_TIME,'update_admin'=>$_SESSION['name'],));
		if(!$result) $this->error('操作失败');
		$this->success('操作成功');
	}

}