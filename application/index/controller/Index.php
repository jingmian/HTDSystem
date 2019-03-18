<?php
namespace app\index\controller;

use app\common\controller\HomeBase;
use app\index\controller\Base;
use think\Cache;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Index extends HomeBase
{
    protected $site_config;

    public function _initialize()
    {
        parent::_initialize();
        if (CBOPEN == 2) {
            $this->redirect(url('bbs/index/index'));
        }

        $this->site_config = Cache::get('site_config');
    }

    public function index()
    {

        $user_id = session('home');
        // dump($user_id);
        // $upUid = Db::name('user')->where(['id'=>1])->setInc('balance', 1);
        // echo $upUid;die;
        // $bss = createWallet(1);
        // p($bss);die;

        // $directData = isEnjoyUser($uid=1,1);
        // if(is_post()){
        //     echo input('post.id/d');die;
        // }else{
        //     echo 2222;die;
        // }
        // p($directData['into_money']['value']);die;
        /* //签到榜 //投稿榜 自由打开？
        //$member = Db::name('user_sign')->alias('a')->join('user u', 'u.id=a.uid')->field('u.*,count(*) as forumnum')->group('a.uid')->order('forumnum desc')->limit(12)->select();
        $member = Db::name('article')->alias('f')->join('user u', 'u.id=f.uid')->field('u.*,count(*) as forumnum')->group('f.uid')->order('forumnum desc')->limit(12)->select();
        $this->assign('member', $member);

        //最近更新
        $article_new = Db::name('article')->alias('a')->join('user u', 'u.id=a.uid')->join('articlecate c', 'c.id=a.tid')->where('a.open', 1)->field('u.userhead,u.username,a.id,a.uid,a.title,a.time,c.template')->order('a.settop desc,a.time desc')->limit($this->site_config['c_home_newlist'])->select();
        $this->assign('article_new', $article_new);

        //分类展示 文字区
        $artbycatelist = Db::name('articlecate')->where('hometextshow=1')->select();
        foreach ($artbycatelist as $k => $v) {
            $artbycatelist[$k]['artlists'] = get_articles_by_cid($v['id'], $this->site_config['c_home_text']);
        }
        $this->assign('artbycatelist', $artbycatelist);
        //分类展示 图片区
        $article_pic = Db::name('articlecate')->where('homepicshow=1')->select();
        foreach ($article_pic as $k => $v) {

            $article_pic[$k]['artlists'] = get_articles_by_cid($v['id'], $this->site_config['c_home_pic']);
        }
        $this->assign('article_pic', $article_pic);

        //最近30天排行榜
        $maptop30['open'] = 1;

        $maptop30['time'] = array('egt', strtotime("-1 month"));
        $art_top30        = Db::name('article')->alias('a')->join('articlecate c', 'c.id=a.tid')->where($maptop30)->field('a.id,a.view,a.title,a.time,c.template')->order('view desc')->limit(10)->select();
        $this->assign('art_top30', $art_top30);

        if ($this->site_config['open_taoke'] == 0) {
            //站长推荐榜
            $mapchoice['open']   = 1;
            $mapchoice['choice'] = 1;
            //$mapchoice['a.tid']=1;
            $art_choice = Db::name('article')->alias('a')->join('articlecate c', 'c.id=a.tid')->where($mapchoice)->field('a.id,a.coverpic,a.view,a.title,a.time,c.template')->order('a.choice desc,a.time desc')->limit(6)->select();
            $this->assign('art_choice', $art_choice);
        } */

        return view();
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
    //提币
<<<<<<< HEAD

=======
>>>>>>> 169ea01267db91086df29bdf723c8593c9ed42fc
    public function present(){
        // if (!session('userid')) {
        //     return $this->error('亲！请先登陆', 'user/login/index');
        // }      
        // $userid = session('userid');
        $userid = 2;
        $list = Db::table('htd_user_wallet')
                ->alias('a')
                ->join('htd_currency c', 'c.id=a.cu_id')
                ->where('uid',$userid)
                ->select();  
        $this->assign('list',$list);
        $this->assign('uid',$userid);
        return $this->fetch();
     
        
    } 
    // 货币汇率
    public function exchange(){
              $data   = input();
              $result = Db::table('htd_currency')->where('id',$data['cu_id'])->value('price');
              $rmb    = $data['val']*$result;
              $base = new Base();
              if($result){
                $base->ajaxReturn(['status' => 1, 'msg' =>'数据获取成功', 'result' =>$rmb]);
              }else{
                $base->ajaxReturn(['status' => 0, 'msg' =>'数据获取失败', 'result' =>'']);
              }
              
    }

    // 提币
    public function pick(){
        $base = new Base();
        $data   = input();
        if(empty($data['number'])){
            $base->ajaxReturn(['status' => 2, 'msg' =>'请输入货币数量', 'result' =>'']);
        }
        $where  = array('uid'=>$data['uid'],'cu_id'=> $data['cu_id']);
        $where1 = [
            'uid' => $data['uid'],
            'cu_id' => $data['cu_id'],
            'cu_num' => $data['number']                 
        ];
        
        if($data['remain_num']<$data['number']){
            $base->ajaxReturn(['status' => 0, 'msg' =>'货币剩余少于输入值', 'result' =>'']);
        }else if($data['remain_num']<=50){
            $base->ajaxReturn(['status' => 0, 'msg' =>'货币大于50才能体现', 'result' =>'']);        
        }else{
            // 先计算剩余货币数量
            $data['remain_num'] = $data['remain_num']-$data['number'];
            // 更新钱包
            $res = Db::table('htd_user_wallet')->where($where)->update(['cu_num' => $data['remain_num']]);
            // 插入记录
            $user_extract = Db::table('htd_user_extract')->insert($where1);
            $base->ajaxReturn(['status' => 1, 'msg' =>'操作成功', 'result' =>'']);           
        }
    }

    //总收益
    public function totalrevenue()
    {
        return view();
    }
    
    //分享
    public function qrcode(){
        

        return view();
    }
	

    public function search()
    {
        $ks  = input('ks');
        $kss = urldecode(input('ks'));
        if (empty($ks) || $kss == ' ') {
            return $this->error('亲！你没有输入关键字');
        } else {
            $article      = Db::name('article');
            $open['open'] = 1;

            $map['f.title|f.keywords|f.description|f.content'] = ['like', "%{$kss}%"];

            $tptc = $article->alias('f')->join('articlecate c', 'c.id=f.tid')->join('user m', 'm.id=f.uid')->field('f.*,c.id as cid,m.id as userid,m.userhead,m.username,c.name,c.template')->order('f.id desc')->where($open)->where($map)->paginate(5, false, $config = ['query' => array('ks' => $ks)]);
            $this->assign('tptc', $tptc);
            return view();
        }
    }

    public function errors()
    {
        return view();
    }

    public function article()
    {
        $id = is_number(input('id')) ? input('id') : '';

        if (empty($id)) {
            return $this->error('亲！你迷路了', 'index/index/index');
        } else {
            $article = Db::name('article');
            $a       = $article->where('open', 1)->find($id);
            if ($a) {
                if ($a['outlink']) {
                    $this->success('正在跳转到外部页面', $a['outlink'], null, 1);
                }

                $article->where("id", $id)->setInc('view', 1);
                $t = $article->alias('a')->join('articlecate c', 'c.id=a.tid')->join('user m', 'm.id=a.uid')->field('a.*,c.id as cid,c.name,c.template,c.alias,m.id as userid,m.grades,m.point,m.userhead,m.username,m.status')->where('a.id', $id)->find();
                $this->assign('t', $t);
                //阅读排行
                $artphb = $article->where('tid', $t['tid'])->order('view desc')->limit($this->site_config['c_list_phb'])->select();
                $this->assign('artphb', $artphb);
                //文章推荐
                $choice['tid']    = $t['tid'];
                $choice['choice'] = 1;
                $artchoice        = $article->where($choice)->order('id desc')->limit($this->site_config['c_view_main'])->select();
                $this->assign('artchoice', $artchoice);

                //查询当前用户是否收藏该文章
                $iscollect  = 0;
                $commentzan = array();
                $uid        = session('userid');
                if ($uid) {
                    $collect = Db::name('collect')->where(array('uid' => $uid, 'sid' => $id, 'type' => 3))->find();
                    if ($collect) {
                        $iscollect = 1;
                    }
                    //查询用户点赞过的文章评论
                    $commentzan = Db::name('zan')->where(array('uid' => $uid, 'type' => 3))->column('sid');

                }
                //评论
                $tptc = Db::name('artcomment')->alias('c')->join('user m', 'm.id=c.uid')->where("fid = {$id}")->order('c.id asc')->field('c.*,m.id as userid,m.grades,m.attestation,m.point,m.userhead,m.username')->paginate(10, false, ['query' => Request::instance()->param()]);

                $this->assign('tptc', $tptc);

                $this->assign('iscollect', $iscollect);
                $this->assign('commentzan', $commentzan);

                return view();
            } else {
                return $this->error('亲！你迷路了', 'index/index/index');
            }
        }
    }
    public function soft()
    {
        $id = is_number(input('id')) ? input('id') : '';
        if (empty($id)) {
            return $this->error('亲！你迷路了', 'index/index/index');
        } else {
            $article = Db::name('article');
            $a       = $article->where('open', 1)->where("id = {$id}")->find();
            if ($a) {
                $article->where("id = {$id}")->setInc('view', 1);
                $t = $article->alias('a')->join('articlecate c', 'c.id=a.tid')->join('user m', 'm.id=a.uid')->field('a.*,c.id as cid,c.name,c.template,c.alias,m.id as userid,m.grades,m.point,m.userhead,m.username,m.sex,m.status')->where('a.id', $id)->find();
                //阅读排行
                $artphb = $article->where("tid = {$t['tid']}")->order('view desc')->limit($this->site_config['c_view_phb'])->select();
                $this->assign('artphb', $artphb);
                $this->assign('t', $t);

                //查询当前用户是否收藏该文章
                $iscollect  = 0;
                $commentzan = array();
                $uid        = session('userid');
                if ($uid) {
                    $collect = Db::name('collect')->where(array('uid' => $uid, 'sid' => $id, 'type' => 3))->find();
                    if ($collect) {
                        $iscollect = 1;
                    }
                    //查询用户点赞过的文章评论
                    $commentzan = Db::name('zan')->where(array('uid' => $uid, 'type' => 3))->column('sid');

                }
                //评论
                $tptc = Db::name('artcomment')->alias('c')->join('user m', 'm.id=c.uid')->where("fid = {$id}")->order('c.id asc')->field('c.*,m.id as userid,m.grades,m.attestation,m.point,m.userhead,m.username')->paginate(10, false, ['query' => Request::instance()->param()]);

                $this->assign('tptc', $tptc);
                $this->assign('iscollect', $iscollect);
                $this->assign('commentzan', $commentzan);
                return view();
            } else {
                return $this->error('亲！你迷路了', 'index/index/index');
            }
        }
    }
    public function page()
    {
        $id = is_number(input('id')) ? input('id') : '';
        if (empty($id)) {
            return $this->error('亲！你迷路了', 'index/index/index');
        } else {
            $article = Db::name('article');
            $a       = $article->where('open', 1)->where("id = {$id}")->find();
            if ($a) {
                $article->where("id = {$id}")->setInc('view', 1);
                $t = $article->alias('a')->join('articlecate c', 'c.id=a.tid')->join('user m', 'm.id=a.uid')->field('a.*,c.id as cid,c.name,c.template,c.alias,m.id as userid,m.grades,m.point,m.userhead,m.username,m.sex,m.status')->where('a.id', $id)->find();
               // print_r($t);
                $this->assign('t', $t);
            }

            return view();
        }
    }
}
