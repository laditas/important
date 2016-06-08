<?php 
/**
 * 出库详情管理
 */
class outStorageAction extends adminBaseAction {
	public function __init(){
		$this->debug = false;
		$this->db=M('public:common')->model('out_storage');
		$this->assign('ship_company',L('ship_company')); //物流公司
		$this->assign('out_type',L('out_type')); //出库类型
		$this->assign('out_storage_status',L('out_storage_status')); //出库状态

	}
	/**
	 * 新增发货记录
	 */
	public function info(){
		$page = sget("pageIndex",'i',0); //页码
		$size = sget("pageSize",'i',20); //每页数
		$sortField = sget("sortField",'s','input_time'); //排序字段
		$sortOrder = sget("sortOrder",'s','desc'); //排序
		$o_id=sget('o_id','i',0);
		if( $o_id<1 ) $this->error('错误的出库信息');
		$out_no=genOrderSn();//出库单号
		$action=sget('action','s');
		if($action=='grid'){
			$where = "`o_id`=".$o_id;
			$list=$this->db->model('sale_log')->where($where)
					->page($page+1,$size)
					->order("$sortField $sortOrder")
					->getPage();
			foreach($list['data'] as $k=>&$v){
				$v['model']=M("product:product")->getModelById($v['p_id']); //获取客户名称
			}
			$result=array('total'=>$list['count'],'data'=>$list['data']);
			$this->json_output($result);
		}
		$out_info=$this->db->where("o_id = '$o_id'")->getRow();
		if(!$out_info) {
			$this->assign('doyet','doyet');
		}
		$c_id = $this->db->model('order')->select('c_id')->where("o_id = '$o_id'")->getOne();
		$out_info['out_date']=date("Y-m-d",$out_info['out_date']);
		$out_info['c_name']=M("user:customer")->getColByName($out_info['c_id']); //获取客户名称
		$out_info['admin_name']=M("product:outStorage")->getNameBySid($out_info['store_aid']); //获得出库人姓名
		$this->assign('c_id',$c_id);
		$this->assign('out_info',$out_info);
		$this->assign('out_no',$out_no);
		$this->assign('o_id',$o_id);
		$this->display('outStorage.edit.html');
	}
	/**
	 * 异步保存
	 */
	public function addSubmit(){
		$this->is_ajax=true; //指定为Ajax输出
		$data=sdata(); //获取UI传递的参数
		$i=0;
		foreach ($data as $k=> $v) {
			if( preg_match('/\d/',$k) ){ //筛选明细id 和 发出的数量
				preg_match_all('/([0-9]+)/',$k,$matches);
				$_out[$i]['id']=$matches[0][0];
				$_out[$i]['number']=$v;
				$totle_out+=$v;
				$i++;
			}else{
				$data[$k]=$v;
			}
		}
		if(empty($data)) $this->error('操作有误');	
		$_data=array(
			'c_id'=>$this->db->model('order')->select('c_id')->where('o_id = '.$data['o_id'])->getOne(),
			'out_no'=>genOrderSn(), //出库单号
			'input_time'=>CORE_TIME,
			'input_admin'=>$_SESSION['name'],
			'customer_manager'=>$_SESSION['adminid'],
			'out_date'=>CORE_TIME,
			'number'=>$totle_out,
		);
		$this->db->startTrans(); //开启事务
		try {
			if( !$this->db->model('out_storage')->add($data+$_data) ) throw new Exception("新增出库单失败");
			$out_id=$this->db->getLastID(); //获取新增出库单ID
			foreach ($_out as $k => $v) {
				if( !$this->db->model('sale_log')->where(' id = '.$v['id'])->update(' number = number - '.$v['number']) ) throw new Exception("明细更新失败");	//在明细中减去出库的数量
				$sale_detail = $this->db->model('sale_log')->where(' id = '.$v['id'])->getRow();
				$sub_price+=$sale_detail['unit_price']*$v['number'];
				$out_detail=array(
					'o_id'=>$data['o_id'],
					'out_id'=>$out_id,
					'detail_id'=>$v['id'],
					'number'=>$v['number'],
					'store_id'=>$sale_detail['store_id'],
					'store_aid'=>$sale_detail['store_aid'],
					'p_id'=>$sale_detail['p_id'],
				);
				if( !$this->db->model('out_log')->add($out_detail+$_data) ) throw new Exception("新增出库明细失败");
			}
			if( !$this->db->model('order')->where('o_id = '.$data['o_id'])->update('total_price = total_price -'.$sub_price) )  throw new Exception("订单总金额更新失败");
		} catch (Exception $e) {
			$this->db->rollback();
			$this->error($e->getMessage());
		}
		$this->db->commit();
		$this->success('操作成功');




		// $diff_num=M("product:outStorage")->checkNum($data['sale_id'],$data['number']); //订单和出库数量比较
		// if(!$diff_num) $this->error('数量有误');
		// if($data['doyet'] == 'doyet') $result=$this->db->add($data+$_data);		
		// $out_log=$this->db->model('out_log')->add($data+$_data);
		// $this->db->model('sale_log')->where("id = ".$data['sale_id'])->update(" number = number - ".$diff_num);
		// if($this->db->commit()){
		// 		$this->success('操作成功');
		// 	}else{
		// 		$this->db->rollback();
		// 		$this->error('数据处理失败');
		// 	}	
	}
	/**
	 * 获取联系人信息
	 * @access public
	 */
	function get_store_aid(){
		$this->is_ajax=true;
		$s_id=sget('sid','i');
		$admin_list=M('product:store_admin')->getColByid($s_id);
		$this->json_output($admin_list);
	}
	/**
	 * Ajax删除流水
	 * @access private 
	 */
	public function remove(){
		$this->is_ajax=true; //指定为Ajax输出
		$ids=sget('ids','s');
		if(empty($ids)){
			$this->error('操作有误');	
		}
		$data = explode(',',$ids);
		if(is_array($data)){
			foreach ($data as $k => $v) {
				$result=$this->db->model('out_log')->where("id in ($ids)")->delete();
			}
		}
		if($result){
			$this->success('操作成功');
		}else{
			$this->error('数据处理失败');
		}
	}


	/**
	 * 编辑已存在的数据
	 * @access public 
	 * @return html
	 */
	public function save(){
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //获取UI传递的参数
		$sql=array();
		if(empty($data)){
			$this->error('操作数据为空');
		}
		foreach($data as $k=>$v){
			$_data=array(
				'number'=>$v['number'],		 
			);
			$diff_num=M("product:outStorage")->checkNum($v['sale_id'],$v['number']); //订单和出库数量比较
			if(!$diff_num) $this->error('数量有误');
			$result=$this->db->model('out_log')->wherePk($v['id'])->update($_data);
		}
		if($result){
			$this->success('操作成功');
		}else{
			$this->error('数据处理失败');
		}
	}
	
}