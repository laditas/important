<?php

/**
 * 用户找回密码- app
 */
class findPwdAction extends homeBaseAction{
	public function __init()
	{
		$this->db=M('public:common')->model('customer_contact');
	}
	public function init()
	{
		$this->display('me_forgetpwd');
	}
	public function finfMyPwd(){
		if($_POST){
			$this->is_ajax=true;
			$mobile=sget('mobile','s');
			$password=sget('password','s');
			if(!$this->_chkmobile($mobile)) $this->error($this->err);
			$mcode=sget('code','s');
			$result=M('myapp:msg')->chkDynamicCode($mobile,$mcode,7);
			if($result['err']>0){
				$this->error($result['msg']);
			}
			//用户重置密码
			$result = M('touch:register')->findpwd(array('mobile'=>$mobile,'password'=>$password));
            if($result['err']){
                $this->error($result['msg']);
            }else{
                $this->success('密码重置成功');
            }


		}

	}
	    /**
     * 验证手机号码
     * @access private
     * @return bool
     */
	private function _chkmobile($value=''){
		if(!is_mobile($value)){
			if(empty($value)){
				$this->err='请输入手机号码';
			}else{
				$this->err='错误的手机号码';
			}

			return false;
		}
		$chk=M('system:sysUser')->usrUnique('mobile',$value);
		if(!$chk){
			return true;
		}else{
			$this->err='没有相关账户';
			return false;
		}
	}

    /**
     * 发送手机验证码
     * @access public
     * @return html
     */
	public function sendmsg(){
		$this->is_ajax=true;
		//验证手机
		$mobile=sget('mobile','s');
		if(!$this->_chkmobile($mobile)){
			$this->error($this->err);
		}
		$sms=M('myapp:msg');
		//请求动态码
		$result=$sms->genDynamicCode($mobile,1);
		if($result['err']>0){ //请求错误
			$this->error($result['msg']);
		}
		$msg=$result['msg']; //短信内容
		//发送手机动态码
		$sms->send(0,$mobile,$msg,7);
		$this->success('发送成功');

	}

}