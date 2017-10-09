<?php
namespace app\admin\controller;

use \think\Db;
use \think\Reuquest;
class auth extends Main
{
    function index(){
        //获取权限列表
        $auth = Db::name('auth_rule')->order(['sort' => 'DESC', 'id' => 'ASC'])->select();
        $auth = array2Level($auth);
        return $this->fetch('index',['auth'=>$auth]);
    }
    function showAdd(){
    	$auth = Db::name('auth_rule')->order(['sort' => 'DESC', 'id' => 'ASC'])->select();
        $auth = array2Level($auth);
    	return  $this->fetch('add',['auth'=>$auth]);
    }
    function add(){
    	$post = $this->request->post();
    	$validate = validate('auth');
    	$res = $validate->check($post);
		if($res!==true){
			$this->error($validate->getError());
		}else{
			Db::name('auth_rule')
			->insert($post);
			$this->success('success');
		}
    }
    function showEdit(){
        $id  = $this->request->get('id');
        $pid = Db::name('auth_rule')
            ->where('id',$id)
            ->value('pid');
        if($pid!==0){
            $p_title = Db::name('auth_rule')
                ->where('id',$pid)
                ->value('title');
        }else{
            $p_title = '顶级菜单';
        }
        $this->assign('p_title',$p_title);
        $data  =   Db::name('auth_rule')
            ->where('id',$id)
            ->find();
        return  $this->fetch('edit',['data'=>$data]);
    }
    function edit(){
        $post =  $this->request->post();
        $id = $post['id'];
        $validate = validate('auth');
        $validate->scene('edit');
        $res = $validate->check($post);
        if($res!==true){
            $this->error($validate->getError());
        }else{
            unset($post['id']);
            Db::name('auth_rule')
            ->where('id',$id)
            ->update($post);
            $this->success('success');            
        }
    }
    function delete(){
        $id = $this->request->post('id');
        $juge = Db::name('auth_rule')
            ->where('pid',$id)
            ->find();
        if($id<5){
                 $this->error('重要节点无法删除'); 
        }
        if(!empty($juge)){ 
                $this->error('请先删除子权限'); 
        }else{

            Db::name('auth_rule')
            ->delete($id);
            $this->success('success');
        }
    }
    function showRole(){
        $role = Db::name('auth_group')
            ->order('id desc')
            ->paginate('15');
        $this->assign('role',$role);
        return $this->fetch('role');
    }
    function addRole(){
        $auth_group = $this->request->post('role_name');
        if(!empty($auth_group)){
            $res = Db::name('auth_group')
            ->where('title',$auth_group)
            ->find();
            if(empty($res)){
                 Db::name('auth_group')
                ->insert(['title'=>$auth_group]);
                $this->success('添加成功');
            }else{
                $this->error('系统中已经存在该用户名');  
            }
        }else{
            $this->error('请输入角色名称再添加');
        }
    }
    function showAuth($id){
        $this->assign('id',$id);
        return $this->fetch('auth');
    }
    //获取规则数据
    public function getJson()
    {   
        $id = $this->request->post('id');
        $auth_group_data = Db::name('auth_group')->find($id);
        $auth_rules      = explode(',', $auth_group_data['rules']);
        $auth_rule_list  = Db::name('auth_rule')->field('id,pid,title')->select();

        foreach ($auth_rule_list as $key => $value) {
            in_array($value['id'], $auth_rules) && $auth_rule_list[$key]['checked'] = true;
        }
        return $auth_rule_list;
    }
    /**
     * 更新权限组规则
     * @param $id
     * @param $auth_rule_ids
     */
    public function updateAuthGroupRule()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
                $group_data['id']    = $post['id'];
                $group_data['rules'] = is_array($post['auth_rule_ids']) ? implode(',', $post['auth_rule_ids']) : '';
                if (Db::name('auth_group')->where('id',$post['id'])->update($group_data) !== false) {
                    $this->success('授权成功');
                } else {
                    $this->error('授权失败');
                }
        }
    }
    function showRoleEdit($id){
        $data = Db::name('auth_group')
        ->where('id',$id)
        ->find();
        return $this->fetch('roleEdit',['data'=>$data]);
    }
    function editRole(){
        $post = $this->request->post();

        $validate = validate('role');
        $res = $validate->check($post);
        if(!$res){
            $this->error($validate->getError());
        }else{
            Db::name('auth_group')
            ->where('id',$post['id'])
            ->update(['title'=>$post['title'],'status'=>$post['status']]);
            $this->success('更新成功');
        }
    }
    function delRole(){
        $id  = $this->request->post('id');
        if($id!=='1'){
            $res =  Db::name('auth_group')
            ->delete($id);
            $this->success('删除成功');
        }else{
            $this->error('超级管理员无法删除');
        }
    }

}
