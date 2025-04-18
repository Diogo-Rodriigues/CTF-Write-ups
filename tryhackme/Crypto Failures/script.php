<?php

$str="admin:Mo";
$salt="C8";
$x=crypt($str,$salt);

echo $x;