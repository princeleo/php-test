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