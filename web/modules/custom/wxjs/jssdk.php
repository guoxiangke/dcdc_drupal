<?php
class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    global $base_url;//DRUPAL!
    $url = "$base_url$_SERVER[REQUEST_URI]";
    // \Drupal::logger('jssdk')->notice('URL:<pre>'.print_r($url,1));
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    
    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    // wxjsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例

    $ticket = $this->get_cache('wxjsapi_ticket');
    if (!$ticket) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      if (is_object($res) && isset($res->ticket)) {
        $this->set_cache('wxjsapi_ticket', $res->ticket);
      }else{
        \Drupal::logger('jssdk')->error('getJsApiTicket error:<pre>'.print_r($res,1));
      }
    }

    return $ticket;
  }

  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $access_token = $this->get_cache('wxjsapi_access_token');
    if (!$access_token) {
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      if (is_object($res) && isset($res->access_token)) {
        $access_token = $res->access_token;
        $this->set_cache('wxjsapi_access_token',$access_token);
      }else{
        \Drupal::logger('jssdk')->error('getAccessToken error:<pre>'.print_r($res,1));
      }
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

  private function get_cache($cache_key) {
    $value = false;
    if ($cache = \Drupal::cache()->get($cache_key)) {
      $value =  $cache->data;
    }
    return $value;
  }
  private function set_cache($cache_key,$cache_value) {
    // $config = \Drupal::service('config.factory')->getEditable('wxjs.settings')->delete();
    \Drupal::cache()->set($cache_key, $cache_value, REQUEST_TIME + 7100, array('config:wxjs.jssdk'));
  }
}

// //0 return TRUE or false
// //1 return string
// function d8_dale_ishttps($get_string=0){
//     $http_forwarded_proto = 'http';
//     if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) $http_forwarded_proto = $_SERVER['HTTP_X_FORWARDED_PROTO'];
//     $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 || $http_forwarded_proto == 'https') ? "https://" : "http://";
//     return $get_string?$protocol:($protocol == 'https://'?true:false);
// }