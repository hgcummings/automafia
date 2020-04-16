<?php
$page_title = '- Create a Setup';
include ('includes/header.inc');

include ('includes/checklogin.inc');

require_once ('../../db/mysql_connect_mafia.php');

echo '<h1>Create a setup</h1>';

if (isset($_POST['submitted'])) {
 if (is_numeric($_POST['players']) && is_numeric ($_POST['creator'])) {
  $query = "INSERT INTO mafia_setups (setup_characters, setup_name, setup_description, setup_creator, setup_players, setup_allknown) VALUES (";
  $query .= "'".escape_data($_POST['characters'])."','".escape_data(strip_tags($_POST['name']))."','".escape_data($_POST['description']).'</ul><p>'.escape_data(strip_tags($_POST['intro']))."</p>',".$_POST['creator'].",".$_POST['players'].",'".escape_data($_POST['allknown'])."')";
  $result = mysql_query($query);
  if ($result) {
   echo '<p>Setup entered into database</p>';
   echo '<p>You can now <a href="newgame.php?setup='.mysql_insert_id().'">start a game</a> using this setup.</p>';
  } else {
   echo '<p>Sorry, there was a database error</p>';
  }
 } else {
  echo '<p>Page error, please try again</p>';
 }
} else {
 
include ('includes/groups.inc');
$query = "SELECT character_id, character_name, character_defwingroup FROM mafia_characters ORDER BY character_name ASC";
$result = mysql_query($query);
$i=0;
while ($characters[$i] = mysql_fetch_array($result, MYSQL_NUM)) {
 $i++;
}
array_pop($characters);
include ('includes/setup.inc')
?>

<p>This page allows you to create a setup to use in mafia games. You can add as many different characters as you like, one at a time <nobr>(<a href="javascript:toggleHelp();">show/hide instructions</a>)</nobr></p>
<div id="help" style="display: none">
 <ul>
  <li>Begin by choosing a character type from the drop down list
  <li>You can then set how many players will play as this character type (so you don't, for example, have to define individual villagers one by one)</li>
  <li>Once you have selected a character type you may choose an allegiance:</li>
   <ul>
    <li>Different families for mafia characters</li>
    <li>Rival cults for cult leaders</li>
    <li>(Optionally) Masonic Lodges for pro-town characters</li>
   </ul>
  <li>You can create semi-random setups by setting a percentage probabality that a given character will appear, and choosing an alternative character that will be used otherwise</li>
  <li>Finally, you can choose whether or not the existence of a character will be public knowledge at the start of the game</li>
  <li>Click the 'Add' button to move on and define the next character
  <li>For more help please see the <a href="tutorial.php">tutorial</a> and the <a href="charlist.php">character list</a></li>
 </ul>
</div>
<fieldset class="nofixie">
 <legend>Select characters</legend>
 <p style="line-height: 2.5; text-align: left"><b>
  <select id="character1" onChange="javascript:updateGroupOptions(1);">
   <option value="-1" selected>Character</option>
<?php
   foreach ($characters as $i) {
    echo "   <option value=\"{$i[0]}\">{$i[1]}</option>\n";
   }
?>
  </select>
  x <input type="text" size="2" id="number" value="1" onclick="javascript:document.getElementById('number').value='';" />
  <span id="gc1"></span>, chance </b>
  <select id="chance" onChange="javascript:altCharacter();">
   <option value="5">100%</option>
   <option value="4">90%</option>
   <option value="3">80%</option>
   <option value="2">70%</option>
   <option value="1">60%</option>
   <option value="0">50%</option>
  </select>
  <nobr><b><span id="alternative" style="display: none">Alternative
   <select id="character2" onChange="javascript:updateGroupOptions(2);">
    <option value="-1" selected>Character</option>
<?php
   foreach ($characters as $i) {
    echo "    <option value=\"{$i[0]}\">{$i[1]}</option>\n";
   }
?>
   </select>
   <span id="gc2"></span></span>
   Known:</b>
   <input type="checkbox" id="known" checked />
   <input type="button" class="sepia" value="Add" onclick="javascript:addCharacter()" /></nobr>
 </p>
 <p><span id="showas1"><input type="hidden" id="knownas1" value="1" /></span><!--[if IE]>&nbsp;<![endif]--><span id="showas2"><input type="hidden" id="knownas2" value="1" /></span></p>
</fieldset>

<h3>Below is the description of your setup. Items in italics will not be shown on other pages:</h3>
<form action="newsetup.php" method="post" id="mainform">
 <p>This setup is for<input type="text" name="players" id="players" value="0" class="camo" size="3" readonly />players. The following roles are included:</p>
 <ul id="charlist"></ul>
 <p id="undo" style="visibility: hidden;"><input class="sepia" type="button" id="undo" value="Delete last line" onclick="javascript:delCharacter()" /></p>
 
 <fieldset>
  <legend>Save setup</legend>
  <p>Please add a <b>name</b> for this setup: <input type="text" size="30" name="name" id="name" /></p>
  <p>You may also write your own <b>description</b>, which will appear in addition to the one shown above (no HTML allowed):</p>
  <textarea name="intro" rows="5" cols="107"></textarea>
  <input type="hidden" name="characters" id="characters" value="" />
  <input type="hidden" name="description" id="description" value="<ul>" />
  <input type="hidden" name="creator" value="<?php echo($_SESSION['player_id']);?>" />
  <input type="hidden" name="allknown" id="allknown" value="yes" />
  <input type="hidden" name="submitted" value="true" />
  <p><input type="button" value="Save to Database" onclick="javascript:validateForm()" class="sepia" /></p>
 </fieldset>
</form>

<?php
mysql_close();
}
include ('includes/footer.inc'); ?>