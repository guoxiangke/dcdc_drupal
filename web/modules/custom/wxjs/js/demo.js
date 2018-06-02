(function($) {
  jQuery(document).ready(function ($) {
    wx.ready(function () {
      var shareData =  drupalSettings.wxjs.shareData;
      wx.onMenuShareTimeline({
		    title: shareData.title,
		    link: shareData.link,
		    imgUrl: shareData.imgUrl,
		    success: function () {
				  // alert('恭喜您获🉐️1积分');
				},
				cancel: function () {
				   alert('分享是一种美德～');
				}
			});
      wx.onMenuShareAppMessage({
				title: shareData.title,
				desc: shareData.desc,
				link: shareData.link,
				imgUrl: shareData.imgUrl,
				type: 'link', // 分享类型,music、video或link，不填默认为link
				dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
				success: function () {
				  // alert('恭喜您获🉐️1积分');
				},
				cancel: function () {
				  alert('分享是一种美德～');
				}
			});

    });
  });
}( jQuery ));
