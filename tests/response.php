<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/14
 * Time: 13:46
 */

$data = [
    'method' => $_SERVER['REQUEST_METHOD'],
];

echo json_encode($data, 312);