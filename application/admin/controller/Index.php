<?php
namespace app\admin\controller;

use think\Session;
use think\Controller;
use think\Db;

    /**
    * 后台Index控制器
    */
    class Index extends Allow
    {
        function getIndex()
        {
            $role_id = session('islogin.role_id');
            $role = min(explode(',',$role_id));
            $role = Db::name('role')->where('id',$role)->field('name')->find()['name'];
            //加载视图
            return view('/index',['role'=>$role]);
        }

        public function getWelcome()
        {
            
            //获取数据
            $info = array(
                'ip'=>$_SERVER['SERVER_ADDR'],
                'domain'=>$_SERVER['SERVER_NAME'],
                'port'=>$_SERVER['SERVER_PORT'],
                'ver'=>$_SERVER['SERVER_SOFTWARE'],
                'tp_ver'=>THINK_VERSION,
                'dir'=>$_SERVER['DOCUMENT_ROOT'],
                'os'=>PHP_OS,
                'time'=>date('Y-m-d H:i:s'),
                'session'=>session_id(),
                'sess_num'=>count(session(''))
            );
            return view('/welcome',$info);
        }
    }
