<?php
/**
 * 账户设置
 */
class mysetAction extends userBaseAction{

	protected $db;
	public function __init()
	{
		$this->db=M('public:common');
	}

	public function init()
	{
		$this->act="myset";
		$user_info=$this->db->from('customer_contact c')
			->join('customer as cu','cu.c_id=c.c_id')
			->join('contact_info i','c.user_id=i.user_id')
			->leftjoin('admin ad','c.customer_manager=ad.admin_id')
			->where("c.user_id=$this->user_id")
			->select("c.user_id,c.mobile,c.name,c.tel,c.fax,c.c_id,email,i.points,i.thumb,ad.name as ad_name")
			->getRow();

		//公司信息
		$customer=$this->db->model('customer')->where("c_id={$user_info['c_id']}")->getRow();

		$this->area=M('system:region')->get_regions(1);

		$origin=explode('|',$customer['origin']);
		if($origin[0]){
			$this->city=M('system:region')->get_regions($origin[0]);
		}
		$this->assign('origin',$origin);
		$this->assign('origin_hidden',$customer['origin']);
		$this->assign('user_info',$user_info);
		$this->assign('customer',$customer);
		$this->display('myset');
	}

	//修改个人信息
	public function edit_info()
	{
		if($_POST)
		{
			$this->is_ajax=true;
			if(!$name=trim(sget('name','s',''))) $this->error('姓名不能为空');
			$_data=array(
				'name'=>$name,
				'tel'=>sget('tel','s',''),
				'email'=>sget('email','s',''),
				'fax'=>sget('fax','s',''),
			);
			$this->db->model('customer_contact')->where("user_id=$this->user_id")->update($_data);
			if($thumb=sget('thumb','s','')){
				$this->db->model('contact_info')->where("user_id=$this->user_id")->update(array('thumb'=>$thumb));
			}
			$this->success('修改成功');
		}
	}

	//修改公司信息
	public function edit_company()
	{
		if($_POST){
			$this->is_ajax=true;
			$_data=saddslashes($_POST);
			$c_id=$_SESSION['uinfo']['c_id'];//用户关联客户id
			$customer=M('user:customer')->getCinfoById($c_id);
			if(empty($customer))$this->error('信息不存在');
			$contact_id=$customer['contact_id'];
			if(!$this->db->model('customer_contact')->where("user_id='$contact_id'")->getRow())
			{
			    $this->error('默认联系人不存在,请联系系统管理员维护当前公司默认联系人!');
			}
			if($contact_id!=$this->user_id) $this->error('不是默认联系人不能修改!'.'请联系:'.$this->db->model('customer_contact')->where("user_id='$contact_id'")->getRow()['name']);
			if(!trim($_data['c_name'])) $this->error('公司名称不能为空');
			if(!trim($_data['need_product'])) $this->error('需求牌号不能为空');
			if(!trim($_data['address'])) $this->error('公司详细地址不能为空');
			if(!$_data['origin']) $this->error('公司地址不能为空');
			$c_name=trim($_data['c_name']);
			if($c_name!=$customer['c_name'] && $this->db->model('customer')->where("c_name='{$c_name}'")->getOne()) $this->error('公司已存在，不能重复新建。');
			$_data['update_time']=CORE_TIME;
			if(!$this->db->model('customer')->where("c_id=$c_id")->update($_data)) $this->error('操作失败，系统错误。');
			$this->success('操作成功');
		}
	}



	/**
	 * 收货地址列表
	 *
	 */
	public function editAddress(){
		$this->act='editAddress';
		$user_id=$this->user_id;
		$model=M('user:userAddress');
		$data=$model->where('user_id='.$user_id)->select('*')->getAll();
		$this->assign('data',$data);
		$this->display('setaddress');
	}

	/**
	 * ajax 接受添加地址信息
	 *
	 */
	public function addAddress(){
		if($_POST){
			$usermodel=M('user:userAddress');
			$this->is_ajax=true;
			$data=$_POST;
			$_data=array(
				'user_id'=>$this->user_id,                     //用户id
				'invoice_header'=>trim($data['header']),       //发票抬头
				'invoice_address'=>trim($data['address']),     //发票收件地址
				'name'=>trim($data['name']),                   //收件人名字
				'mobile'=>trim($data['mobile']),               //收件人电话
				'type'=>trim($data['type'])?:0                 //是否为默认地址
			);
			$info=$usermodel->add($_data);
			if($info){
				$lastid=$usermodel->getLastID();
			}
		}
		$this->json_output($lastid);

	}

	/**
	 * 根据user_id 删除用户地址信息
	 *
     */
	public function delete(){
		if($_GET){
			$this->is_ajax=true;
			$useraddressmodel=M('user:userAddress');
			$where['id']=$_GET['id'];

			$type=$useraddressmodel->where('id='.$where['id'])->select('type')->getOne();
			if($type>0){
				$mes='';
			}else{
				$mes=$useraddressmodel->where('id='.$where['id'])->delete();

			}
		}
	    $this->json_output($mes);

	}

	/**
	 * 修改设置默认地址
	 *
     */
	public function update(){

		if($_GET){
			$this->is_ajax=true;
			$useraddressmodel=M('user:userAddress');
			if($id=trim(sget('id','s',''))){
				//查询数据库中有无默认设置
				$info=$useraddressmodel->where("user_id=$this->user_id and id=$id")->select('id')->getRow();

				if($info){
					$useraddressmodel->where("user_id=$this->user_id")->update(array("type"=>0));
					$useraddressmodel->where("user_id=$this->user_id and id=$id")->update(array("type"=>1));
				}
				$this->json_output('操作成功');
			}

		}
		
	}
	
	/**
	 * 绑定东方付通账号
	 *
	 */
	public function bind_member()
	{
	    if($_POST)
	    {
	        $this->is_ajax=true;
			$_data=saddslashes($_POST);
			$c_id=$_SESSION['uinfo']['c_id'];//用户关联客户id
			$customer=M('user:customer')->getCinfoById($c_id);
			if(empty($customer))$this->error('信息不存在');
			if(!trim($_data['c_name'])) $this->error('公司名称不能为空');
// 			if(!trim($_data['need_product'])) $this->error('需求牌号不能为空');
// 			if(!trim($_data['address'])) $this->error('公司详细地址不能为空');
// 			if(!$_data['origin']) $this->error('公司地址不能为空');
            try { 
                $bind = E('dfftPayment',APP_LIB.'class');//引入dfftPayment类
                $params = array(
                    'mallID'     => $bind->mallID,
                    'payType'    => '09020',
                    'memCode'    => $c_id,
                    'memName'    => $_data['c_name'],
                );
                $this->success($bind->memberbind(json_encode($params)));
//                 file_put_contents(ROOT_PATH.'../static/upload/'.'1111.txt', $bind->memberbind(json_encode($params)));
            } catch (Exception $e) {
                 $this->error('绑定失败');
            }
			$this->success('操作成功');
	    }
	}







}