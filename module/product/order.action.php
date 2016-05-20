<?php 
/**
 * 订单管理
 */
class orderAction extends adminBaseAction {
	public function __init(){
		$this->debug = false;
		$this->db=M('public:common')->model('order');
		$this->doact = sget('do','s');
		$this->assign('order_source',L('order_source')); //订单来源
		$this->assign('pay_method',L('pay_method')); //付款方式
		$this->assign('transport_type',L('transport_type')); //运输方式
		$this->assign('business_model',L('business_model')); //业务模式
		$this->assign('financial_records',L('financial_records')); //财务记录
		$this->assign('order_status',L('order_status')); //订单审核
		$this->assign('goods_status',L('goods_status')); //发货状态
		$this->assign('invoice_status',L('invoice_status')); //开票状态
		$this->assign('price_type',L('price_type')); //价格单位
		$this->assign('in_storage_status',L('in_storage_status')); //入库状态
		$this->assign('order_type',L('order_type')); //销售类型
	}
	/**
	 *
	 * @access public
	 * @return html
	 */
	public function init(){
		$doact=sget('do','s');
		$action=sget('action','s');
		$order_type=sget('order_type','s');
		if($action=='grid'){ //获取列表
			$this->_grid();exit;
		}
		$this->assign('order_type',$order_type);
		$this->assign('doact',$doact);
		$this->assign('page_title','订单管理列表');
		$this->display('order.list.html');
	}

	/**
	 * Ajax获取列表内容
	 * @access private 
	 * @return html
	 */
	private function _grid(){
		$page = sget("pageIndex",'i',0); //页码
		$size = sget("pageSize",'i',20); //每页数
		$sortField = sget("sortField",'s','input_time'); //排序字段
		$sortOrder = sget("sortOrder",'s','desc'); //排序
		//筛选
		$where.= 1;
		//筛选状态
		if(sget('type','i',0) !=0) $order_type=sget('type','i',0);//订单类型
		if(sget('order_type','i',0) !=0) $order_type=sget('order_type','i',0);
		if($order_type !=0)  $where.=" and `order_type` =".$order_type;
		$order_source=sget('order_source','i',0);//订单来源
		if($order_source !=0)  $where.=" and `order_source` =".$order_source;
		$pay_method=sget('pay_method','i',0);//付款方式
		if($pay_method !=0)  $where.=" and `pay_method` =".$pay_method;
		$transport_type=sget('transport_type','i',0);//运输方式
		if($transport_type !=0)  $where.=" and `transport_type` =".$transport_type;
		$business_model=sget('business_model','i',0);//业务模式
		if($business_model !=0)  $where.=" and `business_model` =".$business_model;
		$order_status=sget('order_status','i',0);//订单审核
		if($order_status !=0)  $where.=" and `order_status` =".$order_status;
		$goods_status=sget('goods_status','i',0);//发货状态
		if($goods_status !=0)  $where.=" and `order_source` =".$goods_status;
		//筛选时间
		$sTime = sget("sTime",'s','input_time'); //搜索时间类型
		$where.=getTimeFilter($sTime); //时间筛选
		//关键词搜索
		$key_type=sget('key_type','s','order_sn');
		$keyword=sget('keyword','s');
		if(!empty($keyword) && $key_type=='order_name'  ){
			$where.=" and `$key_type`  like '%$keyword%' ";
		}elseif(!empty($keyword) && $key_type=='c_id'){
			$keyword=M('product:order')->getOidByCname($keyword);
			$where.=" and `$key_type` in ('$keyword') ";
		}elseif(!empty($keyword)){
			$where.=" and `$key_type`  = '$keyword' ";
		}
		$list=$this->db->where($where)
				->page($page+1,$size)
				->order("$sortField $sortOrder")
				->getPage();
		foreach($list['data'] as $k=>$v){
			$list['data'][$k]['c_name']=M("user:customer")->getColByName($list['data'][$k]['c_id']);//根据cid取客户名
			$list['data'][$k]['input_time']=$v['input_time']>1000 ? date("Y-m-d H:i:s",$v['input_time']) : '-';
			$list['data'][$k]['update_time']=$v['update_time']>1000 ? date("Y-m-d H:i:s",$v['update_time']) : '-';
			$list['data'][$k]['sign_time']=$v['sign_time']>1000 ? date("Y-m-d H:i:s",$v['sign_time']) : '-';
			$list['data'][$k]['order_source']=L('order_source')[$v['order_source']]; 
			$list['data'][$k]['pay_method'] =L('pay_method')[$v['pay_method']];
			$list['data'][$k]['transport_type']=L('transport_type')[$v['transport_type']];
			$list['data'][$k]['business_model']=L('business_model')[$v['business_model']];
			$list['data'][$k]['financial_records']=L('financial_records')[$v['financial_records']];
			$list['data'][$k]['order_status']=L('order_status')[$v['order_status']];
			$list['data'][$k]['goods_status']=L('goods_status')[$v['goods_status']];
			$list['data'][$k]['invoice_status']=L('invoice_status')[$v['invoice_status']];
		}
		$result=array('total'=>$list['count'],'data'=>$list['data']);
		$this->json_output($result);
	}
	/**
	* 订单信息
	* @access public
	*/
	public function info(){
		$o_id=sget('oid','i',0);
		$type=sget('type','s');
		if($o_id<1){
			$order_sn=genOrderSn();
			$this->assign('order_sn',$order_sn);
			$this->assign('otype','addopus');
			$this->display('order.edit.html');
			exit;
		}
		$info=$this->db->getPk($o_id); //查询订单信息
		if(empty($info)){
			$this->error('错误的订单信息');	
		}
		if($info['c_id']>0) $c_name = M('user:customer')->getColByName("$info[c_id],c_name");
		$info['sign_time']=date("Y-m-d",$info['sign_time']);
		$info['pickup_time']=date("Y-m-d",$info['pickup_time']);
		$info['delivery_time']=date("Y-m-d",$info['delivery_time']);
		$info['payment_time']=date("Y-m-d",$info['payment_time']);
		$this->assign('c_name',$c_name);
		$this->assign('type',$type);
		$info['total_price']=M("product:order")->getOrdNum($info['o_id'],1);
		$this->assign('info',$info);//分配订单信息
		if($type=="edit"){
			$this->display('order.edit.html');
			exit;
		}	
		$order_type = $info['order_type'] == 1? 'saleLog' : 'purchaseLog';


		$this->assign('order_type',$order_type);
		$this->assign('o_id',$o_id);
		$this->display('order.viewInfo.html');
	}
	/**
	 * 新增及修改订单
	 * @access public 
	 * @return html
	 */
	public function addSubmit() {
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //获取UI传递的参数
		if(empty($data)) $this->error('错误的请求');	
		$data['sign_time']=strtotime($data['sign_time']);
		$data['pickup_time']=strtotime($data['pickup_time']);
		$data['delivery_time']=strtotime($data['delivery_time']);
		$data['payment_time']=strtotime($data['payment_time']);
		foreach ($data as $k=> $v) {
			if( preg_match('/\d/',$k) ){
				preg_match_all('/\d/',$k,$matches);
				$detail[$matches[0][0]][substr($k,0,strlen($k)-1)]=$v;
			}else{
				$data[$k]=$v;
			}
		}
		if($data['o_id']>0){ //编辑
			$up_data = array(
				'total_price'=>M("product:order")->getOrdNum($data['o_id'],1), //计算总金额			
				'update_time'=>CORE_TIME,
				'update_admin'=>$_SESSION['name'],
			);	
			$result = $this->db->where('o_id='.$data['o_id'])->update($data+$up_data);
		}else{ //新增
			$this->db->startTrans(); //开启事务
			$add_data=array(
				'input_time'=>CORE_TIME,
				'input_admin'=>$_SESSION['name'],
			);
			$this->db->add($data+$add_data);
			$o_id=$this->db->getLastID(); //订单ID
			if(!empty($detail)){
				for($i=1;$i<=count($detail);$i++){
					$detail[$i]['o_id']=$o_id;
					$detail[$i]['purchase_order_no']=genOrderSn();
					$this->db->model('sale_log')->add($detail[$i]+$add_data);
				}
			}
			if($this->db->commit()){
				$up_data = array(
					'total_price'=>M("product:order")->getOrdNum($o_id,1), //计算总金额			
					'update_time'=>CORE_TIME,
					'update_admin'=>$_SESSION['name'],
				);	
				$result = $this->db->where('o_id='.$o_id)->update($up_data);
				$this->success('操作成功');
			}else{
				$this->db->rollback();
				$this->error($result['msg']);
			}

		}

	
	}
	/**
	 * Ajax删除
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
				if(M('product:order')->getODidByOid($v)){
					continue;
				}else{
					$result=$this->db->where("o_id = ($v)")->delete();
				}
			}
		}
		if($result){
			$this->success('操作成功');
		}else{
			$this->error('订单有相关明细存在');
		}
	}
}