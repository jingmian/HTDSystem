<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Db;
use Think\Page;

/**
 * 投资列表
 */
class Income extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function info()
    {
        $income_list = Db::name('buy_order')->alias('buy')
            ->join('htd_user u','buy.uid=u.id','left')
            ->join('htd_currency c','buy.cu_id=c.id','left')
            ->field('u.mobile, u.username, buy.*, c.name,c.alias_name,c.note')
            ->order('buy.id desc')->paginate(10);

        $this->assign('act','info');
        $this->assign('income_list',$income_list);
        return $this->fetch();
    }

    public function search($keyword = '',$act = '', $page = 1){

        $map = [];
        $act = input('act');
        if ($act == "info"){
            $type = array();
        }elseif ($act == "static_income"){
            $type = array('type'=>101);
        }elseif ($act == "push_income"){
            $type = array('type'=>102);
        }elseif($act == "dynamic_income"){
            $type = array('type'=>103);
        }
        $map = $type;

        if ($keyword) {
            session('userkeyword',$keyword);
            $map['mobile'] = ['like', "%{$keyword}%"];
        }else{
            if(session('userkeyword')!=''&&$page>1){
                $map['mobile'] = ['like', "%".session('userkeyword')."%"];

            }else{
                session('userkeyword',null);
            }
        }
        $user_list = Db::name('buy_order')->alias('buy')
            ->join('htd_user u','buy.uid=u.id','left')
            ->join('htd_income in','u.id=in.uid','left')
            ->join('htd_currency c','buy.cu_id=c.id','left')
            ->where($map)
            ->order('buy.id desc')->paginate(10);
//        dump($user_list);exit;
        $this->assign('act', $act);
        return $this->fetch('Income_'.$act,['income_list' => $user_list]);
    }

    public function static_income()
    {
//        $income_list = Db::name('income')->alias('in')
//            ->where('type',101)
//            ->join('htd_user u','in.uid=u.id')
//            ->field('in.*,u.username,u.mobile,u.flag')
//            ->order('in.uid desc')->select();
//        //循环合并数组
//        if($income_list){
//            $curr = Db::name('currency')->select();
//            foreach($income_list as $k1=>$v2){
//                foreach($curr as $k=>$v){
//                    if($v2['cu_id'] == $v['id']){
//                        $income_list[$k1]['alias_name'] = $v['alias_name'];
//                    }
//                }
//            }
//        }
        $income_list = Db::name('income')->alias('in')
            ->join('htd_user u','in.uid=u.id','left')
            ->join('htd_currency c','in.cu_id=c.id','left')
            ->field('u.mobile, u.username, in.*, c.name,c.alias_name,c.note')
            ->where('in.type',101)
            ->order('in.id desc')->paginate(10);
//        dump($income_list);

        $this->assign('act','static_income');
        return $this->fetch('Income_static_income',['income_list' => $income_list]);
    }

    public function dynamic_income()
    {
        $income_list = Db::name('income')->alias('in')
            ->join('htd_user u','in.uid=u.id','left')
            ->join('htd_currency c','in.cu_id=c.id','left')
            ->field('u.mobile, u.username, in.*, c.name,c.alias_name,c.note')
            ->where('in.type',103)
            ->order('in.id desc')->paginate(10);
        $this->assign('act','dynamic_income');

        return $this->fetch('Income_dynamic_income',['income_list' => $income_list]);
    }

    public function push_income()
    {
//
        $income_list = Db::name('income')->alias('in')
            ->join('htd_user u','in.uid=u.id','left')
            ->join('htd_currency c','in.cu_id=c.id','left')
            ->field('u.mobile, u.username, in.*, c.name,c.alias_name,c.note')
            ->where('in.type',102)
            ->order('in.id desc')->paginate(10);
        $this->assign('act','push_income');

        return $this->fetch('Income_push_income',['income_list' => $income_list]);
    }

    public function global_dividend()
    {

        return $this->fetch();
    }

    /**
     * 审核
     * @param $id
     * @return mixed
     */
    public function check($id)
    {
        $value = Db::name('buy_order')->where('id',$id)->value('is_check');
        if($value == 0){
            Db::name('buy_order')->update(['is_check' => 1 ,'id' => $id]);
            return json(array('code'=>200, 'msg'=>'审核通过'));
        }elseif ($value == 1){
            return json(array('code'=>200, 'msg'=>'已经审核通过了'));
        }else{
            return json(array('code'=>0,'msg'=>'未知错误'));
        }

    }

}
