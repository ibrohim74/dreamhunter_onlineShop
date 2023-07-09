<?php

require_once(dirname(__FILE__).'/Qrcode.php');

class QrcodeGoogle_from_Goaskle_Com extends Qrcode_from_Goaskle_Com
{
    /**
     * save image with GD conversion function, needs allow_url_fopen
     *  
     * @param  string    $url    URL image path
     * @param  string    $file   local image filename
     */
    public function saveImageGd($url, $file)
    {
        // fetch image from URL and save it
        $img = imagecreatefrompng($url);
        $this->saveImage($img, $file);
        imagedestroy($img);
    }
    
    /**
     * alternative image saving using cURL if allow_url_fopen is disabled
     *
     * @param  string    $url    URL image path
     * @param  string    $file   local image filename
     */
    public function saveImageCurl($url, $file)
    {
        // initialize cURL settings
        $ch = wp_remote_get($url);

        // fetch raw data  
        $rawdata = wp_remote_retrieve_body($ch);

        // convert it to a GD image and save
        $img = imagecreatefromstring($rawdata);
        $this->saveImage($img, $file);
        imagedestroy($img);
    }    
    
    /**
     * Grab an Image from the given URL (with GD function or cURL)
     *  
     * @param  string    $url    URL image path
     * @param  string    $file   local image filename
     */
    public function grabImage($url, $file)
    {
        // get allow_url_fopen setting
        $allow_url_fopen = (ini_get('allow_url_fopen') == 1);
 
        if ($allow_url_fopen) { // use gd function if allowed
            return $this->saveImageGd($url, $file);
        } else { // use cURL as alternative
            return $this->saveImageCurl($url, $file);
        }
     }

    /**
     * Create the QR Code
     * 
     * @param  mixed    $content    QR Code content
     * @param  string   $file       image file name
     * @param  integer  $size       size of the image
     * @param  string   $enc        encoding of the content
     * @param  string   $ecc        error correction code type
     * @param  integer  $margin     QR Code image
     * @param  integer  $version    QR Code version (not used)
     */
    public function create($content, $file, $size, $enc, $ecc, $margin, $version)
     {
         // prepare Google Chart URL
         $url = 'http://chart.apis.google.com/chart?chs=' . $size . 'x' . $size . '&cht=qr&chl=' . urlencode($content) . '&choe=' . $enc . '&chld=' . $ecc . '|' . $margin;
        
         // grab image from URL
         $this->grabImage($url, $file);
    }
    
}