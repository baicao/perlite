<?php

$password = "123456";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;


$hashed_password2 = '$2y$10$1fmfZ9TGytmcCX/z0apDnOsciqfxT5qvp7r9pUnbD/ovWF/X7nPAS';
if (password_verify($password, $hashed_password2)) {
    echo "密码正确";
} else {
    echo "密码错误";
}

?>