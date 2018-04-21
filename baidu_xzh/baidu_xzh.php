<?php
/*
Plugin Name: 百度熊掌号自动推送
Version: 1.1
Plugin URL: http://qsh5.cn
Description: 自动向百度熊掌号提交文章链接，有利于熊掌号收录.基于EMLOG 6.0.1特别版美化
Author: 萧瑟
Author URL: http://qsh5.cn
*/
!defined('EMLOG_ROOT') && exit('access deined!');
function baidu_xzh_menu(){//添加导航
echo '<li><a href="./plugin.php?plugin=baidu_xzh">百度熊掌号提交</a></li>';
}
addAction('adm_sidebar_ext', 'baidu_xzh_menu');


function baidu_xzh_main($logid){//提交链接
	$logid_file = dirname(__FILE__).'/logid_log.txt';
	$logids = file_get_contents($logid_file);
	$logids_info = explode("|", $logids);
	$is_newlog = !in_array($logid, $logids_info);
	$log_model = new  Log_Model();
	$log = $log_model->getOneLogForAdmin($logid);
	include(EMLOG_ROOT.'/content/plugins/baidu_xzh/baidu_xzh_config.php');
	if($log['hide'] !== 'y' && $config["appid"] !== "" && $config["token"] !== "" && $config["type"] !== "" && $is_newlog ){
		$appid = $config["appid"];
		$token = $config["token"];
		$type = $config["type"];
		$url = Url::log($logid);
		$api = 'http://data.zz.baidu.com/urls?appid='.$appid.'&token='.$token.'&type='.$type;
		$ch = curl_init();
		$options =  array(
			CURLOPT_URL => $api,
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => $url,
			CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
			);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		$result = json_decode($result, true);
		if ($result) {
			$time = bdnowtime();
			$bdxzh_file = dirname(__FILE__).'/xzhbmit_log.txt';	

			if ($result['remain_realtime']) {
				$remain_realtime = "1";
			}else {
				$remain_realtime = "0||".$result["message"];
			}
			
			$handle = fopen($bdxzh_file,"a");
			fwrite($handle,"$remain_realtime||$time||$url\r\n");
			fclose($handle);

			$handle_logid = fopen($logid_file ,"a");
			fwrite($handle_logid,"$logid|");
			fclose($handle_logid);
		}
	}
	

}
addAction("save_log","baidu_xzh_main");

function bdnowtime($time = ''){
	date_default_timezone_set('Asia/Shanghai');
	if($time != ''){
		$date = date("Y-m-d H:i:s", $time);
	}else{
		$date = date("Y-m-d H:i:s");
	}
	return $date;  
}