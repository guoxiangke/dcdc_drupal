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
//				$did_you_know = explode("\n",$did_you_know);
				$added = '';
//				if($account->uid==12){
//					//节目为啥变灰色了呢，是小永的病毒[TearingUp],小永可不会这高科技[Cry]，是微信最新版本功能的哦！[Twirl]
//					$add_more = $did_you_know[array_rand($did_you_know)];
//					$added .= "\n------------------------------\n您知道吗[疑问]".$add_more."[嘘]";
//				}
				$CustomMessage = $resources['custommessage'];
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
