<?php
/*zeai.cn 2019*/
require 'mailer/PHPMailerAutoload.php';
require_once('mailer/class.phpmailer.php');
require_once("mailer/class.smtp.php");
if($_SMS['email_port'] == 465){
	$_SMS['email_SMTPAuth'] = true;
	$_SMS['email_SMTPSecure'] = 'ssl';
}else{
	$_SMS['email_SMTPAuth'] = false;
	$_SMS['email_SMTPSecure'] = '';
}
function sendemail($email,$title,$content){
	global $_ZEAI,$_SMS;
	if (ifemail($email)){
		$mail  = new PHPMailer();
		$mail->CharSet    ="UTF-8";                 //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置为 UTF-8
		$mail->IsSMTP();                            // 设定使用SMTP服务
		$mail->SMTPAuth   = $_SMS['email_SMTPAuth'];// 启用 SMTP 验证功能
		$mail->SMTPSecure = $_SMS['email_SMTPSecure'];// 启用SSL
		$mail->SMTPDebug  = $_SMS['email_debug'];//2
		$mail->Host       = $_SMS['email_smtp'];    // SMTP 服务器
		$mail->Port       = $_SMS['email_port'];   // SMTP服务器的端口号
		$mail->Username   = $_SMS['email_uid'];     // SMTP服务器用户名
		$mail->Password   = $_SMS['email_pwd'];     // SMTP服务器密码
		$mail->SetFrom($_SMS['email_email'],$_ZEAI['siteName']);// 设置发件人地址和名称
		//$mail->AddReplyTo("7144100@qq.com","7144100@qq.com");// 设置邮件回复人地址和名称
		$mail->Subject    = $title;// 设置邮件标题
		//$mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";
		$mail->MsgHTML($content);// 设置邮件内容
		$mail->AddAddress($email,$title);//发送给谁
		//$mail->AddAttachment("images/phpmailer.gif"); // 附件
		if(!$mail->Send()) {
			$content = "发送失败：".$mail->ErrorInfo;
			$chkflag = 0;
		} else {
			$content = "恭喜，邮件发送成功！";
			$chkflag = 1;
		}
		$retarr  = array('flag'=>$chkflag,'msg'=>$content);
		return $retarr;
	}
}