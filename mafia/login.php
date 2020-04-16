<?php 
$page_title = '- Login';
include ('includes/header.inc');

if (isset($_POST['submitted'])) {
 require_once ('../../db/mysql_connect_mafia.php');
 if (!empty($_POST['name'])) {
  $n = escape_data($_POST['name']);
 } else {
  echo '<h2>You forgot to enter your username</h2>';
  $n = false;
 }
 if (!empty($_POST['pass'])) {
  $p = escape_data($_POST['pass']);
 } else {
  echo '<h2>You forgot to enter your password</h2>';
  $p = false;
 }
 if ($n && $p) {
  $query = "SELECT player_id, player_name FROM mafia_players WHERE (player_name='$n' AND player_password=SHA('$p'))";
  $result = mysql_query($query);
  if (mysql_num_rows($result) == 1) {
   $row = mysql_fetch_array($result, MYSQL_NUM);
   mysql_free_result($result);
   mysql_close();
   $_SESSION['player_id'] = $row[0];
   $_SESSION['player_name'] = $row[1];
   $url = 'http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
   if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\')) {
    $url = substr($url, 0, -1);
   }
   if (isset($_GET['redirect'])) {
    $url .= "/{$_GET['redirect']}";
   } else {
    $url .= '/index.php';
   }
   ob_end_clean();
   header("Location: $url");
   exit();
  } else {
   echo '<h2>Username and password do not match - Please try again</h2>';
  }
 } else {
  echo '<p>Please try again</p>';
 }
 mysql_close();
}
?>

<h1>Login</h1>
<p>Please <a href="register.php">register</a> first if you are not already a member of the site.</p>

<form action="login.php<?php if (isset($_GET['redirect'])) { echo "?redirect={$_GET['redirect']}"; }?>" method="post">
 <fieldset>
  <p><b>Username:</b> <input type="text" name="name" size="20" maxlength="20" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /></p>
  <p><b>Password:</b> <input type="password" name="pass" size="20" maxlength="64" /></p>
  <input class="sepia" type="submit" name="submit" value="Login" />
  <input type="hidden" name="submitted" value="true" />
 </fieldset>
</form>

<?php include ('includes/footer.inc'); ?>