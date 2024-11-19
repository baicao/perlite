<?php
header('Content-Type: text/html; charset=utf-8'); // 设置内容类型为 UTF-8
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) .'/config.php';

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\SmsClient;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

function smslog($message_type, $message, $type = 'system') {
    static $log = null;
    if ($log === null) {
        $log = new Logger('sms');
        $logDir = dirname(__DIR__) . '/logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/sms_' . date('Y-m-d') . '.log';
        $stream = new StreamHandler($logFile, Logger::DEBUG);
        $formatter = new LineFormatter(null, null, false, true);
        $stream->setFormatter($formatter);
        $log->pushHandler($stream);
    }
    if ($message_type == "info") {
        $log->info($message);
    } else if ($message_type == "error") {
        $log->error($message);
    } else {
        $log->error($message);
    }
}

function handleErrorResponse($errorCode) {
    $errorMessages = [
        'FailedOperation.ContainSensitiveWord' => ['message' => '含有敏感词，请联系腾讯云短信小助手。', 'type' => 'user'],
        'FailedOperation.FailResolvePacket' => ['message' => '请求包解析失败，请检查请求。', 'type' => 'user'],
        'FailedOperation.InsufficientBalanceInSmsPackage' => ['message' => '短信额度不足，请购买套餐。', 'type' => 'user'],
        'FailedOperation.JsonParseFail' => ['message' => '解析 JSON 失败。', 'type' => 'system'],
        'FailedOperation.MarketingSendTimeConstraint' => ['message' => '营销短信只能在 8-22 点发送。', 'type' => 'user'],
        'FailedOperation.PhoneNumberInBlacklist' => ['message' => '手机号在免打扰名单中。', 'type' => 'user'],
        'FailedOperation.SignatureIncorrectOrUnapproved' => ['message' => '签名不正确或未审批。', 'type' => 'user'],
        'FailedOperation.TemplateIncorrectOrUnapproved' => ['message' => '模板不正确或未审批。', 'type' => 'user'],
        'FailedOperation.TemplateParamSetNotMatchApprovedTemplate' => ['message' => '请求内容与模板不匹配。', 'type' => 'user'],
        'FailedOperation.TemplateUnapprovedOrNotExist' => ['message' => '模板未审批或不存在。', 'type' => 'user'],
        'InternalError.OtherError' => ['message' => '内部错误，请联系腾讯云短信小助手。', 'type' => 'system'],
        'InternalError.RequestTimeException' => ['message' => '请求时间不正确，请检查服务器时间。', 'type' => 'system'],
        'InternalError.RestApiInterfaceNotExist' => ['message' => '请求的接口不存在。', 'type' => 'system'],
        'InternalError.SendAndRecvFail' => ['message' => '请求超时或网络问题。', 'type' => 'system'],
        'InternalError.SigFieldMissing' => ['message' => '缺少 Sig 字段。', 'type' => 'system'],
        'InternalError.SigVerificationFail' => ['message' => 'Sig 验证失败。', 'type' => 'system'],
        'InternalError.Timeout' => ['message' => '请求超时。', 'type' => 'system'],
        'InternalError.UnknownError' => ['message' => '未知错误。', 'type' => 'system'],
        'InvalidParameterValue.ContentLengthLimit' => ['message' => '短信内容过长。', 'type' => 'user'],
        'InvalidParameterValue.IncorrectPhoneNumber' => ['message' => '手机号格式不正确。', 'type' => 'user'],
        'InvalidParameterValue.ProhibitedUseUrlInTemplateParameter' => ['message' => '模板变量中禁止使用 URL。', 'type' => 'user'],
        'InvalidParameterValue.SdkAppIdNotExist' => ['message' => 'SdkAppId 不存在。', 'type' => 'user'],
        'InvalidParameterValue.TemplateParameterFormatError' => ['message' => '模板参数格式错误。', 'type' => 'user'],
        'InvalidParameterValue.TemplateParameterLengthLimit' => ['message' => '模板变量字符数超限。', 'type' => 'user'],
        'LimitExceeded.AppCountryOrRegionDailyLimit' => ['message' => '国家/地区短信日限额超出。', 'type' => 'user'],
        'LimitExceeded.AppCountryOrRegionInBlacklist' => ['message' => '国家/地区短信发送受限。', 'type' => 'user'],
        'LimitExceeded.AppDailyLimit' => ['message' => '短信日发送限额超出。', 'type' => 'user'],
        'LimitExceeded.AppGlobalDailyLimit' => ['message' => '国际短信日限额超出。', 'type' => 'user'],
        'LimitExceeded.AppMainlandChinaDailyLimit' => ['message' => '中国大陆短信日限额超出。', 'type' => 'user'],
        'LimitExceeded.DailyLimit' => ['message' => '国际短信日限额超出。', 'type' => 'user'],
        'LimitExceeded.DeliveryFrequencyLimit' => ['message' => '短信发送频率超限。', 'type' => 'user'],
        'LimitExceeded.PhoneNumberCountLimit' => ['message' => '手机号数量超过限制（200个）。', 'type' => 'user'],
        'LimitExceeded.PhoneNumberDailyLimit' => ['message' => '单个手机号短信日限额超出。', 'type' => 'user'],
        'LimitExceeded.PhoneNumberOneHourLimit' => ['message' => '单个手机号每小时短信限额超出。', 'type' => 'user'],
        'LimitExceeded.PhoneNumberSameContentDailyLimit' => ['message' => '单个手机号接收相同内容短信限额超出。', 'type' => 'user'],
        'LimitExceeded.PhoneNumberThirtySecondLimit' => ['message' => '单个手机号30秒内短信次数超限。', 'type' => 'user'],
        'MissingParameter.EmptyPhoneNumberSet' => ['message' => '号码列表为空。', 'type' => 'user'],
        'UnauthorizedOperation.IndividualUserMarketingSmsPermissionDeny' => ['message' => '个人用户无营销短信权限。', 'type' => 'user'],
        'UnauthorizedOperation.RequestIpNotInWhitelist' => ['message' => '请求 IP 不在白名单中。', 'type' => 'user'],
        'UnauthorizedOperation.RequestPermissionDeny' => ['message' => '请求缺少权限。', 'type' => 'user'],
        'UnauthorizedOperation.SdkAppIdIsDisabled' => ['message' => 'SdkAppId 被禁用。', 'type' => 'user'],
        'UnauthorizedOperation.ServiceSuspendDueToArrears' => ['message' => '服务因欠费暂停。', 'type' => 'user'],
        'UnauthorizedOperation.SmsSdkAppIdVerifyFail' => ['message' => 'SmsSdkAppId 验证失败。', 'type' => 'user'],
        'UnsupportedOperation' => ['message' => '操作不支持。', 'type' => 'user'],
        'UnsupportedOperation.ChineseMainlandTemplateToGlobalPhone' => ['message' => '国内模板不能用于国际号码。', 'type' => 'user'],
        'UnsupportedOperation.ContainDomesticAndInternationalPhoneNumber' => ['message' => '请求中包含国内和国际号码。', 'type' => 'user'],
        'UnsupportedOperation.GlobalTemplateToChineseMainlandPhone' => ['message' => '国际模板不能用于中国大陆号码。', 'type' => 'user'],
        'UnsupportedOperation.UnsupportedRegion' => ['message' => '该地区不支持发送短信。', 'type' => 'user'],
    ];

    $error = $errorMessages[$errorCode] ?? ['message' => '未知错误，请微信联系ByteSwan。', 'type' => 'user'];
    return $error;
}

function SendSMSVerifyCode($phone, $verify_code, $valid_minute=3, $requestId=null){
    if($requestId == null){
        $requestId = uniqid('req_', true); // 生成唯一请求 ID
    }
    
    $response = array('rs' => 0, 'message' => '', "request_id" =>$requestId);
    try {
        // 实例化一个认证对象，入参需要传入腾讯云账户 SecretId 和 SecretKey
        $cred = new Credential("AKIDjKtQ4AEGIF5vgLcghebULG4lKmgZFwd9", "Tqzub3bREsMeomZAbrvmTzIQN7f2CAuQ");
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("sms.tencentcloudapi.com");
    
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new SmsClient($cred, "ap-guangzhou", $clientProfile);
    
        $req = new SendSmsRequest();
    
        $params = array(
            "PhoneNumberSet" => array($phone),
            "SmsSdkAppId" => "1400946659",
            "TemplateId" => "2305812",
            "SignName" => "畅知享网",
            "TemplateParamSet" => array(strval($verify_code), strval($valid_minute))
        );
        $req->fromJsonString(json_encode($params));
        smslog("info", "[".$requestId."][REQ]".json_encode($params, JSON_UNESCAPED_UNICODE));
        
        // 返回的resp是一个SendSmsResponse的实例，与请求对象对应
        $resp = $client->SendSms($req);
    
        // 输出json格式的字符串回包
        smslog("info", "[".$requestId."][RES]".$resp->toJsonString());
        
        // 使用对象属性访问响应
        $sendStatusSet = $resp->getSendStatusSet();
        if (isset($sendStatusSet[0]) && isset($sendStatusSet[0]->Code)) {
            $code = $sendStatusSet[0]->Code; // 使用对象属性访问
            if ($code == "Ok") {
                $response["rs"] = 1;
                $response["message"] = "短信已发送到".$phone;
            } else {
                $error = handleErrorResponse($code);
                if ($error["type"] == "user") {
                    $response["rs"] = 0;
                    $response["message"] = $error["message"];
                } else {
                    $response["rs"] = 0;
                    $response["message"] = "未知错误，请微信联系ByteSwan";
                }
            }
        } else {
            $response["rs"] = 0;
            $response["message"] = "未知错误，请微信联系ByteSwan";
        }
    } catch (TencentCloudSDKException $e) {
        smslog("error", "[".$requestId."][ERR]".$e->getMessage());
        $response["rs"] = 0;
        $response["message"] = "发送短信失败，请微信联系ByteSwan";
    } catch (Exception $e) {
        smslog("error", "[".$requestId."][ERR]".$e->getMessage());
        $response["rs"] = 0;
        $response["message"] = "未知错误，请微信联系ByteSwan";
    }
    smslog("info", "[".$requestId."][RET]".json_encode($response, JSON_UNESCAPED_UNICODE));
    return $response;
}

// 处理发送验证码的请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
    $data = json_decode(file_get_contents('php://input'), true); // 解析 JSON 数据
    $response = array('rs' => 0, 'message' => '');
    log_message("[REQ]".json_encode($data, JSON_UNESCAPED_UNICODE));
    try {
        if (isset($data['phone_number']) && isset($data['country_code'])) {
            $phone_number = htmlspecialchars(trim($data['phone_number'])); // 使用 htmlspecialchars 和 trim
            $country_code = htmlspecialchars(trim($data['country_code'])); // 使用 htmlspecialchars 和 trim
            if (empty($phone_number) || empty($country_code)) {
                $response["rs"] = 0;
                $response["message"] = '手机号或者区域号为空';
                $response["error_code"] = -1;
            }else{
                // 检查手机号是否存在于用户表中
                $stmt = $app_conn->prepare("SELECT id FROM users WHERE phone_number = ? and country_code = ?");
                $stmt->bind_param("ss", $phone_number, $country_code);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows === 0) {
                    $response["rs"] = 1;
                    $response["message"] = '手机号不存在';
                    $response["error_code"] = -3;
                }else{
                    // 生成6位随机验证码
                    $verification_code = rand(100000, 999999);
                    log_message("Verification code: $country_code $phone_number $verification_code");
                    // 存储验证码到数据库
                    $send_phone = $country_code.$phone_number;
                    $stmt = $app_conn->prepare("INSERT INTO verification_codes (phone_number, code, create_time, expiry) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 3 MINUTE))");
                    $stmt->bind_param("si", $send_phone, $verification_code);
                    if ($stmt->execute()) {
                        // 发送验证码
                        $send_phone = $country_code.$phone_number;
                        $sms_response = SendSMSVerifyCode($send_phone, $verification_code);
                        if($sms_response["rs"] == 1){
                            $response["rs"] = 1;
                            $response["message"] = $sms_response["message"];
                        }else{
                            $response["rs"] = 0;
                            $response["message"] = $sms_response["message"];
                            $response["error_code"] = -4;
                        }
                    } else {
                        $response["rs"] = 0;
                        $response["message"] = '数据库错误';
                        $response["error_code"] = -5;
                    }
                }
                $stmt->close();
                $app_conn->close();
            }
        }else{
            $response["rs"] = 0;
            $response["message"] = '手机号或者区域号为空';
            $response["error_code"] = -1;
        }
    }catch(Exception $e) {
        log_message("Exception occurred: " . $e->getMessage());
        print_r($e); // 打印异常信息
        $response["rs"] = 0;
        $response["message"] = '未知错误，请微信联系ByteSwan';
        $response["error_code"] = -6;
    }
    log_message("[RES]".json_encode($response, JSON_UNESCAPED_UNICODE));
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

?>