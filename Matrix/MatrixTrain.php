<?php
namespace App\Matrix;

/**
 * 训练器
 *
 * @author mengshaoying
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
        $all = $outputs[0]->col() * $outputs[0]->row();
        foreach ($inputs as $n => $input) {
            // 答案 - 标准答案 = 偏差
            $diff = $input->conv($core)->selfSub($outputs[$n]);
            // 输入与偏差卷积得到总修正量
            $fix = $input->conv($diff);
            // 卷积核减去修正量以纠正偏差
            $core->selfSub($fix->selfSimpleMulti($alpha / $all));
        }
    }

    /**
     * 根据梯度训练卷积核
     *
     * 卷积核的数值在训练后会被改变
     *
     * @param Matrix $core 待训练的作为卷积核的矩阵
     * @param Matrix[] $inputs 被卷积的矩阵
     * @param Matrix[] $gradients 矩阵，梯度值
     * @param float $alpha 修正速度。学习率
     * @return void
     */
    public static function trainConvCoreUsingGradients($core, $inputs, $gradients, $alpha = 0.01)
    {
        $all = $gradients[0]->col() * $gradients[0]->row();
        foreach ($inputs as $n => $input) {
            $fix = $input->conv($gradients[$n]);
            $core->selfSub($fix->selfSimpleMulti($alpha / $all));
        }
    }
}
