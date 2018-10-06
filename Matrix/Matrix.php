<?php
namespace App\Matrix;

/**
 * 二维矩阵类
 *
 * 运算过程中，假如出现如矩阵尺寸不符合计算要求的情况时，会抛出 \Exception
 * 异常
 *
 * @author mengshaoying
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
     * 求平方和
     *
     * 计算当前矩阵的所有元素的各自的平方的和并返回
     *
     * @return float
     */
    public function squareSum()
    {
        $sum = 0;
        foreach ($this->matrix as &$row) {
            foreach ($row as $v) {
                $sum += $v * $v;
            }
        }
        return $sum;
    }

    /**
     * 求哈希
     *
     * 计算当前矩阵进行哈希
     *
     * @return string
     */
    public function hash()
    {
        $str = $this->row().'x'.$this->col();
        foreach ($this->matrix as &$row) {
            foreach ($row as $v) {
                $str .= ','.$v;
            }
        }
        return hash('sha256', $str);
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
     * 每个元素求平方
     *
     * 计算当前矩阵的所有元素的各自的平方
     *
     * @return $this
     */
    public function selfSquare()
    {
        foreach ($this->matrix as &$row) {
            foreach ($row as &$v) {
                $v = $v * $v;
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
     * 将当前矩阵中大于0的数改为1，小于或等于0的数改为0
     *
     * @return $this
     */
    public function selfToBool()
    {
        foreach ($this->matrix as &$row) {
            foreach ($row as &$v) {
                $v = $v > 0 ? 1 : 0;
            }
        }
        return $this;
    }

    /**
     * 自我线性归一化
     *
     * @param float $min 最小值
     * @param float $max 最大值
     * @return $this
     */
    public function selfMinMax($min, $max)
    {
        $nmin = $nmax = $this->matrix[0][0];
        foreach ($this->matrix as &$row) {
            foreach ($row as $v) {
                $nmin = $v < $nmin ? $v : $nmin;
                $nmax = $v > $nmax ? $v : $nmax;
            }
        }
        $k = ($nmax - $nmin) > 0 ? ($max - $min) / ($nmax - $nmin) : 0;
        $b = $min - $k * $nmin;
        foreach ($this->matrix as &$row) {
            foreach ($row as &$v) {
                $v = $k * $v + $b;
            }
        }
        return $this;
    }

    /**
     * 自我削波
     *
     * 小于最小值的等于最小值，大于最大值的等于最大值
     *
     * @param float $min 最小值
     * @param float $max 最大值
     * @return $this
     */
    public function selfHardCut($min, $max)
    {
        foreach ($this->matrix as &$row) {
            foreach ($row as &$v) {
                $v = $v < $min ? $min : ($v > $max ? $max : $v);
            }
        }
        return $this;
    }

    /**
     * 将当前矩阵变形为1行多列的矩阵
     *
     * @return $this
     */
    public function to1d()
    {
        $matrix = array(array());
        foreach ($this->matrix as &$row) {
            foreach ($row as $v) {
                $matrix[0][] = $v;
            }
        }
        $this->matrix = $matrix;
        return $this;
    }

    /**
     * 重建矩阵
     *
     * @param int $row 行
     * @param int $col 列
     * @return $this
     */
    public function rebuild($row, $col)
    {
        $this->matrix = array();
        for ($j = 0; $j < $row; $j++) {
            $this->matrix[$j] = array();
            for ($i = 0; $i < $col; $i++) {
                $this->matrix[$j][$i] = 0;
            }
        }
        return $this;
    }

    /**
     * 重设矩阵行数和列数
     *
     * @param int $row 行
     * @param int $col 列
     * @return $this
     */
    public function selfReshape($row, $col)
    {
        $matrix = array(array());
        $r = 0;
        $c = 0;
        foreach ($this->matrix as &$row) {
            foreach ($row as $v) {
                $matrix[$r][] = $v;
                $c++;
                if ($c >= $col) {
                    $c = 0;
                    $r++;
                }
            }
        }
        $this->matrix = $matrix;
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
        return $this;
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
     * 转换出一个新的矩阵，当前矩阵中大于0的数将转为1，否则转为0
     *
     * @return Matrix
     */
    public function toBool()
    {
        return (new Matrix($this->matrix))->selfToBool();
    }

    /**
     * 转换出一个新的矩阵，并线性归一化
     *
     * @param float $min 最小值
     * @param float $max 最大值
     * @return Matrix
     */
    public function minMax($min, $max)
    {
        return (new Matrix($this->matrix))->selfMinMax($min, $max);
    }

    /**
     * 削波
     *
     * 小于最小值的等于最小值，大于最大值的等于最大值
     *
     * @param float $min 最小值
     * @param float $max 最大值
     * @return Matrix
     */
    public function hardCut($min, $max)
    {
        return (new Matrix($this->matrix))->selfHardCut($min, $max);
    }

    /**
     * 重设矩阵行数和列数，获得新矩阵而不改变现有值
     *
     * @param int $row 行
     * @param int $col 列
     * @return $this
     */
    public function reshape($row, $col)
    {
        if ($row * $col != $this->row() * $this->col()) {
            throw new \Exception('Error, elements number not equal! (You want)['.$row.'x'.$col.']!=(we have)['.$this->row().'x'.$this->col().']');
        }
        $matrix = array(array());
        $r = 0;
        $c = 0;
        foreach ($this->matrix as &$row) {
            foreach ($row as $v) {
                $matrix[$r][] = $v;
                $c++;
                if ($c >= $col) {
                    $c = 0;
                    $r++;
                }
            }
        }
        return new Matrix($matrix);
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

    /**
     * 反卷积，返回一个新的矩阵
     *
     * @param Matrix $core 另一个矩阵，作为卷积核
     * @return Matrix
     */
    public function disConv($core)
    {
        $row = $this->row();
        $col = $this->col();
        $c_row = $core->row();
        $c_col = $core->col();

        $res = array();
        $res_row = $row + $c_row - 1;
        $res_col = $col + $c_col - 1;
        for ($i = 0; $i < $res_row; $i++) {
            $res[$i] = array();
            for ($j = 0; $j < $res_col; $j++) {
                $res[$i][$j] = 0;
            }
        }
        for ($i = 0; $i < $row; $i++) {
            for ($j = 0; $j < $col; $j++) {
                for ($n = 0; $n < $c_row; $n++) {
                    for ($m = 0; $m < $c_col; $m++) {
                        $res[$i + $n][$j + $m] += $core->val($n + 1, $m + 1) * $this->matrix[$i][$j];
                    }
                }
            }
        }
        return new Matrix($res);
    }

    /**
     * 哈希卷积
     *
     * 以指定的大小作为“卷积核”，对当前矩阵进行区域提取，对提取区域进行矩阵哈
     * 希，以哈希字符作为运算结果形成新的矩阵。新的矩阵是字符串矩阵，不能进行
     * 数值运算。
     *
     * @param int $crow 卷积核行数
     * @param int $ccol 卷积核列数
     * @return Matrix
     */
    public function hashConv($crow, $ccol)
    {
        $row = $this->row();
        $col = $this->col();
        $c_row = $crow;
        $c_col = $ccol;
        if ($c_row > $row || $c_col > $col) {
            throw new \Exception("Error, conv core too big!");
        }
        $res = array();
        $res_row = $row - $c_row + 1;
        $res_col = $col - $c_col + 1;
        for ($i = 0; $i < $res_row; $i++) {
            $res[$i] = array();
            for ($j = 0; $j < $res_col; $j++) {
                $res[$i][$j] = $this->copy($i + 1, $j + 1, $c_col, $c_row)->hash();
            }
        }
        return new Matrix($res);
    }

    /**
     * 获取给定矩阵的完全展开散列云
     *
     * @param int $crow 卷积核行数
     * @param int $ccol 卷积核列数
     * @return array 哈希字符串出现次数组成的数组，键名是散列，键值是出现的层数
     */
    public function hashCloud($crow = 3, $ccol = 3)
    {
        $result = $this;
        $row = $result->row();
        $col = $result->col();
        $cloud = array();
        $deep = 0;/* 第几层，从1开始数 */
        while ($row >= $crow && $col >= $ccol) {
            $deep++;
            $result = $result->hashConv($crow, $ccol);
            $row = $result->row();
            $col = $result->col();
            for ($i = 0; $i < $row; $i++) {
                for ($j = 0; $j < $col; $j++) {
                    $a_ij = $result->val($i + 1, $j + 1);
                    if (!isset($cloud[$a_ij])) {
                        $cloud[$a_ij] = $deep;
                    }
                }
            }
        }
        //return array_keys($cloud);
        return $cloud;
    }
}
