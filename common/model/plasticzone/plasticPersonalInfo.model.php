<?php
/**
*获取塑料圈好友的个人信息-gsp
*/
class plasticPersonalInfoModel extends model
{
	public function __construct() {
        parent::__construct(C('db_default'), 'customer_contact');
    }
    public function getPersonalInfo($loginId,$userid){
    	$data = $this->select('con.user_id,con.name,con.c_id,con.is_pass,con.mobile,con.sex,info.thumb,info.thumbqq,info.thumbcard,cus.c_name,cus.need_product,cus.address')
    	->from('customer_contact con')
		->leftjoin('contact_info info','con.user_id=info.user_id')
		->leftjoin('customer cus','con.c_id=cus.c_id')
        ->where("con.user_id=$userid")
        ->getRow();
        //加*****
        $data['mobile'] = substr($data['mobile'],0,7).'****';
        // $data['thumb'] = FILE_URL."/upload/".$data['thumb'];
    	if(empty($data['thumbqq']))
    	{
    	    if (strstr($data['thumb'], 'http')) {
    	        $data['thumb']= $data['thumb'];
    	    } else {
                    if(empty($data['thumb'])){
                        $data['thumb']= "http://statics.myplas.com/upload/16/09/02/logos.jpg";
                    }else{
                    $data['thumb']= FILE_URL."/upload/".$data['thumb'];
                    }
    	    }
    	
    	    // $value['thumb']= FILE_URL."/upload/".$value['thumb'];
    	}else{
    	    $data['thumb']=$data['thumbqq'];
    	}
        $data['sex'] = L('sex')[$data['sex']];
        //ta的求购或报价数量
        $buy = $this->getConut($userid,1);
        $sale = $this->getConut($userid,2);
        $data['buy'] = empty($buy)?0:$buy;//求购
        $data['sale'] = empty($sale)?0:$sale;//报价
        //关注的状态
        $status = $this->getAttentionStatus($loginId,$userid);
        $data['status'] = $status==1?'取消关注':'关注';
        return $data;
    }
    public function getConut($userid,$type,$sync=6){
        if(is_array($sync)){
            return $this->model('purchase')->select('count(id)')->where("user_id=$userid and sync in (".implode(',',$sync).") and type=$type")->getOne();

        }else{
            return $this->model('purchase')->select('count(id)')->where("user_id=$userid and sync=$sync and type=$type")->getOne();

        }
    }
    //查看具体报价或采购见plasticReleaseModel
    //获取关注的状态
    public function getAttentionStatus($loginId,$userid){
        return $this->model('weixin_fans')->select('status')->where("user_id=$loginId and focused_id=$userid")->getOne();
    }
    //获取我的塑料圈
    public function getMyPlastic($userid,$headimgurl){
        $data = $this->select('con.user_id,con.name,con.c_id,con.mobile,con.is_pass,info.thumb,info.thumbqq,info.thumbcard,cus.c_name')
        ->from('customer_contact con')
        ->join('contact_info info','con.user_id=info.user_id')
        ->join('customer cus','con.c_id=cus.c_id')
        ->where("con.user_id=$userid")
        ->getRow();
        if(empty($data['thumbqq']))
        {
            if (strstr($data['thumb'], 'http')) {
                $data['thumb']= $data['thumb'];
            } else {
                if(empty($data['thumb']))
                {
                    $data['thumb']= "http://statics.myplas.com/upload/16/09/02/logos.jpg";
                }else{
                    $data['thumb']= FILE_URL."/upload/".$data['thumb'];
                }
            }
        
        }else{
            $data['thumb']=$data['thumbqq'];
        }
//         $data['thumbqq'] = $headimgurl;
        //$data['thumb'] = empty($headimgurl)?FILE_URL."/upload/".$data['thumb']:$headimgurl;
        //上传到服务器
        // $resource = file_get_contents($headimgurl);
        // $str = substr(date("Y/m/d",CORE_TIME), 2);
        // $path = $str.'/'.time().'.jpg';
        // file_put_contents(FILE_URL."/upload/".$path,$resource);
//         $this->model('contact_info')->where("user_id=$userid")->update(array('thumbqq'=>$headimgurl));
        return $data;
    }
    //获取我的塑料圈
    public function updatethumbqq($userid,$headimgurl){
//         $data = $this->select('con.user_id,con.name,con.c_id,con.mobile,info.thumb,cus.c_name')
//         ->from('customer_contact con')
//         ->join('contact_info info','con.user_id=info.user_id')
//         ->join('customer cus','con.c_id=cus.c_id')
//         ->where("con.user_id=$userid")
//         ->getRow();
//         $data['thumbqq'] = $headimgurl;
        //$data['thumb'] = empty($headimgurl)?FILE_URL."/upload/".$data['thumb']:$headimgurl;
        //上传到服务器
        // $resource = file_get_contents($headimgurl);
        // $str = substr(date("Y/m/d",CORE_TIME), 2);
        // $path = $str.'/'.time().'.jpg';
        // file_put_contents(FILE_URL."/upload/".$path,$resource);
        $this->model('contact_info')->where("user_id=$userid")->update(array('thumbqq'=>$headimgurl));
        return 1;
    }
    
    //查看我的资料(合并)
    public function getSelfInfo($userid){
        $data = $this->select('con.user_id,con.name,con.c_id,con.mobile,con.sex,con.member_level,info.thumb,info.thumbqq,info.thumbcard,info.allow_send,cus.c_name,cus.need_product,cus.address')
        ->from('customer_contact con')
        ->join('contact_info info','con.user_id=info.user_id')
        ->join('customer cus','con.c_id=cus.c_id')
        ->where("con.user_id=$userid")
        ->getRow();
        // $data['thumb'] = FILE_URL."/upload/".$data['thumb'];
        $data['sex'] = L('sex')[$data['sex']];
//         //ta的求购或报价数量
        $buy = $this->getConut($userid,1);
        $sale = $this->getConut($userid,2);
        $data['buy'] = empty($buy)?0:$buy;//求购
        $data['sale'] = empty($sale)?0:$sale;//报价
        //偏好设置
        $data['allow_send'] = json_decode($data['allow_send'],true);
        if(empty($data['allow_send'])) $data['allow_send'] = array('focus'=>0,'repeat'=>0);
        //排名
        $data['total'] = $this->getAllMembers();
        $data['rank'] = $this->getRank($userid);
        $data['member_level'] = L('member_level')[$data['member_level']];
        $data['fans'] = M('plasticzone:plasticPerson')->getFuns($data['user_id']);
        if(empty($data['thumbqq']))
        {
            if (strstr($data['thumb'], 'http')) {
                $data['thumb']= $data['thumb'];
            } else {
                if(empty($data['thumb']))
                {
                    $data['thumb']= "http://statics.myplas.com/upload/16/09/02/logos.jpg";
                }else{
                    $data['thumb']= FILE_URL."/upload/".$data['thumb'];
                }
            }
        
        }else{
            $data['thumb']=$data['thumbqq'];
        }
        return $data;
    }
    //获得排名状态
    public function getRank($userid){
//         $data = $this->model('customer_contact')->select('user_id')->where("chanel=6")->getAll();
//         $ranks = array();
//         $index = 0;
//         foreach ($data as &$value) {
//             $funs = $this->model('weixin_fans')->select('count(id)')->where("focused_id={$value['user_id']}")->getOne();
//             $purs = $this->model('purchase')->select('count(id)')->where("user_id={$value['user_id']} and sync=6")->getOne();
//             $value['sum'] = $funs + $purs;
//             $ranks[] = $value;
//         }
//         foreach ($ranks as $key=>$value){
//           $id[$key] = $value['user_id'];
//           $sum[$key] = $value['sum'];
//         }
//         array_multisort($sum,SORT_NUMERIC,SORT_DESC,$id,SORT_STRING,SORT_ASC,$ranks);
//         foreach ($ranks as $key => $value) {
//             if($userid==$value['user_id']) $index = $key+1;
//         }
//         $sql = "SELECT a.user_id,IFNULL(COUNT(b.id)+COUNT(c.id),0) pm FROM p2p_customer_contact  a 
//         LEFT JOIN p2p_weixin_fans b ON b.focused_id=a.user_id
//         LEFT JOIN p2p_purchase c ON c.user_id=a.user_id
//         WHERE chanel=6 AND a.user_id=".$userid."
//         GROUP BY a.user_id";
        $count = $this->model('customer_contact')->select('count(0)')->where("chanel=6")->getOne();
        $exists = $this->model('weixin_ranking')->select('COUNT(0)')->where("user_id = {$userid}")->getOne();
        if($exists<=0){//不存在 则插入记录
            $sql=" SELECT IFNULL(rownum,0) rownum, IFNULL(pm,0) pm FROM p2p_weixin_ranking WHERE user_id=".$userid;
            $data =$this->db->getRow($sql);
            $data['rownum']=$count;//默认最后一位
            $temp = array();
            $temp['pm'] =  0;
            $temp['rownum'] = $data['rownum'];
            $temp['user_id'] = $userid;
            $this->model('weixin_ranking')->add($temp);
        }else{//存在取记录
            $sql=" SELECT IFNULL(rownum,0) rownum  FROM p2p_weixin_ranking WHERE user_id=".$userid;
            $data =$this->db->getRow($sql);
        }
        return $data['rownum'];
    }
    //获取塑料圈总人数
    public function getAllMembers(){
        $count = $this->model('customer_contact')->select('count(user_id)')->where("chanel = 6")->getOne();
        return $count + 3000;
    }
    //计算会员等级
    public function getMemberLevel($userid){
        $buy = $this->getConut($userid,1);
        $sale = $this->getConut($userid,2);
        $intro = $this->getIntroductionNum();
        $sum = $buy + $sale + $intro;
        if($sum<20) {
            $this->updateMemberLevel($userid,1);
        }elseif ($sum>19 && $sum<70) {
            $this->updateMemberLevel($userid,2);
        }elseif ($sum>69 && $sum<140) {
            $this->updateMemberLevel($userid,3);
        }elseif ($sum>139 && $sum<280) {
            $this->updateMemberLevel($userid,4);
        }elseif ($sum>279 && $sum<500) {
            $this->updateMemberLevel($userid,5);
        }elseif ($sum>499 && $sum<950) {
            $this->updateMemberLevel($userid,6);
        }elseif ($sum>949 && $sum<1800) {
            $this->updateMemberLevel($userid,7);
        }elseif ($sum>1799 && $sum<3500) {
            $this->updateMemberLevel($userid,8);
        }elseif ($sum>3499 && $sum<5500) {
            $this->updateMemberLevel($userid,9);
        }elseif ($sum>5499) {
            $this->updateMemberLevel($userid,10);
        }
    }
    //引荐数
    public function getIntroductionNum(){
        return $this->model('customer_contact')->select('count(user_id)')->where('parent_mobile='.$_SESSION['uinfo']['mobile'])->getOne();
    }
    //更新会员等级
    public function updateMemberLevel($userid,$level){
        $this->model('customer_contact')->where("user_id=$userid")->update(array('member_level'=>$level));
    }
    //获取地址
    public function getMobileAddress($mobile){
         return getCityByMobile($mobile);
        // $_info=file_get_contents("http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=".$mobile);
        // $_info=gbk2utf($_info);
        // if(strstr($_info,'carrier')){
        //     return substr($_info,strpos($_info,'carrier')+9,-6);
        // }
    }
}