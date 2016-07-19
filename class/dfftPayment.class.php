<?php
/**
 * 东方付通支付类
 * add by xqj 2016-07-11
 * 引入示例:$dfft = E('dfftPayment',APP_LIB.'class');
 */
class dfftPayment{
    private $callbackpay ='';//支付回调地址
	private $mallID=''; //商城号码
	private $certificatepassword=''; //证书密码
	private $certificatepath =''; //证书路径
	// 	生产环境接口
	// 	https://www.easternpay.com.cn/gateway/api?order=base64(json)
	// 	测试平台接口
	// 	https://uat.easternpay.com.cn/gateway/api?order=base64(json)
	private $apiurl='https://uat.easternpay.com.cn/gateway/api';
	public  $error='';
	
		
	/**
	 * 构造函数
	 * @access public 
	 */
    public function __construct(array $config){
        
		if(!empty($config)){
			$this->mallID=$config['mallID'];
			$this->certificatepassword=$config['certificatepassword'];
			$this->certificatepath=$config['certificatepath'];
			$this->callbackpay=$config['callbackpay'];
		}else 
		{
    		$this->mallID='000106';
    		$this->certificatepassword='999999';
    		$this->certificatepath=$_SERVER['DOCUMENT_ROOT'].'/Javatest/Java/hbtest2.pfx'; //证书路径
    		$this->callbackpay='http://fkphsk.6655.la:10515/';
		}
    }
    
	
	
	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * @param $para 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	private function _createParamString($param=array()){
		ksort($param);
		$string  = "";
		foreach($param as $key=>$val){
			if($val=='' || $key=='sign'){
				continue;
			}
			$string .= $key."=".$val."&";
		}
		return substr($string,0,-1);
	}
	
	/**
	 * 加密
	 */
	public function _base64Sign($json=''){
		return base64_encode($json);
	}
	
	/**
	 * 生成签名
	 */
	public function _getSign($data)
	{
	    header("content-type:text/html; charset=utf-8");
	    require_once("http://127.0.0.1:8080/JavaBridge/java/Java.inc");
	    java_require($_SERVER['DOCUMENT_ROOT']."/Javatest/Java"); //一定要把刚才生成的jar文件放到这个require的目录下面
	    $signUtil = Java('com.easterpay.base.util.SignUtil');
	    $rtn = $signUtil->signDataDetached($this->certificatepath,$this->certificatepassword,$data);    //打印签名密文
	    return $rtn."";
	}
	
	/**
	 * 验签
	 */
	public function _base64Verify($data='',$sign=''){
	    header("content-type:text/html; charset=utf-8");
	    require_once("http://127.0.0.1:8080/JavaBridge/java/Java.inc");
	    java_require($_SERVER['DOCUMENT_ROOT']."/Javatest/Java"); //一定要把刚才生成的jar文件放到这个require的目录下面
	    $signUtil = Java('com.easterpay.base.util.SignUtil');
	    //验证签名 可以尝试改下字符串 查看结果       
	    $result = $signUtil->verifySignedDataDetached($this->certificatepath,$this->certificatepassword,$sign,$data);
	    if($result == "1"){
	        echo "签名验证成功！";
	        return true;
	    }else{
	        echo "签名验证失败！";
	        return false;
	    }
	}
	
	
	/**
	 * 会员绑定
	 * @access public
	 * @param  string $datajson 请求内容的json字符串
	 * 会员绑定会跳转到东方付通页面故此处只返回提交地址
	 */
	public function memberbind($datajson=''){
	    $params = array(
	        'mallID'     => $this->mallID,
	        'payType'    => '09020',
	        'memCode'    => '0000002',
	        'memName'    => 'cs0000002',
	    );
	    $params['signature'] = $this->_getSign(json_encode($params));
	    $order = $this->_base64Sign(json_encode($params));
	    $url = $this->apiurl . '?' . http_build_query(array('order'=> $order));
// 	    $ret = HttpClient::quickGet($url);
// 	    $data = $this->http($this->apiurl, array('order'=> $order), 'GET');
	    return $url;
	}
	
	/**
	 * 会员绑定查询
	 * @access public
	 * @param  string $datajson 请求内容的json字符串
	 * 返回:
	 * memCode 商城会员代码 
	 * memName 商城会员名称
	 * payStatus 状态  000000：已绑定 
	 * payMessage 提示信息 
	 * signature 签名 
	 */
	public function memberbindquery($datajson=''){
	    $params = array(
	        'mallID'     => $this->mallID,
	        'payType'    => '09011',
	        'memCode'    => '0000001',
	        'memName'    => 'cs0000001',
	    );
	    $params['signature'] = $this->_getSign(json_encode($params));
	    $order = $this->_base64Sign(json_encode($params));
	    $url = $this->apiurl . '?' . http_build_query(array('order'=> $order));
	    $ret = HttpClient::quickGet($url);
	    return $ret;
	}
	
	/**
	 * 保证金处理
	 * @access public
	 * @$datajson包括以下15个参数
	 * mallID 商城号码
	 * payType 支付类型 03031：保证金锁定 03041：保证金释放 03051：保证金支付 03061：保证金追加
	 * payID 支付号码 单笔支付的唯一标识
	 * originalPayID 关联支付号码 关闭挂起、保证金释放、保证金追加、保证金支付时必填  tradeOrder 交易订单号 合同号或其他交易订单号
	 * payMemCode 付款会员代码  payMemName 付款会员名称  recMemCode 相关会员代码  recMemName 相关会员名称 currency 币种 为空则默认为 CNY（人民币）
	 * payAmt 金额  保留两位小数 summary 摘要 交易信息callBackUrl 回调 URL 服务器异步通知结果地址 auditFlag 是否审核 1  保证金释放，需要审核
	 * signature 签名  调用签名程序产生指令签名
	 * */
	public function Margin($datajson='')
	{
	    $params = array(
	        'mallID'     => $this->mallID,
	        'payType'    => '03061',
	        'payID' => 'JG'.date('Ymdhis',time()).'-'.rand(999,9999) ,
	        'originalPayID' => 'JG20160719020244-9024' ,
	        'tradeOrder'    => 'JG20160719020244-9024'  ,
	        'payMemCode' => '0000002' ,
	        'payMemName' => 'cs0000002' ,
	        'recMemCode' => '0000001' ,
	        'recMemName' => 'cs0000001' ,
	        'currency'    => 'CNY',
	        'payAmt'    => '100',
	        'summary'    => '',
	        'callBackUrl'     => 'http://fkphsk.6655.la:10515/pay/rtnMargin',
	        'auditFlag'    => '0',
	    );
	    $params['signature'] = $this->_getSign(json_encode($params));
	    $order = $this->_base64Sign(json_encode($params));
	    $ret = HttpClient::quickPost($this->apiurl,array('order'=>$order));
	    return $ret;
	}
	
	/**
	 * 代扣款
	 * @access public
	 * @$datajson包括以下参数
	 * mallID商城号码 payType支付类型(04011：挂账代扣款 04031：关闭代扣) payID支付号码 originalPayID关联支付号码 tradeOrder合同号或其他交易订单号
	 * payMemCode付款方会员代码 payMemName付款方会员名称 recMemCode收款会员代码 recMemName收款方会员名称 currency币种 为空则默认为 CNY（人民币）
	 * payAmt金额 summary摘要 callBackUrl回调 URL signature签名
	 * @返回结果:
	 * payID支付号码 payStatus支付状态 payMessage状态说明 signature签名
	 */
	public function WithholdPayment($datajson='')
	{
	    $payid = 'JG'.date('Ymdhis',time()).'-'.rand(999,9999);
	    $params = array(
	        'mallID' => $this->mallID ,
	        'payType' => '04011' ,
	        'payID' => $payid ,
	        'originalPayID' => '' ,
	        'tradeOrder' => $payid ,
	        'payMemCode' => '0000002' ,
	        'payMemName' => 'cs0000002' ,
	        'recMemCode' => '0000001' ,
	        'recMemName' => 'cs0000001' ,
	        'currency' => 'CNY' ,
	        'payAmt' => '100' ,
	        'summary' => '' ,
	        'callBackUrl' => 'http://fkphsk.6655.la:10515/pay/rtnCloseWithholdPayment' ,
	    );
	    $params['signature'] = $this->_getSign(json_encode($params));
	    $order = $this->_base64Sign(json_encode($params));
// 	    $url = $this->apiurl . '?' . http_build_query(array('order'=> $order));
		$ret = HttpClient::quickPost($this->apiurl,array('order'=>$order));
	    return $ret;
	}
	
	/**
	 * 关闭代扣款
	 * @access public
	 * @$datajson包括以下参数
	 * mallID商城号码 payType支付类型(04011：挂账代扣款 04031：关闭代扣) payID支付号码 originalPayID关联支付号码 tradeOrder合同号或其他交易订单号
	 * payMemCode付款方会员代码 payMemName付款方会员名称 recMemCode收款会员代码 recMemName收款方会员名称 currency币种 为空则默认为 CNY（人民币）
	 * payAmt金额 summary摘要 callBackUrl回调 URL signature签名
	 * @返回结果:
	 * payID支付号码 payStatus支付状态 payMessage状态说明 signature签名
	 */
	public function CloseWithholdPayment($datajson='')
	{
	    $params = array(
	        'mallID' => $this->mallID ,
	        'payType' => '04031' ,
	        'payID' => 'JG'.date('Ymdhis',time()).'-'.rand(999,9999) ,
	        'originalPayID' => 'JG20160718020730-7346' ,
	        'tradeOrder' => 'JG20160718020730-7346' ,
	        'payMemCode' => 'F1000819' ,
	        'payMemName' => 'cs123' ,
	        'recMemCode' => 'F1000733' ,
	        'recMemName' => '北京化工宝电子商务有限公司' ,
	        'currency' => 'CNY' ,
	        'payAmt' => '100' ,
	        'summary' => '' ,
	        'callBackUrl' => 'http://fkphsk.6655.la:10515/pay/rtnWithholdPayment' ,
	    );
	    $params['signature'] = $this->_getSign(json_encode($params));
	    $order = $this->_base64Sign(json_encode($params));
	    $ret = HttpClient::quickPost($this->apiurl,array('order'=>$order));
	    return $ret;
	}
   	
	/**
	 * 订单查询
	 * @access public
	 * @$datajson包括以下参数
	 * payID 支付号码 多个订单时使用半角英文逗号分隔
	 * payType 支付类型 05011
	 * mallID 商城号码
	 * signature 签名
	 * @订单查询返回结果:
	 * mallID商城号码 payType支付类型 payID支付号码 originalPayID关联支付号码  关闭支付时必填 tradeOrder 交易订单号 合同号或其他交易订单号
	 * payMemCode 付款方会员代码 payMemName 付款方会员名称 recMemCode 收款方会员代码 recMemName 收款方会员名称 currency 为空则默认为 CNY（人民币）
	 * payAmt 金额  保留两位小数 summary 摘要 交易信息 payStatus 查询状态 000000：成功 status 指令状态 01:待支付 03:已提交银行 04:已关闭 0001：已支付
	 * payMessage 支付状态的具体描述 callBackUrl 回调URL signature 签名
	 */
	public function OrderQuery($datajson=''){
	    $params = array(
	        'payID' => 'JG20160718113114-6071' , 
	        'payType' => '05011' ,     
	        'mallID' => $this->mallID , 
	    );
	    $params['signature'] = $this->_getSign(json_encode($params));
	    $order = $this->_base64Sign(json_encode($params));
	    $url = $this->apiurl . '?' . http_build_query(array('order'=> $order));
	    $ret = HttpClient::quickGet($url);
	    return $ret;
	}
	
	/**
	 * 账户查询
	 * @access public
	 * @$datajson包括以下参数
	 * memCode 会员代码
	 * payType 支付类型 余额查询:06011 提现查询:06021 可提现余额查询:06031 提现银行:06041
	 * mallID 商城号码
	 * startDate 开始时间 YYYY-MM-DD
	 * endDate 结束时间 YYYY-MM-DD
	 * signature 签名 
	 * @06011会员余额查询返回结果：
	 * memCode会员代码 totalAmt总金额  freeAmt可用金额  lockedAmt锁定金额  frozenAmt冻结金额  holdAmt挂账金额 ktAmt可提现余额(payType为06031)
	 * payStatus结果状态 payMessage信息 signature签名
	 * @06041会员提现银行查询返回结果：
	 * memCode会员代码 memName会员全称 bank提现银行 bankDeposit提现开户行 bankAccount提现账号 status查询标志 signature签名
	 * @06031会员提现查询返回结果：
	 * memCode会员代码 memName会员全称 withdrawal提现金额 bank提现银行 bankAccount提现开户行 accountNum提现账号 date提现时间YYYY-MM-DD HH:MM:SS
	 * fphm提款单号 details提现明细 signature签名
	 * @06021提现明细信息
	 * payMemCode付款会员 payMemName付款会员全称 payAmt付款金额  remark摘要 contractNum合同号码 payID支付号码 payDate付款时间
	 */
	public function  AccountQuery($datajson='')
	{
	    $params = array(
	        'memCode' => '0000002',
	        'payType' => '06011',
	        'mallID' => $this->mallID,
	        'startDate' => date('Y-m-d',strtotime('1991-01-01')),
	        'endDate' => date('Y-m-d',strtotime('2016-07-20')),
	    );
	    $params['signature'] = $this->_getSign(json_encode($params));
	    $order = $this->_base64Sign(json_encode($params));
	    $url = $this->apiurl . '?' . http_build_query(array('order'=> $order));
	    $ret = HttpClient::quickGet($url);
	    return $ret;
	}
	

	
	/**
	 * 关闭直接支付
	 * @access public
	 * @$datajson包括以下参数
	 * payID 支付号码 多个订单时使用半角英文逗号分隔
	 * payType 支付类型 01021
	 * mallID 商城号码
	 * signature 签名
	 * @订单查询返回结果:
	 * mallID商城号码 payType支付类型 payID支付号码 originalPayID关联支付号码  关闭支付时必填 tradeOrder 交易订单号 合同号或其他交易订单号
	 * payMemCode 付款方会员代码 payMemName 付款方会员名称 recMemCode 收款方会员代码 recMemName 收款方会员名称 currency 为空则默认为 CNY（人民币）
	 * payAmt 金额  保留两位小数 summary 摘要 交易信息 payStatus 查询状态 000000：成功 status 指令状态 01:待支付 03:已提交银行 04:已关闭 0001：已支付
	 * payMessage 支付状态的具体描述 callBackUrl 回调URL signature 签名
	 */
	public function CloseDirectPayment($datajson=''){
	    $params = array(
	        'payID' => 'JG20160718115323-5137' ,
	        'payType' => '01021' ,
	        'originalPayID' => 'JG20160718115323-5137' ,
	        'tradeOrder'    => 'JG20160718115323-5137' ,
	        'mallID' => $this->mallID ,
	    );
	    $params['signature'] = $this->_getSign(json_encode($params));
	    $order = $this->_base64Sign(json_encode($params));
// 	    $url = $this->apiurl . '?' . http_build_query(array('order'=> $order));
// 	    $result = HttpClient::quickGet($url);
	    $result = HttpClient::quickPost($this->apiurl,array('order'=>$order));
	    $obj = json_decode($result);
	    if(isset($obj)){
	        // 关闭状态
	        $payStatus = $obj->payStatus;
	        // 关闭消息
	        $payMessage = $obj->payMessage;
	        // 签名
	        $signature = $obj->signature;	         
// 	        echo '处理消息:<br />';	         
	        //判断订单是否关闭成功 payStatus 为000000 为关闭成功
	        if($payStatus  == '000000'){
	            echo "订单:".$params['originalPayID'].",关闭成功!";
	        }else{
	            echo "订单:".$params['originalPayID'].",".$payMessage."!";
	        }
	    }
	    return $result;
	}
	
// 	/**
// 	 * Curl版本
// 	 */
// 	public function vpost($url,$data){ // 模拟提交数据函数
// 	    $curl = curl_init(); // 启动一个CURL会话
// 	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
// 	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
// 	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
// 	    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
// 	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
// 	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
// 	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
// 	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
// 	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
// 	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
// 	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
// 	    $tmpInfo = curl_exec($curl); // 执行操作
// 	    if (curl_errno($curl)) {
// 	        echo 'Errno'.curl_error($curl);//捕抓异常
// 	    }
// 	    curl_close($curl); // 关闭CURL会话
// 	    return $tmpInfo; // 返回数据
// 	}

	
}
?>
