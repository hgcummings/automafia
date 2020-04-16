<?php
$page_title = '- Characer list';
include ('includes/header.inc');

echo '<h1>Character List</h1>';

require_once ('../../db/mysql_connect_mafia.php');

function show_roles ($group,$name,$chat) {

$query = "SELECT character_name, character_description, character_defwingroup FROM mafia_characters WHERE character_defwingroup".$group;
$result = mysql_query($query);

echo "<div class=\"left\"><h2>$name Roles</h2><p>$chat</p>";

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
  echo "<h3>{$row[0]}</h3>";
  echo "<p>{$row[1]}</p>";
}

echo '</div>';

}

show_roles ('=0','Pro-town','Any pro-town character may also be a member of a Masonic Lodge. Members of a lodge are able to talk together at night.');
show_roles ('=4','Mafia','A mafia character must be a member of a crime family. Members of each family can talk together at night, and can (collectively) kill one other player');
show_roles ('!=0 AND character_defwingroup!=4','Other Antagonistic','');


include ('includes/footer.inc'); ?>