<?php
/**
 * @file
 * Contains \Drupal\wechat_api\Controller\WechatController.
 */

namespace Drupal\wechat_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Entity;
use Drupal\wechat_api\Plugin\Wechat\Wechat;
use Symfony\Component\HttpFoundation\JsonResponse;


class WechatController extends ControllerBase
{
	//调用此类 必须现有 $weObj
	protected $weObj;
	protected $openid;
	protected $account_name;//良友知音
	protected $is_certified;//良友知音
//	protected $account;
//User::load(1); //\Drupal\user\Entity\User::load(1);
	/**
	 * @param int $uid
	 * replace of __constructure
	 */
	private function __init($uid = 1)
	{
		$weObj = &drupal_static(__FUNCTION__);
		if (!isset($weObj)) {
			$config = \Drupal::config('wechat_api.settings')->get('mpaccount_'.$uid);
			$this->account_name = $config['appname'];
			$this->is_certified = $config['is_certified']?:0;
			$options = array(
				'account' => $uid,
				'token' => $config['token'],// \Drupal::config('wechat_api.settings')->get('mpaccount.token'),
				'encodingaeskey' => $config['appaes'],// \Drupal::config('wechat_api.settings')->get('mpaccount.appaes'),
				'appid' => $config['appid'],// \Drupal::config('wechat_api.settings')->get('mpaccount.appid'),
				'appsecret' => $config['appsecret'],// \Drupal::config('wechat_api.settings')->get('mpaccount.appsecret'),
			);
			$weObj= new Wechat($options);
		}
		$this->weObj = $weObj;
	}

	public function getWechatObj()
	{
		return $this->$weObj;
	}

	/**
	 * main()
	 * wechat mp service response enterpoint!
	 * @return [type] [description]
	 */
	public function mpResponse($uid=4)
	{
		$this->__init($uid);
		/* @var $weObj Wechat */
		$weObj = $this->weObj;
		$weObj->valid();

		//Always登记用户信息 TODO: CRM!
		if($this->is_certified){//认证了
			$this->openid = $weObj->getRev()->getRevFrom();
			$wx_user = user_load_by_name($this->openid);
			if (!$wx_user) {
				$user_info = $weObj->getUserInfo($this->openid);
				$wx_user = wechat_api_save_account($user_info);
			}
			if($wx_user)
				$weObj->setWxUid($wx_user->id());
		}else{
			$weObj->setWxUid(0);
		}

		$type = $weObj->getRev()->getRevType();
		$resources = $this->wechat_get_resources($type);
		switch ($resources['type']){
			case 'music':
				if(isset($resources['upyun_path'])){
					$upyun_token = $this->upyun_get_token($resources['upyun_path']);
					$resources['obj']['musicurl'] .= $upyun_token;
					$resources['obj']['hgmusicurl'] .= $upyun_token;
				}
                $desc = $resources['obj']['desc'];
				if($this->account_name!='永不止息')
				    $desc = str_replace('公众号:永不止息','公众号:'.$this->account_name,$desc);
				$weObj->music($resources['obj']['title'], $desc, $resources['obj']['musicurl'], $resources['obj']['hgmusicurl']);
				break;
			case 'text':
                $desc = $resources['obj']['text'];
                if($this->account_name!='永不止息')
                    $desc = str_replace('公众号:永不止息','公众号:'.$this->account_name,$desc);
				$weObj->text($desc);
				break;
			case 'kf_create_session'://TODO:::
				$weObj->transfer_customer_service();
				break;
			case 'news':
				$new = $resources['obj'];
				$weObj->news($new);
				break;
			case 'image':
				$cached_resources_keyword = 'wxresources_' .$weObj->uid.'_value_'. $resources['keyword'];
		    if ($cache = \Drupal::cache()->get($cached_resources_keyword)) {
		        $return = $cache->data;
		    } else {
		        set_time_limit(0);
	        	$return = $weObj->uploadMedia($resources['obj'],'image');
	        	if(isset($return['media_id']))
		        	\Drupal::cache()->set($cached_resources_keyword, $return, isset($resources['expire']) ? $resources['expire'] : -1);
		    }
	       //$return= {"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
        if(isset($return['media_id'])){
          $weObj->image($return['media_id'])->reply();
        }else{
          $weObj->text("活动火爆，系统繁忙，请再试一次！[握手]")->reply();
        }
				break;
			case 'ga':
				// do nothings only for google analytics
				break;
			default:
				\Drupal::logger('type:unknow!')->notice('<pre>'.print_r($resources,1));
		}

		if($this->is_certified){//TODO:认证的且开启附加消息回复!
			if(isset($resources['custommessage'])){
//                \Drupal::logger('$wxresources')->notice('<pre>'.var_export($resources,1));
//				$did_you_know = variable_get('wechat_add_message_'.$account->uid, "");
//
				$CustomMessage = $resources['custommessage'];
				if($uid==4 || $uid==10){
					$did_you_know = <<<EFO
[疑问]您知道吗[嘘]回复【600】可以选取更多您喜欢的节目
[疑问]您知道吗[嘘]点击▶️收听的意思是:点击🎵文件上面的▷
[疑问]您知道吗[嘘]不能后台收听说明您点击的三角不对哦
[疑问]您知道吗[嘘]【400】节目小永努力重构中
[疑问]您知道吗[嘘]改名【云彩助手】,您还认识我吗?
[疑问]您知道吗[嘘]原永不止息主内婚恋网改为永不止息了
[疑问]您知道吗[嘘]原永不止息改为【<a href="http://dwz.cn/NHKXuLmA">云彩助手</a>】了
创世记 9:13-14 我把虹放在云彩中，这就可作我与地立约的记号了。我使云彩盖地的时候，必有虹现在云彩中
马可福音 9:7 有一朵云彩来遮盖他们，也有声音从云彩里出来说：“这是我的爱子，你们要听他。”
希伯来书 12:1 我们既有这许多的见证人，如同云彩围着我们，就当放下各样的重担，脱去容易缠累我们的罪，存心忍耐，奔那摆在我们前头的路程，
启示录 10:1 我又看见另有一位大力的天使从天降下，披着云彩，头上有虹，脸面像日头，两脚像火柱。
出埃及记 40:34 当时，云彩遮盖会幕，耶和华的荣光就充满了帐幕。
出埃及记 40:36 每逢云彩从帐幕收上去，以色列人就起程前往；
出埃及记 40:37 云彩若不收上去，他们就不起程，直等到云彩收上去。
出埃及记 40:38 日间，耶和华的云彩是在帐幕以上；夜间，云中有火，在以色列全家的眼前，在他们所行的路上都是这样。
民数记 9:17 云彩几时从帐幕收上去，以色列人就几时起行；云彩在哪里停住，以色列人就在那里安营。
民数记 9:22 云彩停留在帐幕上，无论是两天，是一月，是一年，以色列人就住营不起行，但云彩收上去，他们就起行。
约伯记 7:9 云彩消散而过；照样，人下阴间也不再上来。
约伯记 36:28 云彩将雨落下，沛然降与世人。
约伯记 36:29 谁能明白云彩如何铺张，和神行宫的雷声呢？
约伯记 37:16 云彩如何浮于空中？那知识全备者奇妙的作为，你知道吗？
诗篇 78:14 他白日用云彩，终夜用火光引导他们。
[疑问]您知道吗[嘘]全新的赞赏功能悄然上线,小伙伴再也不用抱怨不能支付了,<a href="https://wechat.yongbuzhixi.com/donate">点击体验</a>
EFO;
					$did_you_know = explode("\n",$did_you_know);
					$CustomMessage .= "\n------------\n".$did_you_know[array_rand($did_you_know)]."\n永不止息，感恩有你";
				}
				$weObj->sendCustomMessage([
					"touser"=>$weObj->getRev()->getRevFrom(),
					"msgtype"=>'text',
					'text'=>array('content'=>$CustomMessage)
				]);
			}
		}
		if(isset($resources['ga_data']) && \Drupal::moduleHandler()->moduleExists('ga_push')){
			ga_push_add_event(array(
		      'eventCategory'        => $resources['ga_data']['category'],
		      'eventAction'          => $resources['ga_data']['action'],
		      'eventLabel'           => 'wxservice_'.$this->account_name,
		      // 'eventValue'           => 1,
		      // 'nonInteraction'       => FALSE,
		    ));
		}
		$weObj->reply();
		return new JsonResponse(NULL, 500);
	}


	/**
	 * _mp_service_process_text
	 * @return  wxresources
	 */
	private function wechat_get_resources($type = 'text')
	{
		$weObj = $this->weObj;
		/**
		 * case Wechat::MSGTYPE_TEXT:
		 * wxresources_text_alter
		 * wxresources_link_alter //收集文章!
		 * wxresources_event_alter
		 * wxresources_..._alter
		 * wxservice_default_alter
		 * TODO::使用hook机制,但只在Wechat_api中使用,其他module请勿调用!!!
		 */
		$resources = \Drupal::moduleHandler()->invokeAll('wxresources_' . $type . '_alter', array(&$weObj));
		if (!$resources) {
			$resources = \Drupal::moduleHandler()->invokeAll('wxresources_default_alter', array(&$weObj));
		}
		return $resources;
	}
	/**
	 * @param $path 图片相对路径
	 * @param int $etime 授权1分钟后过期
	 * @param string $key
	 * @return string token 防盗链密钥
	 */
	private function upyun_get_token($path, $etime = 86400, $key = 'ly729'){
		$etime = time()+$etime; // 授权1分钟后过期
		return '?_upt='. substr(md5($key.'&'.$etime.'&'.$path), 12,8).$etime;
	}
}
