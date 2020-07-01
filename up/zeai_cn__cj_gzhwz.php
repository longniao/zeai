<?php 
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/11/11 by supdes
*/
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
if($submitok=='add_zeai_cn__cj_gzhwz'){
	if (!ifint($uu) || str_len($pp) != 32)exit;
	$rowadm=$db->ROW(__TBL_ADMIN__,"username,kind,agentid,agenttitle","id=".$uu." AND password='$pp'",'name');
	if($rowadm){
		$session_uname      = $rowadm['username'];
		$session_kind       = $rowadm['kind'];
		$session_agentid    = $rowadm['agentid'];
		$session_agenttitle = $rowadm['agenttitle'];
	}else{
		json_exit(array(flag=>0,'msg'=>'forbidden'));
	}
	$zeaiurl=trimm($zeaiurl);
	if(str_len($zeaiurl)<10)json_exit(array('flag'=>0,'msg'=>'请输入PC端地址栏公众号文章url地址'));
	zeai_cn__cj_gzhwz($zeaiurl);
}
function zeai_cn__cj_gzhwz($zeaiurl) {
	global $db;
	$all=file_get_contents($zeaiurl);
	preg_match_all('/var msg_title = \"(.*?)\";/si',$all,$m);
	$title  = $m[1][0];
	preg_match_all('/var cdn_url_235_1 = \"(.*?)\";/si',$all,$m);
	$path_s = $m[1][0];
	$preg = "/<script[\s\S]*?<\/script>/i";
	$all = preg_replace($preg,"",$all);
	$all = preg_replace( "@<iframe(.*?)</iframe>@is", "", $all ); 
	$all = preg_replace( "@<style(.*?)</style>@is", "", $all ); 
	preg_match('/<div[^>]*id="js_content"[^>]*>(.*?) <\/div>/si',$all,$match);
	$content = $match[0];
	$content = zeai_cj_cleanhtml($content);
	//
	$dbdir  = 'p/news/'.date('Y').'/'.date('m').'/';
	mk_dir(ZEAI2.$dbdir);
	$path_s  = cj_save_pic($path_s,$dbdir);
	$content = cj_pregImg($content,$dbdir);
	$title   = dataIO($title,'in',200);
	$addtime = (empty($addtime))?ADDTIME:strtotime($addtime);
	$content = dataIO($content,'in');
	$db->query("INSERT INTO ".__TBL_NEWS__." (title,content,path_s,px,addtime) VALUES ('$title','$content','$path_s',".ADDTIME.",".ADDTIME.")");
	$fid = intval($db->insert_id());
	AddLog('【文章管理】公众号采集新文章入库->标题:'.$title);
	json_exit(array('flag'=>1,'msg'=>'入库成功','fid'=>$fid));
}
function cj_pregImg($str,$imgpath=''){
	global $_ZEAI;
	$out=cj_get_pic_arr($str);
    $res = array();
	if ($out) {
        $count = count($out[1]);
        for ($i=0; $i < $count; $i++) {
            $url = $out[1][$i];
			$newpic = cj_save_pic($url,$imgpath);
			if(!$newpic){
				$newpic='zeai.jpg';
			}
            $res[$i] = $newpic;
        }
    }
    if ($res) {
        $pattern = array();
        $replacement = array();
        for ($j=0; $j < count($res); $j++) { 
            //重新生成图片正则
           //$pattern[] = '#'.$out[1][$j].'#';
            //替换成的下载后的图片名称
            //$replacement[] = $res[$j];
			$str=str_replace($out[1][$j],$_ZEAI['up2'].'/'.$res[$j],$str);
        }
		$result=$str;
       // $result = preg_replace($pattern, $replacement, $str);
        if (!$result) {
            return false;
        }
    }else{
        return false;
    }
    return $result;
}

function cj_save_pic($url,$imgpath=''){
    if($url==''){return false;}
	$ext='.jpg';
	if ($imgpath) {
		$filename=$imgpath.time().mt_rand(0,10000).$ext;
	}else{
		$filename=time().mt_rand(0,10000).$ext;
	}
	$img=cj_getpic($url);
	if(strlen($img)>100){
		$fp2=@fopen($filename,'a');
		fwrite($fp2,$img);
		fclose($fp2);
		return $filename;
	}else{
		return false;
	}
}

function cj_getpic($url) {
	$host=parse_url($url);
	$host=$host["host"];
	$ch = curl_init();
	$httpheader = array(
	  'Host' => $host,
	  'Connection' => 'keep-alive',
	  'Pragma' => 'no-cache',
	  'Cache-Control' => 'no-cache',
	  'Accept' => 'textml,application/xhtml+xml,application/xml;q=0.9,image/webp,/;q=0.8',
	  'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36',
	  'Accept-Encoding' => 'gzip, deflate, sdch',
	  'Accept-Language' => 'zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4'
	);
	$options = array(
	  CURLOPT_HTTPHEADER => $httpheader,
	  CURLOPT_URL => $url,
	  CURLOPT_TIMEOUT => 10,
	  CURLOPT_FOLLOWLOCATION => 1,
	  CURLOPT_RETURNTRANSFER => true
	);
	curl_setopt_array( $ch , $options );
	$result = curl_exec( $ch );
	curl_close($ch);
	return $result;
}

function cj_get_pic_arr($str) {
	preg_match_all('/<img[^>]*? src="([^"]*?)"[^>]*?>/i',$str,$match);
	return $match;//0img,1src
}
?>