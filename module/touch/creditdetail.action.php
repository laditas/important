<?php
/**
*积分明细
*/
class creditdetailAction extends homeBaseAction
{
	public function __init() {
		$this->debug = false;
		$this->db=M('public:common')->model('points_bill');
    }
    public function init(){
    	$this->display('detail');
    }
    //返回积分明细
    public function get_creditdetail(){
    	//获取用户
		$uid=sget('uid','i',0);
		$result = array();
		if($uid>0){
			$points = M('points:pointsBill')->getUerPoints($uid);
			$result = M('touch:creditdetail')->getCreditDetail($uid);
		}
		$this->json_output(array($points,$result));
	}
}