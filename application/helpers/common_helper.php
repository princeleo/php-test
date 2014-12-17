<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//自定義輸入函數
if(!function_exists('pr')){
    function pr($arr, $escape_html = true, $bg_color = '#EEEEE0', $txt_color = '#000000') {
        echo sprintf('<pre style="background-color: %s; color: %s;">', $bg_color, $txt_color);
        if($arr) {
            if($escape_html){
                echo htmlspecialchars( print_r($arr, true) );
            }else{
                print_r($arr);
            }

        }
        else {
            var_dump($arr);
        }
        echo '</pre>';
    }
}

//统一价格输出格式
if(!function_exists('format_price')){
    function mod_price($price){
        if(($price*100)%100 > 0){
            if(($price*100)%10 > 0){
                return $price;
            }
            else{
                return round($price,1);
            }
        }else{
            return ceil($price);
        }
    }
}