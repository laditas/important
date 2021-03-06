<?php
/**
*个人中心控制器-web-app
*/
class personalCenterAction extends homeBaseAction
{
    protected $userid = '';
    protected $today = '';
	public function __init() {
		$this->db=M('public:common')->model('customer_contact');
        $this->today = strtotime(date('Y-m-d',time()));
    }
    //进入个人中心
    public function init(){
    	$this->display('me_logged');
    }
    //获取个人中心首页数据
    public function getPersonalCenter(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $type1 = sget('type1','i');//$type1 1采购
        $type2 = sget('type2','i');//$type1 2报价
        $is_read=sget('status','i',1);//读取状态筛选 1=>'未读',2=>'已读',空 全部
        $thumb = M('touch:personalcenter')->getUserThumb($this->user_id);
        $name = M('touch:personalcenter')->getUserName($this->user_id);
        $qCount = M('myapp:personalAppCenter')->getMyQuotationCount($this->user_id,$type2);
        $pCount = M('myapp:personalAppCenter')->getMyQuotationCount($this->user_id,$type1);
        $proAttCount = M('myapp:personalAppCenter')->getMyAttentionCount($this->user_id);
        $points = M('points:pointsBill')->getUerPoints($this->user_id);
        $cus_mana = M('myapp:personalAppCenter')->getMyCusManager($this->user_id);//交易员姓名
        $msg = M('myapp:personalAppCenter')->getMsg($this->user_id,$is_read);//获取未读的站内信消息
        if($name){
            $this->json_output(array('thumb'=>empty($thumb)?FILE_URL.'/myapp/img/avator.png':FILE_URL."/upload/".$thumb,'name'=>$name,'qcount'=>$qCount,'pcount'=>$pCount,'proattcount'=>$proAttCount,'points'=>$points,'cus_mana'=>$cus_mana,'msgCount'=>$msg['count']));
        }else{
            $this->json_output(array('err'=>2,'msg'=>'没有相关数据!'));
        }
    }
    //进入站内信
    public function enMsg(){
        $this->display('msg');
    }
    //获取站内信
    public function getMsg(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $is_read=sget('status','i');//读取状态筛选 1=>'未读',2=>'已读',空 全部
        $type=sget('type','i');//语言包的5个状态, 空 全部
        $page=sget('page','i',1);
        $size=sget('size','i',10);
        $msg = M('myapp:personalAppCenter')->getMsg($this->user_id,$is_read,$type,$page,$size);
        $this->_checkLastPage($msg['count'],$size,$page);//是否最后一页
        foreach ($msg['data'] as &$value) {
            $value['msg']=preg_replace("/查看详情/", "", $value['msg']);
            $value['msg'] = mb_strcut($value['msg'], 0, strripos($value['msg'],"，"), 'utf-8');
            $value['left'] = stripos($value['msg'],"：");
            $value['right'] = stripos($value['msg'],"（");
            $value['num'] = mb_strcut(mb_strcut($value['msg'],0,$value['right'],'utf-8'),$value['left']+3,strlen(mb_strcut($value['msg'],0,$value['right'],'utf-8')),'utf-8');//id或订单编号
            if($value['type']==1){
                if($value['input_time']>$this->today){
                    $value['is_read']=1;
                }else{
                    $value['is_read']=2;
                }
            }elseif ($value['type']==2 || $value['type']==3) {
                $value['num'] = intval($value['num']);
            }
            $value['is_read'] = $value['is_read']==1?'new':'已读';
            $value['input_time'] = M('myapp:personalAppCenter')->changeTime($value['input_time']);
        }
        $this->json_output(array('err'=>0,'msg'=>$msg['data']));
    }
    //进入我的报价单
    public function enMyQuotation(){
        $this->display('me_quotation');
    }
    //进入我的采购
    public function enMyPurchase(){
        $this->display('me_purchase');
    }
    //获取我的报价单/我的采购
    public function myQuoAndPur(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        //普通
        $type = sget('type','i');//1:采购 2:报价
        $product_type = sget('product_type','i');//1/2/3/4/5/6/7/8/9 空 全部
        $shelve_type = sget('shelve_type','i');// 1:上架 2:下架 空 全部
        $cargo_type = sget('cargo_type','i');//1现货 2期货 空 全部
        //关键字搜索
        $keywords = sget('keywords','s');
        $page = sget('page','i',1);
        $size = sget('size','i',10);
        $data = M('myapp:personalAppCenter')->getMyQuotation($this->user_id,$type,$product_type,$shelve_type,$cargo_type,$keywords,$page,$size);
        if(!$data['data']) $this->json_output(array('err'=>2,'msg'=>'没有相关的数据!'));
        $this->_checkLastPage($data['count'],$size,$page);
        $this->json_output(array('err'=>0,'data'=>$data['data']));
    }
    //获取采购洽谈(求购信息有人报价) 委托洽谈(采购信息有人报价)
    public function getOptions(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $id = sget('id','i');
        if(!$data = M('myapp:personalAppCenter')->getOptions($id)) $this->json_output(array('err'=>2,'msg'=>'没有相关的洽谈数据!'));
        $this->json_output(array('err'=>0,'data'=>$data));
    }
    //选定报价
    public function selected(){
        $this->is_ajax=true;
        if($this->user_id<=0) $this->error('账户错误');
        $id=sget('id','i',0);//sale_buy的报价id 此处为id不是p_id
        $price=sget('price','f');//成交价格
        $result = M('myapp:personalAppCenter')->selectPrice($this->user_id,$id,$price);
        $this->json_output($result);
    }
    //进入我的订单
    public function enOrder(){
        $this->display('me_ordercenter');
    }
    //获取我的订单(自营、联营)
    public function getMyOrder(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        //普通
        $type = sget('type','i',1);//1,自营 2,联营
        //搜索
        $keywords = sget('keywords','s');//订单编号搜索
        $otype = sget('otype','s');//1/2/3/4/5/6/7/8/9  空 全部  4/5/6有所不同
        $page = sget('page','i',1);
        $size = sget('size','i',10);
        $data = M('myapp:personalAppCenter')->getMyOrder($this->user_id,$type,$keywords,$otype,$page,$size);
        if(!$data['data']) $this->json_output(array('err'=>2,'msg'=>'没有相自营/联营的关数据'));
        $this->_checkLastPage($data['count'],$size,$page);
        $this->json_output(array('err'=>0,'data'=>$data['data']));
    }
    //进入订单详情
    public function enOrderDetail(){
        $this->display('me_orderdetail');
    }
    //获取订单详情
    public function getOrderDetail(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        //普通
        $type = sget('type','i',1);//1,自营 2,联营
        $id = sget('id','i',0);//order表或union_order表的o_id/id
        $result = M('myapp:personalAppCenter')->getOrderDetail($this->user_id,$type,$id);
        $this->json_output($result);
    }
    //保存自营申请开票
    // public function saveAppliBill(){
    //     $this->is_ajax = true;
    //     if($this->user_id<=0) $this->error('账户错误');
    //     $o_id = sget('o_id','i',0);//order的id
    //     M('myapp:personalAppCenter')->saveAppliBill($this->user_id,$o_id);
    // }
    //进入我的关注
    public function enMyAttention(){
        $this->display('me_attention');
    }
    //获取我的关注
    public function myAttention(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $products = M('myapp:personalAppCenter')->getMyAttention($this->user_id);
        $this->json_output(array('err'=>0,'data'=>$products));
    }
    //进入我的积分
    public function enMyPoints(){
        $this->display('me_creditdetail');
    }
    //获取我的积分
    public function getMyPoints(){
        $this->is_ajax = true;
        //if($this->user_id<=0) $this->error('账户错误');
        $points = empty($this->user_id)?"请登录":M('points:pointsBill')->getUerPoints($this->user_id);
        $gtype = sget('gtype','i');//1=>'家居',2=>'数码',空，全部
        $points = M('points:pointsBill')->getUerPoints($this->user_id);
        if(!$result = M('touch:creditshop')->getCreditShop($gtype))  $this->json_output(array('err'=>2,'msg'=>'没有相关的数据!'));
            foreach ($result as &$v) {
                $v['thumb']=FILE_URL."/upload/".$v['thumb'];
            }
            // foreach ($result as &$v) {
            //     if(file_exists(FILE_URL."/upload/".$v['thumb'])){
            //         $v['thumb'] = FILE_URL."/upload/".$v['thumb'];
            //         $newRes[] = $v;
            //     }
            // }
            $this->json_output(array('err'=>0,'points'=>$points,'shop'=>$result));
    }
    //进入我的物流
    public function enMyTrans(){
        $this->display('me_logistics');
    }
    //获取我的物流
    public function getMyTrans(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        //$this->db->model('')
    }
    //进入我的设置
    public function enMySet(){
        $this->display('me_set');
    }
    //获取我的设置
    public function getMySet(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $set = M('user:customerContact')->getUserInfoByid($this->user_id);
        $set['cus_mana'] = M('myapp:personalAppCenter')->getMyCusManager($this->user_id);//交易员姓名
        $set['c_name'] = M('user:customer')->getCinfoById($set['c_id'])['c_name'];//公司名称
        $set['bill_status'] = M('myapp:personalAppCenter')->checkBillInfo($this->user_id)==false?"未上传":"已上传";
        if(empty($set)) $this->json_output(array('err'=>2,'msg'=>'没有相关的设置信息'));
        $this->json_output(array('err'=>0,'data'=>$set));
    }
    /**
     *跳转到下个界面，功能操作
     */
    //报价搜索
    public function doQuoSearch(){
        $this->is_ajax=true;
        if($this->user_id<=0) $this->error('账户错误');
        //搜素关键字
        $keywords = sget('keywords','s');
        //1现货/2期货
        $cargo_type = sget('cargo_type','i');
        //存取数值
        $temp = array();
        $result = array();
        if(empty($keywords)){
            $this->error('请输入关键字');
        }
        //筛选产品类型
        $p_types = array('1'=>'HDPE','2'=>'LDPE','3'=>'LLDPE','4'=>'PP','5'=>'PVC');
        if(in_array($keywords, $p_types)){
            $keyValue = $this->_getProKey($p_types,$keywords);
        }
        //1,上架2,下架,其他,全部
        $type = sget('type','s');
        $where="(fa.f_name like '%{$keywords}%' or pro.model like '%{$keywords}%' or pro.product_type='{$keyValue}') and pur.user_id={$this->user_id} and pur.cargo_type={$cargo_type} and pur.type=2";
        if($type == '1'){
            $where.=" and pur.shelve_type=1";
            $data = $this->db->select('pur.id,pur.p_id,pro.model,pro.product_type,pur.unit_price,fa.f_name,pur.number,pur.store_house,pur.input_time,pur.shelve_type')->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reverseData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reverseData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }elseif ($type == '2') {
            $where.=" and pur.shelve_type=2";
            $data = $this->db->select('pur.id,pur.p_id,pro.model,pro.product_type,pur.unit_price,fa.f_name,pur.number,pur.store_house,pur.input_time,pur.shelve_type')->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reverseData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reverseData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }else{
            $data = $this->db->select('pur.id,pur.p_id,pro.model,pro.product_type,pur.unit_price,fa.f_name,pur.number,pur.store_house,pur.input_time,pur.shelve_type')->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reverseData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reverseData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
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
    //单个上架，下架切换
    public function changestate(){
        $this->is_ajax=true;
        if($this->user_id<=0) $this->error('账户错误');
        //当前报价的id
        $id = sget('id','i',0);
        if($id>0){
            $result = M('touch:myquotation')->changeProductState($id);
        }
        $this->json_output($result);
    }
    //单个更新报价单
    public function refreshCell(){
        $this->is_ajax=true;
        if($this->user_id<=0) $this->error('账户错误');
        $id = sget('id','i',0);
        $data = sget('qdata','a');
        $p_id = sget('pid','i',0);
        $result = M('touch:myquotation')->refreshCell($id, $data, $p_id);
        $this->json_output($result);
    }
    //批量更新报价单
    public function refreshMulCell(){
        $this->is_ajax=true;
        if($this->user_id<=0) $this->error('账户错误');
        //取出所有的id
        $ids = sget('ids','a');
        $up = sget('up','i');
        $down = sget('down','i');
        $del = sget('del','i');
        $result = M('touch:myquotation')->refreshMulCell($ids, $up, $down, $del);
        $this->json_output($result);
    }
    //求购搜索
    public function doPurSearch(){
        $this->is_ajax=true;
        if($this->user_id<=0) $this->error('账户错误');
        //搜素关键字
        $keywords = sget('keywords','s');
        //1现货/2期货
        $cargo_type = sget('cargo_type','i');
        //存取数值
        $temp = array();
        $result = array();
        if(empty($keywords)){
            $this->error('请输入关键字');
        }
        //筛选产品类型
        $p_types = array('1'=>'HDPE','2'=>'LDPE','3'=>'LLDPE','4'=>'PP','5'=>'PVC');
        if(in_array($keywords, $p_types)){
            $keyValue = $this->_getProKey($p_types,$keywords);
        }
        //1.待审核  2.审核通过  3.洽谈中  4.交易成功   5.无效 6:过期
        $type = sget('status','s');
        $where="(fa.f_name like '%{$keywords}%' or pro.model like '%{$keywords}%' or pro.product_type='{$keyValue}') and pur.user_id={$this->user_id} and pur.cargo_type={$cargo_type} and pur.type=1";
        if($type == '1'){
            $where.=" and pur.status=1";
            $data = $this->db->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reversePurData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reversePurData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }elseif ($type == '2') {
            $where.=" and pur.status=2";
            $data = $this->db->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reversePurData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reversePurData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }elseif ($type == '3') {
            $where.=" and pur.status=3";
            $data = $this->db->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reversePurData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reversePurData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }elseif ($type == '4') {
            $where.=" and pur.status=4";
            $data = $this->db->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reversePurData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reversePurData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }elseif ($type == '5') {
            $where.=" and pur.status=5";
            $data = $this->db->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reversePurData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reversePurData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }elseif ($type == '6') {
            $where.=" and pur.status=6";
            $data = $this->db->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reversePurData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reversePurData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }else{
            $data = $this->db->from('purchase pur')
            ->join('product pro','pur.p_id=pro.id')
            ->join('factory fa','pro.f_id=fa.fid')
            ->where($where)
            ->getAll();
            // $resultData = $this->_reversePurData($data);
            // $this->json_output($resultData);
            if(!$resultData = $this->_reversePurData($data))
            $this->json_output(array('err'=>2,'msg'=>'没有相关搜索结果'));
            $this->json_output(array('err'=>0,'resultData'=>$resultData));
        }
    }
    //转换数据
    private function _reversePurData($data){
        //遍历data
            foreach ($data as $value) {
                $temp['model'] = $value['model'];
                $temp['product_type'] = L('product_type')[$value['product_type']];
                $temp['unit_price'] = $value['unit_price'];
                $temp['f_name'] = $value['f_name'];
                $temp['number'] = $value['number'];
                $temp['store_house'] = $value['store_house'];
                $temp['input_time'] = $value['input_time'];
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
    //进入添加我的关注
    public function addMyAttention(){
        $this->display('me_attentionadd');
    }
    //品种->牌号联动
    public function getModelByCla(){
        $this->is_ajax=true; //指定为Ajax输出
        //if($this->user_id<=0) $this->error('账户错误');
        $kid = sget('kid','i');//12345
        $models = $this->db->model('product')->select('model')->where('product_type='.$kid)->order('input_time desc')->limit('0,20')->getAll();
        if(!$models)
            $this->json_output(array('err'=>2,'msg'=>'没有相关牌号结果'));
            $this->json_output(array('err'=>0,'models'=>$models));
    }
    //牌号->厂家联动
    public function getFactoryByMod(){
        $this->is_ajax=true; //指定为Ajax输出
        //if($this->user_id<=0) $this->error('账户错误');
        $model = sget('model','s');
        $facId = $this->db->model('product')->select('f_id')->where("model='{$model}'")->order('input_time desc')->limit('0,20')->getAll();
        $factorys = array();
        foreach ($facId as $key => $value) {
            $f_name = $this->db->model('factory')->select('f_name')->where('fid='.$value['f_id'])->getOne();
            $factorys[$key] = $f_name;
        }
        if(!$factorys)
            $this->json_output(array('err'=>2,'msg'=>'没有相关厂家结果'));
            $this->json_output(array('err'=>0,'factorys'=>$factorys));
    }
    //保存添加新的我的关注(产品)
    public function addProAttention(){
        $this->is_ajax=true; //指定为Ajax输出
        if($this->user_id<=0) $this->error('账户错误');
        // $dataP = sdata(); //传递的参数:产品类别/产品牌号/产地
        if(empty($_POST)){
            //$this->error('请添加关注产品信息');
            $this->json_output(array('err'=>3,'msg'=>'请添加关注产品信息'));
        }
        //检查该产品是否已经关注过
        $fid = M('product:factory')->getIdsByName($_POST['address']);//根据厂家查出fid
        $pid = M('product:product')->getPidByModel($_POST['num'], $fid[0]);//根据牌号和fid查出产品id
        if($this->db->model('concerned_product')->select('product_id')->where('product_id='.$pid)->getOne()) $this->json_output(array('err'=>4,'msg'=>'该产品已经关注过'));

        $userContact = M('user:customerContact')->getListByUserid($this->user_id);
        $company = M('user:customer')->getCinfoById($userContact['c_id']);

        $data['user_id'] = $this->user_id;
        $data['product_name'] = L('product_type')[$_POST['kid']];//产品类别下标
        $data['model'] = $_POST['num'];
        $data['factory_name'] = $_POST['address'];
        $data['user_account_id'] = $userContact['mobile'];
        $data['staff_name'] = $userContact['name'];
        $data['customer_name'] = $company['c_name'];
        $data['product_id'] = $pid;
        $data['status'] = 1;
        $data['operate'] = 1;
        $data['groupno'] = $company['grounp_no'];
        $data['input_time'] = CORE_TIME;
        $data['input_admin'] = $_SESSION['name'];
        $data['update_time'] = CORE_TIME;
        $data['update_admin'] = $_SESSION['name'];

        if(!M('user:account')->add($data)) $this->json_output(array('err'=>2,'msg'=>'添加关注失败'));
        $this->success('添加关注成功');
    }
    //进入管理我的关注
    public function manageMyAttention(){
        $this->display('me_attentionmanage');
    }
    //获取我的关注(管理),同样的方法
    //我的关注全选/不选删除
    public function delMyAttention(){
        $this->is_ajax=true;
        if($this->user_id<=0) $this->error('账户错误');
        $ids = sget('ids','a');
        $result = M('myapp:personalAppCenter')->mulDelMyAttention($ids);
        $this->json_output($result);
    }
    //积分明细
    public function creditDetail(){
        $this->display('me_mycredit');
    }
    //返回积分明细
    public function get_creditdetail(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $points = M('points:pointsBill')->getUerPoints($this->user_id);
        $result = M('touch:creditdetail')->getCreditDetail($this->user_id);
        $this->json_output(array('points'=>$points,'detail'=>$result));
    }
    //签到、兑换路径:/myapp/sign
    //兑换记录
    public function creditRecord(){
        $this->display('me_creditrecord');
    }
    //返回兑换记录
    public function get_creditRecord(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $result = M('touch:creditRecord')->getCreditRecord($this->user_id);
        foreach ($result as  &$v) {
            $v['thumb']=FILE_URL."/upload/".$v['thumb'];
        }
        $this->json_output($result);
    }
    //商品详情页
    public function shopDetail(){
        $this->display('me_shopdetail');
    }
    //点击返回商品详情
    public function get_shopDetail(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $gid = sget('gid','i',0);
        $result = M('touch:creditshop')->getShopDetail($gid);
        $result['image']=FILE_URL."/upload/".$result['image'];
        $result['thumb']=FILE_URL."/upload/".$result['thumb'];
        $this->json_output($result);
    }
    /**
     *设置页面的每个cell操作
     */
    //进入姓名
    public function enName(){
        $this->display('me_set_name');
    }
    //保存姓名
    public function saveName(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $name = sget('name','s');
        $_user=array(
                    'update_time'=>CORE_TIME,
                    'update_admin'=>$_SESSION['name'],//有小问题
                    'name'=>$name,
                );
        $this->_saveSetData($_user);
    }
    //进入公司
    public function enCompany(){
        $this->display('me_set_company');
    }
    //保存公司
    public function saveCompany(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $c_name = sget('c_name','s');
        $c_id = M('user:customerContact')->getListByUserid($this->user_id)['c_id'];
        if(!$this->db->model('customer')->where('c_id='.$c_id)->update(array('c_name'=>$c_name))) $this->json_output(array('err'=>2,'msg'=>'修改失败'));
        $this->json_output(array('err'=>0,'msg'=>'修改成功'));
    }
    //进入我的交易员
    public function enTrader(){
        $this->display('me_set_trader');
    }
    //保存我的交易员
    public function saveTrader(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $customer_manager = sget('cus_manager','i');//交易员下标
        $_user=array(
                    'update_time'=>CORE_TIME,
                    'update_admin'=>$_SESSION['name'],
                    'customer_manager'=>$customer_manager,
                );
        $this->_saveSetData($_user);
    }
    //进入密码修改
    public function enPassword(){
        $this->display('me_set_pwd');
    }
    //保存密码修改
    public function savePassword(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $password = sget('password','s');
        $oldpassword = sget('oldpassword','s');
        $salt=randstr(6);
        $user_model=M('system:sysUser');
        $_user=array(
                    'update_time'=>CORE_TIME,
                    'update_admin'=>$_SESSION['name'],
                    'salt'=>$salt,
                    'password'=>$user_model->genPassword($password.$salt),
                );
        $this->_saveSetData($_user);
    }
    //进入邮箱
    public function enEmail(){
        $this->display('me_set_email');
    }
    //保存邮箱
    public function saveEmail(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $email = sget('email','s');
        $_user=array(
                    'update_time'=>CORE_TIME,
                    'update_admin'=>$_SESSION['name'],
                    'email'=>$email,
                );
        $this->_saveSetData($_user);
    }
    //进入QQ
    public function enQq(){
        $this->display('me_set_qq');
    }
    //保存QQ
    public function saveQq(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $qq = sget('qq','s');
        $_user=array(
                    'update_time'=>CORE_TIME,
                    'update_admin'=>$_SESSION['name'],
                    'qq'=>$qq,
                );
        $this->_saveSetData($_user);
    }
    //进入关于我们
    public function enAboutUs(){
        $this->display('aboutus');
    }
    //获取关于我们
    public function getAboutUs(){

    }
    //进入去评分
    public function enEvaluate(){
        $this->display('');
    }
    //获取评分
    public function getEvaluate(){
        
    }
    //进入开票资料
    public function enBillInfo(){
        $this->display('billinfo');
    }
    //获取开票资料
    public function getBillInfo(){
        $this->is_ajax = true;
        //if($this->user_id<=0) $this->error('账户错误');
        if($data=M('myapp:personalAppCenter')->getBillInfo(3858)) $this->json_output(array('err'=>0,'data'=>$data));
        $this->json_output(array('err'=>2,'msg'=>'暂无开票资料'));
    }
    //保存修改后的开票资料
    public function saveBillInfo(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $id = sget('id','i');
        $tax_id = sget('tax_id','s');
        $invoice_address = sget('invoice_address','s');
        $invoice_tel = sget('invoice_tel','s');
        $invoice_bank = sget('invoice_bank','s');
        $invoice_account = sget('invoice_account','s');
        $result = M('myapp:personalAppCenter')->changeBill($id,$this->user_id,$tax_id,$invoice_address,$invoice_tel,$invoice_bank,$invoice_account);
        $this->json_output($result);
    }
    //进入意见反馈
    public function enMyFeedBack(){
        $this->display('me_opinion');
    }
    //保存意见反馈
    public function saveFeedBack(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        $msg_type = sget('msg_type','i');//留言类型,1:发布/委托问题 2:举报诈骗信息3:其他
        $message = sget('message','s');
        $contact_way = sget('contact_way','s');
        $result = M('myapp:personalAppCenter')->saveFeedBack($this->user_id,$msg_type,$message,$contact_way);
        $this->json_output($result);
     }
    //保存个人设置
    private function _saveSetData($_user){
        if(!$this->db->model('customer_contact')->where('user_id='.$this->user_id)->update($_user)) $this->json_output(array('err'=>2,'msg'=>'修改失败'));
        $this->json_output(array('err'=>0,'msg'=>'修改成功'));
    }
    //判断是否到最后一页
    private function _checkLastPage($count,$size,$page){
        if($count>0){
            if($count%$size==0 && ceil($count/$size)<$page){
                $this->json_output(array('err'=>3,'msg'=>'没有更多数据'));
            }elseif ($count%$size!=0 && ceil($count/$size)<$page) {
                $this->json_output(array('err'=>3,'msg'=>'没有更多数据'));
            }
        }
    }
    //退出登录
    public function logOut(){
        $this->is_ajax = true;
        if($this->user_id<=0) $this->error('账户错误');
        M('user:passport')->setSession();
        $this->json_output(array('err'=>0,'msg'=>'退出成功'));
    }
}