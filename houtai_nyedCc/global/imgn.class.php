<?php
class validateimage
{
    var $x;
    var $y;
    var $numchars;
    var $code;
    var $width;
    var $height;
    var $bg;
    var $coltxt;
    var $colborder;
    var $numcirculos;
    //构造函数、初始值
    function validateimage()
    {
        $this->x           = $x;
        $this->y           = $y = "4";
        $this->numchars    = $numchars = "4"; //number of code
        $this->code        = $code;
        $this->width       = $width = "60"; //width of image
        $this->height      = $height = "23"; //height of image
        $this->bg          = $bg = "0 0 0"; //rgb color of background
        $this->coltxt      = $coltxt = "200 200 125 150"; //rgb color of code
        $this->border      = $colborder = "100 100 100"; //rgb color of border
        $this->numcirculos = $numcirculos = "30"; //number of random point
    }
    //create base image
    function createimage()
    {
        //create a image
        $im = imagecreate($this->width, $this->height) or die("cannot initialize new gd image stream");
        //get the rgb color code
        $colorbg     = explode(" ", $this->bg);
        $colorborder = explode(" ", $this->border);
        $colortxt    = explode(" ", $this->coltxt);
        //put the background color on the image
        $imbg        = imagecolorallocate($im, $colorbg[0], $colorbg[1], $colorbg[2]);
        //put the border on the image
        $border      = imagecolorallocate($im, $colorborder[0], $colorborder[1], $colorborder[2]);
        $imborder    = imagerectangle($im, 0, 0, $this->width - 1, $this->height - 1, $border);
        //put the code color on the image
        $imtxt       = imagecolorallocate($im, $colortxt[0], $colortxt[1], $colortxt[2]);
        //drop 800 points
        for ($i = 0; $i < $this->numcirculos; $i++) {
            $impoints = imagesetpixel($im, mt_rand(0, 80), mt_rand(0, 80), $border);
        }
        //put the code on image
        for ($i = 0; $i < $this->numchars; $i++) {
            //get $x's location
            $this->x = 13 * $i + 8;
            //get the code
            mt_srand((double) microtime() * 1000000 * getmypid());
            $this->code .= (mt_rand(0, 9));
            $putcode = substr($this->code, $i, "1");
            //put the code;
            $code    = imagestring($im, 6, $this->x, $this->y, $putcode, $imtxt);
        }
        $_SESSION['login_check_number'] = $this->code;
        header("content-type:image/png");
        imagepng($im);
        imagedestroy($im);
    }
}
?>
