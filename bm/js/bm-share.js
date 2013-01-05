function share(u){
	var _t = encodeURI(document.title);
	var _url = encodeURI(document.location);
	var _site = '';//你的网站地址
	switch (u){
	   case "tencent":
		   var _u = 'http://v.t.qq.com/share/share.php?title='+_t+'&url='+_url+'&site='+_site;
	   break;
	   case "qzone":
		   var _u = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='+_url;
	   break;
	   case "sina":
		   var _u = 'http://v.t.sina.com.cn/share/share.php?title='+_t +'&url='+_url+'&source=bookmark';	
	   break;
	   case "wy":
		   var _u = 'http://t.163.com/article/user/checkLogin.do?link=http://news.163.com/&amp;source='+ encodeURIComponent('网易新闻')+ '&amp;info='+ _t + ' ' + _url+'&amp;'+new Date().getTime();
	   break;
	   case "douban":
		   var _u = "http://www.douban.com/recommend/?url=" + _url + "&title=" + _t;
	   break;
	   case "renren":
		   var _u = "http://share.xiaonei.com/share/buttonshare.do?link=" + _url + "&title=" + _t;
	   break;
	   default:
		   var _u = 'http://v.t.sina.com.cn/share/share.php?title='+_t +'&url='+_url+'&source=bookmark';	

	}
	var _imgs = jQuery('.post')[0].getElementsByTagName("img");
	if(_imgs.length != 0){
		for (var j = 0; j < _imgs.length; j++) {
		   _u += '&pic=' + encodeURIComponent(_imgs[j].src);
	   }
	}
	window.open( _u,'分享', 'width=550, height=330, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, location=yes, resizable=no, status=no' );
}