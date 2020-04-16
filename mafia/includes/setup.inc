<script type="text/javascript">

var groupOptions = new Array(); //Stores possible group choices for different character alignment

groupOptions[0] = '<?php
foreach (array(0,9,10,11,12) as $i) {
echo "<option value=\"$i\">{$groups[$i][0]}</option>";
}
?>';
groupOptions[1] = '<?php
for ($i=1; $i<4; $i++) {
echo "<option value=\"$i\">{$groups[$i][0]}</option>";
}
?>';
groupOptions[4] = '<?php
for ($i=4; $i<9; $i++) {
echo "<option value=\"$i\">{$groups[$i][0]}</option>";
}
?>';



var winGroup = new Array();

<?php #Transfers default win group data from php array to javascript array
   foreach ($characters as $i) {
    echo "winGroup[{$i[0]}] = {$i[2]};";
   }
?>

function updateGroupOptions(num) {
 var group = 'gc' + num;
 var character = document.getElementById('character'+num);
 var wg = winGroup[character.value];
 if (wg >= 9) {
  document.getElementById(group).innerHTML = '<input type="hidden" id="group' + num + '" value="0" />';
 } else {
  document.getElementById(group).innerHTML = 'in <select id="group' + num + '">' + groupOptions[wg] + '</select>';
  var name = character.options[character.selectedIndex].text;
 }
 if ((wg == 0) && (name.indexOf(' Cop') != -1)) {
  document.getElementById('showas'+num).innerHTML='<b>'+ name + ' shown in role list as <select id="knownas'+num+'"><option value="1" selected>' + name + '</option><option value="0">Cop</option></select>';
 } else {
  document.getElementById('showas'+num).innerHTML='<input type="hidden" id="knownas'+num+'" value="1">';
 }
}

function altCharacter() {
 if (document.getElementById('chance').value == 5) {
  document.getElementById('alternative').style.display = 'none';
  document.getElementById('showas2').innerHTML='<input type="hidden" id="knownas2" value="1" />';
 } else {
  document.getElementById('alternative').style.display = 'inline';
 }
}

function groups(num) { //Works out actual chatgroup and wingroup values for each character
 var group = 'group'+num;
 var character = 'character'+num; 
 var cid = document.getElementById(character).value;
 var gv = document.getElementById(group).value;
 var wg = winGroup[cid];
 if (wg == 0) {
  return '' + gv + ',' + '0';
 } else if (wg == 4) {
  if (cid == 60) {
   return '0,' + gv;
  } else {
   return '' + gv + ',' + gv;
  }
 } else if (wg == 1) {
  return '' + gv + ',' + gv;
 } else {
  return '0,' + wg;
 }
}

function printCharacter(num) { //Returns string describing character name and (if applicable) group
 var cid = document.getElementById('character'+num);
 var gv = document.getElementById('group'+num);
 if (document.getElementById('knownas'+num).value == 0) {
  var retstr = 'Cop';
 } else {
  var retstr = cid.options[cid.selectedIndex].text;
 }
 if (gv.value > 0) { 
  retstr += ' (with the ' + gv.options[gv.selectedIndex].text + ')';
 }
 return retstr;
}

function parseCharacter() {
 var ds=''; //Description string
 var cs=''; //Characters array string
 
 var players = document.getElementById('players');
 
 if (players.value > 0) { cs += ';'; }

 // Check and update number of players
 var num = parseInt(document.getElementById('number').value);
 if ((parseInt(players.value) + num) > 255) {
  alert ('Too many players! Max: 255');
  return;
 } else {
  players.value = parseInt(players.value) + num;
 }

 // Information for primary character
 ds += document.getElementById('number').value + ' x ' + printCharacter(1);
 cs += document.getElementById('character1').value + ',' + document.getElementById('number').value + ',' + groups(1) + ',';
 
 var chance = document.getElementById('chance');
 if (chance.value == 5) { //Check for secondary character
  cs += '5';
 } else { //Information for secondary character
  cs += chance.value + ',' + document.getElementById('character2').value + ',' + groups(2);
  ds += ' with probability ' + chance.options[chance.selectedIndex].text + ' Alternative: ' + printCharacter(2);
 }

 //Update characters array
 document.getElementById('characters').value += cs;
 
 //Update description
 var known = document.getElementById('known');
 
 if (((document.getElementById('character1').value == 0) && (document.getElementById('group1').value == 0) && (chance.value == 5)) || (!known.checked)) {
  document.getElementById('charlist').innerHTML += '<li class="italic">' + ds + '</li>';
 } else {
  document.getElementById('charlist').innerHTML += '<li>' + ds + '</li>';
  document.getElementById('description').value += '<li>' + ds + '</li>';
 }
 
 if ((Number(known.checked)+parseInt(document.getElementById('knownas1').value)+parseInt(document.getElementById('knownas2').value)) < 3) {
  document.getElementById('allknown').value = 'no';
 }
 
 document.getElementById('undo').style.visibility = 'visible';
 
 // Reset form
 document.getElementById('alternative').style.display = 'none';
 document.getElementById('character1').value = document.getElementById('character2').value = -1;
 known.checked = document.getElementById('number').value = 1;
 document.getElementById('gc1').innerHTML = document.getElementById('gc2').innerHTML = '';
 document.getElementById('chance').value = 5;
}

function addCharacter() { //Checks for valid input
 if (document.getElementById('character1').value == -1) {
  alert ("No primary character selected");
 } else if ((document.getElementById('character2').value == -1) && (document.getElementById('chance').value != 5)) {
  alert ("No alternative character selected");
 } else {
  parseCharacter();
 }
}

function delCharacter() {
 var c = document.getElementById('characters');
 c.value = (c.value.substr(0,c.value.lastIndexOf(';', c.value.length-2)));
 var cl = document.getElementById('charlist');
 var cls = cl.getElementsByTagName('li');
 cls = cls[cls.length-1]
 var p = document.getElementById('players');
 p.value = parseInt(p.value) - parseInt(cls.innerHTML.split(' ', 1));
 if (cls.getAttribute('class') != 'italic') {
  var d = document.getElementById('description');
  d.value = (d.value.substr(0,d.value.lastIndexOf('<li>', d.value.length-2)));
 }
 cl.removeChild(cls);
 if (p.value == 0) { document.getElementById('undo').style.visibility = 'hidden'; }
}

function toggleHelp() {
 var help = document.getElementById('help');
 if (help.style.display == 'none') {
  help.style.display = 'block';
 } else {
  help.style.display = 'none';
 }
}

function validateForm() {
 if (document.getElementById('name').value == '') {
  alert ('Please enter a name for this setup');
 } else if (document.getElementById('characters').value == '') {
  alert ('No characters chosen!');
 } else {
  document.getElementById('mainform').submit();
 }
}

</script>