<?php
$page_title = '- Register';
include ('includes/header.inc');

if (isset($_POST['submitted'])) {
 require_once ('../../db/mysql_connect_mafia.php');
 if (eregi ('^[[:alnum:]\-_]{2,20}$', stripslashes(trim($_POST['name'])))) {
  $n = escape_data(trim($_POST['name']));
 } else {
  $n = false;
  echo '<h2>Invalid username. Please try again.</h2>';
 }
 if (eregi ('^[[:alnum:]_\.\-]*@[a-z0-9\.\-]+\.[a-z]{2,6}$', stripslashes(trim($_POST['email'])))) {
  $e = escape_data(trim($_POST['email']));
 } else {
  $e = false;
  echo '<h2>Invalid email address. If problem persists please <a href="contact.php">contact the site administrator</a>.';
 }
 if (eregi ('^[[:alnum:]]{4,}$', stripslashes(trim($_POST['password1'])))) {
  if ($_POST['password1'] == $_POST['password2']) {
   $p = escape_data(trim($_POST['password1']));
  } else {
   $p = FALSE;
   echo '<h2>Password fields did not match</h2>';
  }
 } else {
  echo '<h2>Invalid password</h2>';
 }
 if ($n && $e && $p) {
  $query = "SELECT player_id FROM mafia_players WHERE player_email='$e' OR player_name='$n'";
  $result = mysql_query($query);
  if (mysql_num_rows($result) == 0) {
   $query = "INSERT INTO mafia_players (player_name, player_email, player_password) VALUES ('$n','$e', SHA('$p'))";
   $result = mysql_query($query);
   if (mysql_affected_rows() == 1) {
   	mail($_POST['email'],'Automafia registration confirmation',"Thank you for registering with The Incredible Automafiatron. Your login details are as follows.\n\n\nusername: $n\npassword: $p\n\nPlease note that your password is not stored in plain text in the automafia database can not be retrieved for you if forgotten (though your password can be reset by contacting the site administrator).\n\nCheers,\n- fool_on_the_hill\n(Administrator)");
   echo "<h2>Thank you!</h2>\n<p>Thank you for registering with The Incredible Automafiaton.<br />A confirmation email has been sent to your account.<br />If you experience any problems please contact the site administrator.</p>";
   include ('./includes/footer.inc');
   exit();
   } else {
    echo '<h2>Database error. Please try again. If problem persists please <a href="contact.php">contact the site administrator</a>.</h2>';
   }
  } else {
   echo '<h2>Username or email address already taken. If you wish to reset your password, please <a href="contact.php">contact the site administrator</a>.';
  }
 } else {
  echo '<h2>Please try again</h2>';
 }
 mysql_close();
}
?>
<h1>Register</h1>
<form action="register.php" method="post">
 <fieldset>
 <table>
 <tr><th>
  Username:</th><td><input type="text" name="name" size="30" maxlength="20" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /> <nobr>(Only alphanumeric charcters, hyphen and underscore allowed)</nobr>
  </td></tr><tr><th>
  Email Address:</th><td><input type="text" name="email" size="30" maxlength="64" value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>" />
  </td></tr><tr><th>
  Password:</th><td><input type="password" name="password1" size="30" maxlength="64" /> <nobr>(Alphanumeric characters only, at least four characters long)</nobr>
  </td></tr><tr><th>
  Confirm Password:</th><td><input type="password" name="password2" size="30" maxlength="64" />
  </td></tr><tr><th></th><td>
  <br /><input class="sepia" type="submit" name="submit" value="Register" />
  </td></tr></table>
  <input type="hidden" name="submitted" value="true" />
 </fieldset>
</form>
<?php
include ('includes/footer.inc');
?>