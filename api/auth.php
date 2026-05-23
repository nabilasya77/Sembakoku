<?php

if (
    !isset($_COOKIE['login']) ||
    $_COOKIE['login'] !== 'true'
) {

    header(
        'Location: /api/login.php?pesan=belum_login'
    );

    exit;
}