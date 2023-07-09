<?php

require_once(dirname(__FILE__).'/Qrcode.php');

class QrcodeLib_from_Goaskle_Com extends Qrcode_from_Goaskle_Com
{
    /**
     * Create the QR Code
     * 
     * @param  mixed    $content    QR Code content
     * @param  string   $file       image file name
     * @param  integer  $size       size of the image
     * @param  string   $enc        encoding of the content (not used)
     * @param  string   $ecc        error correction code type
     * @param  integer  $margin     QR Code image
     * @param  integer  $version    QR Code version
     */
    public function create($content, $file, $size, $enc, $ecc, $margin, $version)
    {
        
        // prepare library variables
        $qrcode_data_string = $content;
        $qrcode_error_correct = $ecc;
        $qrcode_module_size = $size;
        $qrcode_version = $version;
        
        // include library and execute
        require(dirname(__FILE__).'./../qr_img/qr_img.php');
       
        // redefine whitespace margin and save file 
        $base_image = $this->cropImage($base_image, $size, $margin);
        $this->saveImage($base_image, $file);
        
        // remove lib image 
        imagedestroy($base_image);
    }
}