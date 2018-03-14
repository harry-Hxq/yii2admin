<?php

namespace common\helpers;

/**
 * 用户各种格式化转换
 */
class Formatter {

    /**
     * 对象转数组
     * @param mixed $object 数组/对象/其他基本类型
     * @return array/基本类型
     */
    public static function objectToArray($object){
        if(!is_object($object) && !is_array($object)){
            return $object;
        }
        $data = array();
        foreach($object as $key=>$value){
            $data[$key] = self::objectToArray($value);
        }
        return $data;
    }


    /**
     * 是否邮件
     * @param $email
     */
    public static function isEmail($email)
    {
        return preg_match(
            '/^([a-zA-Z0-9]*[-_]?[a-zA-Z0-9]+)*@([a-zA-Z0-9]*[-_]?[a-zA-Z0-9]+)+[\\.][A-Za-z]{2,5}([\\.][A-Za-z]{2,3})?$/',
            $email
        );
    }
    /**
     * @param string $phone 手机号
     * @return bool
     */
    public static function isMobile($phone)
    {
        return preg_match("/^1[3|4|5|7|8]{1}[0-9]{9}$/", $phone);
    }

    /**
     * 检测密码格式是否正确
     */
    public static function isGoodPassword($password) {
        $pattern = '/^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]{6,20}$/';
        return (bool)preg_match($pattern,$password);
    }


    /**
     * 密码检查
     * @param string $password 明文密码
     * @return bool
     */

    public static function isPassword($password) {
        $pattern = '/^[^ ]{6,20}$/';
        return (bool)preg_match($pattern,$password);
    }

    /**
     * 身份证检查
     * @param string $cardNubmer
     * @return boolean true if it is an idcard nubmer, otherwise false will be returned.
     */
    public static function isIdCard($cardNubmer)
    {
        static $cities = array(
            11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",
            22=>"吉林",23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",
            35=>"福建",36=>"江西",37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",
            45=>"广西",46=>"海南",50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",
            61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",
            82=>"澳门",91=>"国外"
        );
        $sum = 0;

        if (!preg_match('/^\d{17}(\d|x)$/i', $cardNubmer)) {
            return false;
        }

        $cardNubmer = preg_replace('/x/i', 'a', $cardNubmer);
        if (!isset($cities[intval(substr($cardNubmer, 0, 2))])) { // 地区
            return false;
        }
        if (!checkdate(intval(substr($cardNubmer, 10, 2)), intval(substr($cardNubmer, 12, 2)), intval(substr($cardNubmer, 6, 4)))) { // 出生日期
            return false;
        }

        for ($i = 17; $i>=0; $i--)
            $sum += (pow(2, $i) % 11) * intval($cardNubmer[17 - $i], 11);
        if ($sum % 11 != 1) {
            return false;
        }

        return true;
    }

    /**
     * 数字格式化
     * @param $number
     * @param string $thousands_sep 千位分割符号
     * @param int $decimals 小数点后位数
     * @param string $dec_point 小数点符号
     * @return string
     */
    public static function numberFormat($number, $thousands_sep = ',', $decimals = 2, $dec_point = '.')
    {
        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }

    public static function isBankCard($bankCard, $bankEname)
    {
        if(!preg_match('/^\d{12,19}$/',$bankCard)) {
            return false;
        }
        return true;
    }

    /**
     * 格式化手机号码
     * @param $phone
     * @param string $replaceMent
     * @param int $start
     * @param int $length
     * @return mixed
     */
    public static function phoneFormat($phone, $replaceMent='****', $start=3, $length=4)
    {
        return substr_replace($phone, $replaceMent, 3, 4);
    }

    public static function assignIfEmpty($num, $default=0)
    {
        if(is_null($num) || $num==''){
            $num = $default;
        }
        return $num;
    }

    public static function isMobileWeb()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }

        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }

        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array (
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );

            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }

        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }

        return false;
    }
}