<?php 

session_start();

if (isset($_SESSION['player_id'])) {
 $_SESSION = array();
 session_destroy();
 setcookie (session_name(), '', time()-300, '/', '', 0);
}
$url = 'http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\')) {
 $url = substr($url, 0, -1);
}
$url .= '/index.php';
header("Location: $url");
exit();
?>