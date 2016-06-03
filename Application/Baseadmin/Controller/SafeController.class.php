<?php
namespace Baseadmin\Controller;
use Think\Controller;
class SafeController extends Controller{
    
    public function _initialize(){
          
          $userInfo = \Org\Util\User::_getUserInfo();
          if(empty($userInfo)) {
              redirect(U('Home/Index/index'));
          }
    }
	public function index() {
        
		$result = D('pool')->getPoolInfo();
     	$this->assign("pool_data", $result['data']);
        $this->assign('time', $result['time']);
		$this->display();
	}

    public function addPool(){

    	$result = D("pool")->addPool();

    }

    public function getChoosePool(){

        $data = D("pool")->getChoosePool();
        $this->ajaxReturn($data);
    }

    public function deletePool(){
        if(IS_GET){
            //dump($_GET);exit();
            $pool_id = I('get.id',null);
            $result = M('pool')->where(array('pool_id'=>$pool_id))->delete();
            if($result){
                $this->success('删除成功', U('Baseadmin/Safe/index'));
                //redirect(U('Baseadmin/Safe/index'));
            }else{
                $this->error('删除失败');
            }
        }
        else{
            $this->error('删除失败');
        }
    }
}