<?php

/**
 * help.php
 *
 * Copyright (c) 1999-2002 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Displays help for the user
 *
 * $Id$
 */

require_once('../src/validate.php');
require_once('../functions/display_messages.php');
require_once('../functions/imap.php');
require_once('../functions/array.php');

displayPageHeader($color, 'None' );

$helpdir[0] = 'basic.hlp';
$helpdir[1] = 'main_folder.hlp';
$helpdir[2] = 'read_mail.hlp';
$helpdir[3] = 'compose.hlp';
$helpdir[4] = 'addresses.hlp';
$helpdir[5] = 'folders.hlp';
$helpdir[6] = 'options.hlp';
$helpdir[7] = 'search.hlp';
$helpdir[8] = 'FAQ.hlp';

/****************[ HELP FUNCTIONS ]********************/

/**
 * parses through and gets the information from the different documents.
 * this returns one section at a time.  You must keep track of the position
 * so that it knows where to start to look for the next section.
 */

function get_info($doc, $pos) {
    for ($n=$pos; $n < count($doc); $n++) {
        if (trim(strtolower($doc[$n])) == '<chapter>'
            || trim(strtolower($doc[$n])) == '<section>') {
            for ($n++;$n < count($doc) 
                 && (trim(strtolower($doc[$n])) != '</section>') 
                 && (trim(strtolower($doc[$n])) != '</chapter>'); $n++) {
                if (trim(strtolower($doc[$n])) == '<title>') {
                    $n++;
                    $ary[0] = trim($doc[$n]);
                }
                if (trim(strtolower($doc[$n])) == '<description>') {
                    $ary[1] = '';
                    for ($n++;$n < count($doc) 
                         && (trim(strtolower($doc[$n])) != '</description>');
                         $n++) {
                        $ary[1] .= $doc[$n];
                    }
                }
                if (trim(strtolower($doc[$n])) == '<summary>') {
                    $ary[2] = '';
                    for ($n++; $n < count($doc) 
                         && (trim(strtolower($doc[$n])) != '</summary>'); 
                         $n++) {
                        $ary[2] .= $doc[$n];
                    }
                }
            }
            if (isset($ary)) {
                $ary[3] = $n;
                return $ary;
            } else {
                $ary[0] = 'ERROR: Help files are not in the right format!';
                $ary[1] = 'ERROR: Help files are not in the right format!';
                $ary[2] = 'ERROR: Help files are not in the right format!';
                return $ary;
            }
        }
    }
    $ary[0] = 'ERROR: Help files are not in the right format!';
    $ary[1] = 'ERROR: Help files are not in the right format!';
    return $ary;
}

/**************[ END HELP FUNCTIONS ]******************/

?>

<br>
<table width="95%" align="center" cellpadding="2" cellspacing="2" border="0">
 <tr>
  <td bgcolor="<?php echo $color[0] ?>">
   <center><b><?php echo _("Help") ?></b></center>
  </td>
 </tr>
</table>

<?php do_hook("help_top") ?>

<table width="90%" cellpadding="0" cellspacing="10" border="0" align="center">
<tr>
<td>
<?php 
if (isset($HTTP_REFERER)) {
    $ref = strtolower($HTTP_REFERER);
    if (strpos($ref, 'src/compose')){
        $context = 'compose';
    } else if (strpos($ref, 'src/addr')){
        $context = 'address';
    } else if (strpos($ref, 'src/folders')){
        $context = 'folders';
    } else if (strpos($ref, 'src/options')){
        $context = 'options';
    } else if (strpos($ref, 'src/right_main')){
        $context = 'index';
    } else if (strpos($ref, 'src/read_body')){
        $context = 'read';
    } else if (strpos($ref, 'src/search')){
        $context = 'search';
    }
}
  
if (!isset($squirrelmail_language)) {
    $squirrelmail_language = 'en';
}

/** 
 * This harebrained solution is here because it produces the
 * smallest patchfile.
 * The real solution would be to either:
 * a) move all locales into full-name locale names, like they
 * really should be according to the ISO docs (e.g. en -> en_US, 
 * es -> es_ES, ru -> ru_RU), since it's standard to have a language
 * name + undescore + country name. 
 * b) Provide a $languages['ru_RU']['HELPALIAS'] = 'ru';
 *
 * Konstantin Riabitsev
 */
global $languages;
while (list($key, $val) = each($languages)){
    if ($val['ALIAS'] == $squirrelmail_language){
        $squirrelmail_language = $key;
        break;
    }
}

if (file_exists("../help/$squirrelmail_language")) {
    $help_exists = true;
    $user_language = $squirrelmail_language;
} else if (file_exists('../help/en')) {
    $help_exists = true;
    echo "<center><font color=\"$color[2]\">";
    printf (_("The help has not been translated to %s.  It will be displayed in English instead."), $languages[$squirrelmail_language]['NAME']);
    echo '</font></center><br>';
    $user_language = 'en';
} else {
    $help_exists = false;
    echo "<br><center><font color=\"$color[2]\">";
    echo _("Some or all of the help documents are not present!");
    echo '</font></center>';
    echo '</td></tr></table>';
    /* this is really silly, because there may be some
     * footers. What about them.
     * TODO: Fix this so it's not just "exit".
     */
    exit;
}

if ($help_exists == true) {
    if (!isset($context)){
        $context = '';
    }
    if ($context == 'compose'){
        $chapter = 4;
    } else if ($context == 'address'){
        $chapter = 5;
    } else if ($context == 'folders'){
        $chapter = 6;
    } else if ($context == 'options'){
        $chapter = 7;
    } else if ($context == 'index'){
        $chapter = 2;
    } else if ($context == 'read'){
        $chapter = 3;
    } else if ($context == 'search'){
        $chapter = 8;
    }

    if (!isset($chapter)) {
        echo '<table cellpadding="0" cellspacing="0" border="0" 
              align="center"><tr><td>';
        echo '<b><center>' . _("Table of Contents") . '</center></b><br>';
        do_hook('help_chapter');
        echo '<ol>';
        for ($i=0; $i < count($helpdir); $i++) {
            $doc = file("../help/$user_language/$helpdir[$i]");
            $help_info = get_info($doc, 0);
            echo '<li><a href="../src/help.php?chapter=' . ($i+1) 
                 . '">' . $help_info[0] . '</a>';
                 echo '<ul>' . $help_info[2] . '</ul>';
        }
        echo '</ol>';
        echo '</td></tr></table>';
    } else {
        $doc = file("../help/$user_language/" . $helpdir[$chapter-1]);
        $help_info = get_info($doc, 0);
        echo '<small><center>';
        if ($chapter <= 1){
            echo '<font color="' . $color[9] . '">' . _("Previous") 
                 . '</font> | ';
        } else {
            echo '<a href="../src/help.php?chapter=' . ($chapter-1) 
                 . '">' . _("Previous") . '</a> | ';
        }
        echo '<a href="../src/help.php">' . _("Table of Contents") . '</a>';
        if ($chapter >= count($helpdir)){
            echo ' | <font color="$color[9]">' . _("Next") . '</font>';
        } else { 
            echo ' | <a href="../src/help.php?chapter=' . ($chapter+1)
                 . '">' . _("Next") . '</a>';
        }
        echo '</center></small><br>';

        echo '<font size="5"><b>' . $chapter . ' - ' . $help_info[0]
             . '</b></font><br><br>';
        if (isset($help_info[1])){
            echo $help_info[1];
        } else {
            echo '<p>' . $help_info[2] . '</p>';
        }
             
        $section = 0;
        for ($n = $help_info[3]; $n < count($doc); $n++) {
            $section++;
            $help_info = get_info($doc, $n);
            echo "<b>$chapter.$section - $help_info[0]</b>";
            echo '<ul>';
            echo $help_info[1];
            echo '</ul>';
            $n = $help_info[3];
        }

        echo '<br><center><a href="#pagetop">' . _("Top") . '</a></center>';
    }
}
do_hook('help_bottom');
?>
<tr><td bgcolor="<?php echo $color[0] ?>">&nbsp;</td></tr></table>
<td></tr></table>
</body></html>
