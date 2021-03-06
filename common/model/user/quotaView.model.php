<?php 
/**
* 指标报表：
*/
class quotaViewModel extends model
{
	
	function __construct()
	{
		parent::__construct(C('db_default'), 'report_user');
	}
	public function getDataByTime($time_type,$team_id)
	{
		switch ($time_type) {
			case 'this_month':
				$report_date=strtotime( date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y"))) );
				break;
			case 'last_month':
				$report_date=strtotime( date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y"))) );
				break;
		}
		$team_user_arr = $this->from('adm_role_user as adm')
				->where('adm.role_id='.$team_id)
				->select('adm.user_id')
				->getAll();
		$str = ',';
		foreach ($team_user_arr as $key => $value) {
			$string.=$str.$value['user_id'];
		}
		$in_string = trim($string,',');
		$where = 'report_date = '.$report_date.' and re.admin_id in ('.$in_string.' )';
		$data = $this->from('report_user as re')
					 ->leftjoin('admin as a','re.admin_id=a.admin_id')
					 ->where($where)
					 ->select('re.*,a.name')
					 ->getAll();
		return $data;
	}
	public function getExcelData($time_type)
	{
		switch ($time_type) {
			case 'this_month':
				$report_date=strtotime( date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y"))) );
				break;
			case 'last_month':
				$report_date=strtotime( date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y"))) );
				break;
		}
		$data = $this->from('report_user as re')
					 ->select('ro.name as team,adm.name,re.sale_num,re.saled_num,re.buy_num,re.buyd_num')
					 ->leftjoin('adm_role_user as u','re.admin_id=u.user_id')
					 ->leftjoin('admin as adm','adm.admin_id=re.admin_id')
					 ->leftjoin('adm_role as ro','ro.id=u.role_id')
					 ->where('re.report_date='.$report_date)
					 ->order('ro.name')
					 ->getAll();
					 // echo $this->getLastSql();die();
		return $data;
	}
}
?>