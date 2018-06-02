(function ($) {
  $(document).ready(function(){
    //br p delete!
    $('.field--name-body span').each(function(){
      if($(this).find('img').length>0) {
        return;
      }
      if($(this).text().length<=1)
        $(this).remove()
    })
    $('.field--name-body p').each(function(){
      if($(this).text().length<=1)
        $(this).remove()
    })
    $('.field--name-body section').each(function(){
      if($(this).find('img').length>0) {
        return;
      }
      if($(this).text().length<=2)
        $(this).remove()
    })
    //思麦不删除 br
    if('wxjs' in drupalSettings){
      if(!drupalSettings.wxjs.shareData.imgUrl.includes("Q3auHgzwzM5Up4E4jqUnvPB21niaiabNKDWzsCT4XeJDJ5tDTs0K6sCw"))
        $('.field--name-body p br').remove();
    }

    //add video iframe
    if('video' in drupalSettings.node){
      $('.content').prepend('<iframe frameborder="0" width="100%" height="250px" src="//flowplayer.cdn.bcebos.com/index.html?url='+drupalSettings.node.video+'" allowfullscreen></iframe>')
    }
    if('audio' in drupalSettings.node){
      $('.content').prepend('<iframe id="video_top_audio" frameborder="0" width="100%" height="72px" src="//waveplayer.cdn.bcebos.com/nocors.html?url='+drupalSettings.node.audio+'&tiny=0&auto=0" allowfullscreen></iframe>');
    }

  });
})(jQuery);
