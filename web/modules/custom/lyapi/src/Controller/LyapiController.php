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
use Drupal\taxonomy\Entity\Vocabulary;

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
	/**
	 * 自动更新播放日期编号！
	 */
	public function getJson(){
	  $vids = Vocabulary::loadMultiple();
	  foreach ($vids as $vid) {
	    // dsm($vid->label());
	    if ($vid->label() == '良友节目') {

	      $container = \Drupal::getContainer();
	      $terms = $container->get('entity.manager')->getStorage('taxonomy_term')->loadTree($vid->id(), 0, NULL, TRUE);
	      if (!empty($terms)) {
	        foreach($terms as $term) {
	          $code = $term->get('field_term_lylist_code')->value;
	          // dpm($code);
	          $data[$code]['day'] = $term->get('field_term_lylist_day')->value;
	          $data[$code]['index'] = $term->get('field_term_lylist_index')->value;
	          $data[$code]['ly_index'] = $term->get('field_term_lylist_lywx')->value;
	        }
	       return new JsonResponse(array_filter($data));
	      }
	      break;
	    }
	  }
	}
}
