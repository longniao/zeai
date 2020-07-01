<?php
header("Content-type: text/html; charset=utf-8");
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'buildermodel/AlipayTradeWapPayContentBuilder.php';
require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../config.php';
if (!empty($_POST['WIDout_trade_no'])&& trim($_POST['WIDout_trade_no'])!=""){
    $out_trade_no = $_POST['WIDout_trade_no'];//商户订单号
    $subject = $_POST['WIDsubject']; //订单名称
    $total_amount = $_POST['WIDtotal_amount']; //付款金额
    $body = $_POST['WIDbody']; //商品描述，可空
    $timeout_express="1m";  //超时时间
    $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
    $payRequestBuilder->setBody($body);
    $payRequestBuilder->setSubject($subject);
    $payRequestBuilder->setOutTradeNo($out_trade_no);
    $payRequestBuilder->setTotalAmount($total_amount);
    $payRequestBuilder->setTimeExpress($timeout_express);
    $payResponse = new AlipayTradeService($config);
    $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
    return ;
}
/*商户订单号<input id="WIDout_trade_no" name="WIDout_trade_no" />
订单名称<input id="WIDsubject" name="WIDsubject" />
付款金额<input id="WIDtotal_amount" name="WIDtotal_amount" />
商品描述：<input id="WIDbody" name="WIDbody" />
*/
?>