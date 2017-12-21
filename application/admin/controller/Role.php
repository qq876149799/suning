<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

	/**
	* RBAC权限管理
	*/
	class Role extends controller
	{
		
		public function getIndex()
		{
			$data = Db::name('role')->select();
			return view('/admin-role',['data'=>$data]);
		}

		//添加角色
		public function getAdd()
		{
			$nodes = Db::name('node')->where('pid','eq','0')->select();
			$nodess = Db::name('node')->where('pid','neq','0')->select();

			return view('/admin_role_add',['node'=>$nodes,'nodes'=>$nodess]);
		}

		//执行添加角色
		public function postDoadd_role()
		{
			$request = request();
			$name = $request->param('name');
			$remark = $request->param('remark');
			//验证
			if (empty($name)) {
				$this->error('角色名称不能为空');
			}

			if ($request->param('qx/a')) {
				$qx = implode(',',$request->param('qx/a'));
			}else{
				$qx = '';
			}
			
			
				$data = [
					'name'=>$name,
					'remark'=>$remark
				];
				//先插入role表
				$res = Db::name('role')->insert($data);
				if($res){
					//角色插入成功  拿到id
					$rid = Db::name('role')->getLastInsID();
					//插入node_list表
					$datas = [
						'role_id'=>$rid,
						'nid'=>$qx
					];
					$ress = Db::name('node_list')->insert($datas);
					if ($ress) {
						$this->success('角色添加成功','/admin-role/index');
					}else{
						//失败删除role表中的数据
						Db::name('role')->where('id',$rid)->delete();
						$this->error('角色添加失败');
					}
				}else{
					$this->error('角色添加失败');
				}
		}

		//分配角色
		public function getRole_add()
		{
			$request = request();
			$id = $request->param('id');
			//查询所有角色
			$data = Db::name('role')->field('id,name')->select();
			//查询管理员信息
			$info = Db::name('admins')->where('id',$id)->field('id,username,role_id')->find();
			//将role_id转换为数组
			$info['role_id'] = explode(',',$info['role_id']);
			// var_dump($info);
			// var_dump($res);
			return view('/admin-role-add',['data'=>$data,'info'=>$info]);
		}

		//执行分配角色
		public function postDorole_add()
		{
			// return '执行分配角色';	
			$request = request();
			//处理role数组
			$roles = $request->post('role/a');
			$roles = implode(',',$roles);
			// var_dump($roles);exit;
			// var_dump($request->post(''));
			// 获取原角色id
			$oldinfo = Db::name('admins')->where('id',$request->post('id'))->field('role_id')->find();
			// var_dump($oldinfo);
			if ($oldinfo['role_id'] == $roles) {
				$this->success('数据无修改','/admin-index/index');
			}
			$data = [
				'id'=>$request->post('id'),
				'role_id'=>$roles
			];
			$res = Db::name('admins')->update($data);
			//更新role_list表
			$role_list = [
				'uid'=>$data['id'],
				'role_id'=>$roles
			];
			$res1 = Db::name('role_list')->where('uid',$data['id'])->update($role_list);
			if($res && $res1){
				$this->success('角色分配成功','/admin-index/index');
			}else{
				$this->error('角色分配失败','/admin-index/index');
			}
		}

		//分配权限
		public function getRole_grant()
		{
			$request = request();
			//拿到当前角色id
			$id = $request->param('id');
			//查询角色信息
			$res = Db::name('role')->field('name,id,remark')->where('id',$id)->find();
			// var_dump($res);
			//使用当前角色id查询现有权限
			$quanxian = Db::name('node_list')->where('role_id',$res['id'])->find();
			//拆成数组
			$qx = explode(',',$quanxian['nid']);
			// var_dump($qx);
			//查询现有权限表
			$nodes = Db::name('node')->where('pid','eq','0')->select();
			$nodess = Db::name('node')->where('pid','neq','0')->select();
			return view('/admin_role_grant',['info'=>$res,'node'=>$nodes,'nodes'=>$nodess,'qx'=>$qx]);
		}

		//执行分配权限
		public function postRole_grant()
		{
			$request = request();
			//判断值是否为空
			$name = $request->param('name');
			if (empty($name)) {
				$this->error('角色名称不能为空！');
			}
			//处理权限
			if ($request->param('qx/a')) {
				$qx = implode(',',$request->param('qx/a'));
			}else{
				$qx = '';
			}
			
			//role表
			$data = [
				'id'=>$request->param('id'),
				'name'=>$request->param('name'),
				'remark'=>$request->param('remark')
			];
			//查询原来的数据
			$oldinfo = Db::name('role')->where('id',$data['id'])->find();
			if ($oldinfo['name']!=$data['name'] || $oldinfo['remark']!=$data['remark']) {
				$res = Db::name('role')->update($data);
			}else{
				$res = true;
			}
			
			if ($res) {
				//role表修改成功
				$datas = [
					'role_id'=>$request->param('id'),
					'nid'=>$qx
				];
				//获取原有权限
				$oldinfo = Db::name('node_list')->where('role_id',$datas['role_id'])->find();
				if ($oldinfo['nid'] != $datas['nid']) {
					if (Db::name('node_list')->where('role_id',$datas['role_id'])->update($datas)) {
						$this->success('角色修改成功','/admin-role/index');
					}else{
						$this->error('角色修改失败');
					}
				}else{
					$this->success('角色修改成功','/admin-role/index');
				}
			}else{
				$this->error('角色修改失败');
			}	
		}

		//删除角色
		public function getDel_role()
		{
			$request = request();
			$rid = $request->param('id');
			//判断是否有管理员是这个角色
			$res = Db::name('admins')->where('role_id',$rid)->select();
			// var_dump($res);
			if (!empty($res)) {
				$this->error('该角色下还有管理员，无法删除');
			}else{
				$res = Db::name('role')->where('id',$rid)->delete();
				$res1 = Db::name('node_list')->where('role_id',$rid)->delete();
				if ($res && $res1){
					$this->success('删除角色成功','/admin-role/index');
				}
			}
		}

		//权限列表
		public function getPermission()
		{
			$data = Db::name('node')->where('pid','neq','0')->select();
			return view('/admin-permission',['data'=>$data]);
		}

		//添加权限
		public function getAdd_permission()
		{
			return view('/admin-add-permission');
		}

		//执行添加权限
		public function postDoadd_permission()
		{
			$request = request();
			$data = [
				'name'=>$request->param('name')
			];
			//判断
			if (empty($request->param('name'))) {
				$this->error('权限名称不能为空！');
			}else if(empty($request->param('url'))){
				$this->error('URL地址不能为空！');
			}else{
				$data['url'] = $request->param('url');
						//取最后一个/前的内容 （获取路由前缀）
						$str = substr($data['url'],0,strripos($data['url'],'/'));
						// var_dump($str);
						if ($str == '') {
							$data['pid']=0;
							// var_dump($data);exit;
							//添加父级
						}else{
							//查询pid
							$res = Db::name('node')->where('url','like','%'.$str.'%')->find();
							// var_dump($res['id']);
							$data['pid']=$res['id'];
							//不是添加父级
						}
			}
			// var_dump($data);
			if(Db::name('node')->insert($data)){
				$this->success('添加权限成功','/admin-role/permission');
			}else{
				$this->error('添加权限失败');
			}
		}

		//修改权限
		public function getEditpermission()
		{
			$request = request();
			$id = $request->param('id');
			//查询数据
			$data = Db::name('node')->where('id',$id)->find();
			// var_dump($data);
			return view('/admin-permission-edit',['data'=>$data]);
		}

		//执行修改权限
		public function postDoeditpermission()
		{
			$request = request();
			// var_dump($request->post(''));
			//判断是否为空
			$name = $request->post('name');
			$url = $request->post('url');

			if (empty($name)) {
				$this->error('权限名称不能为空！');
			}else if(empty($url)){
				$this->error('URL地址不能为空！');
			}
			$data = [
				'id'=>$request->post('id'),
				'name'=>$name,
				'url'=>$url
			];
			if(Db::name('node')->update($data)){
				$this->success('修改权限成功！','/admin-role/permission');
			}else{
				$this->error('修改权限失败！');
			}
		}
	}