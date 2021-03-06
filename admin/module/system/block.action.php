<?php
/** 
 * 页面模块管理
 */
class blockAction extends adminBaseAction {
	public function __init(){
		$this->debug = false;
		$this->positions=M('system:block')->getPosition();
		$this->db->model('block');
	}

	/**
	 * 栏目内容列表
	 * @access public 
	 * @return html
	 */
	public function init(){
		$action=sget('action','s');
		if($action=='grid'){ //获取列表
			$page = sget("pageIndex",'i',0); //页码
			$size = sget("pageSize",'i',20); //每页数
			$sortField = sget("sortField",'s','id'); //排序字段
			$sortOrder = sget("sortOrder",'s','desc'); //排序
		
			//搜索条件
			$where=" 1 ";
			$position=sget('position','i');
			if($position>0){
				$where.=' and position='.$position;	
			}
			
			//状态
			$status=sget('status',0);
			if($status>0){
				$where.=' and status='.($status-1);	
			}
			
			$list=$this->db->model('block')->select('id,name,position,status,sort_order,start_time,end_time')
						->where($where)
						->page($page+1,$size)
						->order("$sortField $sortOrder")
						->getPage();
			foreach($list['data'] as $k=>$v){
				$list['data'][$k]['start_time']=$v['start_time']>1000 ? date("Y-m-d H:i:s",$v['start_time']) : '-';
				$list['data'][$k]['end_time']=$v['end_time']>1000 ? date("Y-m-d H:i:s",$v['end_time']) : '-';
			}
			$result=array('total'=>$list['count'],'data'=>$list['data']);
			$this->json_output($result);
		}
		$this->assign('page_title','栏位列表');
		$this->display('block.list.html');
	}

	/**
	 * 首页bannaer
	 */
	public function indexbanner(){
		$this->position = 1;
		$this->init();
	}

	/**
	 * 塑料头条塑市早茶banner
	 */
	public function sszcBanner(){
		$this->position = 8;
		$this->init();
	}

	/**
	 * 塑料头条中晨塑说banner
	 */
	public function zcssBanner(){
		$this->position = 9;
		$this->init();
	}

	/**
	 * 塑料头条数据解读banner
	 */
	public function sjjdBanner(){
		$this->position = 10;
		$this->init();
	}

	/**
	 * 塑料头条竞拍分析banner
	 */
	public function jpfxBanner(){
		$this->position = 11;
		$this->init();
	}


	/**
	 * 塑料头条首页中左banner
	 */
	public function indexLeftBanner(){
		$this->position = 12;
		$this->init();
	}

	/**
	 * 塑料头条首页中右banner
	 */
	public function indexRightBanner(){
		$this->position = 13;
		$this->init();
	}

	/**
	 * 塑料头条首页中晨事件banner
	 */
	public function middleBanner(){
		$this->position = 14;
		$this->init();
	}

	/**
	 * 塑料头条PE中左位置banner
	 */
	public function peLeftBanner(){
		$this->position = 16;
		$this->init();
	}

	/**
	 * 塑料头条PE中1位置banner
	 */
	public function peMiddle1Banner(){
		$this->position = 17;
		$this->init();
	}

	/**
	 * 塑料头条PE中2位置banner
	 */
	public function peMiddle2Banner(){
		$this->position = 18;
		$this->init();
	}

	/**
	 * 塑料头条PE中3位置banner
	 */
	public function peMiddle3Banner(){
		$this->position = 19;
		$this->init();
	}

	/**
	 * 塑料头条PE中4banner
	 */
	public function peMiddle4Banner(){
		$this->position = 20;
		$this->init();
	}

	/**
	 * 塑料头条PP中左位置banner
	 */
	public function ppLeftBanner(){
		$this->position = 21;
		$this->init();
	}

	/**
	 * 塑料头条PP中1位置banner
	 */
	public function ppMiddle1Banner(){
		$this->position = 22;
		$this->init();
	}

	/**
	 * 塑料头条PP中2位置banner
	 */
	public function ppMiddle2Banner(){
		$this->position = 23;
		$this->init();
	}

	/**
	 * 塑料头条PP中3位置banner
	 */
	public function ppMiddle3Banner(){
		$this->position = 24;
		$this->init();
	}

	/**
	 * 塑料头条PP中4banner
	 */
	public function ppMiddle4Banner(){
		$this->position = 25;
		$this->init();
	}

	/**
	 * 塑料头条PVC中左上位置banner
	 */
	public function pvcLeftTopBanner(){
		$this->position = 26;
		$this->init();
	}

	/**
	 * 塑料头条PVC中左下位置banner
	 */
	public function pvcLeftBottomBanner(){
		$this->position = 27;
		$this->init();
	}

	/**
	 * 塑料头条PVC中右位置banner
	 */
	public function pvcRightBanner(){
		$this->position = 28;
		$this->init();
	}

	/**
	 * 塑料头条PVC中1位置banner
	 */
	public function pvcMiddle1Banner(){
		$this->position = 29;
		$this->init();
	}

	/**
	 * 塑料头条PVC中2位置banner
	 */
	public function pvcMiddle2Banner(){
		$this->position = 30;
		$this->init();
	}

	/**
	 * 塑料头条PVC中3位置banner
	 */
	public function pvcMiddle3Banner(){
		$this->position = 31;
		$this->init();
	}

	/**
	 * 塑料头条PVC中4banner
	 */
	public function pvcMiddle4Banner(){
		$this->position = 32;
		$this->init();
	}

	/**
	 * 塑料头条pe详情页1banner
	 */
	public function detailpe1Banner(){
		$this->position = 33;
		$this->init();
	}

	/**
	 * 塑料头条pe详情页2banner
	 */
	public function detailpe2Banner(){
		$this->position = 34;
		$this->init();
	}

	/**
	 * 塑料头条pp详情页1banner
	 */
	public function detailpp1Banner(){
		$this->position = 35;
		$this->init();
	}

	/**
	 * 塑料头条pp详情页2banner
	 */
	public function detailpp2Banner(){
		$this->position = 36;
		$this->init();
	}

	/**
	 * 塑料头条pvc详情页1banner
	 */
	public function detailpvc1Banner(){
		$this->position = 37;
		$this->init();
	}

	/**
	 * 塑料头条pvc详情页2banner
	 */
	public function detailpvc2Banner(){
		$this->position = 38;
		$this->init();
	}

	/**
	 * 塑料头条vip详情页1banner
	 */
	public function detailvip1Banner(){
		$this->position = 39;
		$this->init();
	}

	/**
	 * 塑料头条vip详情页2banner
	 */
	public function detailvip2Banner(){
		$this->position = 40;
		$this->init();
	}
	
	/**
	 * 合作伙伴
	 */
	public function partners(){
		$this->position = 2;
		$this->init();
	}

	/**
	 * 友情链接
	 */
	public function friendlinks(){
		$this->position = 3;
		$this->init();
	}

	/**
	 * 移动bannaer
	 */
	public function mobilebanner(){
		$this->position = 4;
		$this->init();
	}

	/**
	 * 编辑栏目内容详情
	 * @access public 
	 * @return html
	 */
	public function info(){
		$this->assign('mini_list',0);
		
		$id=sget('id','i');
		$position=sget('position','i');
		if(empty($id) && empty($position)){
			$this->error('错误的数据请求');	
		}
		
		if($id>0){
			$info=$this->db->model('block')->wherePk($id)->getRow();
			if(empty($info)){
				$this->error('错误的数据请求');	
			}
			$info['start_time']=$info['start_time']>1000 ? date("Y-m-d H:i:s",$info['start_time']) : '';
			$info['end_time']=$info['end_time']>1000 ? date("Y-m-d H:i:s",$info['end_time']) : '';
			$info['input_time']=$info['input_time']>1000 ? date("Y-m-d H:i:s",$info['input_time']) : '-';
		}else{
			$info=array('id'=>0,'position'=>$position,'status'=>1);
		}
		$this->assign('info',$info); 
		
		//栏目要求
		$pos=$this->positions[$info['position']];
		$this->assign('pos',$pos); 
		
		$this->assign('page_title','栏目内容');
		$this->display('block.info.html');
	}
	
	/**
	 * 保存栏目数据
	 * @access public 
	 * @return html
	 */
	public function submit() {
		$this->is_ajax=true;
		$id=sget('id','i');
		$position=sget('position','i');
		if(empty($id) && empty($position)){
			$this->error('错误的数据请求');	
		}
		$name=sget('name','s');
		if(strlen($name)<2){
			$this->error('请输入栏目名');	
		}
		$data=array(
			'name'=>$name,
			'position'=>$position,
		);

		$content=array();
		
		//位置设置
		$pos=$this->positions[$position]['content'];
		if(isset($pos['info']) && isset($_REQUEST['iid'])){ //商品
			$_arr=array();
			$i=0;
			foreach($_REQUEST['iid'] as $k=>$v){
				if($i>=$pos['info']['num']) break;
				$i++;
				$_arr[$k]=array(
					  'id'=>(int)$v,		  
					  'title'=>trim($_REQUEST['iname'][$k]),		  
					  'img'=>trim($_REQUEST['iimg'][$k]),		  
					  'url'=>trim($_REQUEST['iurl'][$k]),		  
				  );	
			}
			$content['info']=$_arr;
		}
		if(isset($pos['self']) && isset($_REQUEST['sid'])){ //自定义
			$_arr=array();
			$i=0;
			foreach($_REQUEST['sid'] as $k=>$v){
				if($i>=$pos['self']['num']) break;
				$_title=trim($_REQUEST['sname'][$k]);
				if(empty($_title)) continue;
				$i++;
				$_arr[$k]=array(
					  'id'=>$i,		  
					  'title'=>$_title,		  
					  'img'=>trim($_REQUEST['simg'][$k]),		  
					  'url'=>trim($_REQUEST['surl'][$k]),		  
				  );	
			}
			$content['self']=$_arr;
		}
		if(!empty($content)){
			$data['content']=json_encode($content);
		}
		
		$data['status']=sget('status','i');
		$data['admin_name']=$_SESSION['name'];
		$data['input_time']=CORE_TIME;
		
		$start_time=sget('start_time','s');
		$end_time=sget('end_time','s');
		if(strlen($start_time)>10){
			$data['start_time']=strtotime($start_time);
		}
		if(strlen($end_time)>10){
			$data['end_time']=strtotime($end_time);
		}
		$_data=saddslashes($data);
		
		//更新或新增商品数据
		if($id>0){
			$this->db->model('block')->wherePk($id)->update($_data);	
		}else{
			$this->db->model('block')->add($_data);
		}
		$this->cache->delete('blockPos_'.$position);
		$this->clearMCache('blockPos_'.$position);
		$this->success('操作成功');
	}
	
	/**
	 * 删除栏目
	 * @access public 
	 */
	public function remove() {
		$this->is_ajax=true; //指定为Ajax输出
		$ids=sget('ids','s');
		if(empty($ids)){
			$this->error('操作有误');	
		}
		$result=$this->db->where("id in ($ids)")->delete();
		if($result){
			//清除对应的缓存
			$pos=explode(',',sget('pos','s'));
			$pos=array_unique($pos);
			foreach($pos as $k=>$v){
				$this->clearMCache('blockPos_'.$v);	
			}
			$this->success('操作成功');
		}else{
			$this->error('数据处理失败');
		}
	}
	
	/**
	 * 批量编辑列表数据
	 * @access public 
	 * @return html
	 */
	public function save(){
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //获取UI传递的参数
		$sql=array();
		if(empty($data)){
			$this->error('操作数据为空');
		}
		
		foreach($data as $k=>$v){
			$_data=array(
				'name'=>$v['name'],		 
				'status'=>(int)$v['status'],		 
				'sort_order'=>(int)$v['sort_order'],
				'input_time'=>CORE_TIME,
				'admin_name'=>$_SESSION['name'],
			);
			$this->clearMCache('blockPos_'.$v['position']);			
			$sql[]=$this->db->wherePk($v['id'])->updateSql($_data);
		}
		if(empty($sql)){
			$this->error('操作数据为空');
		}
		$result=$this->db->commitTrans($sql);
		if($result){
			$this->success('操作成功');
		}else{
			$this->error('数据处理失败');
		}
	}
	/**
	 * 头条栏位整合
	 */
	public function headlineBlock(){
		$this->display('headline.block.html');
	}
		
}
?>
