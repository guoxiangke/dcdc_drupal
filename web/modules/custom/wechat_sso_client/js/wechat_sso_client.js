/**
 * Created by dale on 2017/1/25.
 */
(function ($, Drupal) {
    Drupal.behaviors.wechatssoclient = {
        attach: function (context, settings) {
            if(typeof(wx) != "undefined" && wx !== null){
                wx.ready(function () {//在微信手机里
                    if(!drupalSettings.user.uid){
                        // alert('即将跳转登录');
                        $('a[href="/user/login"]').click(function(e){
                            e.preventDefault();
                            var redirect_url = 'https://open.yongbuzhixi.com/user/wechat/login?sso=api&dest='+drupalSettings.path.currentPath;
                            window.location.replace(redirect_url);
                        })
                        if($('link[href="/taxonomy/term/13"]').length==1){//cc空中辅导自动登录
                            var redirect_url = 'https://open.yongbuzhixi.com/user/wechat/login?sso=api&dest='+drupalSettings.path.currentPath;
                            window.location.replace(redirect_url);
                        }
                    }else{
                        // if($('link[href="/taxonomy/term/13"]').length==1){
                            //cc空中辅导页面清理 
                            $('.breadcrumb').hide();
                            $('.field--name-comment-body textarea').attr('rows',2);
                        // }
                    }
                });
            }
        }
    };
})(jQuery, Drupal);