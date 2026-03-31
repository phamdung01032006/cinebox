<?php

class FormSanitizer {

    // Hàm xử lý chuỗi nhập vào
    public static function sanitizeFormString($inputText) {
        // xóa HTML tag từ string nhập vào
        $inputText = strip_tags($inputText);
        // Xóa hết khoảng trắng thừa trong chuỗi
        // $inputText = str_replace(" ", "", $inputText);
        $inputText = trim($inputText);
        // lowercase all text
        $inputText = strtolower($inputText);
        // capitalize text
        $inputText = ucfirst($inputText);
        return $inputText;
    }

    public static function sanitizeFormUsername($inputText) {
        // xóa HTML tag từ string nhập vào
        $inputText = strip_tags($inputText);
        // Xóa hết khoảng trắng thừa trong chuỗi
        $inputText = str_replace(" ", "", $inputText);
        return $inputText;
    }

    public static function sanitizeFormPassword($inputText) {
        // xóa HTML tag từ string nhập vào
        $inputText = strip_tags($inputText);
        return $inputText;
    }
    
    public static function sanitizeFormEmail($inputText) {
        // xóa HTML tag từ string nhập vào
        $inputText = strip_tags($inputText);
        // Xóa hết khoảng trắng thừa trong chuỗi
        $inputText = str_replace(" ", "", $inputText);
        return $inputText;
    }

    public static function sanitizeGender($inputText) {
        $inputText = strip_tags($inputText);
        $inputText = strtolower(trim($inputText));

        $allowed = ["male", "female", "other", "prefer_not_to_say"];
        return in_array($inputText, $allowed, true) ? $inputText : "";
    }

}


?>
