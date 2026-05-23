<?php

setcookie("login", "", time() - 3600, "/");
setcookie("username", "", time() - 3600, "/");
setcookie("nama", "", time() - 3600, "/");

header("Location: login.php?pesan=logout");
exit;

?>