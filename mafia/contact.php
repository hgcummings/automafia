<?php
$page_title = '- Statistics';
include ('includes/header.inc');
require_once ('../../db/mysql_connect_mafia.php');

echo '<h1>Contact Form</h1>';

if (isset($_POST['submitted'])) {
 if (is_numeric($_POST['type']) && is_numeric($_POST['from'])) {
  $query = "INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES ({$_POST['from']},0,'".escape_data(strip_tags($_POST['message']))."<br><br><b>Email:</b> ".escape_data(strip_tags($_POST['email']))."',0,NOW(),{$_POST['type']})";
  $result = mysql_query($query);
  if (mysql_affected_rows() == 1) {
   echo '<p>Message accepted. Thank you for your enquiry. We will get back to you as soon as possible at '.strip_tags($_POST['email']).'</p>';
   include ('includes/footer.inc');
   exit();
  } else {
   echo '<p>Database error. Please try again later.</p>';
  }
 } else {
   echo '<p>Page error. Please try again.</p>';
 }
 $t = $_POST['type'];
} else {
 $t = 1;
}

?>

<form action="contact.php" method="post">
<fieldset><legend>Contact the site administrator</legend>

<p><b>Type of query:</b> 
 <select name="type">
  <option value="1"<?php if ($t == 1) { echo ' selected'; } ?>>Help Question</option>
  <option value="2"<?php if ($t == 2) { echo ' selected'; } ?>>Bug Report</option>
  <option value="3"<?php if ($t == 3) { echo ' selected'; } ?>>Feature Request</option>
  <option value="4"<?php if ($t == 4) { echo ' selected'; } ?>>Other</option>
 </select>
</p>

<p><b>Your message (no HTML allowed):</b><br />
<textarea name="message" rows="5" cols="107"><?php if (isset($_POST['message'])) { echo (strip_tags($_POST['message'])); } ?></textarea></p>

<p><b>Your email address:</b> <input type="text" name="email" size="30" maxlength="64" value="<?php
if (isset($_POST['email'])) {
 echo (strip_tags($_POST['email']));
} else {
 if(isset($_SESSION['player_id']) && is_numeric($_SESSION['player_id'])) {
  $email = mysql_fetch_row(mysql_query("SELECT player_email FROM mafia_players WHERE player_id = {$_SESSION['player_id']}"));
  echo $email[0];
  $from = $_SESSION['player_id'];
 } else {
  $from = 0;
 }
}
?>" /> (not required, but if left blank you will not receive a reply)</p>

<input type="hidden" name="from" value="<?php
 if (isset($_SESSION['player_id'])) {
  echo $_SESSION['player_id'];
 } else {
  echo 0;
 }
?>" />
<input type="hidden" name="submitted" value="true" />
<p><input type="submit" class="sepia" value="Submit" /></p>
</fieldset>
</form>

<?php
include ('includes/footer.inc'); ?>