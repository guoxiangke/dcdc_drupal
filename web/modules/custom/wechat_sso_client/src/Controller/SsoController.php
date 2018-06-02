<?php
namespace Drupal\wechat_sso_client\Controller;
/**
 * Created by PhpStorm.
 * User: dale.guo
 * Date: 1/4/17
 * Time: 2:22 PM
 */
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;

class SsoController extends ControllerBase
{
	public function ssoResponse()
	{
		$openid = \Drupal::request()->query->get('openid');
		$access_token = \Drupal::request()->query->get('token');
    $destination = \Drupal::request()->query->get('dest');
		/* @var $account User */
		$account = user_load_by_name($openid);
		if (!$account) {
			$uid = 4;
			$weObj = _mp_service_init_wechat($uid);
			$user_info = $weObj->getOauthUserinfo($access_token, $openid);
			// Array
				// (
				//     [openid] => oTjEws6E1mOsB09ylSPTE7UxsNG4
				//     [nickname] => 神是我的避难所，是我的安慰
				//     [sex] => 0
				//     [language] => zh_CN
				//     [city] => 
				//     [province] => 
				//     [country] => 
				//     [headimgurl] => http://wx.qlogo.cn/mmopen/vi_32/AaJ9zOjFn4XuORBr24AzvoGNBLkOtZtaZOxL8HswiasUQPgUOsFO0Libk1cmyIY8CZJRHBce7umQQScVJZoqh4WA/132
				//     [privilege] => Array
				//         (
				//         )

				//     [unionid] => od0Q-xNSDY_x2jxxD4QTa859u5Gk
			// )
			$account = wechat_api_save_account($user_info);
			user_login_finalize($account);
			drupal_set_message('登录成功!','status');
			// \Drupal::logger('SSO login & create account')->notice($account->id().'login success');
		}else {
			user_login_finalize($account);
			// \Drupal::logger('SSO login')->notice($account->id().' : login success');
		}
    $url = Url::fromUri('internal:/'.$destination);
		return new RedirectResponse($url->toString());//Url::fromRoute('<front>')->toString()
	}
}
