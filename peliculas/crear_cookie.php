<?php
session_start();
setcookie('acepta', '1', time() + 3600 * 24 * 365, '/', '', false, false);
header('Location: index.php');
