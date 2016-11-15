<?php

class PingbaiduAction extends CommonContentAction {



	public function index(){
		$this->type = '生成sitemap'; 
		$this->display();
	}
	
	
	
	
	// 用法  PHP自动PING百度代码 让新网页收录更快

	// PingBaidu('网站名称','网站首页网址','新页面的网址','RSS订阅网址');  

	// 例如：PingBaidu('火端网络','http://www.huoduan.com','http://www.huoduan.com/pingbaidu.html','http://www.huoduan.com/feed');  

	function PingBaidu($sitename,$siteurl,$posturl,$rssurl) {  

		$url = 'http://ping.baidu.com/ping/RPC2';  

		$postvar = '  

			 <!--?xml version="1.0" encoding="UTF-8"?-->  

			<METHODCALL>  

			 <METHODNAME>weblogUpdates.extendedPing</METHODNAME>  

			 <PARAMS>  

			<PARAM /><VALUE><STRING>'.$sitename.'</STRING></VALUE>  

			<PARAM /><VALUE><STRING>'.$siteurl.'</STRING></VALUE>  

			<PARAM /><VALUE><STRING>'.$url.'</STRING></VALUE>  

			 <PARAM /><VALUE><STRING>'.$rssurl.'</STRING></VALUE>  

			</PARAMS>  

			</METHODCALL>';  

		$ch = curl_init();  

		$headers = array(  

			"POST ".$url." HTTP/1.0",  

			"Content-type: text/xml;charset=\"utf-8\"",  

			"Accept: text/xml",  

			"Content-length: ".strlen($postvar)  

			);  

		curl_setopt($ch, CURLOPT_URL, $url);  

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  

		curl_setopt($ch, CURLOPT_POST, 1);  

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  

		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvar);  

		$res = curl_exec ($ch);  

		curl_close ($ch);  

		if (strpos($res, "<INT>0</INT>")){  

			return true;  

		}else{  

			return false;  

		}  

	}  
	
	
}