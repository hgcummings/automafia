<?php #mafiatron header
ob_start();
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <title><?php echo ('The Incredible Automafiaton '.$page_title); ?></title>
  <style type="text/css" media="all">@import "./includes/default.css";</style> 
  <!--[if IE]>
  <style type="text/css" media="all">@import "./includes/fixie.css";</style> 
  <![endif]-->
  </head>
  <body>
   <div class="header">
    <a href="index.php">about</a> |
    <?php
     if (isset($_SESSION['player_id'])) {
      echo "<a href=\"profile.php?p={$_SESSION['player_id']}\">profile</a> |";
     }
    ?>
    <a href="games.php">games</a> |
    <a href="stats.php">stats</a> |
    <a href="help.php">help</a> |
    <?php
     if (isset($_SESSION['player_id'])) {
       echo "<a href=\"logout.php\">logout</a> ({$_SESSION['player_name']})";
      } else {
       echo "    <a href=\"register.php\">register</a> |\n    <a href=\"login.php\">login</a>";
      }
    ?>
   </div>
   <div class="content">
   