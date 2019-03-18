<?php
namespace app\index\controller;

use app\common\controller\HomeBase;
use think\Db;
use think\Request;
class Wallet extends HomeBase
{
    /**
     * 点击首页投资按钮进入获取数据
     * 获取当前用户投资的所有币种订单
    */
    public function wallet()
    {
        $user_id = 1;
        $users = $this->users($user_id);
        $user_wallet = $this->user_wallet($user_id);
        $htd_currency = $this->htd_currency();
        // dump($user_wallet);
        $this->assign('htd_currency',$htd_currency);
        $this->assign('user_wallet',$user_wallet);
        $this->assign('user',$users);
        return $this->fetch();
    }

    /**
     *  用户点击确定投资
     * 
    */
    public function confirmInvest()
    {

        if(!is_post()){
            return json(array('code' => 0, 'msg' => '提交方式错误'));
        }
        $param = input('post.');
        $cu_id = intval($param['cu_id']);
        if(!$cu_id){
            return json(array('code' => 0, 'msg' => '币种id不可为空'));
        }
        if(!$param['num']){
            return json(array('code' => 0, 'msg' => '币种数量不可为空'));
        }
        if(!$param['price']){
            return json(array('code' => 0, 'msg' => '币种价格不可为空'));
        }
        $total_money = $param['num']*$param['price']; // 
        if($total_money!=$param['total_money']){
            return json(array('code' => 0, 'msg' => '投资金额出错'));
        }
        p($param);
    }

    private function htd_currency()
    {
        $htd_currency = db("currency")->select();
        if($htd_currency){
            return $htd_currency;
        }
    }
    //获取用户币种列表
    private function user_wallet($user_id)
    {
        $user_wallet = db('user_wallet')->where(['uid'=>$user_id])->select();
        if($user_wallet){
            return $user_wallet;
        }
    }
    /**
    *获取用户信息
    */
    private function users($user_id){
        $field = "balance,id,pid,username,mobile,promotion,activation";
        $users = db('user')->field($field)->where(['id'=>$user_id])->find();
        return $users;
    }
}