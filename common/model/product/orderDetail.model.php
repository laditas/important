<?php 
//订单模型
class orderDetailModel extends model{
	public function __construct() {
		parent::__construct(C('db_default'), 'order_detail');
	}

	/**
	 * 通过明细ID 或许指定值
	 */
	public function getColByDetId($id=0,$col='p_id'){
		$result=$this->select("$col")->where("id='$id'")->getOne();
		return empty($result) ? false : $result;
	}

}