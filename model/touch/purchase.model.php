<?php
/**
*求购模型
*/
class purchaseModel extends model
{
	public function __construct() {
		parent::__construct(C('db_default'), 'purchase');
	}
	public function getPurchase($c_id){
		$pdata = array();
		$result = array();
		$purchase = $this->model('purchase')->where('c_id='.$c_id)->getAll();
		foreach ($purchase as $value1) {
			$product = $this->model('product')->select('model,f_id')->where('id='.$value1['p_id'])->getRow();
			$f_name = $this->model('factory')->select('f_name')->where('fid='.$product['f_id'])->getRow();
			$pdata[] =$product['model'];
			$pdata[] =$value1['unit_price'];
			$pdata[] =$f_name['f_name'];
			$pdata[] =$value1['number'];
			$pdata[] =$value1['store_house'];
			$pdata[] =$value1['input_time'];
			$result[] = $pdata;
			unset($pdata);
		}
		//二维数组
		return $result;
	}
}