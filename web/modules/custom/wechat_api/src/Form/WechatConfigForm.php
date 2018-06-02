<?php

namespace Drupal\wechat_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\UserInterface;

/**
 * Configure user settings for this site.
 */
class WechatConfigForm extends ConfigFormBase {

  /**
   * The account the shortcut set is for.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'wechat_api.settings',
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wechat.config';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, UserInterface $user = NULL)
  {
    $this->user = $user?$user:\Drupal::currentUser();

    $config = $this->configFactory->get('wechat_api.settings');
    $config = $config->get('mpaccount_'.$this->user->id());

    $form['settings'] = array(
      '#type' => 'details',
      '#title' => $this->t('Config'),
      '#open' => TRUE,
    );
    $form['settings']['appname'] = array(
      '#type' => 'textfield',
      '#title' => '公众号名称',
      '#description' => '必须和公众平台一致',
      '#default_value' => isset($config['appname'])?$config['appname']:'',
      // '#required' => true,
    );
    $desc = '请登陆<a target="_blank" href="https://mp.weixin.qq.com">微信官方公众平台</a> -->开发者中心 -->启用服务器配置-->修改配置-->填写完后<br/>再抄写到这里,提交后会得到URL地址。 URL（服务器地址）最后填写！';
    $form['settings']['appid'] = array(
      '#type' => 'textfield',
      '#title' => '开发者ID',
      '#description' => $desc,
      '#default_value' => isset($config['appid'])?$config['appid']:'',
      // '#required' => true,
    );
    $form['settings']['appsecret'] = array(
      '#type' => 'textfield',
      '#title' => 'AppSecret(应用密钥)',
      // '#description' => 'AppSecret(应用密钥)',
      '#default_value' => isset($config['appsecret'])?$config['appsecret']:'',
      // '#required' => true,
    );
    $form['settings']['token'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('WeChat Token'),
      '#attributes' => array('placeholder'=>'您可以在微信官网随意配置后copy过来！'),
      '#default_value' => isset($config['token'])?$config['token']:'',
      // '#required' => true,
    );
    $form['settings']['appaes'] = array(
      '#type' => 'textfield',
      '#title' => 'EncodingAESKey(消息加解密密钥)',
      // '#description' => t('Account EncodingAESKey'),
      '#default_value' => isset($config['appaes'])?$config['appaes']:'',
      // '#required' => true,
    );
    $form['settings']['is_certified'] = array(
      '#type' => 'checkbox',
      '#title' => '已认证',
      '#description' => '(如果您的公众号已认证，请勾选)',
      '#default_value' => isset($config['is_certified'])?$config['is_certified']:'',
    );
    $form['settings']['menus_tid'] = array(
        '#type' => 'textfield',
        '#title' => '公众号菜单根ID',
        '#default_value' => isset($config['menus_tid'])?$config['menus_tid']:'',
        '#required' => false,
        '#disabled' => true,
        '#states' => [
            'invisible' => [//认证才有!
                'input[name="is_certified"]' => ['checked' => FALSE],
            ],
        ],
    );
    $form['settings']['comments_tid'] = array(
        '#type' => 'textfield',
        '#title' => '公众号留言板根ID',
        '#default_value' => isset($config['comments_tid'])?$config['comments_tid']:'',
        '#required' => false,
        '#disabled' => true,
        '#states' => [
            'invisible' => [//认证才有!
                'input[name="is_certified"]' => ['checked' => FALSE],
            ],
        ],
    );
    $form['settings']['submit_settings'] = array(
        '#type' => 'submit',
        '#value' => '提交',
        '#submit' => ['::settingSubmit'],
    );


    if(isset($config['is_certified']) && $config['is_certified'])
    {
      $form['update_menu'] = array(
          '#type' => 'fieldset',
          '#title' => '更新菜单',
      );
      $form['update_menu']['submit_menu'] = array(
          '#type' => 'submit',
          '#value' => '点击更新',
          '#submit' => ['::wechat_admin_update_menu_submit'],
          '#attributes' => array('class'=>array('btn','btn-success')),
      );
      $form['update_menu']['delete_menu'] = array(
          '#type' => 'submit',
          '#value' => '删除菜单',
          '#submit' => ['::wechat_admin_delete_menu_submit'],
          '#attributes' => array('class'=>array('btn','btn-danger')),
      );
//      Link::createFromRoute($this->t('Simple Form'), 'fapi_example.simple_form'),
//      $form['update_menu']['kf'] = array(
//          '#markup' => l('多客服管理','CustomService/'.$user->uid,array('attributes'=>array('class'=>array('btn','btn-success')))),
//      );
    }


    // Gather information from hook_libraries_info() in enabled modules.
    $list = \Drupal::moduleHandler()->invokeAll('rescources_info');
    foreach($list as $properties){
      $options[$properties['name']] = $properties['desc'];
    }
    $form['rescources'] = array(
        '#type' => 'details',
        '#title' => '资源列表',
        '#open' => TRUE,
    );

    $form['rescources']['wechat_resources'] = array(
        '#type' => 'checkboxes',
        '#options' => $options,
        '#default_value' => isset($config['wechat_resources'])?$config['wechat_resources']:'',
        '#title' => '开启以下资源',
        '#description' => '用户回复【 】内的关键字，会得到相应的内容。',
    );
    $form['rescources']['resources_700'] = array(
        '#type' => 'textarea',
        '#title' => '支持喜马拉和雅荔枝FM,示例：XXX为专辑ID',
        '#default_value' => isset($config['resources_700'])?$config['resources_700']:'',
        '#required' => false,
        '#attributes' => array('placeholder'=>"http://www.lizhi.fm/api/radio_audios?band=XXXX&s=0&l=365|恩典365\nhttp://www.ximalaya.com/10586605/album/259292|喜乐的心"),
        '#states' => [
            'invisible' => [//认证才有!
                'input[name="wechat_resources[700]"]' => ['checked' => FALSE],
            ],
        ],
    );
    $form['rescources']['submit_rescources'] = array(
      '#type' => 'submit',
      '#value' => '更新资源',
      '#submit' => ['::rescourceSubmit'],
  );

    $form['default_reply'] = array(
        '#type' => 'details',
        '#title' => '自动回复',
        '#open' => TRUE,
    );
    $appname  = isset($config['appname'])?$config['appname']:'';
    $form['default_reply']['default_comment_text'] = array(
        '#type' => 'textfield',
        '#title' => '发布到评论留言版关键字',
        '#attributes' => array('placeholder'=>'默认@'.$appname),
        '#default_value' => isset($config['default_comment_text'])?$config['default_comment_text']:'@'.(isset($config['appname'])?$config['appname']:''),
        '#required' => false,
        '#states' => [
            'invisible' => [//认证才有!
                'input[name="is_certified"]' => ['checked' => FALSE],
            ],
        ],
    );
    $form['default_reply']['default_reply_type'] = array(
        '#type' => 'select',
        '#title' => '回复类型',
        '#options' => [
            'text' => '文本',
            'news' => '图文',
            'keyword' => '关键词响应资源',
        ],
        '#default_value' => isset($config['default_reply_type'])?$config['default_reply_type']:'',
        '#empty_option' => $this->t('-select-'),
    );
    $form['default_reply']['default_message_text'] = array(
        '#type' => 'textarea',
        '#title' => '自动回复信息',
        '#description' => '如果关键词无内容时，自动回复给用户的信息',
        '#default_value' => isset($config['default_message_text'])?$config['default_message_text']:'',
        '#required' => false,
        '#attributes' => array('placeholder'=>'您好，谢谢您的反馈！'),
        '#states' => [
            'visible' => [
                ':input[name="default_reply_type"]' => ['value' => 'text'],
            ],
        ],
    );
    $form['default_reply']['default_message_keyword'] = array(
        '#type' => 'textfield',
        '#title' => '',//自动回复资源
        '#description' => '必须填写已开启的资源关键词，如【se】炼爱季节',
        '#default_value' => isset($config['default_message_keyword'])?$config['default_message_keyword']:'',
        '#attributes' => array('placeholder'=>'se'),
        '#states' => [
            'visible' => [
                ':input[name="default_reply_type"]' => ['value' => 'keyword'],
            ],
        ],
    );
    $form['default_reply']['news_reply_count'] = array(
        '#type' => 'textfield',
        '#title' => '图文数量',
        '#description' => '默认回复您当日的？篇图文+置顶的图文？+资源菜单和消息尾部<>与自动回复资源互斥！',
        '#default_value' => isset($config['news_reply_count'])?$config['news_reply_count']:'',
        '#required' => false,
        '#attributes' => array('placeholder'=>"1:1:1"),
        '#states' => [
            'visible' => [
                ':input[name="default_reply_type"]' => ['value' => 'news'],
            ],
        ],
    );
    $form['default_reply']['submit_default_reply'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        '#submit' => ['::replySubmit'],
    );



    $form['default_message'] = array(
        '#type' => 'details',
        '#title' => $this->t('默认消息'),
        '#open' => TRUE,
    );
    $form['default_message']['quotes'] = array(
        '#type' => 'textarea',
        '#title' => '附加消息quotes',
        '#default_value' => isset($config['quotes'])?$config['quotes']:'',
        '#required' => false,
        '#attributes' => array('placeholder'=>"你知道吗？\r\n每行一个关键词"),
        '#states' => [
            'invisible' => [//认证才有!
                'input[name="is_certified"]' => ['checked' => FALSE],
            ],
        ],
    );
    $form['default_message']['kf_create_session'] = array(
        '#type' => 'textarea',
        '#title' => '客服转接关键词',
        '#default_value' => isset($config['kf_create_session'])?$config['kf_create_session']:'',
        '#required' => false,
        '#attributes' => array('placeholder'=>"我找客服\r\n每行一个关键词"),
        '#states' => [
            'invisible' => [//认证才有!
                'input[name="is_certified"]' => ['checked' => FALSE],
            ],
        ],
    );
    $form['default_message']['subscribe_message'] = array(
        '#type' => 'textarea',
        '#title' => '关注时自动回复信息',
        '#description' => '如果不设置回复图文或其他信息',
        '#default_value' => isset($config['subscribe_message'])?$config['subscribe_message']:'',
        '#required' => false,
        '#attributes' => array('placeholder'=>'您好，欢迎关注我们！'),
    );
    $form['default_message']['copyright_header'] = array(
        '#type' => 'textarea',
        '#title' => '附加消息头',
        '#default_value' => isset($config['copyright_header'])?$config['copyright_header']:'',
        '#required' => false,
        '#attributes' => array('placeholder'=>"本资源由<a href='http://wx.yongbuzhixi.com'>主内公众云</a>提供："),
    );
    $form['default_message']['copyright_footer'] = array(
        '#type' => 'textarea',
        '#title' => '附加消息尾',
        '#default_value' => isset($config['copyright_footer'])?$config['copyright_footer']:'',
        '#required' => false,
        '#attributes' => array('placeholder'=>"回复【】内容给我试试吧[调皮]"),
    );

    $form['default_message']['lts_menu1'] = array(
      '#type' => 'textarea',
      '#title' => 'lts#100菜单',
      '#default_value' => isset($config['lts_menu1'])?$config['lts_menu1']:'',
      '#required' => false,
      '#attributes' => array('placeholder'=>"每行一条，回车下一个"),
    );
    $form['default_message']['lts_menu2'] = array(
      '#type' => 'textarea',
      '#title' => 'lts#700菜单',
      '#default_value' => isset($config['lts_menu2'])?$config['lts_menu2']:'',
      '#required' => false,
      '#attributes' => array('placeholder'=>"每行一条，回车下一个"),
    );
//    //TODO::logic of blow!!!
//    $form['default_message']['news_reply'] = array(
//        '#type' => 'checkbox',
//        '#title' => '开启图文回复',
//        '#description' => '本功能与自动回复资源互斥！',
//        '#default_value' => $config['news_reply'],
//        '#required' => false,
//    );
    $form['default_message']['config_focus_url'] = array(
        '#type' => 'textfield',
        '#title' => '图文关注链接',
        '#description' => '比如关于我们，或引导关注公众号官方图文链接',
        '#default_value' => isset($config['config_focus_url'])?$config['config_focus_url']:'',
        '#attributes' => array('placeholder'=>'https://mp.weixin.qq.com/s/***'),
    );
    $form['default_message']['submit_default_message'] = array(
        '#type' => 'submit',
        '#value' => '更新',
        '#submit' => ['::messageSubmit'],
    );




    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => 'actions',
    ];
    return parent::buildForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate submitted form data.
  }

  /**
   * {@inheritdoc}
   */
  public function settingSubmit(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('wechat_api.settings');
    $config->set('mpaccount_'.$this->user->id().'.appname', $form_state->getValue('appname'))
        ->set('mpaccount_'.$this->user->id().'.appid', $form_state->getValue('appid'))
        ->set('mpaccount_'.$this->user->id().'.appsecret', $form_state->getValue('appsecret'))
        ->set('mpaccount_'.$this->user->id().'.token', $form_state->getValue('token'))
        ->set('mpaccount_'.$this->user->id().'.appaes', $form_state->getValue('appaes'))
        ->set('mpaccount_'.$this->user->id().'.is_certified', $form_state->getValue('is_certified'))
        ->save();
    global $base_url;
    $url = Url::fromRoute('wechat_api.mpresponse',['uid'=>$this->user->id()])->toString();
    drupal_set_message('设置成功！您的URL（服务器地址）是：' . $base_url . $url . ' 请拷贝并粘贴到公众平台官网上，选择一种任意加密方式，点击提交！', 'status', FALSE);
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function rescourceSubmit(array &$form, FormStateInterface $form_state) {
    $enable_res = array_filter($form_state->getValue('wechat_resources'));
    $config = $this->configFactory->getEditable('wechat_api.settings');
    $config->set('mpaccount_'.$this->user->id().'.wechat_resources',  $enable_res);

    $resources_700 = $form_state->getValue('resources_700');
    $config->set('mpaccount_'.$this->user->id().'.resources_700',  $resources_700);

    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function messageSubmit(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('wechat_api.settings');
    $config->set('mpaccount_'.$this->user->id().'.quotes', $form_state->getValue('quotes'));
    $config->set('mpaccount_'.$this->user->id().'.kf_create_session', $form_state->getValue('kf_create_session'));
    $config->set('mpaccount_'.$this->user->id().'.subscribe_message', $form_state->getValue('subscribe_message'));
    $config->set('mpaccount_'.$this->user->id().'.copyright_header', $form_state->getValue('copyright_header'));
    $config->set('mpaccount_'.$this->user->id().'.copyright_footer', $form_state->getValue('copyright_footer'));

    $config->set('mpaccount_'.$this->user->id().'.lts_menu1', $form_state->getValue('lts_menu1'));
    $config->set('mpaccount_'.$this->user->id().'.lts_menu2', $form_state->getValue('lts_menu2'));
//    $config->set('mpaccount_'.$this->user->id().'.news_reply', $form_state->getValue('news_reply'));
    $config->set('mpaccount_'.$this->user->id().'.config_focus_url', $form_state->getValue('config_focus_url'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function replySubmit(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('wechat_api.settings');
    $config->set('mpaccount_'.$this->user->id().'.default_reply_type', $form_state->getValue('default_reply_type'));
    $config->set('mpaccount_'.$this->user->id().'.default_comment_text', $form_state->getValue('default_comment_text'));
    $config->set('mpaccount_'.$this->user->id().'.default_message_text', $form_state->getValue('default_message_text'));
    $config->set('mpaccount_'.$this->user->id().'.default_message_keyword', $form_state->getValue('default_message_keyword'));
    $config->set('mpaccount_'.$this->user->id().'.news_reply_count', $form_state->getValue('news_reply_count'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('请分别点击每一个按钮设置!');
    $this->settingSubmit($form,$form_state);
  }
  /**
   * update wechat menu
   */
  public function wechat_admin_update_menu_submit(array &$form, FormStateInterface $form_state) {
    $uid = $this->user->id();
    $menu_arr = _mp_get_wechat_menu($uid);
    if(empty($menu_arr['button'])) {
      drupal_set_message('Empty WeChat Menu', 'error');
    }else {
      $weObj = _mp_service_init_wechat($uid);
      if($weObj->createMenu($menu_arr)) {
        drupal_set_message(t('Update menu success.'));
      }else {
        drupal_set_message($weObj->errMsg.'-'.$weObj->errCode, 'error');
      }
    }
    return parent::submitForm($form, $form_state);
  }
  public function wechat_admin_delete_menu_submit(array &$form, FormStateInterface $form_state) {
    $uid = $this->user->id();
    $weObj = _mp_service_init_wechat($uid);
    if($weObj->deleteMenu())
    {
      drupal_set_message(t('Delete menu success.'));
    }
    else{
      drupal_set_message($weObj->errMsg.'-'.$weObj->errCode, 'error');
    }
    return parent::submitForm($form, $form_state);
  }

}
