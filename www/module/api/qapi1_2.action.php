<?php

/**
 * 塑料圈的api中心
 * create by zp 2016/10/26
 * 备注：为了应成哥的要求，现在这套接口已不复通用性了
 * 现在所有的chanel全部写死变成6  同时quan_type 变为1 才能标示数据是从塑料圈app来的
 * 还有就是我感觉注册的时候，还是写一个交易员比较的好，要不然，等到分配交易员，不知道要到什么时候
 * app 不能重新注册，一个号码注册成功后，不能重新注册
 *
 * edit by zp 2016/12/8
 * 现在的这套接口，微信也在用，因为之前我没有想到微信也要用，
 * 但是不妨事，接口应该是能通用的
 *
 *
 *
 * 现在为了给算法创造数据
 * 在pub  saveSelfInfo   focusOrCancel
 *
 *把 getMyFuns
 * getMyIntroduction
 *
 * 这样的列表型页面，电话号码隐藏
 *
 *
 * 2017年2月21日15:04:03
 * 更新了头条的推荐接口
 *
 * 2017年2月22日 11:39:38
 * 修改了报价推荐的返回，现在不补最新的报价了
 *
 *
 * 2017年3月1日09:44:03
 *
 * ps:问题是 聪明反被聪明误
 *
 *
 * 以后写版本一定要有问题，还没想好怎么做
 *
 *
 *
 * 2017-4-4 17:59:55 *
 * 现在的注册和完善信息放在了一起了，
 * 我偷个小懒，我把原来两个的接口合并在了一起了
 *
 *
 */
class qapi1_2Action extends null2Action
{
    protected $db, $err, $cates, $catesAll, $pointsType, $orderStatus, $rePoints, $points, $newsSubscribe, $newsSubscribeDefault, $cache, $randomTime, $randomMdTime, $shareType;

    public function __init ()
    {
        $this->db    = M ('public:common');
        $this->cache = E ('RedisCluster', APP_LIB.'class');
        $this->cates = array(
            '21' => '期货资讯',
            '20' => '美金市场',
            '2'  => '塑料上游',
            '11' => '装置动态',
            '1'  => '早盘预测',
            '9'  => '企业动态',
            '4'  => '中晨塑说',
            '12' => '期刊报告',
            '22' => '独家解读',
        );
        if (!$this->catesAll = unserialize ($this->cache->get ('qappInitCatesAll'))) {
            $data           = M ("public:common")->model ("news_cate")->select ("cate_id,cate_name")->where ("status=1")
                                                 ->getAll ();
            $this->catesAll = arrayKeyValues ($data, 'cate_id', 'cate_name');
            $this->cache->set ('qappInitCatesAll', serialize ($this->catesAll), 3600);
        }

        $this->pointsType  = array(
            1  => '签到',
            2  => '登陆',
            3  => '发布报价',#供给
            4  => '订单取消塑豆返还',
            5  => '兑换礼品',
            6  => '发布采购',#求购
            7  => '注册完善信息送',
            8  => 'app注册',
            9  => '资源库发布',
            10 => '资源库搜索',
            11 => '塑料圈',
            12 => '塑料圈引荐',
            13 => '塑料圈分享',
            14 => '查看通讯录',
            15 => '查看文章',
            16 =>'现金充值',
            17 =>'发布标准报价',#（供给）
            18 =>'发布标准采购'#（求购）
        );
        $this->shareType   = array(
            1 => '求购分享',
            2 => '供给分享',
            3 => '文章分享',
            4 => '引荐分享',
        );
        $this->orderStatus = array(
            1 => '已兑换，待确认',
            2 => '已确认，待发货',
            3 => '已发货',
            4 => '订单取消',
            5 => '订单完成',
        );
        $this->points      = M ('system:setting')->get ('points')['points']; //这个是加了缓存的
        /**
         *       Array
         * (
         * [login] => 10
         * [pur] => 20
         * [sale] => 10
         * [ref] => 50
         * [share] => 30
         * )
         */
        $this->rePoints = $this->points['ref'];

        $this->newsSubscribe = 6;

        $this->newsSubscribeDefault = array(
            '21',
            '20',
            '2',
            '11',
        );

        $this->randomTime   = mt_rand (10, 20) * 180; // 1-2 h
        $this->randomMdTime = mt_rand (40, 60) * 120; // 4-6 h
    }

    /**
     * @api {get} /api/api1_2/init 初始化页面
     * @apiVersion 3.0.0
     * @apiName  init
     * @apiGroup User
     *
     * @apiSuccessExample Success-Response:
     *     <div style="margin-top:80px;text-align:center;font-size:50px">This is plasticZone app API center</div>
     *
     */
    public function init ()
    {
        echo '<div style="margin-top:80px;text-align:center;font-size:50px">This is plasticZone app API center</div>';
    }

    /**
     * @api {get} /api/api1_2/register 注册
     * @apiVersion 3.0.0
     * @apiName  register
     * @apiGroup User
     *
     * @apiParam {String} mobile       手机号.
     * @apiParam {String} password     密码.
     * @apiParam {String} code         验证码
     * @apiParam {Number} c_name       公司名称
     * @apiParam {Number} name         姓名
     * @apiParam {Number} region       区域
     * @apiParam {Number} chanel       区域
     * @apiParam {Number} region       区域
     * @apiParam {Number} region       区域
     * @apiSuccess {String}  msg   描述
     * @apiSuccess {Boolean} err   错误码
     *
     * @apiSuccessExample Success-Response:
     *     {
     *      "err":0
     *      "msg":"注册成功"
     *      }
     *
     *
     */
    public function register ()
    {
        if ($_POST['mobile']) {
            //$cache = cache::startMemcache();
            $cache         = E ('RedisCluster', APP_LIB.'class');
            $this->is_ajax = true;
            $mobile        = sget ('mobile', 's');
            if (!$this->_chkmobile ($mobile)) {
                $this->error ($this->err);
            }
            $password = sget ('password', 's');
            $password = $this->clearStr ($password);
            if (strlen ($password) < 6) {
                $this->error ('密码格式不正确,至少6位');
            }
            $mcode = sget ('code', 's');
            if (empty($mcode)) {
                $this->error ('请获取验证码');
            }
            $user_model   = M ('system:sysUser');
            $salt         = randstr (6);
            $passwordSalt = $user_model->genPassword ($password.$salt);
            $cache->set ($mobile.'check_reg_ok', true, 1200);
            $cache->set ($mobile.'password', $passwordSalt, 1200);
            $cache->set ($mobile.'salt', $salt, 1200);
            if (!$cache->get ($mobile.'check_reg_ok')) {
                $this->error ('令牌已过期，请重新注册');
            }
            $name = sget ('name', 's');
            if (mb_strlen ($name, 'UTF8') < 2) {
                $this->error ('请输入二位字以上的姓名');
            }
            //            if (!sget ('qq', 's', '')) {
            //                $this->error ('请输入qq号码');
            //            }
            //            if (!is_qq (sget ('qq', 's'))) {
            //                $this->error ('请输入有效的qq号码');
            //            }
            $c_name    = sget ('c_name', 's');
            $region    = sget ('region', 's', '');
            $chanel    = (int)sget ('chanel', 'i', 6);
            $quan_type = sget ('quan_type', 'i');//sget()函数要理解一下，里面有个empty函数
            //$origin = sget('origin','a');
            $ctype = sget ('c_type', 'i');  //'客户类型(1 工厂(买家)、2 贸易(卖家)、3 工贸一体(买卖一体))4物流',
            //$_model = sget('model','a');  //牌号
            //            if(empty($origin)||count($origin)!=2) $this->_errCode(6);
            if (empty($ctype)) {
                $this->_errCode (6);
            }
            if (!in_array ($ctype, array(
                '1',
                '2',
                '3',
                '4',
            ))
            ) {
                $this->_errCode (6);
            }
            $name   = $this->clearStr ($name);
            $c_name = $this->clearStr ($c_name);
            if (mb_strlen ($c_name, 'UTF8') < 5) {
                $this->error ('请输入完整的公司名');
            }
            $regison = $this->clearStr ($region);
            if (!$c_name) {
                $this->error ('请输入公司名称');
            }
            $result = M ('system:sysSMS')->qAppChkDynamicCode ($mobile, $mcode);
            if ($result['err'] > 0) {
                $this->error ($result['msg']);
            }

            $china_area = M ('system:region')->get_system_region_by_phone ($mobile);

            //if(empty($_model)||count($_model)>10) $this->_errCode(6);
            //$_model = implode(',',$_model);

            $cus_model  = $this->db->model ('customer');
            $customer   = $cus_model->select ('c_id')->where ("c_name='$c_name'")->getOne ();//获取公司的id
            $user_model = M ('system:sysUser');
            $user_model->startTrans ();
            try {
                $c_id = $customer;
                if (!$customer) {
                    $_customer = array(
                        'c_name'           => $c_name,
                        'chanel'           => $chanel,
                        'input_time'       => CORE_TIME,
                        'customer_manager' => 859,
                        //交易员
                        //'origin'=>implode('|',$origin),
                        //'need_product'=>$_model,
                        'type'             => $ctype,
                        'china_area'       => $china_area,
                        'quan_type'        => $quan_type,
                    );
                    if (!$cus_model->add ($_customer)) {
                        throw new Exception("系统错误 reg:101");
                    }
                    $c_id = $cus_model->getLastID ();
                }
                //查看是否为老用户
                $old_user      = $this->db->model ('customer_contact')->select ('user_id,parent_mobile')
                                          ->where ("mobile=".$mobile)->getRow ();
                $salt          = $cache->get ($mobile.'salt');
                $password      = $cache->get ($mobile.'password');
                $parent_mobile = sget ('parent_mobile', 's');
                if ($mobile == $parent_mobile) {
                    throw new Exception("引荐人不能是自己，先有蛋，还是先有鸡，这是个问题");
                }
                if (empty($parent_mobile) || $parent_mobile == 'undefined') {
                    $parent_mobile = '';
                } else {
                    if (!$this->db->from ('customer_contact')->select ('user_id')->where ('mobile='.$parent_mobile)
                                  ->getOne ()
                    ) {
                        throw new Exception("引荐人错误，请重新选择引荐人，或者独自踏上征途。");
                    }

                }
                /*
                 * 其实这一步可以省了，因为现在的已经注册过的，不能重新注册了，只能找回密码了
                 * 现在这一步，还是不能省，因为现在成哥又要能重新注册了，
                 * 主要原因是公司名不能删除
                 * 现在还有一个bug，
                 * 也不能算是bug，
                 * 一切都是以手机号来区分的
                 * 而且没有加密，会出现问题的
                 * 暂时没有时间，以后会修改吧
				 *2017-4-19 09:27:54
                 * 还是没有关闭重新注册
                 *
                 * 重新注册，会员状态清零
                 */
                if ($old_user['user_id']) {
                    $_user = array(
                        'salt'          => $salt,
                        'password'      => $password,
                        'name'          => $name,
                        'qq'            => sget ('qq', 's', ''),
                        'parent_mobile' => empty($old_user['parent_mobile']) ? '' : $old_user['parent_mobile'],
                        'c_id'          => $c_id,
                        'sex'           => sget ('sex', 'i', 0),
                        'chanel'        => $chanel,
                        'update_time'   => CORE_TIME,
                        'is_trial'      => 0,
                        //公海状态清零
                        'quan_type'     => $quan_type,
                    );
                    if (!$user_model->where ("mobile=".$mobile)->update ($_user)) {
                        throw new Exception("系统错误 reg:105");
                    }

                    $stype = 2;//老用户
                    //老用户也要检测contact_info 表的信息,防止后台乱添加用户少了信息
                    $mobile_area = getCityByMobile ($mobile);
                    $_info       = array(
                        'user_id'         => $old_user['user_id'],
                        'reg_ip'          => get_ip (),
                        'reg_time'        => CORE_TIME,
                        'thumbcard'       => '',
                        'reg_chanel'      => $chanel,
                        'region'          => empty($region) ? '' : $region,
                        'mobile_province' => empty($mobile_area['province']) ? '' : $mobile_area['province'],
                        'mobile_area'     => empty($mobile_area['city']) ? '' : $mobile_area['city'],
                        'quan_type'       => $quan_type,
                    );
                    if (!$this->db->model ('contact_info')->select ('user_id')->where ("user_id={$old_user['user_id']}")
                                  ->getOne ()
                    ) {
                        if (!$this->db->model ('contact_info')->add ($_info)) {
                            throw new Exception("系统错误 reg:103");
                        }
                        $stype = 1; //新用户
                    }
                } else {
                    $is_default = empty($customer) ? 1 : 0;
                    $_user      = array(
                        'mobile'           => $mobile,
                        'salt'             => $salt,
                        'password'         => $password,
                        'name'             => $name,
                        'qq'               => sget ('qq', 's'),
                        'c_id'             => $c_id,
                        'sex'              => sget ('sex', 'i', 0),
                        'customer_manager' => 859,
                        //交易员
                        'input_time'       => CORE_TIME,
                        'is_default'       => $is_default,
                        'parent_mobile'    => $parent_mobile,
                        'chanel'           => $chanel,
                        'quan_type'        => $quan_type,
                    );
                    if (!$user_model->add ($_user)) {
                        throw new Exception("系统错误 reg:102");
                    }
                    $user_id = $user_model->getLastID ();
                    //throw new Exception('test');
                    //直接关注和加积分
                    if (!empty($_user['parent_mobile'])) {
                        $focused_id = $this->db->model ('customer_contact')->where ("mobile=".$_user['parent_mobile'])
                                               ->select ('user_id')->getOne ();
                        if (!M ("plasticzone:plasticAttention")->getAttention ($user_id, $focused_id)) {
                            throw new Exception("系统错误 reg:111");
                        }
                        //                        if (!M ("qapp:pointsBill")->addPoints ($this->rePoints, $focused_id, 12)) {
                        //                            //var_dump($user_id);var_dump($focused_id);var_dump($_user['parent_mobile']);showTrace();
                        //                            throw new Exception("系统错误 reg:112");
                        //                        }//引荐加积分
                        //                        if (!M ("qapp:pointsBill")->addPoints ($this->points['register'], $user_id, 7)) {
                        //                            //var_dump($user_id);var_dump($focused_id);var_dump($_user['parent_mobile']);showTrace();
                        //                            throw new Exception("系统错误 reg:112");
                        //                        }//注册加积分
                    }
                    $mobile_area = getCityByMobile ($mobile);
                    $_info       = array(
                        'user_id'         => $user_id,
                        'reg_ip'          => get_ip (),
                        'reg_time'        => CORE_TIME,
                        'thumbcard'       => '',
                        'reg_chanel'      => $chanel,
                        'region'          => empty($region) ? '' : $region,
                        'mobile_province' => empty($mobile_area['province']) ? '' : $mobile_area['province'],
                        'mobile_area'     => empty($mobile_area['city']) ? '' : $mobile_area['city'],
                        'quan_type'       => $quan_type,
                    );
                    if (!$this->db->model ('contact_info')->add ($_info)) {
                        throw new Exception("系统错误 reg:103");
                    }
                    //这一步少不了，$c_id之前不知道
                    if (!$customer) {
                        if (!$this->db->model ('customer')->where ("c_id=$c_id")->update ("contact_id=".$user_id)) {
                            throw new Exception("系统错误 reg:104");
                        }
                    }
                    //新增用户默认排序最前
                    $this->db->model ('weixin_ranking')->add (array(
                        'user_id' => $user_id,
                        'pm'      => 0,
                        'rownum'  => 0,
                    ));
                }
            } catch (Exception $e) {
                $user_model->rollback ();
                $this->error ($e->getMessage ());
            }
            $user_model->commit ();
            $cache->delete ($mobile.'password');
            $cache->delete ($mobile.'salt');
            $cache->delete ($mobile.'check_reg_ok');
            //$this->success ('注册成功');
            $this->json_output (array(
                'err'   => 0,
                'msg'   => '注册成功',
                'stype' => $stype,
            ));

        }
        $this->_errCode (6);
    }


    /**
     * @api {post} /api/api1_2/finfMyPwd 找回密码
     * @apiVersion 3.0.0
     * @apiName  finfMyPwd
     * @apiGroup User
     *
     * @apiSuccess {String}  msg   描述
     * @apiSuccess {Boolean} err   错误码
     *
     * @apiSuccessExample Success-Response:
     *      {
     *      "err":0
     *      "msg":"密码重置成功"
     *      }
     *
     *
     */
    public function finfMyPwd ()
    {
        if ($_POST['mobile']) {
            $this->is_ajax = true;
            $mobile        = sget ('mobile', 's');
            if (!$this->_chkmobile ($mobile, 1)) {
                $this->error ($this->err);
            }
            $password = sget ('password', 's');
            if (strlen ($password) < 6) {
                $this->error ('密码格式不正确,至少6位');
            }
            $mcode  = sget ('code', 's');
            $result = M ('system:sysSMS')->qAppChkDynamicCode ($mobile, $mcode);
            if ($result['err'] > 0) {
                $this->error ($result['msg']);
            }
            $user_model   = M ('system:sysUser');
            $salt         = randstr (6);
            $passwordSalt = $user_model->genPassword ($password.$salt);
            //用户重置密码
            $result = $user_model->where ('mobile='.$mobile)->update (array(
                'password' => $passwordSalt,
                'salt'     => $salt,
            ));
            //$this->getDBError();
            if (!$result) {
                $this->error ('密码重置失败，请稍后重试');
            } else {
                $this->success ('密码重置成功');
            }
        }
        $this->_errCode (6);
    }


    //    /**
    //     * 验证密码
    //     */
    //    protected function _chkpass($pass, $repass)
    //    {
    //        if (strlen($pass) < 6) {
    //            $this->err = '密码格式不正确';
    //            return false;
    //        }
    //        if ($pass != $repass) {
    //            $this->err = '两次密码不一致';
    //            return false;
    //        }
    //        return true;
    //    }

    /**
     * 发送手机验证码
     * @access public
     *
     * @param type int 0 zhuce  1 zhaohuimima
     *
     * @return html
     */
    public function sendmsg ()
    {
        if ($_POST['mobile']) {
            $this->is_ajax = true;
            //验证手机
            $mobile = sget ('mobile', 's');
            $type   = sget ('type', 's', '0');//方式
            if (!$this->_chkmobile ($mobile, $type)) {
                $this->error ($this->err);
            }
            $sms = M ('system:sysSMS');
            //检查注册的限制
            $result = $sms->chkRegLimit ($mobile, get_ip ());
            if (empty($result)) {
                $this->error ($sms->getError ());
            }
            //请求动态码
            $result = $sms->qAppDynamicCode ($mobile);
            if ($result['err'] > 0) { //请求错误
                $this->error ($result['msg']);
            }
            $msg = $result['msg']; //短信内容
            if (empty($type)) {
                //发送手机动态码(注册)
                $sms->send (0, $mobile, $msg, 1);
            } else {
                //发送手机动态码（找回密码）
                $sms->send (0, $mobile, $msg, 2);
            }

            $this->success ('发送成功');
        }
        $this->_errCode (6);
    }

    //登录
    //现在默认的login的都是塑料圈web的方式，但是会出现问题的
    //区分不了东西了，其实没什么大不了，app每天的登录记录是出现在取首页数据的时候
    public function login ()
    {
        if ($_POST['username']) {
            $this->is_ajax = true;
            $username      = sget ('username', 's');
            $password      = sget ('password', 's');
            $chanel        = (int)sget ('chanel', 's', 6);
            $username      = $this->clearStr ($username);
            $password      = $this->clearStr ($password);
            if (!$this->_chkmobile ($username, 1)) {
                $this->error ($this->err);
            } elseif (strlen ($password) < 6) {
                $this->error ('密码长度应该大于6个字符');
            }
            $result = M ('user:passport')->login ($username, $password, $chanel);
            if ($result['err'] > 0) {
                $this->error ($result['msg']);
            } else {
                $token = M ('qapp:appToken')->insert ($result['user']['user_id'], $result['user']);
                //$cache=cache::startMemcache();
                //$this->success('登录成功');
                if (!M ("qapp:pointsBill")->select ('id')
                                          ->where ("addtime >".strtotime (date ("Y-m-d"))." and type=2 and uid={$result['user']['user_id']}")
                                          ->order ("id desc")->getOne ()
                ) {
                    $user_id = $result['user']['user_id'];
                    $tmp     = M ('public:common')->model ('contact_info')->where ("user_id=$user_id")->getRow ();
                    if (empty($tmp)) {
                        $this->json_output (array(
                            'err' => 101,
                            'msg' => '注册信息不完整，请联系客服400-6129-965或重新注册',
                        ));
                    }
                    $spoints = $this->points['login'];
                    //                    if (!$arr = M ("qapp:pointsBill")->addPoints ($spoints, $user_id, 2)) {
                    //                        $this->json_output (array( 'err' => 101, 'msg' => '系统错误' ));
                    //                    }
                }
                $this->json_output (array(
                    'err'       => 0,
                    'msg'       => '登录成功',
                    'dataToken' => $token,
                    'user_id'   => $result['user']['user_id'],
                ));
            }
        }
        $this->_errCode (6);
    }


    //退出登录
    public function logOut ()
    {
        if ($_POST['token']) {
            $this->checkAccount ();
            $this->is_ajax = true;
            $token         = sget ('token', 's');
            $cache         = cache::startMemcache ();
            $cache->delete ($token);
            M ('qapp:appToken')->destory ($token);
            $this->json_output (array(
                'err' => 0,
                'msg' => '退出成功',
            ));
        }
        $this->_errCode (6);
    }


    //获取首页数据
    public function getPlasticPerson ()
    {
        $this->is_ajax = true;
        //a b c d e f ...获取联系人(可以取消，现在这个功能不需要了)
        $letter = sget ('letter', 's');
        //搜素关键字
        $keywords = sget ('keywords', 's');
        $keywords = $this->clearStr ($keywords);
        $user_id  = $this->checkAccount (0);
        $page     = sget ('page', 'i', 1);
        //$size = sget('size', 'i', 10);
        $size      = 10;
        $sortField = sget ('sortField', 's', 'default');
        $sortOrder = sget ('sortOrder', 's', 'desc');
        $chanel    = (int)sget ('chanel', 'i', 6);
        $quan_type = sget ('quan_type', 'i');
        $version   = sget ('version', 's');//版本号
        $platform  = $this->checkPlatform ()['platform'];
        $region    = sget ('region', 'i',0);
        $c_type    = sget ('c_type', 'i',0);
        $region    = (int)$region;
        $c_type    = (int)$c_type;
        // 1 工厂 2 贸易商 3 工贸一体 4 服务商  5 其他  0全部
        if (!empty($c_type)&&$page!=1&&!in_array ($c_type, array(
            0,
            1,
            2,
            4,
            5
        ))
        ) {
            $this->json_output (array(
                'err' => 1,
                'msg' => 'c_type参数错误',
            ));
        }

        //0 全部 1 华东 2 华北 3 华南 4 其他
        if (!in_array ($region, array(
            0,
            1,
            2,
            3,
            4,
        ))
        ) {
            $this->json_output (array(
                'err' => 1,
                'msg' => 'region参数错误',
            ));
        }
        if ($sortField != 'default' && $sortField != 'input_time') {
            $this->json_output (array(
                'err' => 1,
                'msg' => 'sortField参数错误',
            ));
        }
        if (!in_array ($sortOrder, array(
            'desc',
            'asc',
        ))
        ) {
            $this->json_output (array(
                'err' => 1,
                'msg' => 'sortOrder参数错误',
            ));
        }


        /**
         * 加搜索记录
         * sort_field  'DEFAULT','INPUT_TIME','NC','SC','CC','ALL','AUTO','CONCERN','DEMANDORSUPPLY'
         * 首页默认排序default  注册时间排序input_time 华北nc  华南sc  华中cc
         * 全国站all  智能推荐auto  我的关注concern  我的供求 demandorsupply
         *
         *sort_order   'ALL','SALE','BUY','ASC','DESC'
         *all 不分求购还是供给  sale 供给  buy 求购  asc 注册时间正序 desc  注册时间倒序
         */

        if (!empty($keywords)) {
            $arr = array(
                'user_id'    => $user_id,
                'sort_field' => strtoupper ($sortField),
                'sort_order' => strtoupper ($sortOrder),
                'content'    => $keywords,
                'version'    => $version,
                'ip'         => get_ip (),
                'chanel'     => $platform,
                'input_time' => CORE_TIME,
            );
            M ('qapp:plasticSearch')->add ($arr);
        }
        //加缓存提交效率
        //备注，修改时，文档和代码需要修改
        $cache = cache::startMemcache ();
        $data  = array();

        if (empty($keywords)) {
            if ($page < 4) {//前三页
                if ($user_id > 0) {

                    if (!$data['data'] = $cache->get ('qgetPlasticPerson'.$sortField.$sortOrder.$page.':'.$size.':'.$region.':'.$c_type)) {
                        if(empty($c_type))
                        {

                            $data = M ('qapp:plasticPerson')->getAllPlasticPerson ($user_id, $keywords, $page, $size, $region);

                        }elseif($c_type==1)
                        {
                            $data = M ('qapp:plasticPerson')->get1PlasticPerson ($user_id, $keywords, $page, $size, $region);
                        }elseif($c_type==2)
                        {
                            $data = M ('qapp:plasticPerson')->get2PlasticPerson ($user_id, $keywords, $page, $size, $region);
                        }else {
                            $data = M ('qapp:plasticPerson')->getPlasticPerson ($user_id, $letter, $keywords, $page, $size, $sortField, $sortOrder, $region, $c_type);
                        }
                        $cache->set ('qgetPlasticPerson'.$sortField.$sortOrder.$page.':'.$size.':'.$region.':'.$c_type, $data['data'], 60);//1分钟缓存
                    }
                } else {
                    if (!$data['data'] = $cache->get ('qgetPlasticPerson0_'.$sortField.$sortOrder.$page.':'.$size.':'.$region.':'.$c_type)) {
                        if(empty($c_type))
                        {
                            $data = M ('qapp:plasticPerson')->getAllPlasticPerson ($user_id, $keywords, $page, $size, $region);
                        }elseif($c_type==1)
                        {
                            $data = M ('qapp:plasticPerson')->get1PlasticPerson ($user_id, $keywords, $page, $size, $region);
                        }elseif($c_type==2)
                        {
                            $data = M ('qapp:plasticPerson')->get2PlasticPerson ($user_id, $keywords, $page, $size, $region);
                        }else {
                            $data = M ('qapp:plasticPerson')->getPlasticPerson ($user_id, $letter, $keywords, $page, $size, $sortField, $sortOrder, $region, $c_type);
                        }
                        $cache->set ('qgetPlasticPerson0_'.$sortField.$sortOrder.$page.":".$size.":".$region.':'.$c_type, $data['data'], 60);//1分钟缓存
                    }
                }
            } else {
                if(empty($c_type))
                {
                    $data = M ('qapp:plasticPerson')->getAllPlasticPerson ($user_id, $keywords, $page, $size, $region);
                }elseif($c_type==1)
                {
                    $data = M ('qapp:plasticPerson')->get1PlasticPerson ($user_id, $keywords, $page, $size, $region);
                }elseif($c_type==2)
                {
                    $data = M ('qapp:plasticPerson')->get2PlasticPerson ($user_id, $keywords, $page, $size, $region);
                }else {
                    $data = M ('qapp:plasticPerson')->getPlasticPerson ($user_id, $letter, $keywords, $page, $size, $sortField, $sortOrder, $region, $c_type);
                }
            }
        } else {
            if(empty($c_type))
            {
                $data = M ('qapp:plasticPerson')->getAllPlasticPerson ($user_id, $keywords, $page, $size, $region);
            }elseif($c_type==1)
            {
                $data = M ('qapp:plasticPerson')->get1PlasticPerson ($user_id, $keywords, $page, $size, $region);
            }elseif($c_type==2)
            {
                $data = M ('qapp:plasticPerson')->get2PlasticPerson ($user_id, $keywords, $page, $size, $region);
            }else {
                $data = M ('qapp:plasticPerson')->getPlasticPerson ($user_id, $letter, $keywords, $page, $size, $sortField, $sortOrder, $region, $c_type);
            }
        }
        if (empty($data['data']) && $page == 1) {
            $this->json_output (array(
                'err' => 2,
                'msg' => '没有相关数据',
            ));
        } elseif (empty($data['data']) && $page > 1) {
            $this->json_output (array(
                'err' => 3,
                'msg' => '没有更多记录了',
            ));
        }

        $this->_checkLastPage ($data['count'], $size, $page);
        if ($page >= 3 && $user_id <= 0) {
            $this->json_output (array(
                'err'   => 1,
                'msg'   => '想要查看更多，请登录',
                'count' => $data['count'],
            ));
        }

        if ($user_id > 0) {
            $dayTime = strtotime (date ("Y-m-d"));
            if ($page == 1 && !$this->db->from ('log_login')->select ('input_time')
                                        ->where ("input_time >".$dayTime." and chanel =6 and quan_type = $quan_type and user_id=$user_id")
                                        ->getOne ()
            ) {

                $arr = array(
                    'user_id'    => $user_id,
                    'input_time' => CORE_TIME,
                    'ip'         => get_ip (),
                    'chanel'     => $chanel,
                    'quan_type'  => $quan_type,
                );
                $this->db->model ("log_login")->add ($arr);
            };
        }

        $goods_id = $this->db->model ("points_goods")->select ('id')->where (" type =2 and status =1")->getOne ();

        $pointsOrder = M ("points:pointsOrder");
        $contact_id  = $pointsOrder->get_supply_demand_top ($goods_id);
        //showTrace();
        if ($contact_id) {
            $top = M ('qapp:plasticPersonalInfo')->getMyOwnInfo ($contact_id['pur_id']);
            foreach ($data['data'] as $key => &$val) {
                if ($val['user_id'] == $top['user_id']) {
                    unset($data['data'][$key]);
                    $data['data'] = array_values ($data['data']);
                }
            }
            unset($val);
        } else {
            $top = (object)array();
        }
        if ($page == 1) {


                // 第一页检测客户角色
                $ret_ctype = 0;

                if ($user_id > 0) {
                    $cus_ctype = M ("qapp:plasticPersonalInfo")->getCusType ($user_id);
                    if ($cus_ctype == 1) {
                        $ret_ctype = 2;
                    } elseif ($cus_ctype == 2) {
                        $ret_ctype = 1;
                    } elseif ($cus_ctype == 4) {
                        $ret_ctype = 2;
                    }
                }

            $members = M ('qapp:plasticPersonalInfo')->getAllMembers ();
            $members = empty($members) ? 0 : $members;
            $arr = array(
                'err'             => 0,
                'persons'         => $data['data'],
                'member'          => $members,
                'is_show_banner'  => 0,
                'is_show_focus'   => 1,
                'is_show_cover'   => 0,
                'banner_url'      => '',
                'banner_jump_url' => '',
                'cover_url'       => '',
                'cover_jump_url'  => '',
                'data'            => CORE_TIME,
                'show_ctype'       => $c_type
            );
            if (!empty($top)) {
                $arr['top'] = $top;
            }
            //是否显示banner
            //M ("system:setting")->del_cache ("setting");
            $setting = M ("system:setting")->getSetting ();
            //var_dump($setting['qapp_banner']);

            if (!empty($setting['qapp_banner']) && !empty($setting['qapp_banner']['start_time']) && !empty($setting['qapp_banner']['end_time']) && !empty($setting['qapp_banner']['url']) && CORE_TIME > $setting['qapp_banner']['start_time'] && CORE_TIME < $setting['qapp_banner']['end_time']) {
                $arr['is_show_banner']  = 1;
                $arr['is_show_focus']   = 0;
                $arr['banner_url']      = $setting['qapp_banner']['url'];
                $arr['banner_jump_url'] = $setting['qapp_banner']['jump_url'];

            }
            //是否显示cover photo 未来待定
            if (!empty($setting['qapp_cover']) && !empty($setting['qapp_cover']['start_time']) && !empty($setting['qapp_cover']['end_time']) && !empty($setting['qapp_cover']['url']) && CORE_TIME > $setting['qapp_cover']['start_time'] && CORE_TIME < $setting['qapp_cover']['end_time']) {
                $arr['is_show_cover']  = 1;
                $arr['cover_url']      = $setting['qapp_cover']['url'];
                $arr['cover_jump_url'] = $setting['qapp_cover']['jump_url'];
            }
            $this->json_output ($arr);
        }
        $arr = array(
            'err'     => 0,
            'persons' => $data['data'],
            'data'    => CORE_TIME,
        );
        if (!empty($top)) {
            $arr['top'] = $top;
        }
        $this->json_output ($arr);
    }

    //我的塑料圈
    public function myZone ()
    {
        $this->is_ajax = true;
        if ($_POST['token']) {
            $user_id = $this->checkAccount ();
            //我的求购
            $s_in_count = M ('qapp:plasticPersonalInfo')->getConut ($user_id, 1, 6);//purchase表
            $s_in_count = empty($s_in_count) ? 0 : $s_in_count;
            //我的报价
            $s_out_count = M ('qapp:plasticPersonalInfo')->getConut ($user_id, 2, 6);//purchase表
            $s_out_count = empty($s_out_count) ? 0 : $s_out_count;
            //我的积分
            $points = M ('qapp:pointsBill')->getUerPoints ($user_id);
            $points = empty($points) ? 0 : $points;
            //我的留言
            $leaveword = M ('qapp:plasticMsgCount')->getMsgCount ($user_id, 1);
            if (!$user_id > 0 || !$leaveword > 0) {
                $leaveword = 0;
            }
            //我的未读消息
            $message = M ('qapp:plasticMsgCount')->getRobotCount ($user_id);
            if (!$user_id > 0 || !$message > 0) {
                $message = 0;
            }
            //我的引荐
            $data         = M ('qapp:plasticIntroduction')->getqAppMyIntroduction ($user_id);
            $introduction = empty($data['count']) ? 0 : $data['count'];
            //我的粉丝
            $data   = M ('qapp:plasticMsgCount')->getMyFunsCount ($user_id, 1);
            $myfans = empty($data) ? 0 : $data;
            //我的关注
            $data       = M ('qapp:plasticMsgCount')->getMyFunsCount ($user_id, 2);
            $myconcerns = empty($data) ? 0 : $data;
            //获取我的塑料圈个人信息
            $headimgurl = '';
            $data       = M ('qapp:plasticPersonalInfo')->getMyPlastic ($user_id, $headimgurl);
            //            var_dump($user_id);
            //            var_dump($data);exit;
            if (empty($data)) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关资料',
                ));
            }
            $data = empty($data) ? array(
                'err' => 2,
                'msg' => '没有相关资料',
            ) : $data;
            $this->json_output (array(
                's_in_count'   => $s_in_count,
                //我的求购
                's_out_count'  => $s_out_count,
                //我的供给
                'points'       => $points,
                //我的积分
                'leaveword'    => $leaveword,
                //我的留言
                'message'      => $message,
                //我的未读消息
                'introduction' => $introduction,
                //我的引荐
                'myfans'       => $myfans,
                //我的粉丝
                'myconcerns'   => $myconcerns,
                //我的关注
                'data'         => $data,
                //我的个人信息
            ));
        }
        $this->_errCode (6);
    }


    //获取我的引荐(引荐数)
    public function getMyIntroduction ()
    {
        $this->is_ajax = true;
        if ($_POST['token']) {
            $user_id = $this->checkAccount ();
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 10);
            $data    = M ('qapp:plasticIntroduction')->getqAppMyIntroduction ($user_id, $page, $size);
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err'   => 2,
                    'msg'   => '没有相关的数据',
                    'count' => 0,
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            //unset($data['data'][2]);
            //            echo '<pre>';
            //            var_dump($data);exit;
            $this->json_output (array(
                'err'   => 0,
                'data'  => $data['data'],
                'count' => $data['count'],
            ));
        }
        $this->_errCode (6);
    }


    //(中间供求信息)获取供求发布和消息回复
    public function getReleaseMsg ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            //筛选条件
            $keywords   = sget ('keywords', 's');
            $keywords   = $this->clearStr ($keywords);
            $type       = sget ('type', 'i', 0);//0 全部 1 求购 2 供给
            $sortField1 = strtoupper (sget ('sortField1', 's'));
            $sortField2 = strtoupper (sget ('sortField2', 's'));
            $version    = sget ('version', 's');//版本号
            $platform   = $this->checkPlatform ()['platform'];
            //$sortField =array('AUTO','NC');
            $fieldNum = '';
            $fieldNum .= $sortField1;
            if (empty($sortField1)) {
                if (empty($sortField2)) {
                    $fieldNum = '';
                } else {
                    $fieldNum = $sortField2;
                }
            } else {
                if (!empty($sortField2)) {
                    $fieldNum .= ','.$sortField2;
                }
            }


            if ($type == 0) {
                $sortOrder = 'All';
            } elseif ($type == 1) {
                $sortOrder = 'BUY';
            } elseif ($type == 2) {
                $sortOrder = 'SALE';
            }
            /**
             * 加上搜索记录
             */
            $arr = array(
                'user_id'    => $user_id,
                'sort_field' => $fieldNum,
                'sort_order' => $sortOrder,
                'content'    => $keywords,
                'version'    => $version,
                'ip'         => get_ip (),
                'chanel'     => $platform,
                'input_time' => CORE_TIME,
            );
            M ('qapp:plasticSearch')->add ($arr);


            //普通条件
            $page = sget ('page', 'i', 1);
            $size = sget ('size', 'i', 10);
            // 检测是否有标准格式供求

            if ($page == 1 && $sortField2 == 'AUTO' && empty($keywords)) {
                $has_purchase = M ('qapp:plasticRelease')->checkPurchase ($user_id,2);
                if(empty($has_purchase))
                {
                    $this->json_output (array(
                        'err' => 7,
                        'msg' => '您最近5天内未发布供求信息,暂无推荐！赶紧点击按钮去发布哦～',
                    ));
                }
                $has_standard = M ('qapp:plasticRelease')->checkPurchase ($user_id,1);
                if (empty($has_standard)) {
                    $this->json_output (array(
                        'err' => 7,
                        'msg' => '您最近5天内未发布标准格式供求信息,暂无推荐！赶紧点击按钮去发布哦～',
                    ));
                }
            }
            //检测是否有塑料圈关注的人
            if ($page == 1 && $sortField2 == 'CONCERN') {
                $has_concern = M ('qapp:plasticIntroduction')->getMyFuns ($user_id, 2, 1, $size);
                if (empty($has_concern['data'])) {
                    $this->json_output (array(
                        'err' => 9,
                        'msg' => '您尚未在系统中关注任何用户，暂无推荐！赶紧点击“通讯录-查看个人信息”页面-【关注】按钮去关注吧！',
                    ));
                }
            }
            //获取供求详细数据
            $data = M ('qapp:plasticRelease')->getReleaseMsg ($keywords, $page, $size, $type, $sortField1, $sortField2, $user_id);

            if ($data == 'tempErr') {
                $this->_errCode (5);
            }
            if (empty($data['data']) && $page == 1 && $sortField2 == 'AUTO' && empty($keywords)) {
                $this->json_output (array(
                    'err' => 4,
                    'msg' => '系统暂未为您匹配到相应的牌号，暂无推荐！',
                ));
            }
            if (empty($data['data']) && $page == 1 && $sortField2 == 'CONCERN') {
                $this->json_output (array(
                    'err' => 6,
                    'msg' => '您关注的塑料圈用户暂无供求信息！',
                ));
            }
            if (empty($data['data']) && $page == 1 && $sortField2 == 'DEMANDORSUPPLY') {
                $this->json_output (array(
                    'err' => 8,
                    'msg' => '您未发布任何供求信息！',
                ));
            }
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关数据',
                ));
            }
            if (empty($data['data'])) {
                $this->json_output (array(
                    'err' => 3,
                    'msg' => '没有更多数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $goods_id    = $this->db->model ("points_goods")->select ('id')->where (" type =1 and status =1")
                                    ->getOne ();
            $pointsOrder = M ("points:pointsOrder");

            $info0 = $pointsOrder->get_supply_demand_top ($goods_id);

            //只有在有置顶头条并且页面是首页或者智能推荐时候有效
            if ($info0 && ($sortField1 == 'ALL' || $sortField2 == 'AUTO')) {

                $top      = M ('qapp:plasticRelease')->getReleaseMsgDetail ($info0['pur_id'],$info0['uid']);
                $personal = M ('qapp:plasticPersonalInfo')->getMyOwnInfo ($info0['uid']);
                unset($personal['type']);
                $_tmp     = $top['info'];
                //showTrace();
                unset($top['info']);
                $top = array_merge ($top, $_tmp, $personal);

                foreach ($data['data'] as $key => &$val) {
                    if ($val['id'] == $top['id']) {
                        unset($data['data'][$key]);
                        $data['data'] = array_values ($data['data']);
                    }
                }
                unset($val);

                $arr = array(
                    'err'  => 0,
                    'data' => $data['data'],
                    'top'  => $top,
                );
            } else {
                $top = (object)array();

                $arr = array(
                    'err'  => 0,
                    'data' => $data['data'],
                    'top'  => $top,
                );

            }

            $this->json_output ($arr);
        }
        $this->_errCode (6);
    }


    /**
     * (中间供求信息)获取供求发布(详情)
     */
    public function getReleaseMsgDetail ()
    {
        if ($_POST['token']) {
            $user_id = $this->checkAccount ();
            $id      = sget ('id', 'i');
            $own_id  = sget ('user_id', 'i');
            if ($id < 1 || $user_id < 1) {
                $this->_errCode (6);
            }
            $data = M ('qapp:plasticRelease')->getReleaseMsgDetail ($id, $own_id, $user_id);
            $this->_errCode (0, $data);
        }
        $this->_errCode (6);
    }

    /**
     * (中间供求信息)获取供求发布(详情)的消息回复
     */
    public function getReleaseMsgDetailReply ()
    {
        if ($_POST['token']) {
            $this->checkAccount ();
            $id      = sget ('id', 'i');
            $user_id = sget ('user_id', 'i');
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 5);
            if ($id < 1 || $user_id < 1) {
                $this->_errCode (6);
            }
            $data = M ('qapp:plasticRelease')->getReleaseMsgDetailReply ($id, $user_id, $page, $size);
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关的数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $this->_errCode (0, $data);
        }
        $this->_errCode (6);
    }


    /**
     * 供求消息中的出价
     */
    public function deliverPrice ()
    {
        if ($_POST['token']) {
            $user_id = $this->checkAccount ();
            $id      = sget ('id', 'i');//对应purchase表的id
            $rev_id  = sget ('rev_id', 'i');//接收报价的人
            $type    = sget ('type', 'i');// 1 求购 2 供给
            $price   = sget ('price', 'f');
            if ($price <= 0 || $price > 50000) {
                $this->json_output (array(
                    'err' => 6,
                    'msg' => '价格输入不正常，请重新输入',
                ));
            }
            $price = sprintf ("%.2f", $price);
            $arr   = array(
                'pur_id'     => $id,
                'send_id'    => $user_id,
                'user_id'    => $rev_id,
                'type'       => $type,
                'price'      => $price,
                'input_time' => CORE_TIME,
            );
            if (M ('qapp:plasticQuote')->setPurchasePrice ($arr)) {
                $this->json_output (array(
                    'err' => 0,
                    'msg' => '发布成功',
                ));
            }
            $this->json_output (array(
                'err' => 5,
                'msg' => '发布失败',
            ));
        }
        $this->_errCode (6);
    }

    /**
     * 获取供求消息的出价
     */
    public function getDeliverPrice ()
    {
        if ($_POST) {
            $this->checkAccount ();
            $id     = sget ('id', 'i');//对应purchase表的id
            $rev_id = sget ('rev_id', 'i');//接收出价的人(即发布purchase报价消息的人)
            $page   = sget ('page', 'i', 1);
            $size   = sget ('size', 'i', 5);
            $data   = M ('qapp:plasticQuote')->getPurchasePrice ($id, $rev_id, $page, $size);
            $this->_errCode (0, $data);
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关的数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
        }
        $this->_errCode (6);
    }





    //判断提交的发布报价(采购1、报价2)数据/user/mypurchase/pub(现已修改到下面的方法)
    /**
     *      data[0][model]:2119
     * data[0][f_name]:陶氏
     * data[0][store_house]:上海
     * data[0][price]:8888.00
     * data[0][type]:1
     * data[0][pt]:1
     * data[0][content]:
     */
    public function pub ()
    {
        $this->is_ajax = true;
        if ($data = $_POST['data']) {
            $user_id = $this->checkAccount ();
            $data    = saddslashes ($data);
            $content = $data[0]['content'];//客户直接填写的求购内容，无格式
            //$cargo_type = $data[0]['cargo_type'];//现货、期货
            $type      = $data[0]['type'];//采购1、报价2
            $quan_type = sget ('quan_type', 'i');
            $pur_model = M ('product:purchase');
            $fac_model = M ('product:factory');
            $pro_model = M ('product:product');
            $soms      = M ('plasticzone:plasticPerson')->select ('c_id,customer_manager')->where ('user_id='.$user_id)
                                                        ->getRow ();
            $content   = str_replace (PHP_EOL, '', $content);
            //判断是否只有content
            if (empty($data[0]['model']) && empty($data[0]['f_name']) && empty($data[0]['store_house'])) {
                $_content = array(
                    'user_id'    => $user_id,
                    //用户id
                    'c_id'       => $soms['c_id'],
                    //客户id
                    'status'     => $type == 1 ? 1 : 2,
                    //状态，报价不需要审核，采购需要审核
                    'sync'       => 6,
                    //报价来源平台
                    'type'       => $type,
                    //采购、报价
                    'quan_type'  => $quan_type,
                    'content'    => $content,
                    //客户直接填写的求购内容
                    'input_time' => CORE_TIME,
                    //创建时间
                );
                $pur_model->startTrans ();
                try {
                    if (!$pur_model->add ($_content)) {
                        throw new Exception("系统错误 pubpur:3");
                    }
                    $pur_id = $pur_model->getLastID ();
                    //                    if ($type == 2) {//报价
                    //                        $spoints = intval (M ('system:setting')->get ('points')['points']['sale']);
                    //                        if (!M ("qapp:pointsBill")
                    //                            ->select ('id')
                    //                            ->where ("addtime >".strtotime (date ("Y-m-d"))."  and type=3 and uid=".$user_id)
                    //                            ->order ("id desc")
                    //                            ->getOne ()
                    //                        ) {
                    //                            if (!$arr = M ("qapp:pointsBill")->addPoints ($spoints, $user_id, 3)) {
                    //                                $this->json_output (array( 'err' => 5, 'msg' => "系统错误 pubpur:103" ));
                    //                            }
                    //                        }
                    //                    } elseif ($type == 1) {//采购
                    //                        $spoints = intval (M ('system:setting')->get ('points')['points']['pur']);
                    //                        if (!M ("qapp:pointsBill")
                    //                            ->select ('id')
                    //                            ->where ("addtime >".strtotime (date ("Y-m-d"))." and type=6 and uid=".$user_id)
                    //                            ->order ("id desc")
                    //                            ->getOne ()
                    //                        ) {
                    //                            if (!$arr = M ("qapp:pointsBill")->addPoints ($spoints, $user_id, 6)) {
                    //                                $this->json_output (array( 'err' => 5, 'msg' => "系统错误 pubpur:103" ));
                    //                            }
                    //                        }
                    //                    }
                } catch (Exception $e) {
                    $pur_model->rollback ();
                    $this->json_output (array(
                        'err' => 3,
                        'msg' => '插入数据失败',
                    ));
                }
                $pur_model->commit ();

                //robot表插入消息
                $tmpFuns    = M ("qapp:plasticIntroduction")->getMyFunsId ($user_id, 1);
                $tmpContent = "您关注的";
                $tmpContent .= M ("public:common")->model ('customer_contact')->select ('name')
                                                  ->where ("user_id=".$user_id)->getOne ();
                $tmpContent .= "发布了1条";
                if ($type == 1) {
                    $tmpContent .= "求购";
                } elseif ($type == 2) {
                    $tmpContent .= "供给";
                }
                $tmpContent .= "信息，信息内容为:".$content;
                if (!empty($tmpFuns)) {
                    foreach ($tmpFuns as $v) {
                        M ("qapp:robotMsg")->saveRobotMsg ($pur_id, $user_id, $v['user_id'], $tmpContent, $type = 1);
                        usleep (10);
                    }
                }
                $this->success ('提交成功');
            }
            //
            foreach ($data as $key => $value) {
                //是否已有该产品
                $model = $this->db->from ('product p')->join ('factory f', 'p.f_id=f.fid');
                //                 $where="p.model='{$value['model']}' and p.product_type={$value['product_type']} and f.f_name='{$value['f_name']}'";
                $where          = "p.model='{$value['model']}'  and f.f_name='{$value['f_name']}'";
                $pid            = $model->where ($where)->select ('p.id')->getOne ();
                $value['price'] = sprintf ("%.2f", $value['price']);
                $value['model'] = $this->clearStr ($value['model']);
                $value['model'] = trim ($value['model']);
                $value['model'] = strtoupper ($value['model']);
                if (empty($value['model'])) {
                    $value['model'] = '';
                }
                $_data = array(
                    'user_id'          => $user_id,
                    //用户id
                    'c_id'             => $soms['c_id'],
                    //客户id
                    'model'            => $value['model'],
                    //牌号
                    'customer_manager' => $soms['customer_manager'],
                    //交易员
                    //'number' => $value['number'],//吨数
                    'unit_price'       => $value['price'],
                    //单价
                    'provinces'        => $value['provinces'],
                    //省份id
                    'store_house'      => $value['store_house'],
                    //仓库
                    //'period' => $value['period'],//期货周期
                    //'bargain' => $value['bargain'],//是否实价
                    'type'             => $type,
                    //采购、报价
                    'sync'             => 6,
                    //报价来源平台
                    'quan_type'        => $quan_type,
                    'status'           => $type == 1 ? 1 : 2,
                    //状态，报价不需要审核，采购需要审核
                    'input_time'       => CORE_TIME,
                    //创建时间
                    // 'remark' => $remark,//备注
                    'content'          => $content,
                    //客户直接填写的求购内容
                );

                if ($pid) {
                    //已有产品直接添加采购信息
                    $_data['p_id'] = $pid;//产品id
                    $pur_model->startTrans ();
                    if ($pur_model->add ($_data)) {
                        $pur_id = $pur_model->getLastID ();
                        $pur_model->commit ();
                    } else {
                        $pur_model->rollback ();
                    }
                } else {
                    //没有产品则新增一个产品
                    $pur_model->startTrans ();
                    try {
                        // 是否已有厂家
                        $f_id = $fac_model->where ("f_name='{$value['f_name']}'")->select ('fid')->getOne ();
                        if (!$f_id) {
                            //创建新厂家
                            $_factory = array(
                                'f_name'     => $value['f_name'],
                                //厂家名称
                                'input_time' => CORE_TIME,
                                //创建时间
                            );
                            if (!$fac_model->add ($_factory)) {
                                throw new Exception("系统错误 pubpur:101");
                            }
                            $f_id = $fac_model->getLastID ();
                        }
                        $_product = array(
                            'model'        => $value['model'],
                            //牌号
                            'product_type' => $value['product_type'],
                            //产品类型
                            'process_type' => $value['process_level'],
                            //加工级别
                            'input_time'   => CORE_TIME,
                            //创建时间
                            'f_id'         => $f_id,
                            //厂家id
                            'status'       => 3,
                            //审核状态
                        );
                        if (!$pro_model->add ($_product)) {
                            throw new Exception("系统错误 pubpur:102");
                        }
                        $pid           = $pro_model->getLastID ();
                        $_data['p_id'] = $pid;
                        if (!$pur_model->add ($_data)) {
                            throw new Exception("系统错误 pubpur:103");
                        }
                        $pur_id = $pur_model->getLastID ();
                    } catch (Exception $e) {
                        $pur_model->rollback ();
                        $this->error ($e->getMessage ());
                    }
                    $pur_model->commit ();
                }
                //                if ($type == 2) {//报价
                //                    $spoints = intval (M ('system:setting')->get ('points')['points']['sale']);
                //                    if (!M ("qapp:pointsBill")
                //                        ->select ('id')
                //                        ->where ("addtime >".strtotime (date ("Y-m-d"))."  and type=3 and uid=".$user_id)
                //                        ->order ("id desc")
                //                        ->getOne ()
                //                    ) {
                //                        if (!$arr = M ("qapp:pointsBill")->addPoints ($spoints, $user_id, 3)) {
                //                            $this->json_output (array( 'err' => 5, 'msg' => "系统错误 pubpur:103" ));
                //                        }
                //                    }
                //                } elseif ($type == 1) {//采购
                //                    $spoints = intval (M ('system:setting')->get ('points')['points']['pur']);
                //                    if (!M ("qapp:pointsBill")
                //                        ->select ('id')
                //                        ->where ("addtime >".strtotime (date ("Y-m-d"))." and type=6 and uid=".$user_id)
                //                        ->order ("id desc")
                //                        ->getOne ()
                //                    ) {
                //                        if (!$arr = M ("qapp:pointsBill")->addPoints ($spoints, $user_id, 6)) {
                //                            $this->json_output (array( 'err' => 5, 'msg' => "系统错误 pubpur:103" ));
                //                        }
                //                    }
                //                }

                //robot表插入消息
                $tmpFuns    = M ("qapp:plasticIntroduction")->getMyFunsId ($user_id, 1);
                $tmpContent = "您关注的";
                $tmpContent .= M ("public:common")->model ('customer_contact')->select ('name')
                                                  ->where ("user_id=".$user_id)->getOne ();
                $tmpContent .= "发布了1条";
                if ($type == 1) {
                    $tmpContent .= "求购";
                } elseif ($type == 2) {
                    $tmpContent .= "供给";
                }
                $tmpContent .= "信息，信息内容为:";
                $tmpContent .= '价格'.$value['price'].'元左右/'.$value['model'].'/'.$value['f_name'].'/'.$value['store_house'];
                if (!empty($tmpFuns)) {
                    foreach ($tmpFuns as $v) {
                        M ("qapp:robotMsg")->saveRobotMsg ($pur_id, $user_id, $v['user_id'], $tmpContent, $type = 1);
                        usleep (10);
                    }
                }
            }
            /**
             *  添加到redis里面
             */
            // M("suggestion:suggestion")->suggestion_purchase($user_id,$pur_id);
            /**
             * 把供求需要的牌号添加到suggestion_model中
             */
            if (!empty($data[0]['model'])) {
                $suggestion_model_arr = array(
                    'user_id'     => $user_id,
                    'name'        => $data[0]['model'],
                    'type'        => 'ME',
                    'create_time' => date ("Y-m-d H:i:s"),
                    'pur_type'    => $data[0]['type'],
                );
                M ("public:common")->model ('suggestion_model')->add ($suggestion_model_arr);
            }


            $this->success ('提交成功');
        }
        $this->_errCode (6);
    }


    /**
     * @param $serverId jssdk文件上传返回的serverId
     *
     * @return string
     */
    public function savePicToServer ()
    {
        $this->is_ajax = true;
        $user_id       = $this->checkAccount ();
        //$this->json_output(array('err'=>1,'msg'=>'test'));
        A ('public:upload')->saveqAppCardImg ('', 2, $user_id);
    }

    //保存名片到服务器
    public function saveCardImg ()
    {
        $this->is_ajax = true; //指定为Ajax输出
        //$this->json_output(array('err'=>1,'msg'=>'test'));
        $user_id = $this->checkAccount ();
        $result  = A ('public:upload')->saveqAppCardImg ('', 1, $user_id);
    }

    public function deleteRepeat ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $this->checkAccount ();
            $id     = sget ('id', 'i', 0);
            $result = M ('plasticzone:plasticMyMsg')->deleteRepeat ($id);
            $this->json_output ($result);
        }
        $this->_errCode (6);
    }

    //回复供求消息
    public function saveMsg ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            // $user_id = sget('user_id','i',0)//回复人id
            $pur_id  = sget ('pur_id', 'i', 0);//purchase表的消息id
            $send_id = sget ('send_id', 'i', 0);//purchase表发报价或采购人的(pur.user_id)
            $content = sget ('content', 's');//回复的内容
            if (empty($content)) {
                $this->json_output (array(
                    'err' => 6,
                    'msg' => '回复内容不能为空',
                ));
            }

            //robot表插入消息
            $where  = "pur.id=$pur_id";
            $detail = M ('product:purchase')->getPurchaseLeftById ($where, null, null);//p($detail);showTrace();exit;
            $value  = $detail;
            if (empty($value['content'])) {
                if ($value['unit_price'] == 0.00 && empty($value['model']) && empty($value['f_name']) && empty($value['store_house'])) {
                    $value['contents'] = '';
                } else {
                    $value['contents'] = '价格'.$value['unit_price'].'元左右/'.$value['model'].'/'.$value['f_name'].'/'.$value['store_house'];
                }
            } elseif (!empty($value['content'])) {
                if ($value['unit_price'] == 0.00 && empty($value['model']) && empty($value['f_name']) && empty($value['store_house'])) {
                    $value['contents'] = $value['content'];
                } else {
                    $value['contents'] = '价格'.$value['unit_price'].'元左右/'.$value['model'].'/'.$value['f_name'].'/'.$value['store_house'].'/'.$value['content'];
                }
            }
            $value['input_time'] = date ("m-d H:i", $value['input_time']);
            $tmpContent          = "您于 ".$value['input_time']." 发布的";
            if ($value['type'] == 1) {
                $tmpContent .= "求购";
            } elseif ($value['type'] == 2) {
                $tmpContent .= "供给";
            }
            $tmpContent .= "信息:".$value['contents']."收到一条回复:$content";
            M ("qapp:robotMsg")->saveRobotMsg ($pur_id, $send_id, $send_id, $tmpContent, $type = 2);
            //$tmpContent.=M("public:common")->model('customer_contact')->select('name')->where("user_id=".$user_id)->getOne();
            $result = M ('qapp:plasticRepeat')->saveMsg ($user_id, $pur_id, $send_id, $content);
            if ($result) {
                $this->json_output (array(
                    'err' => 0,
                    'msg' => '回复消息保存成功',
                ));
            }
        }
        $this->_errCode (6);
    }

    //获取我的供给或求购
    public function getMyMsg ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 10);
            $type    = sget ('type', 'i');//1采购 2报价
            $data    = M ('qapp:plasticMyMsg')->getMyMsg ($user_id, $page, $size, $type);
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关的数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $this->json_output (array(
                'err'  => 0,
                'data' => $data['data'],
            ));
        }
        $this->_errCode (6);
    }

    //删除我的供给或求购
    public function deleteMyMsg ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $this->checkAccount ();
            $id     = sget ('id', 'i');//当前我的报价或采购的id
            $result = M ('plasticzone:plasticMyMsg')->deleteMyMsg ($id);
            $this->json_output ($result);
        }
        $this->_errCode (6);
    }


    //获取我的(留言)
    public function getMyComment ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 10);
            $data    = M ('qapp:plasticMyMsg')->getMyComment ($user_id, $page, $size, 6);//塑料圈app
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关的数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $this->json_output (array(
                'err'  => 0,
                'data' => $data['data'],
            ));
        }
        $this->_errCode (6);
    }

    //获取系统消息robot
    public function getRobotMsg ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 10);
            $data    = M ('qapp:robotMsg')->getRobotMsg ($user_id, $page, $size);
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关的数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $this->json_output (array(
                'err'  => 0,
                'data' => $data['data'],
            ));
        }
        $this->_errCode (6);
    }

    //获取我的粉丝和我的关注(数)
    public function getMyFuns ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $type    = sget ('type', 'i');//1粉丝2关注
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 10);
            $data    = M ('qapp:plasticIntroduction')->getMyFuns ($user_id, $type, $page, $size);
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err'   => 2,
                    'msg'   => '没有相关的数据',
                    'count' => 0,
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $this->json_output (array(
                'err'   => 0,
                'data'  => $data['data'],
                'count' => $data['count'],
            ));
        }
        $this->_errCode (6);
    }


    //查看塑料圈好友资料
    public function getZoneFriend ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $userid  = sget ('userid', 'i');//当前联系人的id
            if ($user_id != $userid) {
                $_tmp = M ("qapp:infoList")->where ("user_id= $user_id and other_id = $userid")
                                           ->order ("info_list_id desc")->getRow ();
                if (!$_tmp) {
                    $this->_errCode (99);
                }
            }
            /**
             * 添加记录
             * -4避免descScore第一次出现相同的记录
            */
            if($_tmp['input_time']< ( CORE_TIME - 4)){
                M ("qapp:infoList")->add (array(
                    'user_id'    => $user_id,
                    'other_id'   => $userid,
                    'input_time' => CORE_TIME,
                ));
            }
            $data = M ('qapp:plasticPersonalInfo')->getPersonalInfo ($user_id, $userid);
            if (empty($data)) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关资料',
                ));
            }
            $this->json_output (array(
                'err'  => 0,
                'data' => $data,
            ));
        }
        $this->_errCode (6);
    }

    //偏好设置-发送短信
    public function favorateSet ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id  = $this->checkAccount ();
            $type     = sget ('type', 'i');//0 关注 1 回复 2是否公开电话
            $is_allow = sget ('is_allow', 'i', 0);//0:允许 1:不允许
            $result   = M ('qapp:plasticAttention')->favorateSet ($user_id, $type, $is_allow);
            $this->json_output ($result);
        }
        $this->_errCode (6);
    }


    //获取我的塑料圈个人信息
    public function getMyPlastic ()
    {
        $cache         = cache::startMemcache ();
        $this->is_ajax = true;
        if ($_POST) {
            $user_id    = $this->checkAccount ();
            $headimgurl = sget ('headimgurl', 's');
            $data       = M ('qapp:plasticPersonalInfo')->getMyPlastic ($user_id, $headimgurl);
            if (empty($data)) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关资料',
                ));
            }
            $this->json_output (array(
                'err'  => 0,
                'data' => $data,
            ));
        }
        $this->_errCode (6);
    }


    //查看我的资料
    public function getSelfInfo ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $data    = M ('qapp:plasticPersonalInfo')->getSelfInfo ($user_id);
            if (empty($data)) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关资料',
                ));
            }
            $this->json_output (array(
                'err'  => 0,
                'data' => $data,
            ));
        }
        $this->_errCode (6);
    }

    //保存我的资料
    public function saveSelfInfo ()
    {
        $this->is_ajax = true;

        if ($_POST) {
            $user_id              = $this->checkAccount ();
            $_tmpAddress          = sget ('address', 's');
            $_tmpSex              = sget ('sex', 's');
            $_tmpMajor            = sget ('major', 's');
            $_tmpConcern          = sget ('concern', 's');
            $_tmpDist             = sget ('dist', 's', 'EC');
            $_tmpType             = sget ('type', 's');
            $_tmpMonthConsum      = sget ('month_consum', 's');
            $_tmpMainProduct      = sget ('main_product', 's');
            $data['address']      = $_tmpAddress;
            $data['sex']          = $_tmpSex;
            $data['major']        = $_tmpMajor;
            $data['concern']      = $_tmpConcern;
            $data['dist']         = $_tmpDist;
            $data['month_consum'] = $_tmpMonthConsum;
            $data['type']         = $_tmpType;
            $data['main_product'] = $_tmpMainProduct;
            foreach ($data as $key => &$row) {
                if ($key == 'address') {
                    $row = $this->clearStr ($row);
                } elseif ($key == 'sex') {
                    if (!in_array ($row, array(
                        '1',
                        '0',
                    ))
                    ) {
                        $this->_errCode (6);
                    }
                    $row = (int)$row;
                } elseif ($key == 'major') {
                    $field = $this->clearStr ($row);
                    if (!is_string ($row)) {
                        $this->json_output (array(
                            'err' => 1,
                            'msg' => '格式错误',
                        ));
                    }
                    if (!empty($field)) {
                        $field = explode (",", $field);
                        $field = array_map ('strtoupper', $field);
                        foreach ($field as $key1 => $row1) {
                            if (empty($row1)) {
                                unset($field[$key1]);
                            }
                        }
                        $field = array_unique ($field);
                        //$field=explode(",",array_map('strtoupper',$field));
                        if (count ($field) > 10) {
                            $this->json_output (array(
                                'err' => 6,
                                'msg' => '牌号个数不能超过十个',
                            ));
                        }
                        $row = $field;
                    } else {
                        unset($data[$key]);
                    }
                } elseif ($key == 'concern') {
                    $row   = $this->clearStr ($row);
                    $field = preg_replace ("/(\n)|(\s)|(\t)|(\')|(')|(，)|( )|(\.)/", ',', $row);
                    //$field=explode(",",array_map('strtoupper',$field));
                    if (!is_string ($row)) {
                        $this->json_output (array(
                            'err' => 1,
                            'msg' => '格式错误',
                        ));
                    }
                    if (!empty($field)) {
                        $field = explode (",", $field);
                        $field = array_map ('strtoupper', $field);
                        foreach ($field as $key1 => $row1) {
                            if (empty($row1)) {
                                unset($field[$key1]);
                            }
                        }
                        $field = array_unique ($field);
                        //$field=explode(",",array_map('strtoupper',$field));
                        if (count ($field) > 10) {
                            $this->json_output (array(
                                'err' => 6,
                                'msg' => '牌号个数不能超过十个',
                            ));
                        }
                        $row = $field;
                    } else {
                        unset($data[$key]);
                    }
                } elseif ($key == 'dist') {
                    if (empty($row) || (!in_array ($row, array(
                            'EC',
                            'NC',
                            'SC',
                            'OT',
                        )))
                    ) {
                        $this->_errCode (6);
                    }
                } elseif ($key == 'type') {
                    if (empty($row) || (!in_array ($row, array(
                            '1',
                            '2',
                            '3',
                            '4',
                        )))
                    ) {
                        $this->_errCode (6);
                    }
                } elseif ($key == 'month_consum') {
                    $row = $this->clearStr ($row);
                    if (mb_strlen ($row) > 15) {
                        $this->json_output (array(
                            'err' => 1,
                            'msg' => '1字符过长',
                        ));
                    }
                } elseif ($key == 'main_product') {
                    $row = $this->clearStr ($row);
                    if (mb_strlen ($row) > 25) {
                        $this->json_output (array(
                            'err' => 1,
                            'msg' => '2字符过长',
                        ));
                    }
                }
            }
            $result = M ('qapp:plasticSave')->saveSelfInfo1_2 ($user_id, $data);
            if ($result['err'] > 0) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => $result['msg'],
                ));
            }
            $this->json_output (array(
                'err' => 0,
                'msg' => '保存资料成功',
            ));
        }
        $this->_errCode (6);
    }

    //获取ta的求购或供给
    public function getTaPur ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id  = $this->checkAccount ();
            $keywords = sget ('keywords', 's');//这里传空值P
            $page     = sget ('page', 'i', 1);
            $size     = sget ('size', 'i', 10);
            $type     = sget ('type', 'i', 1);//1采购 2报价
            $userid   = sget ('userid', 'i');//当前联系人的id
            $data     = M ('qapp:plasticRelease')->getReleaseMsg2 ($keywords, $page, $size, $userid, $type);
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关的数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $this->json_output (array(
                'err'  => 0,
                'data' => $data['data'],
            ));
        }
        $this->_errCode (6);
    }

    //关注或取消关注
    public function focusOrCancel ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id    = $this->checkAccount ();
            $focused_id = sget ('focused_id', 'i');//当前联系人的id
            $result     = M ('qapp:plasticAttention')->getAttention ($user_id, $focused_id);
            $this->json_output ($result);
        }
        $this->_errCode (6);
    }

    //登录时计算会员等级
    public function getMemberLevel ()
    {
        $this->is_ajax = true;
        $user_id       = $this->checkAccount ();
        M ('plasticzone:plasticPersonalInfo')->getMemberLevel ($user_id);
        $this->success ('会员等级更新成功');
    }


    //塑料圈联系人的-发送消息
    public function sendZoneContactMsg ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $userId  = sget ('userId', 'i');//接受消息人的id
            $content = sget ('content', 's');
            $result  = M ('plasticzone:plasticRepeat')->saveZoneMsg ($user_id, $userId, $content);
            if ($result) {
                $this->json_output (array(
                    'err' => 0,
                    'msg' => '消息发送成功',
                ));
            }
        }
        $this->_errCode (6);
    }

    //塑料圈联系人的-我的消息（yuepao）
    public function getZoneContactMsg ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 10);
            $type    = sget ('type', 'i', 1);//1:我接受的 2:我发送的
            $data    = M ('qapp:plasticMyMsg')->getZoneContactMsg ($user_id, $type, $page, $size);
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关的数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $this->json_output (array(
                'err'  => 0,
                'data' => $data['data'],
            ));
        }
        $this->_errCode (6);
    }


    //http-->curl
    protected function http ($url)
    {
        $ch = curl_init ($url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($ch, CURLOPT_HEADER, false);
        $output = curl_exec ($ch);//输出内容
        curl_close ($ch);

        return $output;
    }


    //通过回调方法获取用户的code
    protected function get_authorize_url ($redirect_uri = '', $state = '')
    {
        $redirect_uri = urlencode ($redirect_uri);
        $url          = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->AppID}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
        echo "<script language='javascript' type='text/javascript'>";
        echo "window.location.href='$url'";
        echo "</script>";
    }

    //curl 获取文件数据
    public function curl_file ($url)
    {
        $ch = curl_init ($url);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_NOBODY, 0);//只取body头
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);//curl_exec执行成功后返回执行的结果；不设置的话，curl_exec执行成功则返回true
        $output = curl_exec ($ch);
        curl_close ($ch);

        return $output;
    }



    //判断账户
    /**
     * @param int $type 0 qushouyeshuju  1 biede
     *
     * @return int|string
     */
    public function checkAccount ($type = 1)
    {
        $this->is_ajax = true;
        $token         = sget ('token', 's');
        if (empty($token)) {
            $user_id = 0;
        } else {
            $user_id = M ('qapp:appToken')->deUserId ($token);
            if (is_array ($user_id)) {
                $user_id = 0;
            }
            //$this->json_output($user_id);
        }
        if (empty($type)) {
            if ($user_id < 0) {
                $this->json_output (array(
                    'err' => 1,
                    'msg' => '账号错误',
                ));
            }
        } else {
            if ($user_id <= 0) {
                $this->json_output (array(
                    'err' => 1,
                    'msg' => '您未登录塑料圈,无法查看企业及个人信息',
                ));
            }

            $_tmpRow = M ("public:common")->select ("cus.status,con.status as con_status")->from ("customer cus")
                                          ->leftjoin ("customer_contact con", 'con.c_id=cus.c_id')
                                          ->where ("con.user_id=$user_id")->getRow ();
            if (in_array ($_tmpRow['status'], array(
                    3,
                    4,
                )) || in_array ($_tmpRow['con_status'], array(
                    2,
                    3,
                    4,
                ))
            ) {
                $this->json_output (array(
                    'err' => 1,
                    'msg' => '您账号已冻结，如有疑问请咨询客服：4006129965',
                ));
            }
        }

        return $user_id;
    }

    //判断是否显示电话号码的
    //true默认是显示的
    //false 是不显示的
    public function checkPhoneShow ($user_id = 0)
    {
        $data = M ('qapp:plasticPersonalInfo')->getSelfInfo ($user_id);
        if (isset($data['allow_send']) && isset($data['allow_send']['show'])) {
            if ($data['allow_send']['show'] === 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    //判断头像的显示
    /**
     * @param $thumb
     * $thumb=$this->model('contact_info')->select('thumb,thumbqq,mobile_province')->where('user_id='.$value['user_id'])->getRow();
     */
    public function checkPicture ($thumb = array())
    {
        if (empty($thumb['thumbqq'])) {
            if (strstr ($thumb['thumb'], 'http')) {
                $thumb['thumb'] = $thumb['thumb'];
            } else {
                if (empty($thumb['thumb'])) {
                    $thumb['thumb'] = "http://statics.myplas.com/upload/16/09/02/logos.jpg";
                } else {
                    $thumb['thumb'] = FILE_URL."/upload/".$thumb['thumb'];
                }
            }
        } else {
            $thumb['thumb'] = $thumb['thumbqq'];
        }

        return $thumb;
    }

    //判断是否到最后一页
    protected function _checkLastPage ($count, $size, $page)
    {
        if ($count > 0) {
            if ($count % $size == 0 && ceil ($count / $size) < $page) {
                $this->json_output (array(
                    'err' => 3,
                    'msg' => '没有更多数据',
                ));
            } elseif ($count % $size != 0 && ceil ($count / $size) < $page) {
                $this->json_output (array(
                    'err' => 3,
                    'msg' => '没有更多数据',
                ));
            }
        }
    }

    //判断积分是否足够
    protected function checkSupply ($num = null, $outType = 0)
    {
        $this->is_ajax = true;
        $user_id       = $this->checkAccount ();
        if ($outType == 0) {
            $goods_id  = sget ('goods_id', 'i');
            $pointsRow = M ('public:common')->from ("points_goods")->select ("points,name,cate_id")
                                            ->where ("status = 1 and receive_num < num and id = $goods_id")->getRow ();
            $num       = (int)$pointsRow['points'];
            if ($pointsRow['cate_id'] == 7) {
                $this->json_output (array(
                    'err' => 119,
                    'msg' => '活动时间已过，请关注我们《塑料圈》公众号，参与更多活动',
                ));
            }
            $user = M ('public:common')->model ('contact_info');
            if ($info = $user->where ("user_id=$user_id")->getRow ()) {
                if (($info['quan_points'] - $num) < 0) {
                    $this->json_output (array(
                        'err' => 100,
                        'msg' => '塑豆不足,请多努力!',
                    ));
                }
                $this->json_output (array(
                    'err' => 0,
                    'msg' => '塑豆足够兑换',
                ));
            }
        } elseif ($outType == 1) {
            $num  = (int)$num;
            $user = M ('public:common')->model ('contact_info');
            if ($info = $user->where ("user_id=$user_id")->getRow ()) {
                if (($info['quan_points'] - $num) < 0) {
                    return false;
                }

                return true;
            }
        }
    }


    /**
     * 验证手机号码
     * @access protected
     *
     * @param $type 0 zhuce  1 zhaohuimima/yanzhemadenglu
     *
     * @return bool
     */
    protected function _chkmobile ($value = '', $type = 0)
    {
        if (!is_mobile ($value)) {
            if (empty($value)) {
                $this->err = '手机号码不能为空';
            } else {
                $this->err = '手机号码输入有误,请重新输入';
            }

            return false;
        }
        $chk = M ('system:sysUser')->usrUnique ('mobile', $value);//非重复
        if (!$chk) {
            if (empty($type)) {
                //                $this->err = '手机号已注册,直接登录即可';
                //                return false;
                return true;
            } else {
                return true;
            }
        } else {
            if (empty($type)) {
                return true;
            } else {
                $this->err = '手机号未注册';

                return false;
            }
        }
    }

    //    /**
    //     * 检查浏览器
    //     * @return string
    //     */
    //    public function checkBrowser(){
    //        $agent=$_SERVER["HTTP_USER_AGENT"];
    //        if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0')) //ie11判断
    //            return "ie";
    //        else if(strpos($agent,'Firefox')!==false)
    //            return "firefox";
    //        else if(strpos($agent,'Chrome')!==false)
    //            return "chrome";
    //        else if(strpos($agent,'Opera')!==false)
    //            return 'opera';
    //        else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
    //            return 'safari';
    //        else
    //            return 'unknown';
    //    }

    /**
     * 检查平台来源
     * @return json
     */
    public function checkPlatform ()
    {
        return get_platform ();
    }


    public function somes ()
    {
        $token   = sget ('token', 's');
        $_userid = M ('qapp:appToken')->deUserId ($token);
        var_dump ($_userid);
    }

    /*
     * 下面的都是塑料头条的
     */
    public function topLine ()
    {
        if ($_POST) {
            $this->is_ajax = true;
            //$this->checkAccount();
            $cache = cache::startMemcache ();
            //if (!$info = $cache->get('qtopLineInfo')) {
            $info = M ("qapp:news")->getqAppCateList ('public', 1, array(), null, 10);
            foreach ($info as $key => &$value) {
                $value['input_time'] = $this->checkTime ($value['input_time']);
                //$value['content']=stripslashes($value['content']);
                //  $str=$value['content'];
                // $value['content']=$this->cleanhtml($str,'<p>');
                $value['description'] = mb_substr (strip_tags ($value['description']), 0, 60, 'utf-8').'...';
                if ($value['type'] == 'public') {
                    $arr           = array(
                        'pe',
                        'pp',
                        'pvc',
                    );
                    $tmp           = array_rand ($arr, 1);
                    $value['type'] = $arr[$tmp];
                }
                $value['type'] = strtoupper ($value['type']);
            }
            //unset($value['content']);
            $cache->set ('qtopLineInfo', $info, 300);
            //}
            $this->json_output (array(
                'err'  => 0,
                'data' => array(
                    'topLine' => $this->cates,
                    'info'    => $info,
                ),
            ));
        }
        $this->_errCode (6);
    }

    /**
     * 塑料头条-分类列表
     */
    public function getCateList ()
    {
        if ($_POST) {
            $this->is_ajax = true;
            $page          = sget ('page', 'i', 1);
            $size          = sget ('size', 'i', 10);
            $cate_id       = sget ('cate_id', 'i');
            $this->checkAccount ();
            $cache = cache::startMemcache ();
            if ($page <= 2) {
                $data = array();
                if (!$data['data'] = $cache->get ('qcateListInfo'.$page.'_'.$cate_id)) {
                    $data = M ("qapp:news")->getqAppCateList ('public', $cate_id, array(), $page, $size);
                    if (empty($data['data']) && $page == 1) {
                        $this->json_output (array(
                            'err' => 2,
                            'msg' => '没有相关数据',
                        ));
                    }
                    $this->_checkLastPage ($data['count'], $size, $page);
                    //截取示例文章文字
                    foreach ($data['data'] as $key => &$v) {
                        //$v['content']=$this->cleanhtml(strip_tags($v['content']),'');
                        $data['data'][$key]['description'] = mb_substr (strip_tags ($v['description']), 0, 50, 'utf-8').'...';
                        //取出右键导航分类名称
                        $data['data'][$key]['cate_name']  = $this->cates[$cate_id];
                        $data['data'][$key]['input_time'] = $this->checkTime ($v['input_time']);
                        if ($v['type'] == 'public') {
                            $arr       = array(
                                'pe',
                                'pp',
                                'pvc',
                            );
                            $tmp       = array_rand ($arr, 1);
                            $v['type'] = 'pp';
                        }
                        $v['type'] = strtoupper ($v['type']);
                        //unset($v['content']);
                    }
                    $cache->set ('qcateListInfo'.$page.'_'.$cate_id, $data['data'], 300);
                }
            } else {
                $data = M ("qapp:news")->getqAppCateList ('public', $cate_id, array(), $page, $size);
                if (empty($data['data']) && $page == 1) {
                    $this->json_output (array(
                        'err' => 2,
                        'msg' => '没有相关数据',
                    ));
                }
                $this->_checkLastPage ($data['count'], $size, $page);
                //截取示例文章文字
                foreach ($data['data'] as $key => &$v) {
                    //$v['content']=$this->cleanhtml(strip_tags($v['content']),'');
                    $data['data'][$key]['description'] = mb_substr (strip_tags ($v['description']), 0, 50, 'utf-8').'...';
                    //取出右键导航分类名称
                    $data['data'][$key]['cate_name']  = $this->cates[$cate_id];
                    $data['data'][$key]['input_time'] = $this->checkTime ($v['input_time']);
                    if ($v['type'] == 'public') {
                        $arr       = array(
                            'pe',
                            'pp',
                            'pvc',
                        );
                        $tmp       = array_rand ($arr, 1);
                        $v['type'] = 'pp';
                    }
                    $v['type'] = strtoupper ($v['type']);
                    //unset($v['content']);
                }
            }
            $this->json_output (array(
                'err'  => 0,
                'info' => $data['data'],
            ));
        }
        $this->_errCode (6);
    }

    /**
     * 塑料头条-详情列表
     */
    public function getDetailInfo ()
    {
        A ("api:qapi1_1")->getDetailInfo ();
    }

    /**
     * 获取概率函数
     *
     * @param float $chance
     *
     * @return bool
     */
    public function getArrayChance ($chance = 0.7)
    {
        $chance = 10 * $chance;
        if (empty($chance)) {
            $chance = 7;
        }
        $tmp = array();
        $tmp = array_pad ($tmp, $chance, 'a');
        $tmp = array_pad ($tmp, 10, 'b');
        shuffle ($tmp);
        if ($tmp[1] == 'a') {
            return true;
        } elseif ($tmp[1] == 'b') {
            return false;
        }
    }

    /**
     * 获取订阅频道
     */
    public function getSelectCate ()
    {
        if ($_POST) {
            $this->is_ajax = true;
            $user_id       = $this->checkAccount ();
            $type          = sget ('type', 'i');// 1 set  2 get 3 getallCate
            if ($type == 1) {
                $cate_id = sget ('cate_id', 'a');
                if (empty($cate_id) || !is_array ($cate_id)) {
                    $this->_errCode (6);
                }
                if (M ("qapp:newsSubscribe")->setSubscribeByUserid ($user_id, $cate_id)) {
                    $this->json_output (array(
                        'err' => 0,
                        'msg' => '成功',
                    ));
                }
                $this->_errCode (101);
            } elseif ($type == 2) {
                $tmp = M ("qapp:newsSubscribe")->getSubscribeByUserid ($user_id);
                if (empty($tmp)) {
                    $tmp = $this->newsSubscribeDefault;
                }
                $this->json_output (array(
                    'err'  => 0,
                    'data' => $tmp,
                ));
            } elseif ($type == 3) {
                $this->json_output (array(
                    'err'  => 0,
                    'data' => $this->cates,
                ));
            }
        }
        $this->_errCode (6);
    }


    /**
     * 头条推荐
     */
    public function getSubscribe ()
    {
        A ("api:qapi1_1")->getSubscribe ();
    }


    /**
     * 判断时间和改变时间
     */
    protected function checkTime ($time)
    {
        if (date ("Y-m-d") == date ("Y-m-d", $time)) {
            return date ("H:i", $time);
        } else {
            return date ("Y-m-d", $time);
        }
    }

    /**
     * 内容过滤,过滤html标签
     */
    public function cleanhtml ($str, $tags = '<img><a>')
    {//过滤时默认保留html中的<a><img>标签
        $search = array(
            '@<script[^>]*?>.*?</script>@si',
            // Strip out javascript
            /*  '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags*/
            "'<style[^>]*?>.*?</style>'si",
            /*'@<style[^>]*?>.*?</style>@si',    // Strip style tags properly*/
            '@<![\s\S]*?--[ \t\n\r]*>@',
            // Strip multi-line comments including CDATA
        );
        $array  = array(
            '',
            '',
            '',
        );
        $str    = preg_replace ($search, $array, $str);
        $str    = str_replace (array(
            "\r\n",
            "\r",
            "\n",
            "&nbsp;",
        ), "", $str);
        $str    = strip_tags ($str, $tags);

        return $str;
    }

    /*
     * 塑料圈app兑换积分的接口
     *  注册一人，暂时不送积分
     * 引荐一个人50分
     * 发布报价10分（不需要审核）、
     * 发布采购20分（需要审核，但是直接加积分）
     */

    /*
     * 塑料圈app之积分商品列表
     */
    public function getProductList ()
    {
        if ($_POST) {
            $this->is_ajax = true;
            $user_id       = $this->checkAccount ();
            $page          = sget ('page', 'i', 1);
            $size          = sget ('size', 'i', 10);
            $data          = M ("points:pointsGoods")->select ('id,cate_id,thumb,name,points,type')
                                                     ->where ("status = 1 and receive_num < num and is_mobile =1")
                                                     ->order ('id desc')->page ($page, $size)->getPage ();
            //我的积分
            $points = M ('qapp:pointsBill')->getUerPoints ($user_id);
            $points = empty($points) ? 0 : $points;
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $supply_and_demand = M ("qapp:plasticRelease")->getReleaseMsg ('', 1, 5, 0, 'ALL', 'DEMANDORSUPPLY', $user_id);
            /*if (empty($supply_and_demand['count'])) {
                foreach ($data['data'] as $key => $info) {
                    if ($info['id'] == 35) {
                        unset($data['data'][$key]);
                        break;
                    }
                }
            }*/

            //type 1 是供求 2 是通讯录
            $goods_id = $this->db->model ("points_goods")->select ('id')->where (" type =1 and status =1")->getOne ();

            foreach ($data['data'] as $k => &$v) {
                if (!empty($goods_id) && $v['id'] == $goods_id && !empty($supply_and_demand['count'])) {
                    $v['myMsg'] = $supply_and_demand['data'];
                } else {
                    $v['myMsg'] = array();
                }
                if ($v['thumb']) {
                    $v['thumb'] = FILE_URL.'/upload/'.$v['thumb'];
                }
                if ($v['image']) {
                    $v['image'] = FILE_URL.'/upload/'.$v['image'];
                }
            }
            $ret = array(
                'err'       => 0,
                'info'      => $data['data'],
                'pointsAll' => $points,
            );

            $this->json_output ($ret);
        }
        $this->_errCode (6);
    }

    //区域选择
    public function getDistinct ()
    {
        if ($_POST) {
            $this->is_ajax = true;
            $this->checkAccount ();
            $data = array(
                'EC' => '华东',
                'SC' => '华南',
                'NC' => '华北',
            );
            $this->json_output (array(
                'err'  => 0,
                'data' => $data,
            ));
        }
        $this->_errCode (6);
    }

    /*
     * 塑料圈app之积分商品详情页
     */
    public function getProductInfo ()
    {
        if ($_POST) {
            $this->is_ajax = true;
            $this->checkAccount ();
            $id = sget ('id', i);//商品的id
            if ($id < 1) {
                $this->json_output (array(
                    'err' => 1,
                    'msg' => 'id参数错误',
                ));
            }
            $arr    = $this->db->from ("points_goods")->select ('id,cate_id,thumb,image,name,points,type')
                               ->where ("status = 1 and receive_num < num and id = $id")->getRow ();
            $result = array();
            preg_match_all ("/(?:\（)(.*)(?:\）)/i", $arr['name'], $result);
            $str = (int)$result[1][0];
            if ($arr['image']) {
                $arr['image'] = FILE_URL.'/upload/'.$arr['image'];
            }
            if ($arr['thumb']) {
                $arr['thumb'] = FILE_URL.'/upload/'.$arr['thumb'];
            }
            //if (empty($arr['content'])) $arr['content'] = "<span>本置顶卡可使您的信息在供求信息版面置顶" . $str . "分钟</span><br />备注:<br />1.同一时间内最多一条信息置顶;";
            $this->json_output (array(
                'err'  => 0,
                'info' => $arr,
            ));
        }
        $this->_errCode (6);
    }

    /*
     * 塑料圈app之退货规定
     */
    public function returnRule ()
    {
        if ($_POST) {
            $this->is_ajax = true;
            $this->checkAccount ();
            $v         = array();
            $v['rule'] = '<span>兑换商品若出现以下情况，我的塑料网允许退换货：</span><br />';
            $v['rule'] .= '<span>1）商品本身有质量问题，影响使用</span><br />';
            $v['rule'] .= '<span>2）兑换的商品在运输过程中出现损毁</span><br />';
            $v['rule'] .= '<span>用户可在签收后7日内拨打我的塑料网客服热线400-6129-965，申请退换货，退回时，请务必将原包装、内附赠品及说明书和相关文件一并寄回。</span><br />';
            $v['rule'] .= '<br />';
            $v['rule'] .= '<span>若出现以下情况，我的塑料网有权不予进行商品退换货：</span><br />';
            $v['rule'] .= '<span>1)非我的塑料网积分商城的兑换商品</span><br />';
            $v['rule'] .= '<span>2)不正常使用商品造成的质量问题</span><br />';
            $v['rule'] .= '<span>3)超过我的塑料网积分商城承诺的7天退换货有效时间</span><br />';
            $v['rule'] .= '<span>4)将商品存储、暴露在不适宜环境中造成的损坏</span><br />';
            $v['rule'] .= '<span>5)因未经授权的修理、改动、不正确的安装造成损坏</span><br />';
            $v['rule'] .= '<span>6)不可抗力导致礼品损坏</span><br />';
            $v['rule'] .= '<span>7)商品的正常磨损</span><br />';
            $v['rule'] .= '<span>8)在退换货之前未与我的塑料网客服取得联系，进行过退换货登记</span><br />';
            $v['rule'] .= '<span>9)退回商品包装或其他附属物不完整或有毁损</span><br />';
            $v['rule'] .= '<br />';
            $v['rule'] .= '<span>注：商品图片及文字仅供参考，具体以实物为准。</span><br />';
            $this->json_output (array(
                'err'  => 0,
                'rule' => $v['rule'],
            ));
        }
        $this->_errCode (6);
    }

    /*
     * 塑料圈app之积分规定
     */
    public function pointsRule ()
    {
        $this->display ('plasticzone/points_rule.html');
        //        if ($_POST) {
        //            $this->is_ajax = true;
        //            $this->checkAccount ();
        //            $salePoints = intval (M ('system:setting')->get ('points')['points']['sale']);
        //            $purPoints  = intval (M ('system:setting')->get ('points')['points']['pur']);
        //            $rule       = '';
        //            $rule .= '<span>1. 每日发布报价/求购一条，增加'.$salePoints.'/'.$purPoints.'积分</span><br />';
        //            $rule .= '<span>2. 与我的塑料网成交后自动累计积分，买的多送的多</span><br />';
        //            $rule .= '<span>3. 积分商城积分兑换的商品不但免费还免运费</span>';
        //            $this->json_output (array( 'err' => 0, 'rule' => $rule ));
        //        }
        //        $this->_errCode (6);
    }

    /*
     * 获取兑换时间段
     * @param $type   0是通讯录置顶 1供求信息置顶
     */
    public function exchangeTime ($type = 1)
    {
        $this->is_ajax = true;
        $arr           = $this->db->model ('corn')->where ("type = $type and status_e = 0 ")
                                  ->select ('user_id,exe_time_s,exe_time_e,purchase')->getAll ();

        return $arr;
    }

    /*
     * 兑换置顶
     */
    public function exchangeSupplyOrDemand ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $type    = sget ('type', 'i');//  0 实物  1 通讯录 2 供求信息
            $user_id = $this->checkAccount ();
            if (!in_array ($type, array(
                0,
                1,
                2,
            ))
            ) {
                $this->json_output (array(
                    'err' => 11,
                    'msg' => 'type参数错误',
                ));
            }
            $goods_id = sget ('goods_id', 'i');   //所需要的商品的id
            if ($goods_id < 1) {
                $this->json_output (array(
                    'err' => 12,
                    'msg' => 'goods_id参数错误',
                ));
            }
            $pointsModel = M ("qapp:pointsBill");
            $pointsModel->startTrans ();
            try {
                $pointsRow = $pointsModel->from ("points_goods")->select ("points,name,cate_id")
                                         ->where ("status = 1 and receive_num < num and id = $goods_id")->getRow ();
                $points    = (int)$pointsRow['points'];
                if ($pointsRow['cate_id'] == 7) {
                    throw new Exception("系统错误 pubpur:119");
                }
                if (!$points) {
                    throw new Exception("系统错误 pubpur:101");
                }
                if (!$this->checkSupply ($points, 1)) {
                    throw new Exception("系统错误 pubpur:100");
                }
                //)兑换方式5 ：兑换礼品
                $exchangeType = 5; //兑换方式
                if ($type == 2) {
                    $hour = sget ('hour', 's', 13);
                    $hour = (int)$hour;
                    if ($hour < 0 || $hour > 23) {
                        throw new Exception("系统错误 pubpur:102");
                    }
                    if ($hour < date ("H")) {
                        throw new Exception("系统错误 pubpur:115");
                    }
                    if ($hour == date ("H")) {
                        throw new Exception("系统错误 pubpur:117");
                    }
                    $stime = sget ('stime', 'i', 0);//开始的时间
                    if (!in_array ($stime, array(
                        0,
                        10,
                        20,
                        30,
                        40,
                        50,
                    ))
                    ) {
                        throw new Exception("系统错误 pubpur:103");
                    }
                    if ($hour == date ("H") && $stime < date ("i")) {
                        throw new Exception("系统错误 pubpur:115");
                    }
                    $time = sget ('time', 'i', 60);//所需要兑换的时长
                    if (!$time) {
                        throw new Exception("系统错误 pubpur:104");
                    }
                    if (!in_array ($time, array(
                        10,
                        30,
                        60,
                    ))
                    ) {
                        throw new Exception("系统错误 pubpur:104");
                    }
                    $purchase_id = sget ('purchase_id', 'i', 25895);//供求信息id
                    if ($purchase_id < 1) {
                        throw new Exception("系统错误 pubpur:105");
                    }//$this->error('purchase_id参数错误');
                    $etime      = $stime + $time;
                    $data       = date ("Y-m-d");//当前的年月日
                    $exe_time_s = strtotime ($data." ".$hour.":".$stime);
                    if ($etime >= 60) {
                        $etime = $etime - 60;
                        $hour++;
                    }
                    //年跟月跟日的bug，转钟转月的bug，目前没有解决。
                    $exe_time_e = strtotime ($data." ".$hour.":".$etime);
                    $arr        = $this->exchangeTime (1);//供求信息
                    foreach ($arr as $k => $v) {
                        if ($exe_time_s >= $v['exe_time_s'] && $exe_time_e <= $v['exe_time_e']) {
                            throw new Exception("系统错误 pubpur:106"); //$this->error('选择时间段错误,已被人预定过了');
                        }
                    }
                    //开始往points_bill表中加记录了，和积分开始增加了
                    if (!$pointsModel->decPoints ($points, $user_id, $exchangeType, $goods_id)) {
                        throw new Exception("系统错误 pubpur:101");
                    }

                } elseif ($type == 1) {
                    $year = sget ('year', 's', 2016);
                    if ($year != date ("Y")) {
                        throw new Exception("系统错误 pubpur:107");
                    }//$this->error('年份参数错误');
                    $s_month = sget ('month', 's', 11);
                    if ($s_month < 1 || $s_month > 12) {
                        throw new Exception("系统错误 pubpur:108");
                    }//$this->error('月份参数错误');
                    $day = sget ('d', 's', 30);
                    //一样的，同样没有解决跨月份的问题，现在留在这里，等以后有时间再来解决这个问题
                    //跨月份已经解决了
                    //当月的只能选当月的，当月的最多只能选距离七天的时间里
                    //目前基本完成的东西
                    if ($s_month != date ("m")) {//不是当月date("t",$year."-".$s_month."-".$day)
                        $s_month_copy = date ("m") + 1;
                        if ($s_month_copy > 12) {
                            $s_month_copy = $s_month_copy - 12;
                            if ($s_month_copy != $s_month) {
                                throw new Exception("系统错误 pubpur:109");
                            }
                        } else {
                            if ($s_month < date ("m")) {
                                throw new Exception("系统错误 pubpur:115");
                            }
                            if ($s_month > (date ("m") + 1)) {
                                throw new Exception("系统错误 pubpur:109");
                            }
                            $month_copy = date ("m");
                            if ($s_month == ($month_copy + 1)) {
                                if ($day > date ("t")) {
                                    throw new Exception("系统错误 pubpur:109");
                                }//大于该月拥有的时间
                                if ($day > (date ("d") + 7 - date ("t"))) {
                                    throw new Exception("系统错误 pubpur:109");
                                }//超过七天
                            }
                        }
                    } else {//当月
                        if ($day > date ("t")) {
                            throw new Exception("系统错误 pubpur:109");
                        }//大于该月拥有的时间
                        //以前的时间
                        if ($day < date ('d') && $s_month == date ("m")) {
                            throw new Exception("系统错误 pubpur:115");
                        }//小于当前的日期
                        if ((date ('d') + 7) < $day) {
                            throw new Exception("系统错误 pubpur:109");
                        }
                    }
                    $year       = (int)$year;
                    $s_month    = (int)$s_month;
                    $day        = (int)$day;
                    $exe_time_s = strtotime ($year.'-'.$s_month.'-'.$day);
                    $exe_time_e = $exe_time_s + 86400;
                    $arr        = $this->checkTime (0);
                    $tmp        = 0;
                    foreach ($arr as $k => $v) {
                        if ($exe_time_s >= $v['exe_time_s'] && $exe_time_e <= $v['exe_time_e']) {
                            if (++$tmp >= 3) {
                                throw new Exception("系统错误 pubpur:118");
                            }
                            //$this->error('选择时间段错误,已被人预定过了');
                        }
                    }
                    //开始往points_bill表中加记录了，和积分开始增加了
                    //兑换方式5 ：兑换礼品
                    if (!$pointsModel->decPoints ($points, $user_id, 5, $goods_id)) {
                        throw new Exception("系统错误 pubpur:101");
                    }
                    //if(true) throw new Exception("系统错误 pubpur:999");
                } elseif ($type == 0) {
                    //if($goods_id<10){
                    $points   = (int)$points;
                    $receiver = sget ('receiver', 's');   //所需要的收货人
                    if (empty($receiver)) {
                        throw new Exception("系统错误 pubpur:112");
                    }
                    $phone = sget ('phone', 's');    //收货人的手机号
                    if (!is_mobile ($phone)) {
                        throw new Exception("系统错误 pubpur:113");
                    }
                    $address = sget ('address', 's'); //收货地址
                    if (empty($address)) {
                        throw new Exception("系统错误 pubpur:114");
                    }
                    if (!$pointsModel->decPoints ($points, $user_id, $exchangeType, $goods_id)) {
                        throw new Exception("系统错误 pubpur:101");
                    }
                    //}
                }
                if (in_array ($type, array(
                    1,
                    2,
                ))) {
                    $purchase_id = isset($purchase_id) ? $purchase_id : 0;
                    if ($type == 2) {
                        $type = 1;
                    } elseif ($type == 1) {
                        $type = 0;
                    }
                    $sqlArray = array(
                        'user_id'    => $user_id,
                        'type'       => $type,
                        'exe_time_s' => $exe_time_s,
                        'exe_time_e' => $exe_time_e,
                        'input_time' => CORE_TIME,
                        'purchase'   => $purchase_id,
                    );
                    if (!$this->db->model ('corn')->add ($sqlArray)) {
                        throw new Exception("系统错误 pubpur:111");
                    }//$this->error('兑换失败');
                }
                $orderModel = M ('points:pointsOrder');
                $_orderData = array(
                    'status'      => 5,
                    'create_time' => CORE_TIME,
                    'order_id'    => $this->buildOrderId (),
                    'goods_id'    => $goods_id,
                    'receiver'    => $receiver,
                    'phone'       => $phone,
                    'address'     => $address,
                    'uid'         => $user_id,
                    'usepoints'   => $points,
                    'remark'      => $pointsRow['name'],
                );
                if ($goods_id < 10) {
                    $_orderData['status'] = 1;
                }
                if (!$orderModel->add ($_orderData)) {
                    throw new Exception('系统错误，无法兑换。code:101');
                }
            } catch (Exception $e) {
                $pointsModel->rollback ();
                $code = (int)substr ($e->getMessage (), -3);
                $this->_errCode ($code);
            }
            $pointsModel->commit ();
            $this->success ('兑换成功');
        }
        $this->_errCode (6);
    }


    /**
     * 新版兑换置顶
     * @param goods_id
     * @param num
     * @param pur_id
     *
     * @return json
     */
    public function new_exchangeSupplyOrDemand ()
    {
        $this->is_ajax = true;
        if ($_GET) {
            $user_id = $this->checkAccount ();
            /*if (!in_array ($type, array( 0, 1, 2 ))) {
                $this->json_output (array( 'err' => 11, 'msg' => 'type参数错误' ));
            }*/
            $goods_id = sget ('goods_id', 'i');   //所需要的商品的id
            $num      = sget ('num', 'i');   //所需要的商品的id
            $pur_id   = sget ('pur_id', 'i', 0);

            if ($goods_id < 1 || $num < 1) {
                $this->json_output (array(
                    'err' => 12,
                    'msg' => '参数错误',
                ));
            }
            $goods_info = M ('public:common')->model ('points_goods')->where ("id= $goods_id")->getRow ();
            if ($goods_info['type'] == 1 && empty($pur_id)) {
                $this->json_output (array(
                    'err' => 21,
                    'msg' => '请选择您要置顶的供求信息',
                ));
            }
            if ($goods_info['type'] == 2) {
                $pur_id = $user_id;
            }

            $user = M ('public:common')->model ('contact_info');
            if ($info = $user->where ("user_id=$user_id")->getRow ()) {
                if (($info['quan_points'] - $num * $goods_info['points']) < 0) {
                    $this->json_output (array(
                        'err' => 15,
                        'msg' => '塑豆不足',
                    ));
                }
            }
            $pointsOrder = M ("points:pointsOrder");
            $is_exist    = $pointsOrder->get_supply_demand_top ($goods_id);
            if ($is_exist) {
                $this->json_output (array(
                    'err' => 13,
                    'msg' => '有人抢先一步,如有需要，请联系客服400-6129-965',
                ));
            }
            $pointsRow = M ('public:common')->from ("points_goods")->select ("points,name,cate_id")
                                            ->where ("status = 1 and receive_num < num and id = $goods_id")->getRow ();
            $point     = (int)$pointsRow['points'];

            $_orderData = array(
                'status'      => 5,
                'create_time' => CORE_TIME,
                'order_id'    => $this->buildOrderId (),
                'goods_id'    => $goods_id,
                'uid'         => $user_id,
                'usepoints'   => $point * $num,
                'remark'      => $pointsRow['name'],
                'num'         => $num,
                'pur_id'      => $pur_id,
                'outpu_time'  => CORE_TIME + $num * 24 * 60 * 60,
            );
            if ($goods_id < 10) {
                $_orderData['status'] = 1;
            }
            if (!$pointsOrder->add ($_orderData)) {
                //throw new Exception('系统错误，无法兑换。code:101');
                $this->json_output (array(
                    'err' => 101,
                    'msg' => '系统错误，无法兑换。',
                ));
            }
            $this->json_output (array(
                'err' => 0,
                'msg' => '购买成功',
            ));
        }

    }

    //生产订单号
    protected function buildOrderId ()
    {
        return date ('Ymd').substr (implode (null, array_map ('ord', str_split (substr (uniqid (), 7, 13), 1))), 0, 8);
    }


    /**
     * 积分记录
     */
    public function pointSupplyList ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 10);
            //$data=M("qapp:pointsBill")->select('id,addtime,type,points')->where("uid = $user_id and type in (2,3,5,6)")->order('id desc')->page($page,$size)->getPage();
            $data = M ("qapp:pointsBill")->select ('id,addtime,type,points,share_type')->where ("uid = $user_id and is_mobile =1")
                                         ->order ('id desc')->page ($page, $size)->getPage ();
            if ((empty($data['data']) && $page == 1) || $page>10) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            //我的积分
            $points = M ('qapp:pointsBill')->getUerPoints ($user_id);
            $points = empty($points) ? 0 : $points;
            foreach ($data['data'] as $k => &$v) {
                $v['typename'] = $this->pointsType[$v['type']];
                if ($v['type'] == 13) {
                    $v['typename'] = $this->shareType[$v['share_type']];
                }

                $v['addtime'] = $this->checkTime ($v['addtime']);
                unset($v['type']);
            }
            $this->json_output (array(
                'err'       => 0,
                'data'      => $data['data'],
                'pointsAll' => $points,
            ));
        }
        $this->_errCode (6);
    }

    /*
     * 兑换记录
     */
    public function exchangeList ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id    = $this->checkAccount ();
            $page       = sget ('page', 'i', 1);
            $size       = sget ('size', 'i', 10);
            $orderModel = M ('points:pointsOrder');
            $data       = $orderModel->where ("uid = $user_id")->order ("id desc")->page ($page, $size)->getPage ();
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            foreach ($data['data'] as $k => &$v) {
                $v['name']        = M ("points:pointsGoods")->where ("id =".$v['goods_id'])->select ('name')->getOne ();
                $v['create_time'] = date ("Y-m-d H:i", $v['create_time']);
                $v['status']      = $this->orderStatus[$v['status']];
            }
            $this->json_output (array(
                'err'  => 0,
                'info' => $data['data'],
            ));
        }
        $this->_errCode (6);
    }

    /*
     * 供求信息置顶之供求信息列表
     */
    public function supplyDemandList ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id = $this->checkAccount ();
            $page    = sget ('page', 'i', 1);
            $size    = sget ('size', 'i', 5);
            $type    = sget ('type', 'i');// 0全部  1采购 2报价
            $own_id  = sget ('user_id', 'i', 0);
            if (!empty($own_id)) {
                $where = " pur.sync = 6 and pur.user_id=$own_id ";
            } else {
                $where = " pur.sync = 6 and pur.user_id=$user_id ";
            }
            if (!empty($type)) {
                $where .= " and pur.type=$type";
            }
            $data = $this->db->select ('pur.id,pur.p_id,pur.user_id,pro.model,pur.unit_price,pur.store_house,fa.f_name,pur.type,pur.content,pur.input_time')
                             ->from ('purchase pur')->leftjoin ('product pro', 'pur.p_id=pro.id')
                             ->leftjoin ('factory fa', 'pro.f_id=fa.fid')->page ($page, $size)->where ($where)
                             ->order ('pur.input_time desc')->getPage ();
            if (empty($data['data']) && $page == 1) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关的数据',
                ));
            }
            $this->_checkLastPage ($data['count'], $size, $page);
            $arr = array();
            foreach ($data['data'] as $k => &$value) {
                $value['input_time'] = $this->checkTime ($value['input_time']);
                if (empty($value['content'])) {
                    if ($value['unit_price'] == 0.00 && empty($value['model']) && empty($value['f_name']) && empty($value['store_house'])) {
                        $value['contents'] = '';
                    } else {
                        $value['contents'] .= '价格'.$value['unit_price'].'元左右/'.$value['model'].'/'.$value['f_name'].'/'.$value['store_house'];
                    }
                } elseif (!empty($value['content'])) {
                    if ($value['unit_price'] == 0.00 && empty($value['model']) && empty($value['f_name']) && empty($value['store_house'])) {
                        $value['contents'] = $value['content'];
                    } else {
                        $value['contents'] = '价格'.$value['unit_price'].'元左右/'.$value['model'].'/'.$value['f_name'].'/'.$value['store_house'].'/'.$value['content'];
                    }
                }
                $arr[$k]['type']       = $value['type'];
                $arr[$k]['input_time'] = $value['input_time'];
                $arr[$k]['p_id']       = $value['id'];
                $arr[$k]['content']    = mb_substr (strip_tags ($value['contents']), 0, 50, 'utf-8').'...';
            }
            $this->json_output (array(
                'err'  => 0,
                'data' => $arr,
            ));
        }
        $this->_errCode (6);
    }

    //分享我的供给或其求购
    public function shareMyPur ()
    {
        $this->is_ajax = true;
        $user_id       = $this->checkAccount (0);
        $id            = sget ('id', 'i');
        if ($id < 1) {
            $this->_errCode (6);
        }
        $data = M ('qapp:plasticShare')->getMySharePur ($id);
        $info = M ("product:purchase")->getInfoById ($id);
        //p($info);exit;
        //获取我的塑料圈个人信息
        $headimgurl = '';
        $info       = M ('qapp:plasticPersonalInfo')->getMyPlastic ($info['user_id'], $headimgurl);
        if (empty($data)) {
            $this->json_output (array(
                'err' => 2,
                'msg' => '没有相关的数据',
            ));
        }
        $this->json_output (array(
            'err'  => 0,
            'data' => $data,
            'info' => $info,
        ));
    }

    //验证是否分享成功日志
    public function saveShareLog ()
    {
        if ($_POST) {
            $share_id = sget ('id', 'i');
            $type     = sget ('type', 'i', 1);//分享类容类型  1采购 2报价 3 文章
            $user_id  = $this->checkAccount ();//分享人的id
            $share    = '';
            if (!M ('qapp:plasticShare')->saveShareLog ($share_id, $type, $user_id, $share)) {
                $this->json_output (array(
                    'err' => 6,
                    'msg' => '分享失败',
                ));
            }
            $this->json_output (array(
                'err' => 0,
                'msg' => '分享成功',
            ));
        }
        $this->_errCode (6);
    }


    public function test4 ()
    {
        p (M ('system:setting')->get ('points'));
    }

    //安全过滤用户输入的字符
    public function clearStr ($str)
    {
        $str = htmlspecialchars ($str);
        $str = safe_replace ($str);
        $str = trim ($str);

        return $str;
    }

    //错误输出的方法
    protected function _errCode ($code = 0, $data = '')
    {
        switch ($code) {
            case 0:
                if (empty($data)) {
                    $this->json_output (array(
                        'err' => 0,
                        'msg' => 'success',
                    ));
                }
                $this->json_output (array(
                    'err'  => 0,
                    'data' => $data,
                ));
                break;
            case 1:
                $this->json_output (array(
                    'err' => 1,
                    'msg' => '失效,请重新登录',
                ));
                break;
            case 2:
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有相关数据',
                ));
                break;
            case 3:
                $this->json_output (array(
                    'err' => 3,
                    'msg' => '没有更多数据',
                ));
                break;
            case 5:
                $this->json_output (array(
                    'err' => 5,
                    'msg' => '不定时上线,敬请期待！',
                ));
                break;
            case 6:
                $this->json_output (array(
                    'err' => 6,
                    'msg' => '参数错误',
                ));
                break;
            case 7:
                $this->json_output (array(
                    'err' => 7,
                    'msg' => '服务器繁忙,请稍后再试！',
                ));
                break;
            case 8:
                $this->json_output (array(
                    'err' => 7,
                    'msg' => '服务正在维护,请稍后再试！',
                ));
                break;
            case 99:
                $this->json_output (array(
                    'err' => 99,
                    'msg' => "是否消耗{$this->points['see_list']}塑豆查看?",
                ));
                break;
            case 100:
                $this->json_output (array(
                    'err' => 100,
                    'msg' => '塑豆不足,请多努力!',
                ));
                break;
            case 101:
                $this->json_output (array(
                    'err' => 101,
                    'msg' => '系统错误',
                ));
                break;
            case 102:
                $this->json_output (array(
                    'err' => 102,
                    'msg' => '小时参数错误',
                ));
                break;
            case 103:
                $this->json_output (array(
                    'err' => 103,
                    'msg' => '分钟参数错误',
                ));
                break;
            case 104:
                $this->json_output (array(
                    'err' => 104,
                    'msg' => '兑换时长参数不正确',
                ));
                break;
            case 105:
                $this->json_output (array(
                    'err' => 105,
                    'msg' => 'purchase_id参数错误',
                ));
                break;
            case 106:
                $this->json_output (array(
                    'err' => 106,
                    'msg' => '选择时间段错误,已被人预定过了',
                ));
                break;
            case 107:
                $this->json_output (array(
                    'err' => 107,
                    'msg' => '年份参数错误',
                ));
                break;
            case 108:
                $this->json_output (array(
                    'err' => 108,
                    'msg' => '月份参数错误',
                ));
                break;
            case 109:
                $this->json_output (array(
                    'err' => 109,
                    'msg' => '日子参数错误,只能选今天和以后7天的，不能大于该月的日子',
                ));
                break;
            case 111:
                $this->json_output (array(
                    'err' => 111,
                    'msg' => '兑换失败',
                ));
                break;
            case 112:
                $this->json_output (array(
                    'err' => 112,
                    'msg' => '收货人错误',
                ));
                break;
            case 113:
                $this->json_output (array(
                    'err' => 113,
                    'msg' => '手机号错误',
                ));
                break;
            case 114:
                $this->json_output (array(
                    'err' => 114,
                    'msg' => '收货地址错误',
                ));
                break;
            case 115:
                $this->json_output (array(
                    'err' => 115,
                    'msg' => '白驹过隙，时间一去不复返，不能停留在逝去的时光里',
                ));
                break;
            case 116:
                $this->json_output (array(
                    'err' => 116,
                    'msg' => 'App内暂时不进行现金兑换，请于微信内搜索《塑料圈》进行兑换',
                ));
                break;
            case 117:
                $this->json_output (array(
                    'err' => 117,
                    'msg' => '当前时间段已过，请提前一小时兑换',
                ));
                break;
            case 118:
                $this->json_output (array(
                    'err' => 118,
                    'msg' => '名额已完,下次请早',
                ));
                break;
            case 119:
                $this->json_output (array(
                    'err' => 119,
                    'msg' => '活动时间已过，请关注我们《塑料圈》公众号，参与更多活动',
                ));
                break;
            default:
                $this->json_output (array(
                    'err' => 999,
                    'msg' => '未知错误编码',
                ));
        }
    }

    /*
     * 二次发布
     */
    public function secondPub ()
    {
        if ($_POST) {
            $id = sget ('id', 'i');
            if (empty($id)) {
                $this->_errCode (6);
            }
            $this->checkAccount ();
            $where = " pur.sync = 6 and pur.id=$id ";
            $data  = M ("product:purchase")->getPurchaseLeftById ($where);
            if (empty($data['content'])) {
                if ($data['unit_price'] == 0.00 && empty($data['model']) && empty($data['f_name']) && empty($data['store_house'])) {
                    $this->json_output (array(
                        'err' => 1,
                        'msg' => '此记录输入有误，请手动补充',
                    ));
                }
                $data['f_type'] = 1;//格式化输出
            } elseif (!empty($data['content'])) {
                if ($data['unit_price'] == 0.00 || empty($data['model']) || empty($data['f_name']) || empty($data['store_house'])) {
                    $data['f_type'] = 2;//未格式化输出
                } else {
                    $data['f_type'] = 1;//格式化输出
                }
            }
            if (empty($data)) {
                $this->_errCode (2);
            }
            $this->_errCode (0, $data);
        }
        $this->_errCode (6);
    }

    //关注。粉丝的头像
    public function headPicture ()
    {
        if ($_POST) {
            $user_id = $this->checkAccount ();
            //我的粉丝
            $data   = M ('plasticzone:plasticIntroduction')->getMyFuns ($user_id, 1);
            $myfans = array();
            foreach ($data['data'] as $row) {
                $myfans[] = $row['user_id']['thumb'];
                if (count ($myfans) >= 9) {
                    break;
                }
            }
            //我的关注
            $data       = M ('plasticzone:plasticIntroduction')->getMyFuns ($user_id, 2);
            $myconcerns = array();
            foreach ($data['data'] as $row) {
                $myconcerns[] = $row['focused_id']['thumb'];
                if (count ($myconcerns) >= 9) {
                    break;
                }
            }
            $this->json_output (array(
                'err'        => 0,
                'myfans'     => $myfans,
                'myconcerns' => $myconcerns,
            ));
        }
        $this->_errCode (6);
    }

    /**
     * 获取证书
     */
    public function creditCertificate ()
    {
        $user_id = $this->checkAccount (0);
        $link_id = sget ('link_id', 'i');
        $fname   = sget ('fname', 's');
        $type    = sget ('type', 's');  //   1 精确  2 模糊
        $page    = sget ('page', 'i');
        //模糊查询没有了
        if (!empty($fname) && empty($link_id)) { //获取别人
            if (empty($type)) {
                $this->_errCode (6);
            }
            if ($type == 2 && empty($page)) {
                $this->_errCode (6);
            } //模糊查询没有page，报错
            $data = M ("qapp:plasticPersonalInfo")->getCompanyCredit ($fname, $type, $page);
            if (empty($data)) {
                $this->json_output (array(
                    'err' => 2,
                    'msg' => '没有此公司或此公司尚未被授信！',
                ));
            }
            $this->json_output (array(
                'err'  => 0,
                'data' => $data,
            ));
        }
        if (empty($link_id) || $link_id == 'undefined') {
            if ($user_id <= 0) {
                $this->_errCode (6);
            }//找不到用户
        } else {
            $user_id = $link_id;//获取微信用户分享人的id
        }
        //获取自己的
        $data = M ("qapp:plasticPersonalInfo")->getMyCredit ($user_id);
        if (empty($data)) {
            $this->json_output (array(
                'err' => 2,
                'msg' => '没有此公司或此公司尚未被授信！',
            ));
        }
        $this->json_output (array(
            'err'  => 0,
            'data' => $data,
        ));
    }


    /**
     * 加密的函数
     */
    public function getEncrypt ()
    {
        if ($_POST) {
            $encrypt_id = sget ('encrypt_id', 's');
            if (empty($encrypt_id)) {
                $this->_errCode (6);
            }
            $tmpStr = desEncrypt ($encrypt_id);
            $this->json_output (array(
                'err'  => 0,
                'data' => $tmpStr,
            ));
        }
        $this->_errCode (6);
    }


    /**
     * 解密函数
     */
    public function getDecrypt ()
    {
        if ($_POST) {
            $decrypt_id = sget ('decrypt_id', 's');
            if (empty($decrypt_id)) {
                $this->_errCode (6);
            }
            $tmpStr = desDecrypt ($decrypt_id);
            $this->json_output (array(
                'err'  => 0,
                'data' => $tmpStr,
            ));
        }
        $this->_errCode (6);
    }


    //脚本开启
    /*  public function start()
      {

          ob_implicit_flush();
          set_time_limit(0);

          for ($a = 0; $a < 32000; $a += 100) {
              $sql = "select p1.user_id,p2.mobile,p1.mobile_area from p2p_contact_info p1 join p2p_customer_contact p2 using(user_id) order by user_id desc limit $a,100";
              //echo $sql;
              echo $a;
              echo '<pre>';
              echo '<br />';
              echo '<br />';
              $rs=$this->db->getAll($sql);
              //var_dump($rs);exit;
              if (!$rs) break;
              foreach ($rs as $row) {
  //                echo '<pre>';
  //                var_dump($row);
  //                 echo '<br />';
  //                 echo '<br />';exit;
                  if (empty($row['mobile_area'])) {
                      $_info = file_get_contents("http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=" . $row['mobile']);
                      $_info = iconv("gbk", "UTF-8", $_info);
                      if (strstr($_info, 'carrier')) {
                          $tmp = substr($_info, strpos($_info, 'carrier') + 9, -6);
                          $sql1 = "update p2p_contact_info set mobile_area='$tmp' where user_id=" . $row['user_id'];
                          echo $sql1;
                          echo '<br />';
                          echo '<br />';
                          $this->db->query($sql1);
                          //showTrace();p(2);exit;
                      }
                      usleep(30);
                  }
              }
          }
      }
    */


    public function clearPic ($data)
    {
        if (empty($data['thumbqq'])) {
            if (strstr ($data['thumb'], 'http')) {
                $data['thumb'] = $data['thumb'];
            } else {
                if (empty($data['thumb'])) {
                    $data['thumb'] = "http://statics.myplas.com/upload/16/09/02/logos.jpg";
                } else {
                    $data['thumb'] = FILE_URL."/upload/".$data['thumb'];
                }
            }

        } else {
            $data['thumb'] = $data['thumbqq'];
        }

        return $data['thumb'];
    }


    /*
     * app更新消息
     */
    public function updateApp ()
    {
        $this->is_ajax = true;
        if ($_POST) {
            $user_id   = $this->checkAccount ();
            $appName   = "塑料圈app";//app名字
            $version   = sget ('version', 's');//版本号
            $sysType   = sget ('sysType', 'i');//1 anzhuo 2 ios
            $deviceNum = sget ('deviceNum', 's');//设备号
            $appupdate = M ('system:setting')->get ('appupdate');

            $sysVersion = $appupdate['version'];
            $serverFlag = $appupdate['serverFlag'];//是否显示公告
            $lastForce  = $appupdate['lastForce'];//是否强制更新
            if (empty($sysType)) {
                $this->_errCode (6);
            }
            if ($sysType == 1) {
                $updateUrl   = $appupdate['and_updateurl']; //apk下载地址
                $upgradeInfo = $appupdate['and_upgradeinfo'];//版本的更新描述
            } elseif ($sysType == 2) {
                $updateUrl   = $appupdate['ios_updateurl']; //apk下载地址
                $upgradeInfo = $appupdate['ios_upgradeinfo'];//版本的更新描述
            }

            $versionArray    = explode ('.', $version);
            $sysVersionArray = explode ('.', $sysVersion);


            if ($versionArray[0] > $sysVersionArray[0]) {
                $lastForce = "0";
            } elseif (($versionArray[0] == $sysVersionArray[0]) && ($versionArray[1] >= $sysVersionArray[1])) {
                $lastForce = "0";
            } elseif ($versionArray[1] == $sysVersionArray[1]) {
                if (isset($versionArray[2]) && isset($sysVersionArray[2])) {
                    if ($versionArray[2] >= $sysVersionArray[2]) {
                        $lastForce = "0";//是否强制更新
                    }
                }
            }

            $this->json_output (array(
                'err'  => 0,
                'data' => compact ('serverFlag', 'lastForce', 'updateUrl', 'upgradeInfo'),
            ));

        }
        $this->_errCode (6);
    }


    public function getSignPackage ()
    {
        $jsapiTicket = $this->getJsApiTicket ();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url      = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time ();
        $nonceStr  = $this->createNonceStr ();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1 ($string);

        $signPackage = array(
            "appId"     => $this->AppID,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string,
        );

        return $signPackage;
    }

    //获取票据
    protected function getJsApiTicket ()
    {
        $_key   = 'weixin_jsapi_ticket';
        $cache  = cache::startMemcache ();
        $ticket = $cache->get ($_key);
        if (empty($ticket)) {
            $access_token = $this->wx_get_token ();
            $url          = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token={$access_token}";
            $result       = json_decode ($this->http ($url), true);
            if (isset($result['ticket'])) {
                $ticket = $result['ticket'];
                $cache->set ($_key, $ticket, 7000);

                return $ticket;
            } else {
                return false;
            }
        } else {
            return $ticket;
        }
    }

    /**
     * 获取企查查的东西的接口东西
     */

    public function getQiChaCha ()
    {
        if ($_POST['token'] && $_POST['name']) {
            $user_id = $this->checkAccount ();
            $name    = sget ('name', 's');
            $name    = $this->clearStr ($name);
            if (empty($name)) {
                $this->_errCode (6);
            }
            $tmp = M ('qapp:customerBase')->selectOrNot ($name);
            $this->json_output ($tmp);
        }
        $this->_errCode (6);
    }




    /**
     *分享
     */
    //获取token
    protected function wx_get_token ()
    {
        $tokenFile = "access_token.txt";//缓存文件名
        $data      = json_decode (file_get_contents ($tokenFile), true);
        if ($data['expire_time'] < CORE_TIME) {
            $url          = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->AppID}&secret={$this->AppSecret}";
            $res          = json_decode ($this->http ($url), true);
            $access_token = $res['access_token'];
            if ($access_token) {
                $data['expire_time']  = CORE_TIME + 7000;
                $data['access_token'] = $access_token;
                $fp                   = fopen ($tokenFile, "w");
                fwrite ($fp, json_encode ($data));
                fclose ($fp);
            }

            return $access_token;
        } else {
            return $data['access_token'];
        }
    }

    //生成随机字符串
    protected function createNonceStr ($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr ($chars, mt_rand (0, strlen ($chars) - 1), 1);
        }

        return $str;
    }

    //获取票据
    protected function get_jsapi_ticket ()
    {
        $_key   = 'weixin_jsapi_ticket';
        $cache  = cache::startMemcache ();
        $ticket = $cache->get ($_key);
        if (empty($ticket)) {
            $access_token = $this->wx_get_token ();
            $url          = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token={$access_token}";
            $result       = json_decode ($this->http ($url), true);
            if (isset($result['ticket'])) {
                $ticket = $result['ticket'];
                $cache->set ($_key, $ticket, 7000);

                return $ticket;
            } else {
                return false;
            }
        } else {
            return $ticket;
        }
    }

    //格式化输出字符串
    protected function formatQueryParaMap ($paraMap, $urlencode = false)
    {
        $buff = "";
        ksort ($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v && "sign" != $k) {
                if ($urlencode) {
                    $v = urlencode ($v);
                }
                $buff .= $k."=".$v."&";
            }
        }
        //$reqPar;
        if (strlen ($buff) > 0) {
            $reqPar = substr ($buff, 0, strlen ($buff) - 1);
        }

        return $reqPar;
    }

    //获取url
    protected function get_url ()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url      = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        return $url;
    }


    public function janfly ()
    {
        function em_getallheaders ()
        {
            foreach ($_SERVER as $name => $value) {
                if (substr ($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace (' ', '-', ucwords (strtolower (str_replace ('_', ' ', substr ($name, 5)))))] = $value;
                }
            }

            return $headers;
        }

        echo '<pre>';
        //var_dump(em_getallheaders());
        //throw_exception("No support db:");
        //        $this->json_output(array('err'=>0,'data'=>em_getallheaders()));
        //http_status(404);
        //$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ?  $_SERVER['ALL_HTTP']: '';
        //        $_SERVER['HTTP_USER_AGENT'];
        //        echo '<pre>';
        //        var_dump($_SERVER['HTTP_USER_AGENT']);
        //        echo '<hr />';
        //        var_dump($_SERVER['ALL_HTTP']);
        var_dump (M ("system:block")->getBlock (3, 6));
    }


    public function getRegion ()
    {
        $pid  = sget ('id', 'i', 1);
        $_tmp = unserialize ($this->cache->get ('getqappRegion'.$pid));
        if (!empty($_tmp)) {
            $this->json_output (array(
                'err'  => 0,
                'data' => $_tmp,
            ));
        }
        $_tmp    = M ('system:region')->select ('id,pid,name')->where ('pid='.$pid)->getAll ();
        $_tmpRow = '';
        foreach ($_tmp as $key => &$row) {
            if ($row['name'] == '上海' && $row['pid'] == 1) {
                unset($row['pid']);
                $_tmpRow = $row;
                unset($_tmp[$key]);
            }
            unset($row['pid']);
        }
        if (!empty($_tmpRow)) {
            array_unshift ($_tmp, $_tmpRow);
        }
        $this->cache->set ('getqappRegion'.$pid, serialize ($_tmp), $this->randomMdTime);
        $this->json_output (array(
            'err'  => 0,
            'data' => $_tmp,
        ));
    }

    public function getModel ()
    {
        $keywords = sget ('keywords', 's');
        $keywords = strtoupper ($keywords);
        if (empty($keywords)) {
            $this->_errCode (6);
        }
        $_tmpModel = M ('qapp:product')->select ('model')->where ('status=1 and model like "%'.$keywords.'%"')
                                       ->limit (20)->getAll ();
        if (empty($_tmpModel)) {
            $this->_errCode (2);
        }
        $this->json_output (array(
            'err'  => 0,
            'data' => $_tmpModel,
        ));
    }

    /**
     * APP检查更新接口
     * @api {get} /api/qapi1_2/checkVersion APP检查更新接口
     * @apiVersion 3.0.0
     * @apiName  checkVersion
     * @apiGroup Common
     *
     * @apiParam   {String} version  3.0.1
     * @apiParam   {String} platform  ios/andriod/h5
     *
     * @apiSuccess {String}  msg   描述
     * @apiSuccess {String}  err   错误码
     * @apiSuccess {String}  url   apk下载地址
     *
     * @apiSuccessExample Success-Response:
     *      {
     *      "err":0
     *      "msg":"密码重置成功"
     *      }
     */
    public function checkVersion ()
    {
        $version  = sget ('version', 's');
        $platform = sget ('platform', 's');
        if (!in_array ($platform, array(
                'ios',
                'android',
                'weixin',
                'pc'
            )) || empty($version)
        ) {
            $this->json_output (array(
                'err' => 4,
                'msg' => '参数错误',
            ));
        }
        $version = explode ('.', $version);
        $version = array_splice ($version, 0, 3);
        if (count ($version) != 3) {
            $this->json_output (array(
                'err' => 2,
                'msg' => '不规范的版本格式，不予支持',
            ));
        }
        $settings        = M ('system:setting')->getSetting ();
        $newest_version0 = $settings['qapp_newest_version'];
        $newest_qapp_url = $settings['qapp_newest_url'];

        if (empty($newest_version0) || empty($newest_qapp_url)) {
            $this->json_output (array(
                'err' => 3,
                'msg' => '系统错误',
            ));
        }
        $newest_version = explode ('.', $newest_version0);

        if ($version[0] < $newest_version[0]) {
            $this->json_output (array(
                'err'         => 1,
                'msg'         => '当前版本已经停止支持，请迅速更新',
                'new_version' => $newest_version0,
                'url'         => FILE_URL.$newest_qapp_url[$platform],
            ));
        } elseif ($version[1] < $newest_version[1]) {
            $this->json_output (array(
                'err'         => 1,
                'msg'         => '当前版本已经停止支持，请迅速更新',
                'new_version' => $newest_version0,
                'url'         => FILE_URL.$newest_qapp_url[$platform],
            ));
        } elseif ($version[2] < $newest_version[2]) {

            $this->json_output (array(
                'err'         => 1,
                'msg'         => '当前版本已经停止支持，请迅速更新',
                'new_version' => $newest_version0,
                'url'         => FILE_URL.$newest_qapp_url[$platform],
            ));
        } else {
            $this->json_output (array(
                'err' => 0,
                'msg' => '当前版本是最新版本，棒棒哒',
            ));
        }

    }

    /* public function test()
     {
         $id= $_GET['id'];
         $contacts = $this->db->model('customer_contact')->where("c_id=" . $id)->getAll();
         var_dump($contacts);

         foreach ($contacts as $contact) {
             if(!empty($contact['mobile'])) {
                 $region = M("system:region")->get_system_region_by_phone($contact['mobile']);

                 if (!empty($region)) {
                     $this->db->model('customer')->where("c_id=" . $id)->update(array('china_area' => $region));
                     break;
                 }
             }
             if(!empty($contact['tel'])) {
                 $region = M("system:region")->get_system_region_by_phone($contact['tel']);
                 if (!empty($region)) {
                     $this->db->model('customer')->where("c_id=" . $id)->update(array('china_area' => $region));
                     echo 111;
                     var_dump($this->db->getLastSql());
                     break;
                 }
             }
         }
     }*/
    //    public function json_output ($result = array())
    //    {
    //        //header('Content-Type:text/html; charset=utf-8');
    //        header ('Content-type: application/json; charset=utf-8');
    //        $result       = json_encode ($result);
    //        $jsoncallback = sget ('jsoncallback');
    //        if (!empty($jsoncallback)) {
    //            $result = $jsoncallback."($result)";
    //        }
    //        echo $result;
    //        if ($this->debug || isset($_GET[C ('SHOW_DEBUG')])) {
    //            log::showTrace ();
    //        }
    //        die();
    //    }


}