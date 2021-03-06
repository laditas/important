<?php
/**
 * Created by PhpStorm.
 * User:  yjyaddSupplier
 * Time: 9:07
 * 物流供应商管理
 */
class supplierAction extends adminBaseAction{

    public function __init(){
        $this->debug=false;
        $this->assign('status',L('status'));// 供应商状态
        $this->assign('supplier_type',L('supplier_type'));//工厂类型
        $this->assign('credit_level',L('credit_level'));//信用等级
        $this->assign('supplier_contact_type',L('supplier_contact_type'));  // 联系人用户状态
        $this->assign('is_default',L('is_default'));
        $this->assign('sex',L('sex'));
        $this->db=M('public:common')->model('logistics_supplier');
        $this->doact = sget('do','s');
    }
    /**
     * 供应商列表
     *
     */
    public function init(){
        $action=sget('action','s');
        if($action=='grid'){ //获取列表
            $this->_grid();exit;
        }elseif($action=='remove'){ //获取列表
            $this->_remove();exit;
        }elseif($action=='submit'){ //获取列表
            $this->_submit();exit;
        }elseif($action=='save'){ //获取列表
            $this->_save();exit;
        }

        $this->assign('choose',sget('choose','s')); //单选传参
//        $doact=sget('do');
//        $this->assign('doact',$doact);
        $this->display('supplier.html');

    }

    public function _grid(){
        $page = sget("pageIndex",'i',0); //页码
        $size = sget("pageSize",'i',20); //每页数
//        $pt = sget("pt",'i',2);
        $doact=sget('do');
        $this->assign('doact',$doact);
        $sortField = sget("sortField",'s','supplier_id'); //排序字段
        $sortOrder = sget("sortOrder",'s','desc');        //排序
        $where = ' 1 ';
        // 关键词
        $key_type=sget('key_type','s','supplier_id');
        $keyword=sget('keyword','s');

        if(!empty($keyword)){
            if($key_type=='supplier_id'){
                $where.=" and $key_type='$keyword' ";
            }else{
                $where.=" and `$key_type` like '%$keyword%' ";
            }
        }
        $list=$this->db ->where($where)->page($page+1,$size)->order("$sortField $sortOrder")->getPage();
        foreach($list['data'] as $k=>$v){
              $s_id=$v['supplier_id'];
              $s_contact=M('public:common')->model('logistics_contact')->select('contact_name,contact_tel,mobile_tel')->where("`supplier_id` = $s_id  and `is_default`= 1")->getRow();
              $list['data'][$k]['type']=L('supplier_type')[$v['type']];  //  供应商类型
              $list['data'][$k]['status']=L('status')[$v['status']];     // 供应商状态
              $list['data'][$k]['create_time']=date('y-m-d H:i:s',$v['create_time']);  // 创建时间
              $list['data'][$k]['update_time']=date('y-m-d H:i:s',$v['update_time']);  // 更新时间
              $list['data'][$k]['supplier_name']=$v['supplier_name'];    // 供应商名称
              /* $list['data'][$k]['remark']= ($v['remark']==NULL ?'--':$v['remark']); */
              $list['data'][$k]['remark']= $v['remark']; // 备注
              $list['data'][$k]['contact_name']=$s_contact['contact_name']; //主联系人姓名
              $list['data'][$k]['contact_tel']=$s_contact['contact_tel']; //主联系人固定电话
              $list['data'][$k]['mobile_tel']=$s_contact['mobile_tel']; //主联系人手机
        }
        $result=array('total'=>$list['count'],'data'=>$list['data'],'msg'=>'');
        $this->json_output($result);
    }

    /**
     * 1、新增供应商按钮
     * 2、查看供应商信息
     */
    public function addSupplier(){
        $supplier_id=sget('id','i');
        $cType=sget('ctype','i'); //用户类型
        $this->assign('regionList', arrayKeyValues(M('system:region')->get_reg(),'id','name'));
        $this->assign('sex',L('sex'));    // 性别
        $this->assign('ctype',$cType);    //人员类型
        //**************************
        if($supplier_id<1){
            if($cType==1){
                $this->assign('ctype',$cType);                      //单页面新增供应商联系人
                $this->assign('page_title','新增个人联系人');
                $this->assign('supplier_contact_type',L('supplier_contact_type'));            // 供应商联系人状态
                $this->display('supplier_contact.html');
            }elseif($cType==3){                                     //新增供应商
                $this->assign('ctype',$cType);
                $this->assign('is_pur',sget('supplier'));           //添加客户的入口
                $this->assign('type',L('supplier_type'));           //供应商类型
                $this->assign('supplier_contact_type',L('supplier_contact_type'));            // 供应商联系人状态
                $this->assign('status',L('status'));                // 供应商状态
                $this->assign('credit_level',L('credit_level'));    //信用等级
                $this->assign('page_title','新增企业用户');

                $this->display('add_supplier.html');
            }
            exit;
        }
        //***************************
        //查询供应商信息
        $info=$this->db->getPk($supplier_id);
        if(empty($info)){
            $this->error('错误的公司信息');
        }
        $info['business_licence_pic'] = FILE_URL.'/upload/'.$info['business_licence_pic'];     // 营业执照图片
        $info['organization_code_pic'] = FILE_URL.'/upload/'.$info['organization_code_pic'];   // 组织机构代码证图片
        $info['tax_registration_pic'] = FILE_URL.'/upload/'.$info['tax_registration_pic'];     // 税务登记证图片
        $info['legal_person_pic_1'] = FILE_URL.'/upload/'.$info['legal_person_pic_1'];         // 身份证图片(正面)
        $info['legal_person_pic_2'] = FILE_URL.'/upload/'.$info['legal_person_pic_2'];         // 身份证图片(反面)
        $info['social_credit_code_pic'] = FILE_URL.'/upload/'.$info['social_credit_code_pic']; // 三证合一图片
        $info['fund_date']= substr($info['fund_date'],0,10);
        //联系人详情
        $this->assign('c_id',$supplier_id);                        //供应商id
        $this->assign('regionList', arrayKeyValues(M('system:region')->get_reg(),'id','name'));//第一级省市
        $this->assign('type',L('supplier_type'));                  //供应商类型
        $this->assign('status',L('status'));                       // 供应商状态
        $this->assign('credit_level',L('credit_level'));           //信用等级
        $this->assign('info',$info);
        $this->display('show_supplier.html');

    }

    /**
     * 提交过来的新增供应商和联系人信息
     *
     */
    public function addSubmit() {
        $this->is_ajax=true;
        $data = sdata();
        $data['supplier_name'] = trim($data['supplier_name']);
        $ctype = $data['ctype'];    // ctype   1：新增供应商联系人   3：新增供应商
        if($ctype==1){              //单独新增供应商联系人
            if(empty($data['mobile_tel']) && empty($data['contact_tel'])) $this->error('手机或者电话至少填写一个');
            //验证联系人信息
            $var=$this->db->model('logistics_supplier')->where('supplier_id='.$data['supplier_id'])->getRow();
            $param=array(
                'supplier_id'=> $data['supplier_id'],
                'contact_name'=> $data['contact_name'],
	            'supplier_name'=> $var['supplier_name'],
                'mobile_tel'=>$data['mobile_tel'],
                'contact_tel'=> $data['contact_tel'],
                'comm_fax'=> $data['comm_fax'], //联系人传真
                'comm_email' => trim($data['comm_email']),     // 联系人邮箱
                'sex'=> $data['sex'],
                'qq'=>$data['qq'],
                'is_default'=>'0',                             // 是否默认联系人   1：是 0：否
                'create_time' => time(),                       // 创建时间
                'create_name' => trim($_SESSION['name']),      // 创建者
	            'update_name'=>trim($_SESSION['name']),
	            'update_time'=> CORE_TIME,
                'remark'=> $data['remark'],
                'status'=> $data['supplier_contact_type'],     // 状态
            );
            $info=$this->db->model('logistics_contact')->add($param);
            if($info==1){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }
        // 新增供应商和联系人
        $this->db->startTrans();
        try{
            if($ctype==3){
                $var=$this->db->model('logistics_supplier')->select('supplier_name')->where('supplier_name='.$data['supplier_name'])->getCol();
                if($var) $this->error("此供应商已存在");
                if(empty($data['mobile_tel']) && empty($data['contact_tel'])) $this->error('手机或者电话至少填写一个');
                //验证公司信息
                $param_1=array(                                                  // logistics_supplier 表
                    'supplier_name'=>trim($data['supplier_name']),               //   供应商名称
                    'legal_person'=> trim($data['legal_person']),                //   法人姓名
                    'legal_person_code' =>trim($data['legal_person_code']),      // 法人身份证号码
                    'legal_person_pic_1' => $data['legal_person_pic_1'],         // 身份证 正面
                    'legal_person_pic_2' => $data['legal_person_pic_2'],         // 法人身份证 反面
                    'company_tel'  =>  trim($data['company_tel']),                     // 公司固话
                    'company_fax'  =>  trim($data['company_fax']),                    //公司传真
                    'invoice_tel'  =>  trim($data['invoice_tel']),                     // 开票电话
                    'invoice_bank' =>  trim($data['invoice_bank']),                    //  开票银行
                    'invoice_account' => trim($data['invoice_account']),                  // 开票账户
                    'invoice_address' > trim($data['invoice_address']),                   // 开票地址
                    'province'   => $data['province'],                              //　省份
                    'city'      =>  $data['company_city'],                          // 城市
                    'address'  =>  trim($data['address']),                                 //  公司地址
                    'main_line' => trim($data['main_line']),                           // 主营线路
                    'zip_code'   => trim($data['zip_code']),                              // 邮编
                    'fund_date'  => strtotime($data['fund_date']),                             //成立时间
                    'register_capital'  => trim($data['register_capital']),               // 注册资本
                    'credit_level'  => $data['credit_level'],                       // 信用等级
                    'status'=>   $data['status'],     
                    'type'=>   $data['type'],                //供应商类型
                    'merge_three'=>$data['cards'],                                  // 是否三证合一
                    'business_licence_code' => trim($data['business_licence_code']),      // 营业执照号码
                    'business_licence_pic'  => $data['business_licence_pic'],       // 营业执照照片
                    'organization_code'     => trim($data['organization_code']),          //组织机构代码
                    'organization_code_pic' => $data['organization_code_pic'],      // 组织机构代码照片
                    'tax_registration'      => trim($data['tax_registration']),           // 税务登记证号码
                    'tax_registration_pic'  => $data['tax_registration_pic'],       // 税务登记证照片
                    'tax_id'     => trim($data['tax_id']),                                // 纳税人识别号
                    'social_credit_code' => $data['social_credit_code'],            // 社会统一信证码
                    'social_credit_code_pic' => $data['social_credit_code_pic'],    // 三证合一照片
                    'create_time' => time(),                                        // 创建时间
                    'create_name' => trim($_SESSION['name']),                        // 创建者
	                'update_name'=>trim($_SESSION['name']),
	                'update_time'=> CORE_TIME,
                );
                $param_2= array(
                    'contact_name' => trim($data['contact_name']), // 供应商联系人name
                    'supplier_name'=>trim($data['supplier_name']),  //  供应商名称
                    'sex'   => $data['sex'],                 // 性别
                    'mobile_tel'=> trim($data['mobile_tel']),       // 联系人手机
                    'contact_tel' => trim($data['contact_tel']),  // 联系人固话
                    'qq'  =>  trim($data['qq']),                  //联系人QQ
                    'comm_fax' => trim($data['comm_fax']),        // 传真
                    'comm_email' => trim($data['comm_email']),    // 联系人邮箱
                    'is_default'=>'1',                           // 是否默认联系人   1：是 2：否
                    'create_time' => time(),               // 创建时间
                    'create_name' => trim($_SESSION['name']),    // 创建者
                    'remark'=> $data['remark'],
                    'status'=> $data['supplier_contact_type'],          // 状态
	                'update_name'=>trim($_SESSION['name']),
	                'update_time'=> CORE_TIME,
                );

            }
                    $info=$this->db->add($param_1);                                  // 返回受影响行数
                    if($info==1){
                        $param['supplier_id']=$this->db->getLastID();               //  返回自增id
                        $param_2['supplier_id']=$param['supplier_id'];
                        $res=$this->db->model('logistics_contact')->add($param_2);   // 返回受影响行数

                        if($res!=1) $this->db->getError('供应商联系人新增失败');
                    }else{
                        $this->db->getError('供应商新增失败');
                    }
            $this->db->commit();
        }catch (\Exception $e){
            $this->db->rollback();
        }
        $this->success('操作成功');
    }

    /**
     * 编辑保存供应商及供应商联系人信息
     *
     */
    public function editSubmit(){
        $this->is_ajax=true;
        $data = sdata();
        if($data['supplier_id']<1) $this->error('信息错误');
        $this->db->startTrans();//开启事务
        try {
             $logistics_supplier=array(
                 'supplier_name'=>$data['supplier_name'],
                 'legal_person'=>$data['legal_person'],
                 'company_tel'=>$data['company_tel'],
                 'company_fax'=>$data['company_fax'],
                 'legal_person_code'=>$data['legal_person_code'],
                 'legal_person_pic_1'=>$data['legal_person_pic_1'],
                 'legal_person_pic_2'=>$data['legal_person_pic_2'],
                 'province'=>$data['province'],
                 'city'=>$data['city'],
                 'zip_code'=>$data['zip_code'],
                 'address'=>$data['address'],
                 'main_line'=>$data['main_line'],
                 'fund_date'=>strtotime($data['fund_date']),
                 'register_capital'=>$data['register_capital'],
                 'type'=>$data['supplier_type'],
                 'business_licence_code'=>$data['business_licence_code'],
                 'business_licence_pic'=>$data['business_licence_pic'],
                 'organization_code'=>$data['organization_code'],
                 'organization_code_pic'=>$data['organization_code_pic'],
                 'tax_registration'=>$data['tax_registration'],
                 'tax_registration_pic'=>$data['tax_registration_pic'],
                 'social_credit_code'=>$data['social_credit_code'],
                 'social_credit_code_pic'=>$data['social_credit_code_pic'],
                 'credit_level'=>$data['credit_level'],
                 'status'=>$data['status'],                
                 );
             $logistics_supplier['update_time']=time();
             $logistics_supplier['update_name']=$_SESSION['name'];
            if(!$this->db->where('supplier_id='.$data['supplier_id'])->update($logistics_supplier)) throw new Exception("系统错误 更新失败:100");
            $logistics_contact=array(
                'contact_name'=>$data['contact_name'],
                'sex'=>$data['sex'],
                'mobile_tel'=>$data['mobile_tel'],
                'qq'=>$data['qq'],
                'comm_fax'=>$data['comm_fax'],
                'comm_email'=>$data['comm_email'],               
                'remark'=>$data['remark'],
                'status'=>$data['supplier_contact_type'],
            );
            $logistics_contact['update_time']=time();
            $logistics_contact['update_name']=$_SESSION['name'];
            if(!(M('public:common')->model('logistics_contact')->where('supplier_id='.$data['supplier_id'])->update($logistics_contact))) throw new Exception("系统错误 更新失败:101");    
        } catch (Exception $e) {
            $this->db->rollback();
            $this->error($e->getMessage());
        }
        $this->db->commit();//提交事务
        $this->json_output(array('err'=>0,'msg'=>'更新成功'));

    }


    /**
     * 修改供应商信息
     *
     */
    public function edit(){
            $this->is_ajax=true;
            $supplier_id=sget('id','i');
            $info=$this->db->model('logistics_supplier')->where('supplier_id='.$supplier_id)->getAll();          
            $var=M('public:common')->model('logistics_contact')->select('id,supplier_id,supplier_name,contact_name,sex,status,contact_tel,mobile_tel,qq,comm_fax,is_default,remark,comm_email')->where("`is_default`=1 and `supplier_id` in ('$supplier_id')")->getRow();            
            $list=array();
            foreach ($info as $k=> $v){
                $list['supplier_id']= $v['supplier_id'];     // 供应商id
                $list['supplier_name']=$v['supplier_name'];  // 供应商名称
                $list['legal_person']= $v['legal_person'];   // 供应商法人
                $list['legal_person_code'] = $v['legal_person_code'];  // 法人身份证号码
                $list['company_tel']= $v['company_tel']; // 供应商电话
                $list['company_fax']= $v['company_fax']; // 供应商传真
                $list['supplier_type'] =$v['type'];          // 类型
                $list['province'] =$v['province'];    // 省份
                $list['city'] =$v['city'];      // 城市
                $list['address'] = $v['address']; // 公司地址
                $list['main_line'] = $v['main_line']; // 主营线路
                $list['zip_code'] =$v['zip_code']; // 邮编
                $list['fund_date'] = date('Y-m-d',$v['fund_date']); // 成立时间
                $list['register_capital'] =$v['register_capital']; // 注册资本
                $list['credit_level'] = $v['credit_level']; // 信用等级
                $list['status'] = $v['status'];// 状态
                $list['merge_three'] =$v['merge_three']; // 是否三证合一  1：是 0:否
                $list['business_licence_code'] =$v['business_licence_code'];// 营业执照
                $list['business_licence_pic'] =$v['business_licence_pic']; // 营业执照图片
                $list['organization_code']= $v['organization_code']; // 组织机构代码
                $list['organization_code_pic'] = $v['organization_code_pic']; // 组织机构代码图片
                $list['tax_registration'] = $v['tax_registration'];//税务登记证号
                $list['tax_registration_pic'] =$v['tax_registration_pic']; // 税务登记证图片
                $list['legal_person_pic_1'] =$v['legal_person_pic_1'];// 身份证图片(正面)
                $list['legal_person_pic_2'] =$v['legal_person_pic_2']; // 身份证图片(反面)
                $list['tax_id']= $v['tax_id'];// 纳税人识别号
                $list['invoice_address'] =$v['invoice_address']; // 开票地址
                $list['invoice_tel'] =$v['invoice_tel'];// 开票电话
                $list['invoice_account']= $v['invoice_account'];// 开票账户
                $list['social_credit_code'] =$v['social_credit_code'];// 社会统一信用代码
                $list['social_credit_code_pic'] =$v['social_credit_code_pic'];// 社会统一信用代码图片
            }
             $this->assign('info',$var);
             $this->assign('user',$list);
             $this->display('supplier_info.html');
    }

    /**
     * 审核供应商状态（页面）
     *
     */
    public function chkPage(){
        $supplier_id = sget('id','i',0);
        if($supplier_id<1) $this->error('信息错误');
        $this->assign('id',$supplier_id);
        $this->assign('status',L('status'));// 联系人用户状态
        $this->display('supplier.chk.html');
    }

    /**
     * 审核供应商状态
     *
     */
    public  function chkSubmit(){
        $this->is_ajax=true;
        $supplier_id =sget('supplier_id','i',0);
        $status = sget('status','i');
        $remark= sget('remark','i','');
        $data=array();
        $data['status']=$status;
        $data['remark']= empty($remark)==''?'--':$remark;
        $data['update_time']=time();
        $data['update_name']=$_SESSION['name'];
        $res=$this->db->model('logistics_supplier')->where('supplier_id='.$supplier_id)->update($data);
        if($res){
            $this->json_output(array('err'=>0,'msg'=>'更新成功'));
        }else{
            $this->json_output(array('err'=>1,'msg'=>'更新失败'));
        }
    }

    /**
     *供应商查寻重复
     *
     */
    public function supplierUnique(){
        $data=$_POST['data'];
        if(!empty($data)){
            $info=$this->db->select('supplier_name')->where("supplier_name="."'$data'")->getCol();
            if($info) {
                $this->error("此供应商已存在");
            }else{
                $this->success('此公司名不重复，可添加');
            }
        }
    }
}