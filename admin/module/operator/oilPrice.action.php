<?php
/**
 * 原油指数
 */
class oilPriceAction extends adminBaseAction{

	public function __init(){
		$this->db=M('operator:oilPrice');
	}

	public function init(){
		$this->assign('oil_type',L('oil_type'));//原油类型

		$action=sget('action','s');
		if($action=='grid'){
			$this->_grid();exit;
		}
		$this->display('oil');
	}

	// 列表
	protected function _grid(){
		$page = sget("pageIndex",'i',0); //页码
		$size = sget("pageSize",'i',20); //每页数

		$list=$this->db->order('input_time desc')->page($page+1,$size)->getPage();
		foreach ($list['data'] as &$value) {
			$value['input_time']= $value['input_time']==0?'--':date('y-m-d H:i:s',$value['input_time']);
			$value['update_time']= $value['update_time']==0?'--':date('y-m-d H:i:s',$value['update_time']);
		}

		$result=array('total'=>$list['count'],'data'=>$list['data']);
		$this->json_output($result);
	}

	/**
	 * 删除调价动态
	 * @access public
	 */
	public function del(){
		$this->is_ajax=true;
		$id=sget('id','i',0);
		if(!$this->db->wherePk($id)->getRow()) $this->error('信息不存在');
		$this->db->wherePk($id)->delete();
		$this->success('操作成功');
	}

	/**
	 * 保存新增调价动态
	 * @access public
	 */
	public function ajaxSave(){
		$this->is_ajax=true; //指定为Ajax输出
		$data = sdata(); //传递的参数
		if(empty($data)){
			$this->error('错误的请求');
		}
		$id=$data['id'];
		unset($data['id']);
		if(!$id){
			$data['input_time']=CORE_TIME;
			$result = $this->db->add($data);
		}else{
			$data['update_time']=CORE_TIME;
			$result = $this->db->wherePk($id)->update($data);
		}
		M('operator:oilPrice')->delCache();
		if(!$result) $this->error('操作失败');
		$this->success('操作成功');
	}


	public function saveTags(){
		$this->is_ajax=true;
		$data=sdata();
		if(empty($data)) $this->error('信息不存在');
		foreach ($data as $key => $value) {
			unset($value['input_time']);
			$value['update_time']=CORE_TIME;
			$this->db->wherePk($value['id'])->update($value);
		}
		M('operator:oilPrice')->delCache();
		$this->success('操作成功');
	}
}