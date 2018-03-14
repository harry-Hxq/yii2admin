<?php

namespace common\helpers;
use common\components\ValidateCode;
use Yii;

/**
 * StringHelper
 *
 */
class StringHelper extends \yii\helpers\StringHelper{

    /**
     * 字符串截取，支持中文和其他编码
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * @return string
     */
    public static function substr($str, $start=0, $length, $charset="utf-8", $suffix=false) {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
            if(false === $slice) {
                $slice = '';
            }
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice.'...' : $slice;
    }
    /**
     * 过滤emoji表情
     * @param string $str
     * @return string
     */
    public static function replace_emoji($str){
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }
    /**
     * @brief 获取图片验证码(base64)
     * @return string
     */
    public static function getImgCaptcha(){
        $Captcha = new ValidateCode();
        $CaptchaInfo =  $Captcha->doimg();
        Yii::$app->session->set('CaptchaCode',$CaptchaInfo['code']);
        return 'data:image/jpeg;base64,'.$CaptchaInfo['image'];
    }
}
