<?php
namespace app\index\controller;

use Captcha\Captcha;
use think\Config;
use think\Controller;
use think\Db;
use think\Session;
use app\index\controller\createWallet;
class Login extends Controller
{//登录成功通过session值判断，如果已经登录自动跳转主页
      public function index(){
          $home = session('home');
            // dump($home['id']);die;
            if(!empty($home['id'])){
                
				$url = "http://".$_SERVER ['HTTP_HOST']."/index/my/my";
			    header("refresh:1;url=$url");
			}else{
				return $this->fetch();
			}
    
    }
    //登录
   public function login()
    {
        $arr = $this->request->post();
        
       $arr['password'] = md5($arr['password']);
       $resa = DB::name('user')->where($arr['username'])->find();
       $res = DB::name('user')->where(['username'=>$arr['username'],'password'=>$arr['password']])->find();
       $resv=DB::name('user')->where($arr)->find();
       if($res&&$resv){
           
           Session::set('home',$res);
           setcookie("id",$res['id'],time()+60*10);
           $url = "http://".$_SERVER ['HTTP_HOST']."/index/my/my";
           header("refresh:1;url=$url");
           
           
       }else if($resa&&$res==false){
           
           $data=array('msg'=>'密码错误');
           $data=json_encode($data);
           echo $data;
           exit;
       }else if($resa){
           
           $data=array('msg'=>'账号已经存在');
           $data=json_encode($data);
           echo $data;
           
           exit;
       }
       
      
    }
//团队
	public function directDrive(){
  
      
        $res = DB::name('user')->where("id",Session::get('home')['id'])->find();
        if($res){
            $ress = DB::name('user')->where("pid",$res['id'])->select();
			$aas=json_encode($ress);
            $this->assign('aa', $aas);
        }else{
            
            $this->assign('aa',0);
            
        }
        return view();
    }

    public function register()
    {
        return $this->fetch();
    }
    //注册
    public function regis()
    {
        $arr = $this->request->post();
        $users=[
            'username' => $arr['username'],
            'usermail'=>$arr['usermail'],
            'mobile'=>$arr['mobile']
        ];

        $user = Db::name('user')->whereOr($users)->find();
        if ($user){
            echo "<script>history.go(-1);</script>";exit;
        }

        $users = Db::name('user')->where('promotion',$arr['promotion'])->find();
        if(!$users){
             echo "<script>history.go(-1);</script>";exit;
        }
        $arr['pid'] = $users['id'];
        $arr['userip'] = $_SERVER['REMOTE_ADDR'];
        $arr['password'] = md5($arr['password']);
        $arr['regtime'] = date('Y-m-d H:i:s',time());
        unset($arr['verify']);

        $res = DB::name('user')->insert($arr);
        if ($res){
//          createWallet($uid);
            return $this->fetch('index');
        } else {
            echo "<script>history.go(-1);</script>";
            exit;
        }
    }


    public function retrieve()
    {
        return $this->fetch();
    }



}