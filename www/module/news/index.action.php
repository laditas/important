<?php 
	class indexAction extends homeBaseAction {
		public function __init(){
			$this->db=M('news:news');
		} 

		//取出首页数据
		public function init(){
			//如果用户是从移动端，则转到移动页面
			if(M('public:common')->is_mobile_request()){
				$this->display('qq_page');exit;
			}
			$type=sget('type','s','');
			if ($type != 'vip'){
				//取出广告
				$this->sszcBanner=$this->getAD(8);
				$this->zcssBanner=$this->getAD(9);
				$this->sjjdBanner=$this->getAD(10);
				$this->jpfxBanner=$this->getAD(11);
				if (empty($type)) {
					$this->middleLeftBanner=$this->getAD(12);
					$this->middleRightBanner=$this->getAD(13);
					$this->middleBanner=$this->getAD(14);	
				}else{
					$arr=L('headline_ads')[$type];
					$this->cLeftTopBanner=$this->getAD($arr[0]);
					$this->cMiddle1Banner=$this->getAD($arr[1]);
					$this->cMiddle2Banner=$this->getAD($arr[2]);
					$this->cMiddle3Banner=$this->getAD($arr[3]);
					$this->cMiddle4Banner=$this->getAD($arr[4]);
					if($type=='pvc'){
						$this->middleLeftBanner=$this->getAD(27);
						$this->middleRightBanner=$this->getAD(28);
					} 
				}
				// $this->cMiddleBanner=$this->getAD(16);
				//前台显示星期
				$arr=array('星期一','星期二','星期三','星期四','星期五','星期六','星期日',);
				$this->date=$arr[date('N')-1].'&nbsp;&nbsp;'.date('Y-m-d',time());
			}
			//导航栏选中状态
			$this->nav=$type;
			//取出首页数据
			$data=$this->db->getIndex($type);		
			//由于首页和pvc页面相同，pe和pp页面相同，所以为了区分，通过type进行区分判断该显示哪一块
			if(empty($type))$type='public';
			if($type=='pp')$type='pe';	
			$this->assign(array(
				'data'=>$data,
				'type'=>$type,
			));
			$this->display('index');
		}

		//取广告函数
		private function getAD($id){
			return array_slice(array_reverse(M('system:block')->getBlock($id,1000)),0,1);
		}

		//取出列表页数据
		public function lst(){
			//分页
				$page=sget('page','i',1);
				$page_size=10;
			//获取搜索值
			$keywords=sget('keywords','s');
			if($keywords){
				//Sphinx取出关键词搜索数据
					$sphinx = new SphinxClient;
					$sphinx->SetServer('localhost',9312);
					$sphinx->SetMatchMode(SPH_MATCH_BOOLEAN);
					$sphinx->SetSortMode(SPH_SORT_EXTENDED, "input_time DESC, @id DESC" );
					$sphinx->setLimits(abs($page-1)*$page_size,$page_size,1000);
					$result = $sphinx->query("$keywords",'news');
					$ids = array_keys($result['matches']);
					$data['data'] = $this->db->search($ids);
					$this->pages = pages($result['total'], $page, $page_size);
				//取出排行榜文章
					$chartsData=$this->db->charts('','',$keywords);
			}else{
				$type=sget('type','s');
				//导航栏选中状态
					$this->nav=$type;
				$cate=sget('cate','s');
				if($cate=='djjd'){
					$this->cate_name='独家解读';
				}
				//取出该分类对应的分类ID，用于取出相对应的文章
				$where=' 1 ';
				if ($type=='vip') {
					$where.='and pid=36';
					$this->upperType='行情内参';
					$num=34;
				}else{
					$this->upperType=strtoupper($type);
					$num=100;
				}
				$cate_id=M('public:common')->model('news_cate')->select('cate_id')->where($where.' and spell="'.$cate.'"')->getOne();	
				//取出列表页数据
					$data=$this->db->getList($type,$cate_id,$page,$page_size);
				//取出排行榜文章
					$chartsData=$this->db->charts($type,$cate_id);
				//分页	
					$this->pages=pages($data['count'],$page,$page_size);
			}
			//截取示例文章文字
				foreach ($data['data'] as $key=>$v) {
						$data['data'][$key]['content']=mb_substr(strip_tags($v['content']),0,$num,'utf-8');
						//取出右键导航分类名称
						$data['data'][$key]['cate_name']=$this->db->model('news_cate')->select('cate_name')->where('cate_id="'.$v['cate_id'].'"')->getOne();
						//判断是否有前缀
						if(array_search($v['cate_id'], array(1,3,4,13,14,15))===false){
							$data['data'][$key]['prefix']=1;
						}
					}
					
				$this->assign(array(
					'data'=>$data,
					'chartsData'=>$chartsData,
					'cate'=>$cate,
					'type'	=>$type,
					'keywords'=>$keywords,
				));
			$this->display('list');
		}
                        
		//取出详情页数据
		public function detail(){
			$type=sget('type','s');
			if(!empty($type)){
				$stype='detail_'.$type;
				$arr=L('headline_ads')[$stype];
				$this->detail1banner=$this->getAD($arr[0]);
				// $this->detail2banner=$this->getAD($arr[1]);
			}
			//导航栏选中状态 
				$this->nav=$type;
			$cate=sget('cate','s');
			$id=sget('id','i');
			//如果找不到，跳转404页面
			if(!$id=sget('id','i')) {$this->forward('/page/index/notFind');exit;};
			//获取文章详情数据
			$data=sstripslashes($this->db->getNews($id));
			//获取排行榜文章
			$cate_id=M('public:common')->model('news_cate')->select('cate_id')->where('spell="'.$cate.'"')->getOne();
			$chartsData=$this->db->charts($type,$cate_id);
			//更新文章访问量
			$data['pageViews']=$this->db->updatePv($id);
			$this->seo = array('title'=>$data['title'],'key'=>$data['keywords'],'desc'=>$data['description']);
			//获取微信接口配置
			$this->wxApi=A('plasticzone:plastic')->headlineApi();
			//文章状态设置
			$status=4;
			//详情页导航
			if ($type=='vip') {				
				if ($_SESSION['userid']<1) {
					$status=1;
				}else{
					$row=$this->db->model('customer_contact')->wherePk($_SESSION['userid'])->select('user_id,name,mobile,headline_vip,cate_id,free_time')->getRow();
					if($row['free_time']>0 && $row['free_time']<=CORE_TIME){
						if($row['headline_vip']!=1 || !strstr($row['cate_id'],$cate_id)){
							$status=2;
						}else{
							$cache= E('RedisCluster',APP_LIB.'class');
							$name=$row['user_id'].'_time_'.$cate_id;
							$total_time=$cache->get($name);
							if (empty($total_time)) {
								$total_time=$this->db->model('customer_headline')->where('user_id='.$_SESSION['userid'].' and cate_id='.$cate_id)->select('total_time')->order('id desc')->getOne();
								$cache->set($name,json_encode($total_time),86400);
							}else{
								$total_time=json_decode($total_time);
							}
							if ($total_time<=CORE_TIME) {
								$info['user_id']=$row['user_id'];
								$info['c_name']=$row['name'];
								$info['mobile']=$row['mobile'];
								$info['sale_name']='--';
								$info['input_time']=CORE_TIME;
								$info['type']=5;
								$info['cate_id']=$cate_id;
								$info['total_time']=$total_time;
								$result=$this->db->model('customer_headline')->add($info);
								if ($result) {
									$cate_arr=explode(',', $row['cate_id']);
									foreach ($cate_arr as $k => $v) {
										if ($v == $cate_id) {
											unset($cate_arr[$k]);
										}
									}
									if (empty($cate_arr)) {
										$update_contact=array('headline_vip'=>0,'cate_id'=>'','opening_date'=>0);
										$this->db->model('customer_tel_sale')->where('mobile='.$c_row['mobile'])->update(array('member_status'=>0,'update_time'=>CORE_TIME));
									}else{
										$cate_id=implode(',', $cate_arr);
										$update_contact=array('headline_vip'=>1,'cate_id'=>$cate_id);
									}
									$result2=$this->db->model('customer_contact')->wherePk($_SESSION['userid'])->update($update_contact);
									if ($result2) {
										$this->success("<br /><br /><br />尊敬的客户，您的会员信息已过期，浏览权限已被关闭！<a href='/news/index/vipIntroduce' target='_blank'>&ensp;>>点此进入会员介绍</a><br /><br /><br /><br />");
									}else{
										$this->error("<br /><br /><br />尊敬的客户，您的会员信息已经过期！<a href='/news/index/vipIntroduce' target='_blank'>&ensp;>>点此进入会员介绍</a><br /><br /><br /><br />");
									}
								}else{
									$this->error("尊敬的客户，您的该类目会员已自动过期！<a href='/news/index/vipIntroduce' target='_blank'>&ensp;>>点此进入会员介绍</a>");
								}
								$status=3;
							}
						}
					}elseif($row['free_time']==0) {
						$free_time=CORE_TIME+L('free_time')[1]*86400;
						$ok=$this->db->model('customer_contact')->wherePk($_SESSION['userid'])->update(array('free_time'=>$free_time));
						if ($ok) {
							$status=6;
						}
					}
					$this->db->model('log_news')->add(array('user_id'=>$_SESSION['userid'],'news_id'=>$id,'input_time'=>CORE_TIME));
				}
				//p($status);exit;
				$this->upperType='行情内参';
			}else{
				$this->upperType=strtoupper($type);
			}
			$this->assign(array(
				'data'=>$data,
				'chartsData'=>$chartsData,
				'cate'=>$cate,
				'type'	=>$type,
				'status'=>$status
			));
			$this->display('detail');
		}
		//取出详情页数据
		public function detail2(){
			//如果找不到，跳转404页面
			if(!$id=sget('id','i')) {$this->forward('/page/index/notFind');exit;};
			//获取文章详情数据
			$data=sstripslashes($this->db->getNews($id));
			//文章分类并取出广告
			$type=$data['type'];
			if(!empty($type)){
				$stype='detail_'.$type;
				$arr=L('headline_ads')[$stype];
				$this->detail1banner=$this->getAD($arr[0]);
				// $this->detail2banner=$this->getAD($arr[1]);
			}
			//导航栏选中状态 
			$this->nav=$type;
			//获取文章分类拼音
			$cate=M('public:common')->model('news_cate')->select('spell')->where('cate_id="'.$data['cate_id'].'"')->getOne();
			//获取排行榜文章
			$chartsData=$this->db->charts($type,$data['cate_id']);
			//更新文章访问量
			$data['pageViews']=$this->db->updatePv($id);
			$this->seo = array('title'=>$data['title'],'key'=>$data['keywords'],'desc'=>$data['description']);
			//获取微信接口配置用于文章微信分享时修改名片
			$this->wxApi=A('plasticzone:plastic')->headlineApi();
			//文章状态设置
			$status=4;
			//详情页导航
			if ($type=='vip') {				
				if ($_SESSION['userid']<1) {
					$status=1;
				}else{
					$row=$this->db->model('customer_contact')->wherePk($_SESSION['userid'])->select('user_id,name,mobile,headline_vip,cate_id,free_time')->getRow();
					if($row['free_time']>0 && $row['free_time']<=CORE_TIME){
						
						if($row['headline_vip']!=1 || !strstr($row['cate_id'],$data['cate_id'])){
							// p($row['cate_id']);exit;
							$status=2;
						}else{
							$cache= E('RedisCluster',APP_LIB.'class');
							$name=$row['user_id'].'_time_'.$data['cate_id'];
							$total_time=$cache->get($name);
							if (empty($total_time)) {
								$total_time=$this->db->model('customer_headline')->where('user_id='.$_SESSION['userid'].' and cate_id='.$data['cate_id'])->select('total_time')->order('id desc')->getOne();
								$cache->set($name,json_encode($total_time),86400);
							}else{
								$total_time=json_decode($total_time);
							}
							if ($total_time<=CORE_TIME) {
								$info['user_id']=$row['user_id'];
								$info['c_name']=$row['name'];
								$info['mobile']=$row['mobile'];
								$info['sale_name']='--';
								$info['input_time']=CORE_TIME;
								$info['type']=5;
								$info['cate_id']=$data['cate_id'];
								$info['total_time']=$total_time;
								$result=$this->db->model('customer_headline')->add($info);
								if ($result) {
									$cate_arr=explode(',', $row['cate_id']);
									foreach ($cate_arr as $k => $v) {
										if ($v == $data['cate_id']) {
											unset($cate_arr[$k]);
										}
									}
									if (empty($cate_arr)) {
										$update_contact=array('headline_vip'=>0,'cate_id'=>'','opening_date'=>0);
										$this->db->model('customer_tel_sale')->where('mobile='.$c_row['mobile'])->update(array('member_status'=>0,'update_time'=>CORE_TIME));
									}else{
										$cate_id=implode(',', $cate_arr);
										$update_contact=array('headline_vip'=>1,'cate_id'=>$cate_id);
									}
									$result2=$this->db->model('customer_contact')->wherePk($_SESSION['userid'])->update($update_contact);
									if ($result2) {
										$this->success("<br /><br /><br />尊敬的客户，您的会员信息已过期，浏览权限已被关闭！<a href='/news/index/vipIntroduce' target='_blank'>&ensp;>>点此进入会员介绍</a><br /><br /><br /><br />");
									}else{
										$this->error("<br /><br /><br />尊敬的客户，您的会员信息已经过期！<a href='/news/index/vipIntroduce' target='_blank'>&ensp;>>点此进入会员介绍</a><br /><br /><br /><br />");
									}
								}else{
									$this->error("尊敬的客户，您的该类目会员已自动过期！<a href='/news/index/vipIntroduce' target='_blank'>&ensp;>>点此进入会员介绍</a>");
								}
								$status=3;
							}
						}
					}elseif($row['free_time']==0) {
						$free_time=CORE_TIME+L('free_time')[1]*86400;
						$ok=$this->db->model('customer_contact')->wherePk($_SESSION['userid'])->update(array('free_time'=>$free_time));
						if ($ok) {
							$status=6;
						}
					}
					$this->db->model('log_news')->add(array('user_id'=>$_SESSION['userid'],'news_id'=>$id,'input_time'=>CORE_TIME));
				}
				//p($status);exit;
				$this->upperType='行情内参';
			}else{
				$this->upperType=strtoupper($type);
			}
			$this->assign(array(
				'data'=>$data,
				'chartsData'=>$chartsData,
				'cate'=>$cate,
				'type'	=>$type,
				'status'=>$status
			));
			$this->display('detail');
		}
		//阿里头条抓取数据
		public function newsXML(){
			$sTime=CORE_TIME-3600;
			$eTime=CORE_TIME+3600;
			$data=$this->db->model('news_content')->select('id,title,content,cate_id,author,type')->where('input_time between '.$sTime.' and '.$eTime.' and cate_id not in (7,19) and status=1')->getAll();
			header("Content-Type:text/xml");
			echo '<?xml version="1.0" encoding="utf-8"?>';
			echo '<rss version="2.0">';
			echo '<channel>';	
			echo '<title>我的塑料网</title>';		
			echo '<pubDate>'.date('Y-m-d H:i:s',CORE_TIME).'</pubDate>';
			foreach ($data as $key => $value) {
				echo "<item>";
				echo "<id>{$value['id']}</id>";
				echo "<title>{$value['title']}</title>";
				echo "<author><![CDATA[我的塑料网]]></author>";
				$content=sstripslashes($value['content']);
				echo "<description><![CDATA[{$content}]]></description>";
				echo "<link><![CDATA[http://news.myplas.com/{$value['id']}.html]]></link>";		
				echo "</item>";				
			}
			echo "</channel>";
			echo "</rss>";			
		}

		//百度站长提交链接
		public function webSubmitLink(){
			$sTime=sget('sTime','i');
			$eTime=sget('eTime','i');
			if (empty($sTime) || empty($eTime)) {
				echo '丁公子，你URL时间戳格式不正确！';
			}else{
				$data=$this->db->model('news_content')->select('id,cate_id,type')->where('input_time between '.$sTime.' and '.$eTime.' and status=1 and type != "vip"')->getAll();
				$urls=array();
				foreach ($data as $k => $v) {
					$urls[$k]='news.myplas.com/'.$v['id'].'.html';				
				}
				$api = 'http://data.zz.baidu.com/urls?site=news.myplas.com&token=Iw7ntA4oWY4KHdVD';
				$ch = curl_init();
				$options =  array(
				    CURLOPT_URL => $api,
				    CURLOPT_POST => true,
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_POSTFIELDS => implode("\n", $urls),
				    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
				);
				curl_setopt_array($ch, $options);
				$result = curl_exec($ch);
				echo $result;			
			}
		}

		//跳转行情内参介绍页面
		public function vipIntroduce(){
			$this->nav='vip';
			$this->display('vip_introduce');
		}

		//五日内更新访问量定时任务接口
		public function pvAdd(){
			$time=CORE_TIME-432000;
			$this->db->model('news_content')->query('update p2p_news_content set pv=pv+210 where input_time>'.$time);
		}

		/**
		 * 定时清除行情内参日志历史
		 */
		public function clearHistory(){
			$time=CORE_TIME-2592000;
			$this->db->model('log_news')->where('input_time<'.$time)->delete();
		}
	} 
 ?>