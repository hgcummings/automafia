<?php
$page_title = '- Create a Setup';
include ('includes/header.inc');
include ('includes/groups.inc');
require_once ('../../db/mysql_connect_mafia.php');

function check_win() {
 global $gid;
 global $groups;
 $largest = mysql_fetch_row(mysql_query("SELECT role_wingroup FROM mafia_roles WHERE game_id=$gid GROUP BY role_wingroup ORDER BY COUNT(*) DESC LIMIT 0,1"));
 $largest = $largest[0];
 $total = mysql_fetch_row(mysql_query("SELECT COUNT(*) from mafia_roles WHERE game_id=$gid AND role_status='Alive'"));
 if ((($largest == 0) && ($largest==$total[0])) || (($largest != 0) && (($largest*2) > $total[0]))) {
  $message = "<b><i>GAME OVER</i></b></p><p><b>Victory for the {$groups[$largest][1]}!</b></p><p><b>Winners:</b> ";
  $result = mysql_query("UPDATE mafia_games SET game_phase=-128 WHERE game_id=$gid");
  $result = mysql_query("SELECT player_name FROM mafia_roles, mafia_players WHERE game_id=$gid AND mafia_roles.player_id = mafia_players.player_id AND role_wingroup=$largest");
  while ($row = mysql_fetch_row($result)) {
  $message .= $row[0].', ';
  }
  $message = substr($message,0,-2).'</p><p><b>Losers:</b> ';
  mysql_free_result($result);
  $result = mysql_query("SELECT player_name FROM mafia_roles, mafia_players WHERE game_id=$gid AND mafia_roles.player_id = mafia_players.player_id AND role_wingroup!=$largest");
  while ($row = mysql_fetch_row($result)) {
  $message .= $row[0].', ';
  }
  $message = substr($message,0,-2).'</p>';
  mysql_free_result($result);
  $result = mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,0,'$message',$gid,NOW(),-128)");
  $result = mysql_query("UPDATE mafia_players, mafia_role SET player_wins=player_wins+1 WHERE game_id=$gid AND mafia_players.player_id = mafia_roles.player_id AND AND role_wingroup=$largest");
  $result = mysql_query("UPDATE mafia_players, mafia_role SET player_score=player_wins+{$total[0]} WHERE game_id=$gid AND mafia_players.player_id = mafia_roles.player_id AND AND role_wingroup=$largest");
  $result = mysql_query("UPDATE mafia_players, mafia_role SET player_losses=player_losses+1 WHERE game_id=$gid AND mafia_players.player_id = mafia_roles.player_id AND AND role_wingroup!=$largest");
 }
}

function demotions() {
 global $gid;
 global $ph;
 $linked[75] = 78;
 $linked[81] = 84;
 $linked[87] = 90;
 foreach ($linked as $nasty => $nice) {
  $result = mysql_query("SELECT * FROM mafia_roles WHERE game_id=$gid AND character_id=$nasty");
  if (mysql_num_rows($result) == 0) {
   mysql_free_result($result);
   $result = mysql_query("SELECT player_id FROM mafia_roles WHERE game_id=$gid AND character_id=$nice");
   while ($row = mysql_fetch_row($result)) {
    $update = mysql_query("UPDATE mafia_roles SET character_id=\"0\" character_wingroup=\"0\" WHERE game_id=$gid AND player_id={$row[0]}");
    $insert = mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,{$row[0]},'You are now an ordinary player on the side of the {$groups[0][1]}.',$gid,NOW(),$ph)");
   }
  }
 }
}

function kill_player($player_id,$phase) {
 global $gid;
 global $groups;
 $result = mysql_query("UPDATE mafia_roles SET role_status = 'Dead' WHERE game_id=$gid AND player_id=$player_id AND role_status='Alive'");
 if (mysql_affected_rows() == 1) {
  $info = mysql_fetch_row(mysql_query("SELECT character_name, player_name, role_chatgroup FROM mafia_characters, mafia_players, mafia_roles WHERE game_id=$gid AND mafia_roles.player_id=$player_id AND mafia_characters.character_id=mafia_roles.character_id AND mafia_roles.player_id = mafia_players.player_id"));
  $message = "<b>{$info[1]} was killed</b>. They were a ";
  if (substr($info[0],-4) == ' Cop') {
   $message .= 'Cop';
  } else {
   $message .= $info[0];
  }
  if ($info[2] != 0) {
   $message .= " and a member of the {$groups[$info[2]][0]}.";
  } else {
   $message .= '.';
  }
  $result = mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,0,'$message',$gid,NOW(),$phase)"); 
 }
}

function night_actions() {
 global $gid;
 global $ph;
 global $groups;
 #Checks if all actions are submitted and implements them if necessary
 $allin = mysql_fetch_row(mysql_query("SELECT (((SELECT COUNT(*) FROM mafia_roles, mafia_characters WHERE game_id=$gid AND mafia_roles.character_id = mafia_characters.character_id AND character_action!='none')+(SELECT COUNT(*) FROM (SELECT role_wingroup FROM mafia_roles WHERE game_id=$gid AND role_wingroup>3 AND role_wingroup<9 GROUP BY role_wingroup) AS families))=(SELECT COUNT(*) FROM mafia_actions WHERE game_id=$gid))"));
 if ($allin[0]) {
  #Delete mafia votes
  $result = mysql_query("DELETE FROM mafia_votes WHERE game_id=$gid AND game_phase<0");
  #Select roleblocks
  $result = mysql_query("SELECT action_target FROM mafia_actions WHERE game_id=$gid AND action_type='block'");
  #Apply (delete actions by targets of roleblocks)
  while ($row = mysql_fetch_row($result)) {
   $delete = mysql_query("DELETE FROM mafia_actions WHERE game_id=$gid AND action_by=$row[0]");
   if (mysql_affected_rows > 0) {
    $insert = mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,$row[0],'You were roleblocked!',$gid,NOW(),$ph)");
   }
  }
  #Select investigates
  $result = mysql_query("SELECT action_by, tr.character_id, br.character_id, character_guilt, player_name, character_name, tr.role_chatgroup, action_target FROM mafia_actions, mafia_roles AS tr, mafia_roles AS br, mafia_players, mafia_characters WHERE mafia_actions.game_id=$gid AND br.game_id=$gid AND tr.game_id=$gid AND action_by=br.player_id AND mafia_players.player_id=action_target AND tr.player_id=action_target AND mafia_characters.character_id=tr.character_id AND action_type='investigate'");
  #Apply investigates
  while ($row = mysql_fetch_row($result)) {
   switch ($row[2]) {
    case 6:
      $message = "<b>{$row[4]}</b> {$row[3]} mafia";
     break;
    case 42:
      $message = "<b>{$row[4]}</b> is not mafia";
     break;
    case 45:
      $message = "<b>{$row[4]}</b> is mafia";
     break;
    case 48:
      if ($row[3] = 'is') {
       $guilt = 'is not';
      } else {
       $guilt = 'is';
      }
      $message = "<b>{$row[4]}</b> $guilt mafia";
     break;
    case 51:
      $message = "<b>{$row[4]}</b> visited:'";
      $select = mysql_query("SELECT player_name FROM mafia_actions, mafia_players WHERE game_id=$gid AND action_by={$row[7]} AND player_id=action_target");
      if (mysql_num_rows($select) > 0) {
       while ($row=mysql_fetch_row($select)) {
        $message .= "<br /><b>{$row[0]}</b>";
       }
      } else {
       $message .= ' No-one';
      }
     break;
    case 54:
      $message = "<b>{$row[4]}</b> was visited by:'";
      $select = mysql_query("SELECT player_name FROM mafia_actions, mafia_players WHERE game_id=$gid AND action_target={$row[7]} AND player_id=action_by");
      if (mysql_num_rows($select) > 0) {
       while ($row=mysql_fetch_row($select)) {
        $message .= "<br /><b>{$row[0]}</b>";
       }
      } else {
       $message .= ' No-one';
      }
     break;
    case 57:
      $message = "<b>{$row[4]}</b> is a {$row[5]}";
      if ($row[6] > 0) {
       $message .= "and a member of the {$groups[$row[6]][0]}";
      }
     break;
    case 78:
      if ($row[1] == 75) {
       $message = "<b>{$row[4]}</b> is a {$row[5]}";
      } else {
       $message = "Your investigation was fruitless";
      }
     break;
    case 84:
      if ($row[1] == 81) {
       $message = "{$row[4]} was a {$row[5]}! You have cured them";
       $update = mysql_query("UPDATE mafia_roles SET character_id=\"0\" character_wingroup=\"0\" WHERE game_id=$gid AND player_id={$row[7]}");
       $insert = mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,{$row[7]},'You have been cured! You no longer yearn to kill. You are now an ordinary player on the side of the {$groups[0][1]}.',$gid,NOW(),$ph)");
       $delete = mysql_query("DELETE FROM mafia_actions WHERE game_id=$gid AND action_by={$row[7]} AND action_type='kill'");
      } else {
       $message = "You did not find the player most urgently in need of therapy. They may kill again..";
      }
      break;
    }  
   $insert=mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,$row[0],'$message.',$gid,NOW(),$ph)");
   }
  #Select protects along with character types
  $result = mysql_query("SELECT action_target, character_id, action_by FROM mafia_actions, mafia_roles WHERE mafia_actions.game_id=$gid AND mafia_roles.game_id=$gid AND action_by=player_id AND action_type='protect'");
  #Apply protects accordingly (switch for different characters)
  while ($row = mysql_fetch_row($result)) {
   switch ($row[1]) {
    case 9:
     $delete = mysql_query("DELETE FROM mafia_actions WHERE game_id=$gid AND action_target={$row[0]} AND action_type='kill'");
     break;
    case 36:
     break;
    case 39:
     if (rand(0,100)>50) {
      $delete = mysql_query("DELETE FROM mafia_actions WHERE game_id=$gid AND action_target={$row[0]} AND action_type='kill'");
     }
     break;
    case 63:
      $delete = mysql_query("DELETE FROM mafia_actions WHERE game_id=$gid AND action_target={$row[0]} AND action_type='kill'");
     break;
    case 66:
      $wingroup = mysql_fetch_row(mysql_query("SELECT role_wingroup FROM mafia_roles WHERE game_id=$gid AND player_id={$row[0]}"));
      if ($wingroup[0] > 3) {
       kill_player($row[2],$ph);
      } else {
       $delete = mysql_query("DELETE FROM mafia_actions WHERE game_id=$gid AND action_target={$row[0]} AND action_type='kill'");
      }
     break;
    case 90:
      $delete = mysql_query("DELETE FROM mafia_actions USING mafia_roles WHERE mafia_actions.game_id=$gid AND mafia_roles.game_id=$gid AND action_target={$row[0]} AND action_by=player_id AND character_id=87 AND action_type='kill'");
     break;
   }
  }
  #Select and apply kills one at a time (limit returned results, ordered by rand() - kills won't be selected twice due to 'Alive' condition)
  do {
   $result = mysql_query("SELECT action_target FROM mafia_roles AS tr, mafia_actions, mafia_roles AS br, WHERE game_id=$gid AND br.player_id=action_by AND action_type='kill' AND tr.player_id=action_target AND br.role_status='Alive' AND tr.role_status='Alive' LIMIT 0,1");
   if (mysql_num_rows($result)==1) {
    $target=mysql_fetch_row($result);
    kill_player($target[1],$ph);
   }
  } while (mysql_num_rows($result)==1);
  #Select recruits and apply
  $result = mysql_query("SELECT action_target, action_by, tr.character_id, tr.role_wingroup, br.role_chatgroup, br.role_wingroup FROM mafia_roles AS tr, mafia_actions, mafia_roles AS br, WHERE game_id=$gid AND br.player_id=action_by AND action_type='kill' AND tr.player_id=action_target AND br.role_status='Alive' AND tr.role_status='Alive' LIMIT 0,1");
  while ($recruit=mysql_fetch_row($result)) {
   if (($recruit[2] = 69) || ($recruit[2] = 72)) {
    $insert=mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,{$recruit[1]},'Recruit failed: You cannot recruit that type of character',$gid,NOW(),$ph)");
   } else if ($recruit[3] > 3) {
    kill_player($recruit[1],$ph);
   } else {
    $update=mysql_query("UPDATE mafia_roles SET roles_chatgroup={$recruit[4]}, roles_winggroup={$recruit[5]} WHERE game_id=$gid AND player_id={$recruit[0]}");
    $insert=mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,{$recruit[0]},'You have been recruited into the <b>{$groups[$recruit[4]][0]}</b>',$gid,NOW(),$ph)");
   }
  }
  #Delete remaining actions
  $delete = mysql_query("DELETE FROM mafia_actions WHERE game_id=$gid");
  #Update game phase to day
  $update = mysql_query("UPDATE mafia_games SET game_phase=".(1-$ph)." WHERE game_id=$gid");
  #Check win conditions
  check_win();
  demotions();
 }
}

# Check supplied game id and retrieve game from database

if ((isset($_GET['id'])) && is_numeric($_GET['id'])) {
 $gid = $_GET['id'];
 $result = mysql_query("SELECT game_phase, game_name, setup_id, game_start FROM mafia_games WHERE game_id=$gid");
 if (mysql_num_rows($result) == 1) {
  $game = mysql_fetch_array($result, MYSQL_NUM);
 } else {
  echo 'Game does not exist or database error. Please try again';
  include ('includes/footer.inc');
  exit();
 }
} else {
 echo 'Invalid game id. Please go back and try again.';
 include ('includes/footer.inc');
 exit();
}

#Check phase being viewed (Night #/Day #/Pre-game)

if ((isset($_GET['ph'])) && is_numeric($_GET['ph'])) {
 $ph = $_GET['ph'];
} else {
 $ph = $game[0];
}

if ($game[0] == 0) {
#Handle player joining game
  if(isset($_POST['join'])) {
   include ('includes/checklogin.inc');
   if (is_numeric($_SESSION['player_id'])) {
    $query = "INSERT INTO mafia_roles (game_id, player_id) VALUES ($gid, {$_SESSION['player_id']})";
    $result = mysql_query($query);
    #Check if game is now filled
    $query = "SELECT COUNT(*) FROM mafia_roles WHERE game_id=$gid";
    $result = mysql_query($query);
    $have = mysql_fetch_array($result, MYSQL_NUM);
    mysql_free_result($result);
    $query = "SELECT setup_players, setup_characters FROM mafia_setups WHERE setup_id={$game[2]}";
    $result = mysql_query($query);
    $setup = mysql_fetch_array($result, MYSQL_NUM);
    mysql_free_result($result);
    if ($have[0] == $setup[0]) { #If game is filled
     $query = "UPDATE mafia_games SET game_phase={$game[3]} WHERE game_id=$gid";
     $result = mysql_query($query);
     $query = "SELECT player_id FROM mafia_roles WHERE game_id=$gid";
     $result = mysql_query($query);
     while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $players[] = $row[0];
     } 
     shuffle($players);
     $k = 0;
     $characters = explode(';',$setup[1]);
     foreach ($characters as $i => $csv) {
      $characters[$i] = explode(',',$csv);
      for ($j = 1; $j<= $characters[$i][1]; $j++) {
       if (($characters[$i][4] != 5) && (rand(0,100) > (($characters[$i][4] + 5)*10))) {
        $cid = $characters[$i][5];
        $ccg = $characters[$i][6];
        $cwg = $characters[$i][7];
       } else {
        $cid = $characters[$i][0];
        $ccg = $characters[$i][2];
        $cwg = $characters[$i][3];
       }
       $query = "UPDATE mafia_roles SET character_id=$cid, role_chatgroup=$ccg, role_wingroup=$cwg WHERE player_id={$players[$k]} AND game_id=$gid";
       $result = mysql_query($query);
       $k++;
      }
     }
     #Send role messages
     $query = "SELECT player_id, role_chatgroup, role_wingroup, character_id FROM mafia_roles WHERE game_id=$gid";
     $result1 = mysql_query($query);
     while ($role = mysql_fetch_row($result1)) {
      $query = "SELECT character_message FROM mafia_characters WHERE character_id=$role[3]";
      $result2 = mysql_query($query);
      $message = mysql_fetch_array($result2);
      $message = $message[0];
      mysql_free_result($result2);
      if ((($role[2] == 0) && ($role[1] != 0)) || ((1 <= $role[2]) && ($role[2] <= 8))) {
       $message .= ' You are with the <b>'.$groups[$role[1]][0].'</b>';
       $query = "SELECT player_name FROM mafia_roles, mafia_players WHERE game_id=$gid AND mafia_players.player_id!={$role[0]} AND mafia_players.player_id = mafia_roles.player_id AND role_chatgroup=$role[1]";
       echo $query;
       $result2 = mysql_query($query);
       if (mysql_num_rows($result2) == 0) {
        $message .= '.';
       } else {
        $message .= ' along with ';
        while ($others = mysql_fetch_row($result2)) {
         $message .= $others[0].', ';
        }
        $message = substr($message,0,-2).'.';
       }
      }
      mysql_free_result($result2);
      $query = "INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,{$role[0]},'$message',$gid,NOW(),{$game[3]})";
      $result2 = mysql_query($query);     
     } 
     mysql_free_result($result1);
     $query = "UPDATE mafia_setups SET setup_used=setup_used+1 WHERE setup_id={$game[2]}";
     $result1 = mysql_query($query);
     $url = 'http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
     if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\')) {
      $url = substr($url, 0, -1);
     }
     $url .= "/game.php?id=$gid";
     header("Location: $url");
     ob_end_clean();
     exit();
    }
   }
  }
}

#Page title
echo "<h1><i>Game $gid</i> - {$game[1]}</h1>\n";

#Retrieve players

$query = "SELECT mafia_players.player_id, player_name FROM mafia_players, mafia_roles WHERE game_id=$gid AND mafia_players.player_id = mafia_roles.player_id AND role_status='Alive'";
$result = mysql_query($query);

if (mysql_num_rows($result) > 0) {
 while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
  $player[$row[0]] = $row[1];
 }
} else {
 $player = false;
 
}

if ($game[0] == 0) {#Pre-game

 $showjoin = (isset($_SESSION['player_id'])); #Don't show join button for players not logged in

 #Display players
 echo "<form action=\"game.php?id=$gid&ph=$ph\" method=\"post\">\n <fieldset><legend>Players</legend>\n<p><b>Current players in this game:</b> ";
  if ($player) {
   $players = '';
   foreach ($player as $id => $name) {
    $players .= "<a href=\"profile.php?p=$id\">$name</a>, ";
     if ($showjoin && ($id == $_SESSION['player_id'])) {
      $showjoin = false; #Don't show join button for players already in game
     }
    }
   $players = substr($players, 0, -2);
   echo $players;
   } else {
    echo "None";
   }
 
 #Display join button
 if ($showjoin) {
  echo '</p><p><input type="hidden" name="join" value="true" /><input type="submit" class="sepia" value="Join" />';
 }

 echo "\n</p>\n</fieldset>\n</form>";
 
} else { #Game in progress or finished


  #Show message browser
  
  echo '<form action="game.php" method="get"><p><b>Currently displaying</b> <select name="ph">';
  
   if ($game[0] == -128) {
    $p = mysql_fetch_row(mysql_query("SELECT game_phase FROM mafia_messages WHERE game_id=$gid ORDER BY message_id DESC LIMIT 0, 1"));
    $p = $p[0];
   } else {
    $p = $game[0];
   }

   do {
    if ($p > 0) {
     echo "\n<option value=\"$p\"";
     if ($p == $ph) {
      echo ' selected';
     }
     echo ">Day $p</option>";
     $p = -$p+1;
    } else {
     $p = abs($p);
     echo "\n<option value=\"-$p\"";
     if (-$p == $ph) {
      echo ' selected';   
     }
     echo ">Night $p</option>";
    }
   } while ($p !=0);
   echo '<option value="0"';
   if ($ph == 0) {
      echo ' selected';
   }
   echo '>Pre-game</option>';
?>
  
  </select>
  <input type="hidden" name="id" value="<?php echo $gid; ?>" />
  <input type="submit" class="sepia" value="Change" />
  </p></form>
<?php
}


if ($ph !=0) {
 $cg=0;
 $showmafvotes = false;
 $active = false;
  echo "<fieldset><legend>Voting</legend>\n";
  if ($ph == $game[0]) {
   if ((isset($_SESSION['player_id'])) && (is_numeric($_SESSION['player_id']))) {
    $pid = $_SESSION['player_id'];
    #Check if in game
    $query = "SELECT role_chatgroup, role_status FROM mafia_roles WHERE game_id=$gid AND player_id=$pid";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 1) {
     $row = mysql_fetch_array($result, MYSQL_NUM);
     $cg = $row[0];
     mysql_free_result($result);
     if ($row[1] == 'Alive') { #Check if alive
      $active = true;
      if (isset($_POST['vote'])) {
       if ((is_numeric($_POST['votefor'])) && (is_numeric($_POST['group']))) {
        if (($_POST['group'] == 0) || ($_POST['group'] == $cg)) {
          $query = "DELETE FROM mafia_votes WHERE vote_by=$pid AND game_id=$gid AND game_phase=$ph";
          $result = mysql_query($query);
          if ($_POST['votefor'] == 0) {
           if (mysql_affected_rows() == 1) {
           $query = "INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,{$_POST['group']},'<b>{$player[$pid]}</b> has unvoted',$gid,NOW(),$ph)";
           $result = mysql_query($query);
           }
          } else {
           $query = "INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,{$_POST['group']},'<b>{$player[$pid]}</b> has voted";
            if ($_POST['votefor'] == 1) {
             $victim = ' not to lynch';
            } else {
             $victim = ' to lynch '.$player[$_POST['votefor']];
            }
           $query .= $victim.".',$gid,NOW(),$ph)";
           $result = mysql_query($query);
          } 
          $query = "INSERT INTO mafia_votes (game_id, game_phase, vote_by, vote_for) VALUES ($gid, $ph, $pid, {$_POST['votefor']})";
          $result = mysql_query($query);
                    
          if ($ph > 0) {
           $numvotes = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM mafia_votes WHERE vote_for={$_POST['votefor']} AND game_id=$gid AND game_phase=$ph"));
           $voters = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM mafia_roles WHERE game_id=$gid AND role_status='Alive'"));
           if (($numvotes[0]*2) > $voters[0]) {
            $result = mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,0,'The people have spoken. They have elected$victim!',$gid,NOW(),$ph)"); 
            $result = mysql_query("UPDATE mafia_games SET game_phase=-$ph WHERE game_id=$gid");
            kill_player($_POST['votefor'],-$ph);
            check_win();
           }
          } else {
           $numvotes = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM mafia_votes, mafia_roles WHERE vote_for={$_POST['votefor']} AND mafia_votes.game_id=$gid AND mafia_roles.game_id=$gid AND game_phase=$ph AND vote_by=mafia_roles.player_id AND role_chatgroup=$cg"));
           $voters = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM mafia_roles WHERE game_id=$gid AND role_status='Alive' AND role_chatgroup=$cg"));
           if (($numvotes[0]*2) > $voters[0]) {
            $result = mysql_query("INSERT INTO mafia_actions (game_id, action_by, action_target, action_type) VALUES ($gid, $pid, {$_POST['votefor']}, 'kill')");
            night_actions($gid);
            $result = mysql_query("UPDATE mafia_votes, mafia_roles SET vote_for=2 WHERE mafia_votes.game_id=$gid AND mafia_roles.game_id=$gid AND game_phase=$ph AND vote_by=mafia_roles.player_id AND role_chatgroup=$cg");
            $result = mysql_query("INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES (0,$cg,'Your group has elected to kill <b>{$player[$_POST['votefor']]}</b> tonight. The kill will be carried out by {$player[$pid]}.',$gid,NOW(),$ph)");
           } 
          }
        }
       }
      }
      
      $query = "SELECT vote_for FROM mafia_votes WHERE vote_by=$pid AND game_id=$gid AND game_phase=$ph";
      $result = mysql_query($query);
      if (mysql_num_rows($result) == 1) {
       $vote = mysql_fetch_array($result);
       $vote = $vote[0];
      } else {
       $vote = 0;
      }
      if (($ph > 0) || (($ph <0) && ($ph != -128) && ($cg >= 4) && ($cg <= 8) && ($vote != 2))) {
       echo "<form action=\"game.php?id=$gid&ph=$ph\" method=\"post\">\n<p><b>Cast vote for</b> <select name=\"votefor\">\n";
       echo '<option value="0"';
       if ($vote==0) {
        echo ' selected';
       }
       echo ">Abstain</option>\n";
       foreach ($player as $id => $name) {
        echo "<option value=\"$id\"";
        if ($vote==$id) {
         echo ' selected';
        }
        echo ">$name</option>\n";
       }
       echo '<option value="1"';
       if ($vote==1) {
        echo ' selected';
       }
       if ($ph > 0) {
        echo ">No Lynch</option>\n</select>\n<input type=\"hidden\" name=\"group\" value=\"0\"> ";
       } else {
        echo ">No Kill</option>\n</select>\n<input type=\"hidden\" name=\"group\" value=\"$cg\"> ";
        $showmafvotes = true;
       }
       echo'<input type="hidden" name="vote" value="true"> <input type="submit" class="sepia" value="Vote"></p></form>';
      }
      

      
     }
    }
   }
  }
  
  
  if ($ph > 0) {
   $query = "SELECT player_name, COUNT(*) FROM mafia_players, mafia_votes WHERE player_id=vote_for AND game_id=$gid AND game_phase=$ph GROUP BY vote_for ORDER BY COUNT(*) DESC";
  } else if ($showmafvotes) {
   $query = "SELECT player_name, COUNT(*) FROM mafia_players, mafia_votes, mafia_roles WHERE mafia_players.player_id=vote_for AND mafia_votes.game_id=$gid AND mafia_roles.game_id=$gid AND game_phase=$ph AND vote_by=mafia_roles.player_id AND role_chatgroup = $cg GROUP BY vote_for ORDER BY COUNT(*) DESC";
  } else {
   $query = false;
  }
  if ($query) {   
   $result = mysql_query($query);
   echo "<p>";
   while ($line = mysql_fetch_row($result)) {
    echo "<br>{$line[0]} has {$line[1]}";
    if ($line[1] > 1) {
     echo " votes\n";
    } else {
     echo " vote\n";
    }
   }
  }
  
  echo "</p></fieldset>";
  
 if ($ph < 0) {
  if ($active) {
   $action = mysql_fetch_row(mysql_query("SELECT character_action FROM mafia_characters, mafia_roles WHERE mafia_characters.character_id=mafia_roles.character_id AND game_id=$gid AND player_id=$pid"));
   if ($action[0] != 'none') {
    $result = mysql_query("SELECT * FROM mafia_actions WHERE game_id=$gid AND action_by=$pid AND action_type='{$action[0]}'");
    if (mysql_num_rows($result) == 0) {
     if (isset($_POST['action'])) {
      if (is_numeric($_POST['target'])) {
       $result = mysql_query("INSERT INTO mafia_actions (game_id,action_by,action_target,action_type) VALUES ($gid,$pid,{$_POST['target']},{$action[0]})");
       $result = mysql_query("INSERT INTO mafia_messages (message_from,message_to,message_body,game_id,message_timestamp,game_phase) VALUES (0,$pid,'You have chosen to {$action[0]} {$player[$_POST['target']]}.',$gid,NOW(),$ph)");
       night_actions($gid);
      }
     } else {
     echo "<form action=\"game.php?id=$gid&ph=$ph\" method=\"post\"><fieldset>Night action</legend>\n<p><b>Choose player to {$action[0]}</b> <select name=\"target\">\n<option value=\"0\">No-one</option>";
     foreach ($player as $id => $name) {
      if ($id != $pid) {
       echo "<option value=\"$id\">$name</option>\n";
      }
     }
     echo'<input type="hidden" name="action" value="true"> <input type="submit" class="sepia" value="Submit"></p></fieldset></form>';
     }
    } 
   }
  }
 }
}

#Messages
  
echo '<h2>Messages</h2>';
 
$cg = 0; #Chatgroup

$showmsg = false; #Whether to show form to post message

if ((isset($_SESSION['player_id'])) && (is_numeric($_SESSION['player_id']))) {
 $pid = $_SESSION['player_id'];
 #Check if in game
 $query = "SELECT role_chatgroup, role_status FROM mafia_roles WHERE game_id=$gid AND player_id=$pid";
 $result = mysql_query($query);
 if (mysql_num_rows($result) == 1) {
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $cg = $row[0];
  mysql_free_result($result);
  if ($row[1] != 'Dead') { #Check if not dead
    $showmsg = true;
    if (isset($_POST['newmsg'])) { #Handle message posted
     if ((is_numeric($_POST['phase'])) && (is_numeric($_POST['msgto']))) {
     $query = "INSERT INTO mafia_messages (message_from, message_to, message_body, game_id, message_timestamp, game_phase) VALUES ($pid, {$_POST['msgto']},'".escape_data(strip_tags($_POST['message']))."',$gid,NOW(),{$_POST['phase']})";
     $result = mysql_query($query);
     }
    }
  }
 }
} else {
 $pid = 0;
}

if ($showmsg && ($game[0] < 0) && ($cg == 0)) { #Only allow chatgroup players (Mafia/Cult/Masons) to post at night
 $showmsg = false;
}

if (($ph == $game[0]) && $showmsg) { #If viewing current phase, display message form

echo "<form action=\"game.php?id=$gid&ph=$ph\" method=\"post\">";
echo "<input type=\"hidden\" name=\"phase\" value=\"$game[0]\">";
echo "<input type=\"hidden\" name=\"msgto\" value=\"";
if ($game[0] >= 0) { #Post to public in day or to chargroup at night
 echo '0">';
} else {
 echo "$cg\">";
}

?>
<fieldset><legend>Post Message</legend>
<p><textarea name="message" rows="5" cols="107"></textarea>
<input type="hidden" name="newmsg" value="true" /></p>
<p><input type="submit" class="sepia" value="Submit" /></p>
</fieldset>
</form>
<p></p>
<?php
}

#Show messages

if ((isset($_GET['start'])) && (is_numeric($_GET['start']))) {
 $start = $_GET['start'];
} else {
 $start = 0;
}
$ten=10;

$query = "SELECT message_from, message_body, message_timestamp FROM mafia_messages WHERE game_id=$gid AND game_phase=$ph AND (message_to=0 OR message_to=$cg OR message_to=$pid) ORDER BY message_id DESC LIMIT $start, $ten";

$result=mysql_query($query);

if (!((mysql_num_rows($result) == 0) && ($start == 0))) {

  if ($start > 0) {
   echo "<div class=\"sepia center\"><a href=\"game.php?id=$gid&ph=$ph&start=".($start-$ten).'">Previous page</a></div>'; 
  }
  
  echo '<div class="messages">';
  
  while ($message = mysql_fetch_array($result)) {
   if ($message[0] == 0) {
    $from = 'AutoModerator';
   } else {
    $from = $player[$message[0]];
   }
   
   echo "\n<p class=\"strip\">Message from <b>$from</b> (<i>{$message[2]}</i>)...</p>";
   echo "\n<p>".nl2br($message[1])."</p>";
   
  }
  
  echo '</div>';
  
  if (mysql_num_rows($result) == $ten) {
   echo "<div class=\"sepia center\"><a href=\"game.php?id=$gid&ph=$ph&start=".($start+$ten).'">Next page</a></div>'; 
  }
}

include ('includes/footer.inc');
?>