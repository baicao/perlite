<?php
require_once __DIR__ . '/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$servername = "localhost";
$ip_list = get_client_ip();
$private_ip = $ip_list[0];
$public_ip = $ip_list[1];
if($private_ip == "127.0.0.1"){
    $username = "root";
    $password = "Wxy2025qwe";
    define('SITE_URL', 'http://'.$public_ip.'/');
    
}else{
    $username = "changedu";
    $password = "Wxy2025qwe";
    define('SITE_URL', 'https://'.$public_ip.'/');
}
// 连接到应用数据库
$app_dbname = "changedu";
$app_conn = new mysqli($servername, $username, $password, $app_dbname);
if ($app_conn->connect_error) {
    die("连接失败: " . $app_conn->connect_error);
}
define('ERROR_PAGE', 'error/server_error.php');

// 生成 sessionid
if (empty($_SESSION['unique_id'])) {
    // 生成 sessionID
    $sessionId = generateSessionId();
    $_SESSION['unique_id'] = $sessionId;
    // 获取或创建 device ID
    $deviceId = getOrCreateDeviceId();
    // 获取用户设备信息
    $deviceInfo = getUserDevice();
    log_message("Device id is $deviceId, Device info is ".json_encode($deviceInfo));
}




define('SITE_TITLE', 'Chang Edu'); // 替换为您的实际域名

// 邮件设置
define('EMAIL_FROM', 'ChangEdu'); // 替换为您的发件邮箱地址

// SMTP设置
define('SMTP_HOST', 'smtp.qq.com'); // QQ邮箱的SMTP服务器
define('SMTP_PORT', 465); // QQ邮箱的SMTP端口
define('SMTP_USERNAME', '909019241@qq.com'); // 您的QQ邮箱地址
define('SMTP_PASSWORD', 'kergxquzhkzebdbe'); // 您的QQ邮箱SMTP授权码

function get_client_ip() {
    $private_ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // 来自共享互联网的 IP
        $private_ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // 检查通过代理服务器的 IP
        $private_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // 直接访问的 IP
        $private_ip = $_SERVER['REMOTE_ADDR'];
    }
    if($private_ip == "127.0.0.1"){
        $public_ip = "localhost:8080";
    }else{
        $public_ip = "changedu.com.cn";
    }

    return [$private_ip, $public_ip];
}

function log_message($message) {
    static $log = null;
    if ($log === null) {
        $log = new Logger('main');
        $logDir = __DIR__ . '/logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/login_debug_' . date('Y-m-d') . '.log';
        $stream = new StreamHandler($logFile, Logger::DEBUG);
        $formatter = new LineFormatter(null, null, false, true);
        $stream->setFormatter($formatter);
        $log->pushHandler($stream);
    }
    $unique_id = isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : 'unknown';
    $log_entry = "[" . $unique_id . "] " . $message;
    $log->info($log_entry);
}

// 获取用户当前的设备信息和生成 sessionID 的脚本
function getUserDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $deviceType = 'Unknown';
    if (preg_match('/mobile|android|iphone|ipad|ipod/i', $userAgent)) {
        $deviceType = 'Mobile';
    } elseif (preg_match('/macintosh|windows|linux/i', $userAgent)) {
        $deviceType = 'Desktop';
    }
    
    return [
        'userAgent' => $userAgent,
        'deviceType' => $deviceType
    ];
}

function generateSessionId() {
    return bin2hex(random_bytes(16));
}

function getOrCreateDeviceId() {
    // 检查是否已经存在 device_id 的 cookie
    if (isset($_COOKIE['device_id'])) {
        return $_COOKIE['device_id'];
    }
    
    // 如果不存在，则生成一个新的 device_id
    $deviceId = bin2hex(random_bytes(16));
    // 设置 cookie，有效期为 1 年
    setcookie('device_id', $deviceId, time() + (365 * 24 * 60 * 60), "/");
    
    return $deviceId;
}

// 定义选项数据
$options = [
    'genders' => [
        'male' => '男',
        'female' => '女',
        'other' => '其他'
    ],
    'grades' => [
        'DSE' => [
            'dse_s1' => '中一',
            'dse_s2' => '中二',
            'dse_s3' => '中三',
            'dse_s4' => '中四',
            'dse_s5' => '中五',
            'dse_s6' => '中六',
        ],
        'IGCSE' => [
            'igcse_g1' => 'G1',
            'igcse_g2' => 'G2',
        ],
        'ASAL' => [
            'as' => 'AS',
            'al' => 'AL',
        ],
        'AP' => [
            'ap_grade_9' => 'Grade 9',
            'ap_grade_10' => 'Grade 10',
            'ap_grade_11' => 'Grade 11',
            'ap_grade_12' => 'Grade 12',
        ],
        'other' => '其他'
    ],
    'countries' => [
        'china' => '中国',
        'usa' => '美国',
        'uk' => '英国',
        'canada' => '加拿大',
        'australia' => '澳大利亚',
        'other' => '其他'
    ],
    'cities' => [
        'beijing' => '北京',
        'shanghai' => '上海',
        'shenzhen' => '深圳',
        'new_york' => '纽约',
        'london' => '伦敦',
        'sydney' => '悉尼',
        'other' => '其他'
    ],
    'schools' => [
        'shenzhen_college_of_international_education'=> '深圳国际交流学院',
        'shenzhen_hong_kong_pui_kiu_xinyi'=> '深圳培侨信义',
        'guangzhou_minxin_school'=> '广州南沙民心',
        'affiliated_school_of_jnu'=> [
            'guangzhou_affiliated_school_of_jnu'=> '广州暨大港澳子弟学校',
            'dongguan_affiliated_school_of_jnu'=> '东莞暨大港澳子弟学校',
            'foshan_affiliated_school_of_jnu'=> '佛山暨大港澳子弟学校'
        ],
        'hkta_tang_hin_memorial_secondary_school'=> '香港道教聯合會鄧顯紀念中學',
        'twghs_kap_yan_directors_college'=> '東華三院甲寅年總理中學',
        'elegantia_college'=> '風采中學',
        'skh_chan_young_secondary_school'=> '聖公會陳融中學',
        'tin_ka_ping_secondary_school'=> '田家炳中學',
        'twghs_li_ka_shing_college'=> '東華三院李嘉誠中學',
        'fanling_rhenish_church_secondary_school'=> '粉嶺禮賢會中學',
        'fung_kai_liu_man_shek_tong_secondary_school'=> '鳳溪廖萬石堂中學',
        'po_leung_kuk_ma_kam_ming_college'=> '保良局馬錦明中學',
        'christian_alliance_s_w_chan_memorial_college'=> '宣道會陳朱素華紀念中學',
        'queens_college'=> '皇仁书院',
        'diocesan_boys_school'=> '拔萃男书院',
        'st_pauls_co_educational_college'=> '圣保罗男女中学',
        'heep_yunn_school'=> '协恩中学',
        'la_salle_college'=> '喇沙书院',
        'basis_international_school'=> [
            'basis_international_school_shenzhen_shekou_campus'=> '深圳贝赛思国际学校（蛇口校区）',
            'basis_bilingual_school_shenzhen_futian_campus'=> '深圳贝赛思外国语学校（福田校区）',
            'basis_international_school_huizhou'=> '惠州小径湾贝赛思国际学校',
            'basis_international_school_guangzhou'=> '广州贝赛思国际学校',
            'basis_bilingual_school_shenzhen_guangming_campus'=> '深圳贝赛思外国语学校（光明校区）',
            'basis_kindergarten_nanshan'=> '南山贝赛思幼儿园'
        ],
        'harrow_international_school'=> [
            'beijing_campus'=> '北京校区',
            'shanghai_campus'=> '上海校区',
            'shenzhen_campus'=> '深圳校区'
        ],
        'wellington_college'=> [
            'shanghai_campus'=> '上海校区',
            'tianjin_campus'=> '天津校区'
        ],
        'dulwich_college'=> [
            'beijing_campus'=> '北京校区',
            'shanghai_campus'=> '上海校区',
            'suzhou_campus'=> '苏州校区'
        ],
        'yew_chung_international_school'=> '耀中国际学校',
        'concordia_international_school'=> '协和国际学校',
        'other'=> '其他'
    ]
];


?>
