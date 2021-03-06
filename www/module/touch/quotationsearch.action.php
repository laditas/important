<?php
/**
*条件搜索信息
*/
class quotationsearchAction extends homeBaseAction
{

	public function __init() {
		$this->debug = false;
		$this->db=M('public:common')->model('purchase');
    }
    //开始搜索
    public function doSearch(){
    	//当前用户的id
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
    	//搜素关键字
        $keywords = sget('keywords','s');
        //存取数值
        $temp = array();
        $result = array();
        if(empty($keywords)){
        	$this->error('非法来路');
        }
        //筛选产品类型
        $p_types = array('1'=>'HDPE','2'=>'LDPE','3'=>'LLDPE','4'=>'PP','5'=>'PVC');
        if(in_array($keywords, $p_types)){
        	$keyValue = $this->_getProKey($p_types,$keywords);
        }
        //1,上架2,下架
        $type = sget('type','s');
        $where="(fa.f_name like '%{$keywords}%' or pro.model like '%{$keywords}%' or pro.product_type='{$keyValue}') and pur.user_id={$this->user_id}";
        if($type == '1'){
    		$where.=" and pur.shelve_type=1 and type=2";
    		$data = $this->db->select('pur.id,pur.p_id,pro.model,pro.product_type,pur.unit_price,fa.f_name,pur.number,pur.store_house,pur.input_time,pur.shelve_type')->from('purchase pur')
	        ->join('product pro','pur.p_id=pro.id')
	        ->join('factory fa','pro.f_id=fa.fid')
	        ->where($where)
	        ->getAll();
	        $resultData = $this->_reverseData($data);
	        $this->json_output($resultData);
        }elseif ($type == '2') {
    		$where.=" and pur.shelve_type=2 and type=2";
    		$data = $this->db->select('pur.id,pur.p_id,pro.model,pro.product_type,pur.unit_price,fa.f_name,pur.number,pur.store_house,pur.input_time,pur.shelve_type')->from('purchase pur')
	        ->join('product pro','pur.p_id=pro.id')
	        ->join('factory fa','pro.f_id=fa.fid')
	        ->where($where)
	        ->getAll();
	        $resultData = $this->_reverseData($data);
	        $this->json_output($resultData);
        }else{
            $where.=" and type=2";
	        $data = $this->db->select('pur.id,pur.p_id,pro.model,pro.product_type,pur.unit_price,fa.f_name,pur.number,pur.store_house,pur.input_time,pur.shelve_type')->from('purchase pur')
	        ->join('product pro','pur.p_id=pro.id')
	        ->join('factory fa','pro.f_id=fa.fid')
	        ->where($where)
	        ->getAll();
	        $resultData = $this->_reverseData($data);
	        $this->json_output($resultData);
        }

    }
    //转换数据
    private function _reverseData($data){
    	//遍历data
	        foreach ($data as $value) {
                $temp['id'] = $value['id'];
                $temp['p_id'] = $value['p_id'];
	        	$temp['model'] = $value['model'];
                $temp['product_type'] = L('product_type')[$value['product_type']];
	        	$temp['unit_price'] = $value['unit_price'];
	        	$temp['f_name'] = $value['f_name'];
	        	$temp['number'] = $value['number'];
	        	$temp['store_house'] = $value['store_house'];
	        	$temp['input_time'] = $value['input_time'];
	        	$temp['shelve_type'] = $value['shelve_type'];
	        	$result[]=$temp;
	        	unset($temp);
	        }
	        return $result;
    }
    //获取当前类型的键
    private function _getProKey($p_types,$keywords){
    	foreach ($p_types as $key => $value) {
    		if($value == strtoupper($keywords))
    			return $key;
    	}
    }
}