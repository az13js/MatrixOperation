<?php
namespace App\Matrix;

/**
 * 音频文件矩阵
 *
 * 目前仅支持单声道，16bit,44100Hz的wav文件。我无法保证所有符合条件的wav文件头部格式一定是
 * 相同的，所以如果需要用编辑软件编辑文件，最好用此类生成出来的wav文件作为编辑的基础。
 *
 * @author mengshaoying
 */
class WaveMatrix extends Matrix
{
    /**
     * 构造方法，传入音频文件路径
     *
     * @param string $file
     */
    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new \Exception("File({$file}) not exists!");
        }
        $matrix = array(array());
        $data = file_get_contents($file);
        if (!isset($data[44])) {
            throw new \Exception("File({$file}) format error!");
        }
        $len = strlen($data);
        for ($i = 44; $i < $len; $i += 2) {
            $src = intval(bin2hex($data[$i + 1]).bin2hex($data[$i]), 16);
            $sig = ($src & 0x08000) >> 15;
            // [-1, 1)
            if ($sig == 0) {
                // 小于或等于 32767 为了对称取 32768
                $matrix[0][] = $src / 32768;
            } else {
                // 大于或等于 -32768
                $matrix[0][] = -(0xffff & ~($src-1)) / 32768;
            }
        }
        parent::__construct($matrix);
    }

    /**
     * 将矩阵保存为指定的wav文件
     *
     * 假如文件存在，会先删除原文件再重新生成目标文件
     *
     * @param string $file
     * @return bool
     */
    public function saveWave($file)
    {
        $head = array("52","49","46","46","ac","58","01","00","57","41","56","45","66","6d","74","20","10","00","00","00","01","00","01","00","44","ac","00","00","88","58","01","00","02","00","10","00","64","61","74","61","88","58","01","00");
        $length = $this->col() * $this->row();
        $byteNum = $length * 2;
        $head[43] = dechex(($byteNum & 0xff000000)>> 24);
        $head[42] = dechex(($byteNum & 0x00ff0000)>> 16);
        $head[41] = dechex(($byteNum & 0x0000ff00)>> 8);
        $head[40] = dechex(($byteNum & 0x000000ff)>> 0);
        $bit = $byteNum + 36;
        $head[7] = dechex(($bit & 0xff000000)>> 24);
        $head[6] = dechex(($bit & 0x00ff0000)>> 16);
        $head[5] = dechex(($bit & 0x0000ff00)>> 8);
        $head[4] = dechex(($bit & 0x000000ff)>> 0);
        if (file_exists($file)) {
            unlink($file);
        }
        $fileData = '';
        foreach ($head as $v) {
            $str = $v;
            $fileData .= hex2bin(mb_strlen($str) == 1 ? '0'.$str : $str);
            //file_put_contents($file, hex2bin(mb_strlen($str) == 1 ? '0'.$str : $str), FILE_APPEND|LOCK_EX);
        }
        foreach ($this->matrix as &$row) {
            foreach ($row as $number) {
                $number = $number > 1 ? 1 : ($number < -1 ? -1 : $number);
                $v = round($number * 32767); // 4舍5入
                $v = $v < -32768 ? -32768 : ($v > 32767 ? 32767 : $v);
                $str = dechex($v & 0xff);
                $fileData .= hex2bin(mb_strlen($str) == 1 ? '0'.$str : $str);
                //file_put_contents($file, hex2bin(mb_strlen($str) == 1 ? '0'.$str : $str), FILE_APPEND|LOCK_EX);
                $str = dechex(($v & 0xff00) >> 8);
                $fileData .= hex2bin(mb_strlen($str) == 1 ? '0'.$str : $str);
                //file_put_contents($file, hex2bin(mb_strlen($str) == 1 ? '0'.$str : $str), FILE_APPEND|LOCK_EX);
            }
        }
        if (false !== file_put_contents($file, $fileData, FILE_APPEND|LOCK_EX)) {
            return true;
        } else {
            return false;
        }
    }
}
