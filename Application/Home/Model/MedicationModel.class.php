<?php
namespace Home\Model;
use Think\Model;
class MedicationModel extends Model{

	public function adds() {
        
    	if(IS_POST) {
           
      
           $userInfo = \Org\Util\User::_getUserInfo();
           $params = I("post.");
           // $params['medication_pool_id'] = $userInfo['member_pool_id'];
           $params['medication_member_id'] = $userInfo['member_id'];
           $params['medication_time'] = date('Y-m-d');

           if(! $this->add($params)) {
              return 0;
           }
            return 1;
      }
	}
    public function querys()
    {
        if (IS_GET) {
            $userInfo = \Org\Util\User::_getUserInfo();
            $member_id = $userInfo['member_id'];
            $query['medication_member_id']=$member_id;
            $params['medication_pool_id'] = I("get.medication_pool_id");
            $params['medication_cage_id'] = I('get.medication_cage_id');
            $params['medication_medicine_id'] = I('get.medication_medicine_id');
            if ($params['medication_pool_id'] != 0)
                $query['medication_pool_id'] = $params['medication_pool_id'];
            if ($params['medication_cage_id'] != 0)
                $query['medication_cage_id'] = $params['medication_cage_id'];
            if ($params['medication_medicine_id'] != 0)
                $query['medication_medicine_id'] = $params['medication_medicine_id'];
            /*if($params['stocking_cage_id']!=0)
                $query['stocking_cage_id']=$params['stocking_cage_id'];*/
            $data = $this->where($query)->select();
            foreach ($data as $key => $value) {
                $cage = $data[$key]['medication_cage_id'];
                if ($cage == null || $cage == 0) {
                    $data[$key]['medication_cage_id'] = '无网箱';
                } else {
                    $data[$key]['medication_cage_id'] = M('cage')->getFieldBycage_id($data[$key]['medication_cage_id'], 'cage_rowname');
                }
            }
            foreach ($data as $key => $value) {

                $data[$key]['medication_medicine_id'] = M('medicine')->getFieldBymedicine_id($data[$key]['medication_medicine_id'], 'medicine_name');
                $data[$key]['medication_pool_id'] = M('pool')->getFieldBypool_id($data[$key]['medication_pool_id'], 'pool_name');
                $data[$key]['medication_pool_img'] = C('PIC_UPLOADS') . $data[$key]['medication_pool_img'];
            }

            return $data;
        }
    }
	public function getChoose(){

       if(IS_GET) {
           $params = I('get.val');
           $data = $this->where("medication_id = $params")->find();
           //$data['medication_feed_id'] = M('feed')->getFieldByfeed_id($data['medication_feed_id'],'feed_name');
           //$data['medication_cage_id'] = M('cage')->getFieldBycage_id($data['medication_cage_id'],'cage_rowname');
           return $data;
       }else {
         echo 'getChoosePool wrong';
       }
    }
	public function get(){
       

	     $userInfo = \Org\Util\User::_getUserInfo();
       $member_id = $userInfo['member_id'];
       
       $count = $this->where("medication_member_id = $member_id")->count();

       $Page  = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(2)
       $Page->setConfig('prev',  '上一页');//上一页
       $Page->setConfig('next',  '下一页');//下一页
       $Page->setConfig('first', '<span aria-hidden="true">首页</span>');//第一页
       $Page->setConfig('last',  '<span aria-hidden="true">尾页</span>');//最后一页
       $Page->setConfig ( 'theme', '<li><a>当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
       $show = $Page->show();
       $data = $this->where("medication_member_id = $member_id")->limit($Page->firstRow.','.$Page->listRows)->order('medication_time desc')->select();
       foreach ($data as $key => $value) {
       	  $cage = $data[$key]['medication_cage_id'];
       	  if($cage == null || $cage == 0) {
       	  	 $data[$key]['medication_cage_id'] = '无网箱';
       	  }
           else{
               $data[$key]['medication_cage_id'] = M('cage')->getFieldBycage_id($data[$key]['medication_cage_id'],'cage_rowname');
           }
       }
       foreach ($data as $key => $value) {

            $data[$key]['medication_medicine_id'] = M('medicine')->getFieldBymedicine_id($data[$key]['medication_medicine_id'],'medicine_name');
            $data[$key]['medication_pool_id'] = M('pool')->getFieldBypool_id($data[$key]['medication_pool_id'],'pool_name');
            $data[$key]['medication_pool_img'] = C('PIC_UPLOADS').$data[$key]['medication_pool_img'];
       }
       $result['page'] = $show;
       $result['data'] = $data;

       return $result;
	}

  public function modify(){

      if(IS_POST) { 

           $data = I('post.');
           $id = $data['medication_id'];
           if(!($this->where("medication_id = $id")->save($data))) {
               echo ' wrong';
           }
      }else {
        echo 'wrong';
      }
    }

    public function show(){
        if(IS_POST){

            $medication_medicine_id= I("get.medication");
            $medication_pool_id= I("get.pool");
            $data = $this->where("medication_pool_id = $medication_pool_id and  medication_medicine_id= $medication_medicine_id")->limit(5)->order('medication_time desc')->select();
            foreach ($data as $key => $value) {
                $data[$key]['medication_medicine_id'] = M('medicine')->getFieldBymedicine_id($data[$key]['medication_medicine_id'],'medicine_name');
                $data[$key]['medication_pool_id'] = M('pool')->getFieldBypool_id($data[$key]['medication_pool_id'],'pool_name');

            }
            return $data;
            

        }
    }
    public function syMedication($cage){
        $data=$this->where("medication_cage_id = '{$cage}'")->order('medication_time desc')->select();
        foreach ($data as $key => $value) {
            $data[$key]['medication_cage_id'] = M('cage')->getFieldBycage_id($data[$key]['medication_cage_id'],'cage_rowname');
            $data[$key]['medication_medicine_id'] = M('medicine')->getFieldBymedicine_id($data[$key]['medication_medicine_id'],'medicine_name');
            $data[$key]['medication_pool_id'] = M('pool')->getFieldBypool_id($data[$key]['medication_pool_id'],'pool_name');
            $data[$key]['medication_pool_img'] = C('PIC_UPLOADS').$data[$key]['medication_pool_img'];
        }
        return $data;
    }

}