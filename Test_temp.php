<?php
require 'Matrix.php';
require 'MatrixTrain.php';

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