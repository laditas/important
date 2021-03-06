<?php
/**
 *注册控制器
 */
class registerAction extends homeBaseAction
{
	public function __init() {
		$this->debug = false;
		$this->db=M('public:common')->model('customer_contact');
    }
	public function init()
	{
		$this->display('register');
	}
	//ajax注册
	public function register()
	{
			$this->is_ajax=true;
			$username=sget('username','s');
			$password=sget('password','s');
			$vcode = sget('vcode','i');
			$resVcode=M('system:sysSMS')->chkDynamicCode($username,$vcode);
			if($resVcode['err']>0){
				$this->error($resVcode['msg']);
			}
			$result = M('touch:register')->register(array('mobile'=>$username,'password'=>$password));
			if($result['err']){
				$this->error($result['msg']);
			}else{
				$this->success('注册成功');
			}
	}
	//ajax获取短信验证码
	public function sendmsg(){
		$this->is_ajax=true;
		//验证手机
		$mobile=sget('mobile','s');
		if(!is_mobile($mobile)){
				$this->error('您的手机号码格式不正确');
			}
		//密码检查
		$password=sget('password','s');
		if(strlen($password)<8){
			$this->error('密码长度应为(8-20位)');
		}
		//系统短信模型
		$sms=M('system:sysSMS');
		//检查注册的限制
		$result=$sms->chkRegLimit($mobile,get_ip());
		if(empty($result)){
			$this->error($sms->getError());
		}
		//请求动态码
		$result=$sms->genDynamicCode($mobile);
		if($result['err']>0){ //请求错误
			$this->error($result['msg']);
		}
		$msg=$result['msg']; //短信内容
		//发送手机动态码
		$sms->send(0,$mobile,$msg,1);
		$this->success('发送成功');
	}
}