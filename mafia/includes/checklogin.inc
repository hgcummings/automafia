<?php
 if (!isset($_SESSION['player_id'])) {
  $url = 'http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
  if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\')) {
   $url = substr($url, 0, -1);
  }
  $url .= '/login.php?redirect='.basename($_SERVER['PHP_SELF']);
  header("Location: $url");
  exit();
 }
?>