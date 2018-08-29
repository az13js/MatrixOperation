<?php
/**
 * 图像矩阵
 *
 * 此矩阵继承 Matrix 类，区别在于此类使用图片文件作为初始化参数。此类初始后
 * 的矩阵是颜色灰度值，只有黑白色，范围区间是[0,1]，0 代表黑色，1 代表白色
 *
 * 假如图片是png格式，有透明度的话，透明度会被忽略
 *
 * @author az13js
 * @version 0.0.1
 */
class ImageMatrix extends Matrix
{
    /**
     * GD图像资源
     * @var resource|null
     */
    protected $imageResource = null;

    /**
     * 图像类型，值为jpeg或png
     * @var string
     */
    protected $imageType = '';

    /**
     * 构造方法，传入图片文件路径
     *
     * @param string $file
     */
    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new \Exception("File({$file}) not exists!");
        }
        $info = getimagesize($file);
        if (false === $info) {
            throw new \Exception("File({$file}) is not a image!");
        }
        switch ($info[2]) {
            case 2:
                $this->imageResource = imagecreatefromjpeg($file);
                $this->imageType = 'jpeg';
                break;
            case 3:
                $this->imageResource = imagecreatefrompng($file);
                $this->imageType = 'png';
                break;
            default:
                throw new \Exception("Type {$info[2]} not support!");
        }

        // 提取 $this->imageResource 的图像灰度
        $w = $info[0];
        $h = $info[1];
        $matrix = array();
        for ($i = 0; $i < $h; $i++) {
            $matrix[$i] = array();
            for ($j = 0; $j < $w; $j++) {
                $matrix[$i][$j] = imagecolorat($this->imageResource, $j, $i);
                $r = ($matrix[$i][$j] >> 16) & 0xFF;
                $g = ($matrix[$i][$j] >> 8) & 0xFF;
                $b = $matrix[$i][$j] & 0xFF;
                $matrix[$i][$j] = ($r + $g + $b) / (255 * 3);
            }
        }
        parent::__construct($matrix);
    }

    /**
     * 对象被销毁时，销毁图像资源
     */
    public function __destruct()
    {
        imagedestroy($this->imageResource);
    }

    /**
     * 保存当前矩阵的数值为一张PNG格式的图片
     *
     * 元素的值范围视为[0,1]，乘以255，上色时取整，最后小于0的当做0，大于
     * 255的当做255
     *
     * @param string $file
     * @return bool
     */
    public function savePng($file)
    {
        $w = $this->col();
        $h = $this->row();
        $img = imagecreatetruecolor($w, $h);
        for ($i = 0; $i < $h; $i++) {
            for ($j = 0; $j < $w; $j++) {
                $color = intval($this->matrix[$i][$j] * 255);
                if ($color < 0) {
                    $color = 0;
                }
                if ($color > 255) {
                    $color = 255;
                }
                imagesetpixel($img, $j, $i, imagecolorallocate($img, $color, $color, $color));
            }
        }
        if (!imagepng($img, $file)) {
            imagedestroy($img);
            return false;
        }
        imagedestroy($img);
        return true;
    }
}
