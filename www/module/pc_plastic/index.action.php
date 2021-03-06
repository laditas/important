<?php

class indexAction extends homeBaseAction
{

	// 初始api 版本
	protected $api;
	public function __init()
	{
		$this->api = 'qapi1_2';
	}
	// 通讯录
	public function init()
	{
//		if($_GET['token']){
//			$_SESSION['token']=$_GET['token'];
//			$_SESSION['userid']=$_GET['user_id'];
//		}
		$this->display('../pc_plastic/index.html');
	}
	// 中间
	public function middle(){

			$region=(isset($_POST['region']))?$_POST['region']:'0';
		    $c_type=(isset($_POST['c_type']))?$_POST['c_type']:'0';
		    $keywords=(isset($_POST['keywords']))?$_POST['keywords']:'';
			header('Content-type:text/html;charset=utf-8');
			$token=isset($_SESSION['token'])?$_SESSION['token']:'';
			$url='http://test.myplas.com/api/'.$this->api.'/getPlasticPerson';
		    $postJson = array(
				'token'=>$token,
				"keywords" => $keywords,
				"region"=>$region,
				"c_type" => $c_type,
				"page" => "",
				"sortField1"=>"",
				"quan_type" =>"",
			);
			$postJson=json_encode($postJson);
			$res=$this->http_curl($url,'get','json',$postJson);

			if($res['err']==0){
				$template='';
				$str='';
				$str.='
					<li data-val="'.$res["top"]["user_id"].'">
						<div class="pic flt">
							<img src="'.$res["top"]["thumb"].'">
							<div class="authen no">V</div>
						</div>
						<div class="info flt">
							<p>
								<span class="company">上海企辉物流有限公司</span>
								<span class="name">张飞扬 女</span>
							</p>
							<p>
								<span class="supply">供：196</span>
								<span class="demand">求：34</span>
							</p>
							<p>主营:LDPE,LLDPE,HDPE,1000S,7042...</p>
						</div>
						<div class="set-top">已置顶</div>
				    </li>';
				foreach ($res['persons'] as $val){
					$template.='
					 <li data-val="'.$val["user_id"].'">
							<div class="pic flt">
								<img src="'.$val["thumb"].'">
								<div class="authen no">V</div>
							</div>
							<div class="info flt">
								<p>
									<span class="company">'.$val["c_name"].'</span>
									<span class="name">'.$val["name"].' '.$val["sex"].'</span>
								</p>
								<p>
									<span class="supply">供：'.$val["buy_count"].'</span>
									<span class="demand">求：'.$val["sale_count"].'</span>
								</p>
								<p>主营:'.$val["need_product"].'</p>
							</div>
					 </li>';
				}

			}
		$this->assign('str',$str);
		$this->assign('template',$template);
		$this->display('../pc_plastic/center.html');
	}

	// 个人info 详情
	public function info()
	{
		if($_GET['user_id']){
//			header('Content-type:text/html;charset=utf-8');
			$token=$_SESSION['token'];
			$url='http://test.myplas.com/api/'.$this->api.'/getZoneFriend';
			$params = array(
				'token'=>$token,
				"userid" => $_GET['user_id'],
			);
			$postJson=json_encode($params);
			$res=$this->http_curl($url,'post','json',$postJson);
			var_dump($res);return;
		}

		$this->display('../pc_plastic/info.html');
	}
	// 默认右边
	public  function right()
	{
		$this->display('../pc_plastic/right.html');
	}

	// 求购信息
	public function info_buy()
	{
		$this->display('../pc_plastic/info_buy.html');
	}
	// 供给信息
	public function info_sell()
	{
		$this->display('../pc_plastic/info_sell.html');
	}

	// 登录
	public function login()
	{

		$this->display('../pc_plastic/login.html');
	}

	// 重置密码
	public function forget_pwd()
	{
		$this->display('../pc_plastic/forget_pwd.html');
	}

	public function register()
	{
		$this->display('../pc_plastic/register.html');
	}

	public function agreement()
	{
		$this->display('../pc_plastic/agreement.html');
	}


	/**
	 *  模块 2  供求
	 */
	public function buy_sell()
	{
		$this->display('../pc_plastic/buy_sell.html');
	}


	/**
	 * 模块 3  发现 center
	 */
	public function dis_center()
	{
		$this->display('../pc_plastic/center2.html');
	}
	// 头条
	public function head_line()
	{
		$this->display('../pc_plastic/headline.html');
	}
	public function head_line_2()
	{
		$this->display('../pc_plastic/headline2.html');
	}
	// 查自己
	public function credit_1()
	{
		$this->display('../pc_plastic/credit1.html');
	}

	// 查别人
	public function credit_3()
	{
		$this->display('../pc_plastic/credit3.html');
	}

	/**
	 * 我的  模块
	 *
	 */
	public function my_info()
	{
		$this->display('../pc_plastic/center3.html');
	}


	/*
	* $url 接口url string
	* $type 请求类型 string
	* $res 返回数据类型 string
	* $arr post 请求参数
	*
	* */
	function http_curl($url,$type='get',$res='json',$arr=''){
		$ch= curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		if($type='get'){
			curl_setopt($ch,CURLOPT_HEADER,0);
		}
		if($type == 'post'){
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
		}
		$output= curl_exec($ch);
		curl_close($ch);
		if($res =='json'){
			if(curl_errno($ch)){
				//请求失败，返回错误信息
				return curl_errno($ch);
			}else{
				return json_decode($output,true);
			}
		}else{
			return json_decode($output,true);
		}
	}
}