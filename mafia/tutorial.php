<?php
$page_title = '- Setup Tutorial';
include ('includes/header.inc');
?>

<h1>Setup Tutorial</h1>
<div style="width: 800px;">
  <h2>Introduction</h2>
  <p>This tutorial will talk you through creating a basic setup for seven players. Note that the setup page might look slightly different in your browser. The functionality will be the same though. Let's start by adding two mafia members. Simply select the 'Mafioso' character type, enter 2 in the second box, then click 'Add'.</p>
  <p><img src="images/tutorial1.png"></p>
  <h2>Semi-Random Setups</h2>
  <p>Note in the image below how our two mafiosi have been added to the game description. We will now add a cop, but with a random element. In 50% of games we will get a cop, the rest of the time just a regular villager. To do this, first select 'Cop' as the character type, then in the 'chance' drop-down box select the likelihood that this character will appear. If you choose a probability less than 100%, you will be asked to specify what character will otherwise appear in it's place. In this case just an ordinary villager.</p>
  <p><img src="images/tutorial2.png"></p>
  <h2>Hidden Roles</h2>
  <p>In the image below we have added a doctor (in the same way as the cop) and three villagers (in the same way as the mafiosi). Note that line for the villagers is in italics, meaning they won't be shown in the description of this setup on other pages. If you like, you can do the same with other characters to by unchecking the 'Known' checkbox before clicking 'Add'. This allows you to add special roles that the players are unaware of at the start of the game.
  <p><img src="images/tutorial3.png"></p>
  <h2>Delete/Undo</h2>
  <p>Let's say we want to have a couple of Masons instead of three ordinary villagers. Mason's are members of a secret society who, like the mafia, can talk to each other at night. However, the Masons are on the side of the town. First of all, we need to delete the three villagers we just added. We can do this by clicking 'Delete last line'.</p>
  <p><img src="images/tutorial4.png"></p>
  <h2>Groups</h2>
  <p>Now we can add a single villager (already done in the image below) and our two Masons. Any pro-town character can be a member of a Masonic Lodge, for example a cop could also be a member of the Masons. To allow for this, the setup page lets you choose a character type first then assign them to a Masonic Lodge. Since our Masons will have no other special abilities, we start by choosing two regular villagers. Then assign them to a Masonic Lodge (there are different Masonic Lodges so you can have seperate groups of Masons - this would be very unusual in all but the largest games though).
  <p><img src="images/tutorial5.png"></p>
  <h2>Name and Description</h2>
  <p>Finally, we need to define a name for our setup. You can also add your own description to the automatically generated role list. Don't forget to click 'Save to Database' when you're done!</p>
  <p><img src="images/tutorial6.png"></p>
</div>

<?php include ('includes/footer.inc'); ?>