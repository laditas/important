<?php
/**
*我的引荐-gsp
*/
class plasticIntroductionModel extends model
{
	public function __construct() {
        parent::__construct(C('db_default'), 'customer_contact');
    }
    //获取我的引荐
    public function getMyIntroduction($page=1,$size=10){
        $data =	$this->select('con.user_id,con.name,con.mobile,con.is_pass,cus.c_name,info.thumb,info.thumbqq')
			->from('customer_contact con')
    		->leftjoin('customer cus','con.c_id=cus.c_id')
            ->leftjoin('contact_info info','con.user_id=info.user_id')
	    	->page($page,$size)
	    	->where('con.parent_mobile='.$_SESSION['uinfo']['mobile'])
	    	->order("con.input_time desc")
	        ->getPage();
        foreach ($data['data'] as &$value) {
        	$value['buy'] = M('plasticzone:plasticPersonalInfo')->getConut($value['user_id'],1);
        	$value['sale'] = M('plasticzone:plasticPersonalInfo')->getConut($value['user_id'],2);
            $value['mobile'] = substr($value['mobile'],0,7).'****';
            if(empty($value['thumbqq']))
            {
                if (strstr($value['thumb'], 'http')) {
                    $value['thumb']= $value['thumb'];
                } else {
                    if(empty($value['thumb'])){
                        $value['thumb']= "http://statics.myplas.com/upload/16/09/02/logos.jpg";
                    }else{
                        $value['thumb']= FILE_URL."/upload/".$value['thumb'];
                    }
                }
            }else{
                $value['thumb']=$value['thumbqq'];
            }
        }
        return $data;
    }

	//塑料圈app获取我的引荐
	public function getqAppMyIntroduction($user_id,$page=1,$size=10){
		//引荐人的手机号
		$mobile=$this->model('customer_contact')->select('mobile')->where('user_id='.$user_id)->getOne();
		$data =	$this->select('con.user_id,con.name,con.mobile,con.is_pass,cus.c_name,info.thumb')
				->from('customer_contact con')
				->join('customer cus','con.c_id=cus.c_id')
				->join('contact_info info','con.user_id=info.user_id')
				->page($page,$size)
				->where('con.parent_mobile='.$mobile)
				->order("con.input_time desc")
				->getPage();
		foreach ($data['data'] as &$value) {
			$value['buy'] = M('plasticzone:plasticPersonalInfo')->getConut($value['user_id'],1,6);//purchase表
			$value['sale'] = M('plasticzone:plasticPersonalInfo')->getConut($value['user_id'],2,6);//purchase表

			if(empty($value['thumbqq']))
			{
				if (strstr($value['thumb'], 'http')) {
					$value['thumb']= $value['thumb'];
				} else {
					if(empty($value['thumb'])){
						$value['thumb']= "http://statics.myplas.com/upload/16/09/02/logos.jpg";
					}else{
						$value['thumb']= FILE_URL."/upload/".$value['thumb'];
					}
				}

				// $value['thumb']= FILE_URL."/upload/".$value['thumb'];
			}else{
				$value['thumb']=$value['thumbqq'];
			}

		}
		return $data;
	}

    //获取我的粉丝和我的关注
    public function getMyFuns($userid,$type,$page=1,$size=10){
    	$where = "status=1";
    	switch ($type) {//1粉丝2关注
    		case 1:
    			$where.=" and focused_id=$userid ";
    			break;
    		case 2:
    			$where.=" and user_id=$userid ";
    			break;
    	}
    	$data = $this->model('weixin_fans')->select('user_id,focused_id')
		    	->page($page,$size)
		    	->where($where)
		    	->order("input_time desc")
		    	->getPage();
    	foreach ($data['data'] as &$value) {
    		if($type==1){
    			$value['user_id'] = $this->get_funs($value['user_id']);//粉丝
    		}elseif($type==2){
    			$value['focused_id'] = $this->get_funs($value['focused_id']);//关注
    		}
    	}
    	return $data;
    }
    //根据不同类型获取粉丝或关注的相关
    public function get_funs($id){
		$data = $this->select('con.user_id,con.name,con.mobile,con.is_pass,cus.c_name,info.thumb,info.thumbqq')
			->from('customer_contact con')
			->leftjoin('customer cus','con.c_id=cus.c_id')
            ->leftjoin('contact_info info','con.user_id=info.user_id')
	    	->where('con.user_id='.$id)
	        ->getRow();
        //加***
        $data['mobile'] = substr($data['mobile'],0,7).'****';
    	$data['buy'] = M('plasticzone:plasticPersonalInfo')->getConut($id,1);
    	$data['sale'] = M('plasticzone:plasticPersonalInfo')->getConut($id,2);
        $data['fans'] = $this->getFansCount($id);
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
    	return $data;
    }
    //粉丝
    public function getFansCount($id){
        return $this->model('weixin_fans')->where("focused_id = $id")->select('count(id)')->getOne();
    }
}