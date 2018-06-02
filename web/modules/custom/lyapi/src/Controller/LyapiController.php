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

namespace Drupal\lyapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LyapiController extends ControllerBase {
	public function getResource($code) {
		$res = lyapi_wxresources_alter($code);
		return  new JsonResponse($res);
	}
	public function getMetaNid($tid=NULL,$date=NULL) {
		// Make sure you don't trust the URL to be safe! Always check for exploits.
		if (!is_numeric($tid) || !is_numeric($date)) {
			// We will just show a standard "access denied" page in this case.
			throw new AccessDeniedHttpException();
		}
		$entity_ids = lyapi_get_meta_nid($tid,$date);

		return new JsonResponse($entity_ids);
	}

	/**
	 * Cron get lymeta node from txly2.net/cc
	 * @return JsonResponse
	 */
	public function getMeta() {
		lyapi_get_meta();
		return new JsonResponse([]);
	}

	public function clearCache() {
		drupal_flush_all_caches();
		\Drupal::logger('Cron clearCache')->notice(date('Ymd H:i:s'));
		return new JsonResponse([]);
	}
}
