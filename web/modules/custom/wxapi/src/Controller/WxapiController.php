<?php
/**
 * Created by PhpStorm.
 * User: dale.guo
 * Date: 11/29/16
 * Time: 10:04 AM
 */

/**
 * @file
 * Contains \Drupal\wechat_api\Controller\DemoController.
 */

namespace Drupal\wxapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\comment\CommentInterface;
use Drupal\comment\Entity\Comment;
use Drupal\Component\Utility\Unicode;

class WxapiController extends ControllerBase {
	public function getUid(Request $request) {
		$uid =0;
		if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
			$user_info['openid'] = $data['openid'];
			$user_info['province'] = $data['province'];
			$user_info['nickname'] = $data['nickName'];
			$user_info['language'] = $data['language'];
			$user_info['sex'] = $data['gender'];
			$user_info['country'] = $data['country'];
			$user_info['city'] = $data['city'];
			$user_info['headimgurl'] = $data['avatarUrl'];
			$user = user_load_by_name($user_info['openid']); //$users = \Drupal::entityTypeManager()->
			if(!$user){//create this wx user!
				$user = wechat_api_save_account($user_info,'wxuser');
			}else{//update profiles
	    	wechat_api_save_profile($user, $user_info);
			}
			$uid = $user->id();
    }
		return new JsonResponse([$uid]);
	}
	//创建一个grace node from fuyin.tv 0点执行
	// public function graceInit(){
	// 	return  new JsonResponse(['emptyrun']);
	// 	//TODO delete!!!
	// 	$url = 'https://m.fuyin.tv/movie/detail/movid/2784.html';
	// 	$html = str_get_html(file_get_contents($url));
 //    if (!$html) {
 //        \Drupal::logger(__FUNCTION__)->notice('error get m.fuyin.tv/');
 //        return new JsonResponse(['error get m.fuyin.tv']);
 //    }
 //    $res=[];
 //    $count=0;
 //    foreach($html->find('.am-padding-top-xs') as $element) { //init!!!
 //    	$count ++; if($count<(date('z')-20)) continue;
 //    // $element = $html->find('.am-padding-top-xs',-1);
 //    // {
	// 			$title = $element->find('.am-text-truncate',0)->plaintext;//第02天 20170102单独来面对神
	// 			$href = $element->find('a',0)->href;// /movie/player/movid/2784/urlid/45054.html
	// 			preg_match_all('/\d+/', $title,$matches);
	// 			$date = $matches[0][1]; //20170102
	// 			preg_match_all('/\d+/', $href,$matches);
	// 			$video_id = $matches[0][1];// 45054
	// 			//save node!!!
	// 			preg_match_all('/第\d+天 \d+(\S+)/',$title,$matches);
	// 			$title = $matches[1][0];// 45054
	// 			$time_str = implode('-',[substr($date, 0,4),substr($date, 4,2),substr($date, 6,2) ]) . ' 00:00:00';
	// 			$created = strtotime($time_str);

	// 			$fields = ['status'=>1,'created'=>$created];
	// 			$nid = tools_wxapi_get_nid($fields,'grace');
	// 			if($nid) {
	// 				$node = Node::load($nid);
	// 				if($node->getTitle() !== $title){
	// 					$node->set('title',$title);
	// 					$node->set('field_fytv_video_id',$video_id);
	// 					$node->save();
	// 					\Drupal::logger('wxapi_cron_init_fy')->notice('Grace %nid title udpated @title.',['%nid'=>$node->id(),'@title'=>$title]);
	// 				}
	// 		 		// \Drupal::logger($nid.$time_str)->notice($title);
	// 				if($time_str === date("Y-m-d 00:00:00")){//today!!!
	// 					$res[] = $node->id();
	// 				}
	// 			}else {
	//        	$newNode = [
	//           'type'             => 'grace',
	//           'created'          => $created,
	//           'changed'          => $created,
	//           'uid'              => 46, //恩典365基督之家 https://api.yongbuzhixi.com/user/46
	//           'title'            => $title,
	//           // An array with taxonomy terms.
	//           'field_fytv_video_id' => [$video_id],
	//           'body'             => [
	//               'summary' => '这里是经文',
	//               'value'   => '内容待填充',
	//               'format'  => 'full_html',
	//           ],
	//         ];
	//         $node = Node::create($newNode);
	//         $node->save();
	//         \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/grace/$date", "und");
	// 			  \Drupal::logger('wxapi_cron_init_fy')->notice('Grace %nid created @title.',['%nid'=>$node->id(),'@title'=>$title]);
	// 				$res[] = $node->id();
	// 		}
 //    }
	// 	return new JsonResponse($res);
	// }
	//cron every day!!!
	public function graceUpdate(){
		$url = 'http://www.hvsha-tpehoc.com/api/PortalIPS/vwInfoCategory/GetvwInfoByCategoryIDPage?CategoryID=0f4b33a2-be09-4ed5-80d3-c0fc58713680&page=1&pageSize=2';
		$str = file_get_contents($url);
		$str=json_decode($str);
    $res=[];
    $data = array_reverse($str->data);
		foreach ($data as $key => $item) {
			$time_str = $item->ShowTime; //2017-09-23 00:00:00
			$created = strtotime($time_str);
			$begin_time =  strtotime('2017-01-01 00:00:00');
			if($created<$begin_time) continue;//not 2016
			$date = date('Ymd',$created);
			$fields = ['status'=>1,'created'=>$created,'field_tags'=>369];//'uid'=>46,
			$nid = tools_wxapi_get_nid($fields,'article');
			if($nid){// load set save
				$res[] = $nid;
				// $node = Node::load($nid);
				// if($node->body->summary !==$item->Description){
				// 	$node->body->value = $item->ContentNoHTML;
				// 	$node->body->summary = $item->Description;
				// 	$node->save();
				// }
			}else{//create the node!!!
				$new_title = explode('》', $item->Title);
				if(isset($new_title[1])) {
					$new_title=$new_title[1];
				}else{
					$new_title=$item->Title;
				}
        $body = $item->Content;
        $body = preg_replace('/style="(.*?)"/', '', strip_tags($body,'<span><p><ul><li><ol><section>')) . '<p>' . $item->Description .'</p>';
        $vid = 14253+date('z')-339;//change by time & year!!!
				$newNode = [
          'type'             => 'article',
          'created'          => $created,
          'changed'          => $created,
          'uid'              => 46, //恩典365基督之家 https://api.yongbuzhixi.com/user/46
          'title'            => $new_title,
          // An array with taxonomy terms.
          'field_tags' =>[369],//恩典365
          'field_article_wechat_term' => [240],//恩典365基督之家 
          'field_upyun_path' =>  '/tmp/grace/'.date('Y/Ymd.\mp4'),
          'body'             => [
              'summary' => $item->Description,
              'value'   => $body,
              'format'  => 'full_html',
          ],
        ];
        $fuyintv_has_video_date = strtotime('2017-10-13 00:00:00');
        // http://xyjs.mov.cnfuyin.com/cdn/01%E7%A6%8F%E9%9F%B3%E8%A7%81%E8%AF%81/%E6%AF%8F%E6%97%A5%E7%81%B5%E4%BF%AE/%E6%81%A9%E5%85%B8365/2017/20171013ED.mp4
        if($fuyintv_has_video_date >=$created){
        	$field_video_url = 'http://xyjs.mov.cnfuyin.com/cdn/01%E7%A6%8F%E9%9F%B3%E8%A7%81%E8%AF%81/%E6%AF%8F%E6%97%A5%E7%81%B5%E4%BF%AE/%E6%81%A9%E5%85%B8365/2017/'.date('Ymd',$created).'ED.mp4';
       		$tmp_res = get_headers($field_video_url,TRUE); 
        	if(isset($tmp_res[0]) &&  strpos($tmp_res[0], '200' ) !== false){
        		$newNode['field_video_url'] = $field_video_url;
        	}
        }
        $node = Node::create($newNode);
				$node->save();
	      \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/grace365/$date", "und");
				\Drupal::logger('wxapi_cron_update_tp')->notice('Grace %nid created @title.',['%nid'=>$node->id(),'@title'=>$new_title]);
		    $res[]=['nid'=>$node->id(),'title'=>$newNode['title']];
			}
		}
		//每日亲近神
		$url = 'http://www.hvsha-tpehoc.com/api/PortalIPS/vwInfoCategory/GetvwInfoByCategoryIDPage?CategoryID=2ed87021-5ac1-4fe8-860c-efb88331e859&page=1&pageSize=1';
    $str = file_get_contents($url);
    $str=json_decode($str);
    $res=[];
    foreach ($str->data as $key => $item) {
      $time_str = $item->ShowTime; //2017-09-23 00:00:00
      $created = strtotime($time_str);

			$date = date('Ymd',$created);
			$fields = ['status'=>1,'created'=>$created,'field_tags'=>514];//'uid'=>46,
			$nid = tools_wxapi_get_nid($fields,'article');
      if(!$nid){//create the node!!!
        $new_title = explode('》', $item->Title);
        if(isset($new_title[1])) {
          $new_title=$new_title[1];
        }else{
          $new_title=$item->Title;
        }
        $body = $item->Content;
        $body = preg_replace('/style="(.*?)"/', '', $body);

        $newNode = [
          'type'             => 'article',
          'created'          => $created,
          'changed'          => $created,
          'uid'              => 46, //恩典365基督之家 https://api.yongbuzhixi.com/user/46
          'title'            => $new_title,
          // An array with taxonomy terms.
          // 514
          'field_tags' =>[514],
          'field_article_wechat_term' => [240],
          'field_article_audio'=>'http://www.hvsha-tpehoc.com'.$item->VideoUrl,
          'body'             => [
              'summary' => $item->Description,
              'value'   => $body,
              'format'  => 'full_html',
          ],
        ];
        $node = Node::create($newNode);
        $node->save();
        // \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/grace/$date", "und");
        \Drupal::logger('wxapi_cron_update_tp')->notice('Daily %nid created @title.',['%nid'=>$node->id(),'@title'=>$new_title]);
        // dpm($node->id(),$newNode);
		    $res[]=['nid'=>$node->id(),'title'=>$newNode['title']];
      }
      break;
    }
		//每日亲近神 end
		//inhim cron begin only for 2018!!!
	  if(date('Y')=='2018'){
	  	$today_id = date('z');//0-365 15
	    $status=0;
	    // if($i>$today_id) break;
	    $created = strtotime('2018-01-01 00:00:00');
	    $created += $today_id * 86400;
	    $date = date("md",$created);
	    // $fields = ['status'=>$status,'created'=>$created];//tid=514
			$fields = ['status'=>1,'created'=>$created,'field_tags'=>594];//'uid'=>46,
			$nid = tools_wxapi_get_nid($fields,'article');
      if(!$nid){
		    $newNode = [
		      'type'             => 'article',
		      'created'          => $created,
		      'changed'          => $created,
		      'uid'              => 268, // https://api.yongbuzhixi.com/user/268
		      'title'            => date('md在天父怀中',$created),
	        'body'             => [
	            'summary' => date('在天父怀中：n月d日 ',$created),
	            'value'   => date('在天父怀中：n月d日 ',$created).'暂无内容，欢迎听写，评论在下方，祝福他人，永不止息，感恩有你！',
	            'format'  => 'full_html',
	        ],
		      // An array with taxonomy terms.
		      'field_tags' =>[594],
		      'field_article_album_term' => [597],
		      'field_article_audio'=>'http://inhim.yongbuzhixi.com/2013Daily_13'.$date.'.mp3'
		      //'http://www.wxbible.net/wxapi/data/wxbible/audios/%E6%AF%8F%E6%97%A5%E7%81%B5%E4%BF%AE/%E5%9C%A8%E5%A4%A9%E7%88%B6%E6%80%80%E4%B8%AD/'.$date.'.mp3',
		    ];
		    $node = Node::create($newNode);
		    $node->save();
		    \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/inhim/$date", "und");
		    \Drupal::logger('wxapi_cron_update_inhim')->notice('Daily %nid created @title.',['%nid'=>$node->id(),'@title'=>$newNode['title']]);
		    $res[]=['nid'=>$node->id(),'title'=>$newNode['title']];
		    // dpm($node->id(),$newNode);
		    // break;
	    }else{
	    	//published it!!!
				$node = Node::load($nid);
				$node->status=1;
				$node->created = $node->created->value + 80400;
				$node->save();
	    }
	  }
		//inhim cron end
		return new JsonResponse($res);
	}
	public function getWxPost(Request $request){
		$data = array();
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $link = $data['link'];// http://mp.weixin.qq.com/s/NjVh2b8woG5Fng5lckJdgw
      $data = mp_getwxcontent($link);
    }
    $response['data'] = $data;
    return new JsonResponse($response);
	}
  /**
   * @param $id == nid == node->id()
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   * @see statistics.php &statistics_get()
   */
	public function getNodeStatistics($id,$uid){
		\Drupal::service('statistics.storage.node')->recordView($id); //+1
		$statistics = statistics_get($id);
		$counts = 0;
		if ($statistics) {
			$counts = $statistics['totalcount'];
		}
		//get statics && votes!
		return new JsonResponse([
			'statistics'=>$counts,
			'votes'=>is_voted($id,$uid)
		]);
	}
	public function setNodeUseful($id,$uid){
		return new JsonResponse(save_vote($id,$uid));
	}
	public function postComment(Request $request){
		$data = array();
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $comment = Comment::create([
                'uid'          => $data['uid'],
                'field_name'   => isset($data['field_name'])?$data['field_name']:'comment' ,// 'field_wechat_comments',
                'entity_type'  => isset($data['entity_type'])?$data['entity_type']:'node' ,//node
                'entity_id'    => $data['entity_id'],//nid
                'subject'      => Unicode::truncate( $data['comment_body'], '100', TRUE, TRUE),
                'comment_body' => array(
                    'value'  => $data['comment_body'],
                    'format' => 'plain_text',
                ),
                'status'       => CommentInterface::PUBLISHED,
            ]
        );
        $comment->save();
        \Drupal::logger(__FUNCTION__)->notice('comment->save:' . $comment->id());
        // $url = $comment->toUrl('canonical', array('absolute' => true))->toString();
      // $link = $data['link'];// http://mp.weixin.qq.com/s/NjVh2b8woG5Fng5lckJdgw
      // $data = mp_getwxcontent($link);

    	return new JsonResponse($comment->id());
    }
    return new JsonResponse($response);
	}

}
