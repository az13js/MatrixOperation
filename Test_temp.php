<?php
/**
 * 二维矩阵类
 *
 * 运算过程中，假如出现如矩阵尺寸不符合计算要求的情况时，会抛出 \Exception
 * 异常
 *
 * @author az13js
 * @version 0.0.1
 */
class Matrix
{
    /**
     * 一个二维数组
     *
     * 保存的是构造函数接到的参数，后面的计算会用到
     *
     * @var array[]
     */
    protected $matrix = array();

    /**
     * 构造方法
     *
     * 传入一个二维数组作为初始化矩阵的参数
     *
     * @param array[] $matrix
     */
    public function __construct($matrix)
    {
        $this->matrix = $matrix;
    }

    /**
     * 返回行数
     *
     * @param int
     */
    public function row()
    {
        return count($this->matrix, COUNT_NORMAL);
    }

    /**
     * 返回列数
     *
     * @param int
     */
    public function col()
    {
        if (empty($this->matrix)) {
            return 0;
        }
        return count($this->matrix[0], COUNT_NORMAL);
    }

    /**
     * 获取或设置某个位置的数值
     *
     * 下标从 1 开始算，而不是从 0 开始算
     *
     * @param int $i 第几行
     * @param int $j 第几列
     * @param float $num 传入数值则进行赋值，并返回赋值结果，不传值或传入 null
     *     则表示取得此位置的值，默认为 null
     * @return float 指定位置的值
     */
    public function val($i, $j, $num = null)
    {
        if (!isset($this->matrix[$i - 1])) {
            throw new \Exception("value[".($i-1)."] not exists!");
        }
        if (!isset($this->matrix[$i - 1][$j - 1])) {
            throw new \Exception("value[".($i-1)."][".($j-1)."] not exists!");
        }
        if (!is_null($num)) {
            $this->matrix[$i - 1][$j - 1] = $num;
        }
        return $this->matrix[$i - 1][$j - 1];
    }

    /**
     * 求和
     *
     * 计算当前矩阵的所有元素的和并返回
     *
     * @return float
     */
    public function sum()
    {
        $sum = 0;
        foreach ($this->matrix as &$row) {
            foreach ($row as $v) {
                $sum += $v;
            }
        }
        return $sum;
    }

    /**
     * 将自身的值设置为 0
     *
     * @return $this
     */
    public function selfZeros()
    {
        foreach ($this->matrix as &$row) {
            foreach ($row as &$val) {
                $val = 0;
            }
        }
        return $this;
    }

    /**
     * 将自身的值设置为 1
     *
     * @return $this
     */
    public function selfOnes()
    {
        foreach ($this->matrix as &$row) {
            foreach ($row as &$val) {
                $val = 1;
            }
        }
        return $this;
    }

    /**
     * 当前矩阵与另外一个矩阵相加，改写当前矩阵的值
     *
     * 计算结果保存在当前矩阵对应位置，因此之前的值会被覆盖
     *
     * @param Matrix $another 另一个矩阵对象，形状必须与当前矩阵一致
     * @return $this
     */
    public function selfAdd($another)
    {
        foreach ($this->matrix as $i => $row) {
            foreach ($row as $j => $v) {
                $this->val(
                    $i + 1,
                    $j + 1,
                    $this->val($i + 1, $j + 1) + $another->val($i + 1, $j + 1)
                );
            }
        }
        return $this;
    }

    /**
     * 当前矩阵减去另外一个矩阵，改写当前矩阵的值
     *
     * @param Matrix $another 另一个矩阵对象，形状必须与当前矩阵一致
     * @return $this
     */
    public function selfSub($another)
    {
        foreach ($this->matrix as $i => $row) {
            foreach ($row as $j => $v) {
                $this->val(
                    $i + 1,
                    $j + 1,
                    $this->val($i + 1, $j + 1) - $another->val($i + 1, $j + 1)
                );
            }
        }
        return $this;
    }

    /**
     * 当前矩阵与另外一个矩阵进行哈达马积，改写当前矩阵的值
     *
     * @param Matrix $another 另一个矩阵对象，形状必须与当前矩阵一致
     * @return $this
     */
    public function selfHadamardProduct($another)
    {
        foreach ($this->matrix as $i => $row) {
            foreach ($row as $j => $v) {
                $this->val(
                    $i + 1,
                    $j + 1,
                    $this->val($i + 1, $j + 1) * $another->val($i + 1, $j + 1)
                );
            }
        }
        return $this;
    }

    /**
     * 随机给自身设置数值
     *
     * @param float $min
     * @param float $max
     * @return $this
     */
    public function selfRandom($min = 0.0, $max = 1.0)
    {
        foreach ($this->matrix as $i => $row) {
            foreach ($row as $j => $v) {
                $this->val(
                    $i + 1,
                    $j + 1,
                    mt_rand() / mt_getrandmax() * ($max - $min) + $min
                );
            }
        }
        return $this;
    }

    /**
     * 矩阵与一个实数相乘，结果保存在当前矩阵
     *
     * @param float $a
     * @return $this
     */
    public function selfSimpleMulti($a)
    {
        foreach ($this->matrix as $i => $row) {
            foreach ($row as $j => $v) {
                $this->val(
                    $i + 1,
                    $j + 1,
                    $a * $this->val($i + 1, $j + 1)
                );
            }
        }
        return $this;
    }

    /**
     * 从当前矩阵复制全部或部分区域，形成新的矩阵并返回
     *
     * 此方法的参数都有默认值，不传值情况下复制自身所有元素。换言之
     * 默认情况下以左上角matrix(1,1)作为起点，自身宽和高作为复制区
     * 域进行复制
     *
     * @param int $i0 复制起始行，默认左上角，第1行
     * @param int $j0 复制起始列，默认左上角，第1列
     * @param int $width 复制宽度，默认null，null等于当前矩阵列数
     * @param int $hight 复制高度，默认null，null等于当前矩阵行数
     * @return Matrix
     */
    public function copy($i0 = 1, $j0 = 1, $width = null, $hight = null)
    {
        $w = $width;
        $h = $hight;
        if (is_null($w)) {
            $w = $this->col();
        }
        if (is_null($h)) {
            $h = $this->row();
        }
        $res = array();
        for ($i = 0; $i < $h; $i++) {
            $res[$i] = array();
            for ($j = 0; $j < $w; $j++) {
                $res[$i][$j] = $this->val($i + $i0, $j + $j0);
            }
        }
        return new Matrix($res);
    }

    /**
     * 以当前矩阵尺寸大小，从另一个矩阵复制一块区域，赋值到自身
     *
     * 默认情况下，总是以对方的矩阵左上角第一个元素为起点，复制一个与当前矩阵
     * 大小一致的区域的所有值
     *
     * @param Matrix $another
     * @param int $i0
     * @param int $j0
     * @return $this
     */
    public function copyFrom($another, $i0 = 1, $j0 = 1)
    {
        $width = $this->col();
        $hight = $this->row();
        $copyZoom = $another->copy($i0, $j0, $width, $hight);
        for ($i = 0; $i < $hight; $i++) {
            for ($j = 0; $j < $width; $j++) {
                $this->val($i + 1, $j + 1, $copyZoom->val($i + 1, $j + 1));
            }
        }
    }

    /**
     * 与另外一个矩阵相加，返回一个新的矩阵对象
     *
     * @param Matrix $another 另一个矩阵对象，形状必须与当前矩阵一致
     * @return Matrix 新的矩阵对象
     */
    public function add($another)
    {
        return (new Matrix($this->matrix))->selfAdd($another);
    }

    /**
     * 当前矩阵减去另外一个矩阵，返回一个新的矩阵对象
     *
     * @param Matrix $another 另一个矩阵对象，形状必须与当前矩阵一致
     * @return Matrix 新的矩阵对象
     */
    public function sub($another)
    {
        return (new Matrix($this->matrix))->selfSub($another);
    }

    /**
     * 当前矩阵与另外一个矩阵进行哈达马积，返回一个新的矩阵对象
     *
     * @param Matrix $another 另一个矩阵对象，形状必须与当前矩阵一致
     * @return Matrix 新的矩阵对象
     */
    public function hadamardProduct($another)
    {
        return (new Matrix($this->matrix))->selfHadamardProduct($another);
    }

    /**
     * 随机设置数值，返回新矩阵
     *
     * @param float $min
     * @param float $max
     * @return Matrix
     */
    public function random($min = 0.0, $max = 1.0)
    {
        return (new Matrix($this->matrix))->selfRandom($min, $max);
    }

    /**
     * 当前矩阵与另外一个矩阵进行乘法运算，返回一个新的矩阵对象
     *
     * @param Matrix $another 另一个矩阵对象，列数等于此矩阵行数，行数等于此
     *     矩阵列数
     * @return Matrix 新的矩阵对象
     */
    public function multi($another)
    {
        $res = array();
        $m1_row = $this->row();
        $m1_col = $this->col();
        $m2_row = $another->row();
        $m2_col = $another->col();
        if ($m1_row != $m2_col || $m1_col != $m2_row) {
            throw new \Exception("Shape error!");
        }
        for ($i = 0; $i < $m1_row; $i++) {
            $res[$i] = array();
            for ($j = 0; $j < $m2_col; $j++) {
                $res[$i][$j] = 0;
                for ($k = 0; $k < $m1_col; $k++) {
                    $res[$i][$j] += $this->val($i+1, $k+1) * $another->val($k+1, $j+1);
                }
            }
        }
        return new Matrix($res);
    }

    /**
     * 矩阵与一个实数相乘，返回新矩阵
     *
     * @param float $a
     * @return Matrix
     */
    public function simpleMulti($a)
    {
        return (new Matrix($this->matrix))->selfSimpleMulti($a);
    }

    /**
     * 将当前矩阵与一个作为卷积核的矩阵进行卷积运算，返回一个新的矩阵
     *
     * @param Matrix $core 另一个矩阵，作为卷积核
     * @return Matrix
     */
    public function conv($core)
    {
        $row = $this->row();
        $col = $this->col();
        $c_row = $core->row();
        $c_col = $core->col();
        if ($c_row > $row || $c_col > $col) {
            throw new \Exception("Error, conv core too big!");
        }
        $res = array();
        $res_row = $row - $c_row + 1;
        $res_col = $col - $c_col + 1;
        for ($i = 0; $i < $res_row; $i++) {
            $res[$i] = array();
            for ($j = 0; $j < $res_col; $j++) {
                $res[$i][$j] = $this->copy($i + 1, $j + 1, $c_col, $c_row)->selfHadamardProduct($core)->sum();
            }
        }
        return new Matrix($res);
    }
}

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

/**
 * 训练器
 *
 * @author az13js
 * @version 0.0.1
 */
class MatrixTrain
{
    /**
     * 训练卷积核
     *
     * 卷积核的数值在训练后会被改变
     *
     * @param Matrix $core 待训练的作为卷积核的矩阵
     * @param Matrix[] $inputs 被卷积的矩阵
     * @param Matrix[] $outputs 矩阵，卷积的结果
     * @param float $alpha 修正速度。学习率
     * @return void
     */
    public static function trainConvCore($core, $inputs, $outputs, $alpha = 0.01)
    {
        foreach ($inputs as $n => $input) {
            // 答案 - 标准答案 = 偏差
            $diff = $input->conv($core)->selfSub($outputs[$n]);
            // 输入与偏差卷积得到总修正量
            $fix = $input->conv($diff);
            // 卷积核减去修正量以纠正偏差
            $core->selfSub($fix->selfSimpleMulti($alpha));
        }
    }
}

// 测试一下
$core = new Matrix([[1,2],[2,1]]);

$inputs = [];
$outputs = [];
for ($i = 0; $i < 10; $i++) {
    $inputs[$i] = (new Matrix([[1,2,3],[1,2,3],[1,2,3]]))->selfRandom(0, 1);
    $outputs[$i] = $inputs[$i]->conv($core);
}

$core->selfRandom(0, 1);
var_dump('before');
var_dump($core);

for ($i = 0; $i < 100; $i++) {
    MatrixTrain::trainConvCore($core, $inputs, $outputs, 0.05);
}
var_dump('after');
var_dump($core);