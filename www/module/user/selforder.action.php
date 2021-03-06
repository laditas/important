<?php

/**
 * 自营商城订单
 */
class selforderAction extends userBaseAction{

	protected $db;
	public function __init(){
		$this->db=M('public:common');
	}


	public function init()
	{
		$this->act="selforder";

		$this->transport_type=L('transport_type');                //运输状态
		$this->goods_status=L('goods_status');			          //发货状态
		$this->invoice_status=L('invoice_status');                //订单开票状态
		$this->order_status=L('order_status');                    //订单状态(含交易完成)
		$this->collection_p_status=L('collection_p_status');      //订单付款状态
		$this->type=1;

		$where="o.user_id=$this->user_id";

		//订单筛选
		if($orderSn=trim(sget('sn','s',''))){
			$this->sn=$orderSn;
			$where.=" and o.order_sn='{$orderSn}'";
		}

		if($type=sget('type','s','')){
			//全部订单
			if($type==1){
				$this->type=$type;
				$where;
			}
			//已审核
			if($type==2){
				$order_status=2;
				$this->type=$type;
				$where.=" and o.order_status={$order_status}";
			}
			//待审核
			if($type==3){
				$order_status=1;
				$this->type=$type;
				$where.=" and  o.order_status={$order_status}";
			}

			//待开票
			if($type==4){
				$invoice_status=1;
				$this->type=$type;
				$where.= " and o.invoice_status={$invoice_status} and o.order_status=2";
			}
			//待付款
			if($type==5){
				$collection_status=1;
				$this->type=$type;
				$where.=" and o.collection_status={$collection_status} and o.order_status=2";
			}
			if($type==6){
				$order_status=3;
				$this->type=$type;
				$where.=" and o.order_status={$order_status}";
			}
		}

		$page=sget('page','i',1);
		$size=4;
		$orderList=$this->db->model('order as `o`')
				->leftjoin('collection as col','col.o_id=o.o_id')
			->select('o.o_id,o.order_name,o.order_sn,o.user_id,o.total_num,o.total_price,o.pay_method,o.transport_type,o.freight_price,o.order_status,o.invoice_status,o.out_storage_status,o.input_time,o.remark,o.collection_status,o.pickup_time,o.delivery_time,col.collected_price,col.payment_time')
				->where($where)
				->page($page,$size)
				->order('`o`.input_time desc')
				->getPage();


		$this->pages = pages($orderList['count'], $page, $size);
		foreach ($orderList['data'] as &$value) {
			$value['totalNum']=$this->db->model('sale_log')->where("o_id={$value['o_id']}")->select("sum(number)")->getOne();
			$value['model']=$this->db->model('sale_log as s')
				->leftjoin('product p','s.p_id=p.id')
				->leftjoin('factory f','p.f_id=f.fid')
				->select("p.model")
				->where("o_id={$value['o_id']}")->getOne();
			$value['f_name']=$this->db->model('sale_log as s')
				->leftjoin('product p','s.p_id=p.id')
				->leftjoin('factory f','p.f_id=f.fid')
				->select("f_name")
				->where("o_id={$value['o_id']}")->getOne();
		}

		$this->assign('orderList',$orderList);
		$this->display('selforder');
	}


	/**
	 * 自营订单明细
	 * @Author: yuanjiaye
     */
	public function detail()
	{
		$id=sget('id','i',0);
		if(!$this->db->model('order')->where("o_id=$id and user_id=$this->user_id")->getRow()) $this->forward('/');
		$order=$this->db->from('order o')
			->leftjoin('admin ad','o.customer_manager=ad.admin_id')
			->select('o.o_id,o.order_name,o.order_sn,o.user_id,o.total_price,o.pay_method,o.transport_type,o.freight_price,o.order_status,o.invoice_status,o.out_storage_status,o.input_time,o.remark,o.collection_status,o.pickup_time,o.delivery_time,o.sign_time,o.sign_place,o.payment_time,ad.name,ad.mobile')
			->where("o_id=$id and user_id={$this->user_id}")
			->getRow();

		$order['order_name']='-';
		$order['transport_type']==1?($order['pickup_time']):($order['delivery_time']=$order['delivery_time']);
		$order['transport_type']==1?($order['pickup_location']='--'):($order['delivery_location']=$order['delivery_location']);

		$sale_log=$this->db->from('sale_log s')
			->leftjoin('product p','s.p_id=p.id')
			->leftjoin('factory f','p.f_id=f.fid')
			->select('s.id,s.number,s.unit_price,p.model,p.product_type,f.f_name')
			->where("o_id={$order['o_id']}")
			->getAll();
		foreach ($sale_log as $key => &$value) {
			$value['totalPrice']=$value['number']*$value['unit_price'];
		}

		$this->assign('sale_log',$sale_log);
		$this->assign('order',$order);
		$this->display('selforder.detail');
	}
	
	// 订单支付
	public function pay()
	{
	    if($_POST){
	        $this->is_ajax=true;
	        $data=saddslashes($_POST);
	        $id = empty($data['id'])?0:$data['id'];
	        if(!$this->db->model('order')->where("o_id=$id and user_id=$this->user_id")->getRow()) $this->forward('/');
	        $order=$this->db->from('order o')
	        ->join('admin ad','o.customer_manager=ad.admin_id')
	        ->select('o.*')
	        ->where("o_id=$id and user_id={$this->user_id}")
	        ->getRow();
	        if($order['collection_status']=='3'){
	            $this->error('该订单已付款');
	        }
	        $pay_method = $order['pay_method'];//付款方式 6是东方付通
	        if($pay_method==6){
	            $obj = E('dfftPayment',APP_LIB.'class');//引入dfftPayment类
	            $payID = 'PAY'.date('Ymdhis',time()).'-'.rand(999,9999);
	            //参数封装
	            $params['payID'] =  $payID; // 支付号码，东方付通对此订单的唯一标识，商城必须保存此订单号，下次支付时使用
	            $params['tradeOrder'] =  $order['order_sn']; // 合同号码，商城的订单号
	            $params['mallID'] = $obj->mallID;  // 商城号
	            $params['payType'] = '01010'; // 接口类型，直接支付
	            $c_info = M('user:customer')->getCinfoById($order['c_id']);
	            if($order['order_type']=='1')//销售 中晨卖方
	            {
	                $params['payMemCode'] = $order['c_id'];                    // 付款人代码
	                $params['payMemName'] = $c_info['c_name'];                 // 付款人名称
	                $params['recMemCode'] = '5041';                            // 收款人代码
	                $params['recMemName'] = '上海中晨电子商务股份有限公司';       // 收款人名称
	            }
	            if($order['order_type']=='2')                                  //销售 中晨买方
	            {
	                $params['payMemCode'] = '5041';                            // 付款人代码
	                $params['payMemName'] = '上海中晨电子商务股份有限公司';       // 付款人名称
	                $params['recMemCode'] = $order['c_id'];                    // 收款人代码
	                $params['recMemName'] = $c_info['c_name'];                 // 收款人名称
	            }
	            $params['currency'] = 'CNY';                                   // 人民币
	            $params['payAmt'] = $order['total_price'];                     // 付款金额
	            $params['originalPayID'] = '';                                 // 直接支付不需要赋值
	            $params['callBackUrl'] = str_replace("///", "//",str_replace("http:/", "http://", APP_URL)).'/pay/rtnpay/selforder_callback';                                        //回调通知地址，订单支付成功后通知商城的地址
	            $params['summary'] = '';                                       //摘要
	            // 	        echo "支付号码：".$payID;
	            $params['customFiels'] ='';//自定义字段
	            $params['instAccount']='0'; //优先记账0 或空：不记账99：记账
	            $params['locktag']='0';// 锁定标识 1 锁定到收款方  到货支付此处必须为1 直接支付为0或空
	            $params['bankUse']='';//银行用途
	            $params['bankDigest']='';//银行摘要
	            // 生成签名
	            $params['signature'] = $obj->_getSign(json_encode($params));    //打印签名密文
	            $json = json_encode($params);
	            $dataorder = $obj->_base64Sign($json);
	            $this->db->startTrans();
	            $update=array(
	                'pay_id'      => $payID,
	            );
	            $this->db->model('order')->where("o_id=$id and user_id=$this->user_id")->update(saddslashes($update));
	            $tmp=$this->db->model('pay_message')->select('payID')->where("tradeorder='".$order['order_sn']."' and refundMark=0")->getOne();
	            if(!empty($tmp)){
	                $this->db->model('pay_message')->where("tradeorder='".$order['order_sn']."' and refundMark=0")->delete();
	                $this->db->model('pay_message')->add(saddslashes($params));
	            }else{
	                $this->db->model('pay_message')->add(saddslashes($params));
	            }
	            //自营订单支付操作
	            $remarks = "自营订单支付操作";
	            M('user:applyLog')->addLog($this->user_id,'selforder/pay','',json_encode($params),1,$remarks);
	            if($this->db->commit()){
	                $this->success($dataorder);
	            }else{
	                $this->db->rollback();
	                $this->error('生成失败:'.$this->db->getDbError());
	            }
	            $this->success($dataorder);	            
	        }else{
	            $this->success('线下支付');
// 	            $this->db->startTrans();
// 	            //前台传入后台默认为全部付款
// 	            $update=array(
// 	                'collection_status'   => 3,
// 	            );
// 	            $this->db->model('order')->where("o_id=$id and user_id=$this->user_id")->update(saddslashes($update));
// 	            $collection=$this->db->model('collection')->where("order_sn='".$order['order_sn']."'")->getOne();
// 	            if(empty($collection)){
// 	                $update2=array(
// 	                    'order_sn'=>$order['order_sn'],
// 	                    'order_type'=>$order['order_type'],
// 	                    'c_id'=>$order['c_id'],
// 	                    'o_id'=>$order['o_id'],
// 	                    'total_price'=>$order['total_price'],
// 	                    'collected_price'=>CORE_TIME,
// 	                    'uncollected_price'=>CORE_TIME,
// 	                    'pay_method'=>CORE_TIME,
// 	                    'payment_time'=>CORE_TIME,
// 	                    'remark'=>'网站前台付款',
// 	                    'account'=>CORE_TIME,
// 	                    'collection_status'=>CORE_TIME,
// 	                    'customer_manager'=>CORE_TIME,
// 	                    'input_time'=>CORE_TIME,
// 	                    'input_admin'=>$this->user_id,
// 	                );
// 	                $this->db->model('collection')->add($update2);
// 	            }else{
// 	                $update2=array(
// 	                    'input_time'=>CORE_TIME,
// 	                    'input_admin'=>$this->user_id,
// 	                );
// 	                p($collection+$update2);
// 	                die;
// 	                $this->db->model('collection')->where("order_sn='".$order['order_sn']."'")->add($collection+$update2);
// 	            }
// 	            if($this->db->commit()){
// 	               $this->success('线下支付');
// 	            }else{
// 	                $this->db->rollback();
// 	                $this->error('生成失败:'.$this->db->getDbError());
// 	            }
	        }
	    }
	}
	
// 	// 支付成功回调
// 	public function callback()
// 	{   
// 	    // $token=cookie::get(C('SESSION_TOKEN'));
// 	    file_put_contents($_SERVER['DOCUMENT_ROOT']."/Javatest/Java/pay.txt",$_POST['postdata']);
//         //获取参数
//         if(isset($_POST['postdata']) || !empty($_POST['postdata'])){
//             $postdata = $_POST['postdata'];
//         }else{
//             $postdata = file_get_contents("php://input");
//         }
// //         file_put_contents("./pay.txt", $postdata,FILE_APPEND);
//         $param = json_decode($postdata);
//         if(isset($param)){
//           // 支付消息
//           $message = $param->payMessage;
//           // 支付订单的支付号码
//           $payID = $param->payID;
//           // 支付状态
//           $payStatus = $param->payStatus;
//           // 签名
//           $signature = $param->signature;
          
//           $obj = E('dfftPayment',APP_LIB.'class');//引入dfftPayment类
//           $rtn = $obj->_base64Verify($postdata,$signature);
//           //验证签名，是否为东方付通发送的指令
//           if($rtn == "1"){
//         	// 订单支付成功(其他不处理)
//         	if ($payStatus == "000000") {
//         		//$payID
//         	    $this->db->startTrans();
//         		// 修改订单状态 已支付 (商城逻辑处理)
//         		//状态为3默认全部付款
//         	    $update=array(
//         	        'collection_status' => "3",
//         	    );
//         	    if(!$this->db->model('order')->where("pay_id={$payID}")->update(saddslashes($update))) throw new Exception("更新支付状态失败!");
//         	    if(!$this->db->model('pay_message')->add($param)) throw new Exception("插入支付信息失败!");
//         	    if($this->db->commit()){
//         	        $this->success('生成成功');
//         	    }else{
//         	        $this->db->rollback();
//         	        $this->error('生成失败:'.$this->db->getDbError());
//         	    }
//         	}
//             // 响应支付平台已接收,接收到消息必须返回  // echo "{\"payStatus\":\"000000\"}";
//           }else{
//         	//签名验证失败！
//               $this->error('签名验证失败!');
//           }
//         }else{
//             $this->error('支付失败,回调内容为空!');
//         }
// 	}
	
	//时时查询支付状态
	public function querySucess(){
	    if($_POST){
	        $this->is_ajax=true;
	        $data=saddslashes($_POST);
	        $id = empty($data['id'])?0:$data['id'];
	        if(!$this->db->model('order')->where("o_id=$id and user_id=$this->user_id")->getRow()) $this->error('查询订单失败!');
	        $order=$this->db->from('order o')
	        ->join('admin ad','o.customer_manager=ad.admin_id')
	        ->select('o.*,ad.name,ad.mobile')
	        ->where("o_id=$id and user_id={$this->user_id}")
	        ->getRow();
	        $payid = $order['pay_id'];
	        $rtn = $this->db->model('pay_message')->where("payID='$payid'")->getRow();
	        if(!$rtn) $this->error('查询支付明细失败!');
	        if(trim($rtn['payStatus'])=='000000'){
	            $this->success('支付成功');
	        }else{
// 	            $this->error('支付失败');
	        }
	    }
	}
	
	//取消支付
	//1.先退款 2.退相关库存处理
	public function payCancel(){
		if($_POST){
	        $this->is_ajax=true;
	        $data=saddslashes($_POST);
	        $id = empty($data['id'])?0:$data['id'];
	        if(!$this->db->model('order')->where("o_id=$id and user_id=$this->user_id")->getRow()) {
	            $this->error('查询订单失败!');
	        }
	        $order=$this->db->from('order o')
	        ->join('admin ad','o.customer_manager=ad.admin_id')
	        ->select('o.*,ad.name,ad.mobile')
	        ->where("o_id=$id and user_id={$this->user_id}")
	        ->getRow();
	        $pay_method = $order['pay_method'];//付款方式 6是东方付通
	        if($order['order_status']==3){
	            $this->error('该订单已取消!');
	        }
	        if($pay_method==6){
//     	        $payid = $order['pay_id'];
//     	        $rtn = $this->db->model('pay_message')->where("payID='$payid'")->getRow();
//     	        if(!$rtn) $this->error('查询支付明细失败!');
//     	        $obj = E('dfftPayment',APP_LIB.'class');//引入dfftPayment类
//     	        $payID = 'PAY'.date('Ymdhis',time()).'-'.rand(999,9999);
//     	        //参数封装
//     	        $params['payID'] =  $payID; // 支付号码，东方付通对此订单的唯一标识，商城必须保存此订单号，下次支付时使用
//     	        $params['tradeOrder'] =  $rtn['tradeOrder']; // 合同号码，商城的订单号	        
//     	        $params['mallID'] = $rtn['mallID'];  // 商城号
//     	        $params['payType'] = '01010'; // 接口类型，直接支付
//                 $params['payMemCode'] = $rtn['recMemCode'];                    // 付款人代码
//                 $params['payMemName'] = $rtn['recMemName'];                 // 付款人名称
//                 $params['recMemCode'] = $rtn['payMemCode'];                            // 收款人代码
//                 $params['recMemName'] = $rtn['payMemName'];       // 收款人名称
//     	        $params['currency'] = $rtn['currency'];                                   // 人民币
//     	        $params['payAmt'] = $rtn['payAmt'];                     // 付款金额
//     	        $params['originalPayID'] = '';                                 // 直接支付不需要赋值
//     	        $params['callBackUrl'] = str_replace("///", "//",str_replace("http:/", "http://", APP_URL)).'/pay/rtnpay/selforder_payCancel';                                        //回调通知地址，订单支付成功后通知商城的地址
//     	        $params['summary'] = '';                                       //摘要
//     // 	        echo "支付号码：".$payID;      	        	              
//     	        $params['customFiels'] ='';//自定义字段
//     	        $params['instAccount']='0'; //优先记账0 或空：不记账99：记账
//     	        $params['locktag']='0';// 锁定标识 1 锁定到收款方  到货支付此处必须为1 直接支付为0或空
//     	        $params['bankUse']='';//银行用途
//     	        $params['bankDigest']='';//银行摘要	
//     	        // 生成签名
//     	        $params['signature'] = $obj->_getSign(json_encode($params));    //打印签名密文      
//     	        $json = json_encode($params);     
//     	        $dataorder = $obj->_base64Sign($json);
//     	        $this->db->startTrans();
//     	        $update=array(
//     	            'pay_id'      => $payID,
//     	        );
//     	        $this->db->model('order')->where("o_id=$id and user_id=$this->user_id")->update(saddslashes($update));
//     	        $tmp=$this->db->model('pay_message')->select('payID')->where("tradeorder='".$order['order_sn']."' and refundMark=1")->getOne();
//     	        $params['refundMark'] = '1';//退款标识
//     	        if(!empty($tmp)){
//     	           $this->db->model('pay_message')->where("tradeorder='".$order['order_sn']."' and refundMark=1")->delete();
//     	           $this->db->model('pay_message')->add(saddslashes($params));
//     	        }else{
//     	           $this->db->model('pay_message')->add(saddslashes($params));
//     	        }
//     	        //自营订单支付操作
//     	        $remarks = "自营订单取消支付操作";
//     	        M('user:applyLog')->addLog($this->user_id,'payCancel','',json_encode($params),1,$remarks);
//     	        if($this->db->commit()){
//     	            $this->success($dataorder);
//     	        }else{
//     	            $this->db->rollback();
//                     $this->error('生成失败:'.$this->db->getDbError());
//     	        }
//     	   	    $this->success($dataorder);
	            $this->db->startTrans();
    	        $update=array(
    	            'order_status'      => 3,//取消
    	        );
    	        $this->db->model('order')->where("o_id=$id and user_id=$this->user_id")->update(saddslashes($update));
	            //同步到后台付款信息
	            $collection=$this->db->model('collection')->select('*')->where("order_sn='".$order['order_sn']."' and "." order_name='退款'")->getRow();
	            if(empty($collection)){
	                $update3=array(
	                    'order_sn'=>$order['order_sn'],
	                    'order_type'=>$order['order_type'],
	                    'c_id'=>$order['c_id'],
	                    'o_id'=>$order['o_id'],
	                    'total_price'=>-$order['total_price'],
	                    'collected_price'=>-$order['total_price'],
	                    'uncollected_price'=>0,
	                    'pay_method'=>6,
	                    'payment_time'=>CORE_TIME,
	                    'order_name'=>'退款',
	                    'remark'=>'网站前台取消付款',
	                    //'account'=>CORE_TIME,
	                    'collection_status'=>2,
	                    'customer_manager'=>$order['customer_manager'],
	                    'input_time'=>CORE_TIME,
	                    'input_admin'=>$this->user_id,
	                );
	                $this->db->model('collection')->add(saddslashes($update3));
	            }
	            if($this->db->commit()){
	                $this->success('生成成功');
	            }else{
	                $this->db->rollback();
	                $this->error('生成失败:'.$this->db->getDbError());
	            }
	        }else{
	            $this->success('线下支付');
	        }
	    }
	}


}