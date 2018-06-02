<?php
/**
 * Created by PhpStorm.
 * User: dale.guo
 * Date: 12/22/16
 * Time: 5:51 PM
 */
function bbn_audio_list(){
	$bbn = array();
	$bbn['401'] = array(
		'code'				=>	'C0SER',
		'day'					=>	7,//1-7
		'title'				=>	'中信之声',
		'description'	=>	'打开心锁，尽情倾诉，对你的日常生活必定带来很多的帮助和启发',
	);
//	$bbn['402'] = array(
//		'code'				=>	'C0SWS2',
//		'day'					=>	1,
//		'title'				=>	'主日崇拜（一）',
//		'description'	=>	'洛杉矶国语浸信会',
//	);
//	$bbn['403'] = array(
//		'code'				=>	'C0SWS4',
//		'day'					=>	1,
//		'title'				=>	'主日崇拜（二）',
//		'description'	=>	'美国马利兰中华圣经教会',
//	);
//	$bbn['404'] = array(
//		'code'				=>	'C0SBS',
//		'day'					=>	1,
//		'title'				=>	'主日查经',
//		'description'	=>	'黄子嘉牧师',
//	);
	$bbn['405'] = array(
		'code'				=>	'C0CBS',
		'day'					=>	5,
		'title'				=>	'儿童圣经故事',
		'description'	=>	'圣经广播网 “睡梦乡”故事系列是其中的节目。',
	);
	$bbn['406'] = array(
		'code'				=>	'C6BAW',
		'day'					=>	67,
		'title'				=>	'听听女人心',
		'description'	=>	'周六日播出',
	);
	$bbn['407'] = array(
		'code'				=>	'C0AS',
		'day'					=>	7,
		'title'				=>	'圣经导读',
		'description'	=>	'查经性广播节目，将全本新旧约圣经，逐卷的带领听众朋友查考，适合给基督徒和慕道友',
	);
//	$bbn['408'] = array(
//		'code'				=>	'C0BH',
//		'day'					=>	67,
//		'title'				=>	'圣经时光',
//		'description'	=>	'开路者国际传播协会',
//	);
//	$bbn['409'] = array(
//		'code'				=>	'C0BH',
//		'day'					=>	67,//1-7
//		'title'				=>	'圣经时光',
//		'description'	=>	'每周六播出近60分钟的讲座，并在星期天重播',
//	);
	$bbn['410'] = array(
		'code'				=>	'C0CP',
		'day'					=>	16,
		'title'				=>	'圣经讲坛',
		'description'	=>	'华人教会名牧的讲道专题系列，帮助听众灵命进深，信仰成长',
	);
	$bbn['411'] = array(
		'code'				=>	'C0BGM',
		'day'					=>	67,//6 7
		'title'				=>	'天人之间',
		'description'	=>	'由的节目，深入浅出地运用圣经真理来帮助基督徒灵命成长，也适合慕道友收听',
	);
	$bbn['412'] = array(
		'code'				=>	'C0HF',
		'day'					=>	5,//1-5
		'title'				=>	'天粮时光',
		'description'	=>	'康来昌 牧师',
	);
	$bbn['413'] = array(
		'code'				=>	'C0RLW',
		'day'					=>	5,//1-5
		'title'				=>	'安歇水边',
		'description'	=>	'希望国际之声; 卢彩秀 师母',
	);
	$bbn['414'] = array(
		'code'				=>	'C0FLS',
		'day'					=>	5,//1-5
		'title'				=>	'家庭生活讲座',
		'description'	=>	'圣经广播网编辑',
	);
	$bbn['415'] = array(
		'code'				=>	'C0FA',
		'day'					=>	67,
		'title'				=>	'家庭祭坛',
		'description'	=>	'圣经广播网编辑',
	);
//	$bbn['416'] = array(
//		'code'				=>	'C0BF',
//		'day'					=>	5,//1-5
//		'title'				=>	'幸福满家园',
//		'description'	=>	'远东广播公司',
//	);
	$bbn['417'] = array(
		'code'				=>	'C0WPC',
		'day'					=>	5,//1-5
		'title'				=>	'心底话',
		'description'	=>	'赵亮 牧师',
	);
//	$bbn['418'] = array(
//		'code'				=>	'C0JI',
//		'day'					=>	67,
//		'title'				=>	'快乐岛',
//		'description'	=>	'救恩之声广播中心，亚文阿姨',
//	);
//	$bbn['419'] = array(
//		'code'				=>	'C0RL',
//		'day'					=>	67,
//		'title'				=>	'更新生命讲座',
//		'description'	=>	'更新传道会,李定武 牧师',
//	);
	$bbn['420'] = array(
		'code'				=>	'C0WS',
		'day'					=>	1,
		'title'				=>	'每周一歌',
		'description'	=>	'圣经广播网 摩西',
	);
	$bbn['421'] = array(
		'code'				=>	'C0DW',
		'day'					=>	7,
		'title'				=>	'每日箴言',
		'description'	=>	'普世佳音机构',
	);
	$bbn['422'] = array(
		'code'				=>	'C0DBR',
		'day'					=>	7,
		'title'				=>	'每日读经',
		'description'	=>	'磐石有声事工',
	);
	$bbn['423'] = array(
		'code'				=>	'C0FI',
		'day'					=>	16,
		'title'				=>	'灵泉心语',
		'description'	=>	'开路者国际传播协会',
	);
//	$bbn['424'] = array(
//		'code'				=>	'C0IOS',
//		'day'					=>	67,
//		'title'				=>	'灵言蜜语',
//		'description'	=>	'圣经广播网',
//	);
	$bbn['425'] = array(
		'code'				=>	'C0PW',
		'day'					=>	16,
		'title'				=>	'牧者心声',
		'description'	=>	'李秀全牧师、唐崇荣牧师、周主培牧师、陈中明等多位牧师',
	);
	$bbn['426'] = array(
		'code'				=>	'C0MOL',
		'day'					=>	7,
		'title'				=>	'生命旋律',
		'description'	=>	'开路者国际传播协会',
	);
//	$bbn['427'] = array(
//		'code'				=>	'C0IFL',
//		'day'					=>	5,
//		'title'				=>	'生活的透视',
//		'description'	=>	'美国Insight for Living(Rev.Chuck Swindoll); 向天 牧师',
//	);
	$bbn['428'] = array(
		'code'				=>	'C0VOTL',
		'day'					=>	7,
		'title'				=>	'真光之声',
		'description'	=>	'庄祖鲲 牧师& 庄刘真光 师母',
	);
	$bbn['429'] = array(
		'code'				=>	'C0CM',
		'day'					=>	67,
		'title'				=>	'神奇创造',
		'description'	=>	'圣经广播网',
	);
	$bbn['430'] = array(
		'code'				=>	'C0PT',
		'day'					=>	7,
		'title'				=>	'祷告良辰',
		'description'	=>	'圣经广播网',
	);
	$bbn['431'] = array(
		'code'				=>	'C0EW',
		'day'					=>	5,
		'title'				=>	'英语世界',
		'description'	=>	'普世佳音机构, 郭文洁 主持',
	);
	$bbn['432'] = array(
		'code'				=>	'C0SD',
		'day'					=>	5,
		'title'				=>	'荒漠甘泉乐侣',
		'description'	=>	'圣经广播网 肖以勒',
	);
	$bbn['433'] = array(
		'code'				=>	'C0TTB',
		'day'					=>	5,
		'title'				=>	'认识圣经',
		'description'	=>	'Thru The Bible Radio',
	);
	$bbn['434'] = array(
		'code'				=>	'C0MGK',
		'day'					=>	7,
		'title'				=>	'讲道信息',
		'description'	=>	'普世佳音机构',
	);
	$bbn['435'] = array(
		'code'				=>	'C0WOP',
		'day'					=>	5,
		'title'				=>	'赞美之道',
		'description'	=>	'Dr. Donald R. Hubbard; 林在名',
	);
	return $bbn;
}

function mp_get_bbn($keyword){
	$bbns = bbn_audio_list();
	if(!isset($bbns[$keyword])) return false;
	$bbn = $bbns[$keyword];
	$url_pre = 'http://bbnradio.org.edgesuite.net/BBNOnDemand/chinese/';
	$weekday = date('N')-1;
	switch ($bbn['day']) {
		case '1':// only sunday!!
			$code =str_replace('0', '6', $bbn['code']);
			break;

		case '7':// every day!!
			$code =str_replace('0', $weekday, $bbn['code']);
			break;

		case '5':// work day
			if (date('N')>=6){
				$weekday = date('N')/2?0:1;
			}
			$code =str_replace('0', $weekday, $bbn['code']);
			break;

		case '67':// weekend
			if (date('N')<6){
				$weekday = date('N')/2?5:6;
			}
			$code =str_replace('0', $weekday, $bbn['code']);
			break;

		case '16':// weekend
			if (date('N')==7){
				$weekday = 3;
			}
			$code =str_replace('0', $weekday, $bbn['code']);
			break;
	}
	$res = [
			'keyword' => $keyword,
			// 'musicurl' =>$url_pre.$code.'.m4a?q='.date('ymd'),
			'title'=>$bbns[$keyword]['title']
		];
	return strpos("$code", "$weekday")===false?false:$res;
}

function bbn_audio_menu(){
	$rs = bbn_audio_list();
	$ss = '';
	foreach ($rs as $key => $value) {
		$ss .= '【'.$key.'】'.$value['title'].'('.$value['day'].')'."\r\n";
	}
	return "▶圣经广播网节目单
	-------------------------------------------
	$ss
	-------------------------------------------
	周日:1，周末:67，周间:5
	永不止息，需要有你[抱拳][调皮]";
}