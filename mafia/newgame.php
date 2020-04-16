<?php
$page_title = '- Create a New Game';
include ('includes/header.inc');
include ('includes/checklogin.inc');
require_once ('../../db/mysql_connect_mafia.php');

echo "<h1>Create a Game</h1>\n";

if (isset($_POST['submitted'])) {
 if ((is_numeric($_POST['start'])) && (is_numeric($_POST['setup_id']))) {
  $query = "SELECT setup_name, setup_description, setup_players, setup_allknown, setup_used FROM mafia_setups WHERE setup_id={$_POST['setup_id']}";
  $result = mysql_query($query);
  if (mysql_num_rows($result) == 1) {
    $setup = mysql_fetch_array($result, MYSQL_NUM);
    mysql_free_result($result);
    $query = "INSERT INTO mafia_games (game_name, setup_id, game_start) VALUES ('".escape_data(strip_tags($_POST['name']))."',{$_POST['setup_id']},{$_POST['start']})";
    $result = mysql_query($query);
    if ($result) {
     $gid = mysql_insert_id();
     $query = "INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0, 0,'This setup is for {$setup[2]} players. The following roles are included:</p>".substr($setup[1],0,-4);
     if ($setup[3] == 'no') {
      $query .= "</p><p><b>Note:</b> The roles of this setup are not revealed in full by the above list."; 
     }
     $query .= "',$gid,NOW(),0)";
     $result = mysql_query($query);
     echo '<p>Game entered into database</p>';
     echo "<p>You can now <a href=\"game.php?id=$gid\">join this game</a> as a player.</p>";
    } else {
     echo '<p>Sorry, there was a database error</p>';
    }
  } else {
   echo '<p>Invalid setup id. Please try again</p>';
  }
 } else {
  echo '<p>Page error, please try again</p>';
 }
} else {

  if ((isset($_GET['setup'])) && (is_numeric($_GET['setup']))) {
   $query = "SELECT setup_name, setup_description, setup_players, setup_creator, setup_allknown FROM mafia_setups WHERE setup_id={$_GET['setup']}";
   $result = @mysql_query($query);
   if (mysql_num_rows($result) != 1) {
    echo "<h2>Setup {$_GET['setup']} not found. Please try again. If the problem persists please <a href=\"contact.php\">contact the site administrator</a>.</h2>";
    include ('./includes/footer.inc');
    exit();
   }
   $setup = mysql_fetch_array($result, MYSQL_NUM);
   mysql_free_result($result);
   $query = "SELECT player_name FROM mafia_players WHERE player_id={$setup[3]}";
   $result = @mysql_query($query);
   if (mysql_num_rows($result) == 1) {
    $setup[] = mysql_fetch_array($result, MYSQL_NUM);
   }
   
  echo "<h2>You have selected the following setup:</h2>\n";
  echo "<p><b>{$setup[0]}</b> by <a href=\"profile.php?p={$setup[3]}\">{$setup[5][0]}</a></p>";
  echo "<p>This setup is for <b>{$setup[2]}</b> players. The following roles are included:<br />".nl2br($setup[1])."</p>";
  
  if ($setup[4] == 'no') {
   echo "<p><b>Note:</b> The roles of this setup are not revealed in full by the above list.</p>"; 
  }
  
  ?>
  
  </p>
  
  <p></p>
  
  <form action="newgame.php" method="post">
  <fieldset>
  <legend>Confirm details and start game</legend>
  
  <p><b>Game title: </b><input type="text" name="name" value="<?php echo $setup[0] ?>" size="60" maxlength="80" /></p>
  <b>Start game with:</b>
  <input type="radio" name="start" value="1" checked /> Day
  <input type="radio" name="start" value="-1" /> Night</p>
  <input type="hidden" name="setup_id" value="<?php echo $_GET['setup']; ?>" />
  <input type="hidden" name="submitted" value="true" />
  <p><input type="submit" class="sepia" value="Launch Game" /></p>
  
  </fieldset>
  </form>
  
  <?php
  
  } else {
  
   include ('includes/common.inc');
  
    ?>
    <h2>Choose Setup</h2>
    <form action="newgame.php" method="get">
    <fieldset>
    <legend>Limit results</legend>
    <p><b>Only show me setups for
    <input type="text" size="4" maxlength="3" name="players" value="<?php if(isset($_GET['players'])) { echo $_GET['players']; } ?>" /> players.</b>
    <input class="sepia" type="submit" value="Go" /></p>
    </fieldset>
    <h3>Your Setups</h3>
    <p>You can <a href="newsetup.php">create a new setup</a> of your own or choose an existing setup below:</p>
    <?php
    $query1 = "SELECT setup_id, setup_name, setup_description, setup_players FROM mafia_setups WHERE setup_creator";
 
    $query2 = "SELECT setup_id, setup_name, setup_description, setup_players, player_id, player_name FROM mafia_setups, mafia_players WHERE setup_creator";
    
    $query1 .= "={$_SESSION['player_id']}";
    $query2 .= "!={$_SESSION['player_id']} AND setup_creator = player_id";
    
    if (isset($_GET['players'])) {
     if ((is_numeric($_GET['players'])) && ($_GET['players'] < 256)) {
      $qryadd = " AND setup_players={$_GET['players']} ORDER BY setup_used DESC";
     } else {
      echo '<p><b>Warning:</b> Invalid number of players selected (max. 255). Displaying all setups.</p>'; 
     }
    } else {
     $qryadd = " ORDER BY setup_used DESC";
    }
    
    $query1 .= $qryadd;
    $query2 .= $qryadd;
    
    show_setups($query1,false);
    
    echo "<h3>Other Setups (Most Popular First)</h3>";
    
    if ((isset($_GET['start'])) && (is_numeric($_GET['start']))) {
     $start = $_GET['start'];
    } else {
     $start = 0;
    }
    
    $query2 .= " LIMIT $start, $ten";
    
    if ($start > 0) {
     $nav = '<div class="sepia center"><a href="newgame.php?start='.($start-$ten);
     if (isset($_GET['players'])) {
      $nav .= "&players={$_GET['players']}";
     }
     echo $nav.'">Previous page</a></div>'; 
    }
    
    show_setups($query2,true);
    
    if ($numrows == $ten) {
     $nav = '<div class="sepia center"><a href="newgame.php?start='.($start+$ten);
     if (isset($_GET['players'])) {
      $nav .= "&players={$_GET['players']}";
     }
     echo $nav.'">Next page</a></div>';
    } 
  }

include ('includes/footer.inc');

}

?>