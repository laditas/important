<?php
/**
*开票资料管理控制器
*/
class customer_billingAction extends adminBaseAction
{
	public function __init(){
		$this->db=M('public:common')->model('customer_billing');
		// $res = $this->db->model('customer')->select('c_id,customer_manager')->getAll();
		// set_time_limit(0);
		// foreach ($res as $key => $v) {
		// 	$this->db->model('customer_billing')->where('c_id='.$v['c_id'])->update(array('customer_manager'=>$v['customer_manager'],));
		// }

	}
	/**
	 *
	 * @access public
	 * @return html
	 */
	public function init(){
		$action=sget('action','s');
		if($action=='grid'){ //获取列表
			$this->_grid();exit;
		}
		$this->assign('choose',sget('choose','s'));
		$this->assign('doact',$doact);
		$this->assign('page_title','开票资料管理');
		$this->display('customer_billing.list.html');

	}

	public function _grid(){
		//获取列表数据
		$page = sget("pageIndex",'i',0); //页码
		$size = sget("pageSize",'i',20); //每页数
		$sortField = sget("sortField",'s','c_id'); //排序字段
		$sortOrder = sget("sortOrder",'s','desc'); //排序
		//搜索条件
		$where="`display_status`=1 ";//未假删除的数据
		//审核状态搜索
		$status = sget('status','i');
		$where .=" and `status` = $status ";
		//关键词
		$key_type=sget('key_type','s','c_name');
		$keyword=sget('keyword','s');
		if(!empty($keyword)){
			if ($key_type == 'c_name') {
				$c_ids = M('user:customer')->getLikeCidByCname($keyword,$condition='c_id');
				$where.=" and `c_id` in ($c_ids)";
			}else if($key_type == 'admin'){
				$customer_manager = M('rbac:adm')->getAdmin_Id($keyword);
				$where.=" and `customer_manager` = $customer_manager";
			}else{
				$where.=" and $key_type = '$keyword' ";
			}
		}

		//筛选自己的客户
		$res=array();
		if($_SESSION['adminid'] != 1 && $_SESSION['adminid'] > 0){
			$sons = M('rbac:rbac')->getSons($_SESSION['adminid']);  //领导
			$pools = M('user:customer')->getCidPoolCus($sons);
			$where .= " and `customer_manager` in ($sons) ";

			if(!empty($keyword) && $c_ids){//shousuo
				//我用这个用户的id去共享表查询下看有没有这个id
				if(M('user:customer')->judgeShare($c_ids)) $where .= " or `c_id` in ($c_ids)";
			}else{
				// 默认列表显示全部的共享客户
				if(!empty($pools)){
					$cids = explode(',', $pools);
					$where .= " or `c_id` in ($pools)";
				}
			}
		}
		$list=$this->db->model('customer_billing')->where($where)
					->page($page+1,$size)
					->order("$sortField $sortOrder")
					->getPage();
		foreach($list['data'] as $k=>$v){
			$list['data'][$k]['input_time']=$v['input_time']>1000 ? date("Y-m-d H:i:s",$v['input_time']) : '-';
			$list['data'][$k]['update_time']=$v['update_time']>1000 ? date("Y-m-d H:i:s",$v['update_time']) : '-';
			// $list['data'][$k]['invoice_account']=desDecrypt($v['invoice_account']);
			$list['data'][$k]['c_name'] = M('user:customer')->getColByName($v['c_id']);
			//关联业务员
			$list['data'][$k]['username']=M('rbac:adm')->getUserByCol($v['customer_manager']);
			//审核状态
			if ($v['status']==1) {
				$list['data'][$k]['status']='审核通过';
			}elseif($v['status']==2){
				$list['data'][$k]['status']='未审核';
			}else{
				$list['data'][$k]['status']='数据有误';
			}

		}

		$result=array('total'=>$list['count'],'data'=>$list['data'],'msg'=>'');
		$this->json_output($result);


	}

	/**
	 * 打开新增或修改申请开票资料页面
	 */
	public function info(){
		$id = sget('id','i',0);
		$list=$this->db->from('customer_billing cb')
			->join('customer c','c.c_id=cb.c_id')
			->where("cb.id={$id}")
			->select("cb.*,c.c_name")
			->getRow();
		$this->assign('id',$list['id']);
		$this->assign('user_id',$list['user_id']);
		$this->assign('c_id',$list['c_id']);
		$this->assign('tax_id',$list['tax_id']);
		$this->assign('invoice_bank',$list['invoice_bank']);
		$this->assign('invoice_address',$list['invoice_address']);
		$this->assign('invoice_tel',$list['invoice_tel']);
		$this->assign('invoice_account',$list['invoice_account']);
		$this->assign('fax',$list['fax']);
		$this->assign('c_name',$list['c_name']);
		$this->assign('ems_address',$list['ems_address']);

		$this->display('customer_billing.add.html');
	}

	/**
	 * 保存添加的开票信息
	 * @access public
	 */
	public function ajaxSave(){
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //传递的参数
		if(empty($data)){
			$this->error('错误的请求');
		}
		if(validCompanyBankNo($data['invoice_account'])['err']==1){$this->error('银行卡号错误');}
		// $data['invoice_account'] = desEncrypt($data['invoice_account']);
		if ($data['id']>0) {
			$result = $this->db->where('id='.$data['id'])->update($data+array('update_time'=>CORE_TIME,'update_admin'=>$_SESSION['name'],'status'=>2,));
		}else{
			if(!M('user:customer_billing')->curUnique($name='c_id',$value="$data[c_id]")){
				$this->error('此客户已添加开票资料');
			}
			$data['customer_manager'] = M('rbac:adm')->getAdmin_Id($data['manager']);
			$data['status'] ='2';
			$result = $this->db->add($data+array('input_time'=>CORE_TIME,'input_admin'=>$_SESSION['name'],));
		}

		if(!$result) $this->error('操作失败');
		$cache=cache::startMemcache();
		$cache->delete('customer_billing');
		$this->success('操作成功');
	}

	// 显示开票资料，不可编辑
	public function billingInfo(){
		$id = sget('id','i',0);
		$this->info=$this->db->from('customer_billing cb')->leftjoin('customer c','c.c_id=cb.c_id')->where("cb.c_id={$id}")->select("cb.*,c.c_name")->getRow();
		$this->display('customer_billing.info.html');
	}

	/**
	 * 保存行内编辑仓库数据
	 * @access public
	 * @return html
	 */
	public function save(){
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //获取UI传递的参数
		if(empty($data)) $this->error('错误的操作');
		$sql=array();
		foreach($data as $v){
			$_id=$v['id'];
			if($_id>0){
				$update=array(
					// 'input_time'  =>strtotime($v['input_time']),
					'remark'=>$v['remark'],
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
			$cache->delete('customer_billing');
			$this->success('操作成功');
		}else{
			$this->error('数据处理失败');
		}
	}


}