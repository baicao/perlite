<?php

$password = "123456";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;

$hashed_password2 = '$2y$10$o1vjWieSbtTsEkCcG6.MeOMif.yL9dunfX6DOQLUX0vdD1s3kha2.';
if (password_verify($password, $hashed_password2)) {
    echo "密码正确";
} else {
    echo "密码错误";
}

?>