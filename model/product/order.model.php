<?php 
//订单模型
class orderModel extends model{
	public function __construct() {
		parent::__construct(C('db_default'), 'order');
	}
	/**
	 * 更具字段取出对应的值
	 */
	public function getColByName($value=0,$col='order_name',$condition='o_id'){
		$result =  $this->select("$col")->where("$condition='$value'")->getOne();
		return empty($result) ? '-' : $result;
	}
	/**
	 * 模糊查询客户名匹配的订单
	 */
	public function getOidByCname($value=''){
		$arr=$this->model('customer')->select('c_id')->where("c_name like '%".$value."%'")->getAll();
		foreach ($arr as $key => $value) {
			$cids[]=$value['c_id'];
		}
		$data = implode(',',$cids);
		return empty($data)? false : $data;
	}
	/**
	 * 模糊查询订单名匹配的明细
	 */
	public function getidByOname($value=''){
		$arr=$this->select('o_id')->where("order_name like '%".$value."%'")->getAll();
		foreach ($arr as $key => $value) {
			$oids[]=$value['o_id'];
		}
		$data = implode(',',$oids);
		return empty($data)? false : $data;
	}

	/**
	 * 根据id查订单明细表的值
	 */
	public function getColByOid($o_id=0,$col='id'){
		$result=$this->select("$col")->where("o_id='$o_id'")->getOne();
		return empty($result) ? false : $result;
	}

	/**
	 * 根据o_id去查对应的客户名
	 */
	public function getCnameByOid($o_id=0){
		$result = $this->select('c.c_name')->from('order o')->join('customer c' , 'o.c_id = c.c_id')->where(" o_id = $o_id ")->getOne();
		return empty($result) ? '-' : $result ;
	}

	/**
	 * 根据id查订单明细表的值
	 */
	public function getODidByOid($o_id=0,$col='id'){
		$result=$this->model('sale_log')->select("$col")->where("o_id='$o_id'")->getOne();
		return empty($result) ? false : $result;
	}

	/**
	 * 根据字段取出该表所有的值
	 */
	public function getAllByName($value=0,$condition='o_id'){
		$result =  $this->select("*")->where("$condition='$value'")->getAll();
		return empty($result) ? '-' : $result;
	}

	/** (自营)
	 *获取近三个月(待审核)订单信息('待审核 order_status=1，已取消order_status=3，$col =collection_status 代开票 ')
     	*/
	public function getOrder($uid=0,$status = 1, $col ='order_status'){
		$tm = date(strtotime('-90 day'));
		return $this->from('order')->select('COUNT(o_id) AS number')->where("user_id=$uid and input_time > $tm and $col = $status")->getOne();
	}
	
}