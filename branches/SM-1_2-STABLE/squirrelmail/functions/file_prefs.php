<?php

/**
 * file_prefs.php
 *
 * Copyright (c) 1999-2002 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This contains functions for manipulating user preferences in files
 *
 * $Id$
 */


/**
 * Check the preferences into the session cache.
 */
function cachePrefValues($data_dir, $username) {
    global $prefs_are_cached, $prefs_cache;
    if ( isset($prefs_are_cached) && $prefs_are_cached) {
        return;
    }
    
    sqsession_unregister('prefs_cache');
    sqsession_unregister('prefs_are_cached');
 
    /* Just in case we've got a slash into the user name */
    $username = str_replace( '/', '.', $username );
 
    /* Calculate the filename for the user's preference file */
    $filename = getHashedFile($username, $data_dir, "$username.pref");

    /* this to be called throughout the rest of the SquirrelMail code. */
    checkForPrefs($data_dir, $username, $filename);

    /* Make sure that the preference file now DOES exist. */
    if (!file_exists($filename)) {
        include_once( '../functions/display_messages.php' );
        logout_error( sprintf( _("Preference file, %s, does not exist. Log out, and log back in to create a default preference file."), $filename)  );
        exit;
    }
    /* Open the file, or else display an error to the user. */
    if(!$file = @fopen($filename, 'r')) {
        include_once( '../functions/display_messages.php' );
        logout_error( sprintf( _("Preference file, %s, could not be opened. Contact your system administrator to resolve this issue."), $filename) );
        exit;
    }

    /* Read in the preferences. */
    $highlight_num = 0;
    while (! feof($file)) {
	$pref = '';
	/* keep reading a pref until we reach an eol (\n (or \r for macs)) */
	while($read = fgets($file, 1024))
	{
		$pref .= $read;
		if(strpos($read,"\n") || strpos($read,"\r"))
			break;
	}
        $pref = trim($pref);
        $equalsAt = strpos($pref, '=');
        if ($equalsAt > 0) {
            $key = substr($pref, 0, $equalsAt);
            $value = substr($pref, $equalsAt + 1);
            /* this is to 'rescue' old-style highlighting rules. */
            if (substr($key, 0, 9) == 'highlight') {
                $key = 'highlight' . $highlight_num;
                $highlight_num ++;
            }

            if ($value != '') {
                $prefs_cache[$key] = $value;
            }
        }
     }
     fclose($file);

    $prefs_are_cached = TRUE;

    sqsession_register($prefs_cache, 'prefs_cache');
    sqsession_register($prefs_are_cached, 'prefs_are_cached');
}
   
/**
 * Return the value for the prefernce given by $string.
 */
function getPref($data_dir, $username, $string, $default = '') {
    global $prefs_cache;
    $result = '';

    cachePrefValues($data_dir, $username);

    if (isset($prefs_cache[$string])) {
        $result = $prefs_cache[$string];
    } else {
        $result = $default;
    }

    return ($result);
}

/**
 * Save the preferences for this user.
 */
function savePrefValues($data_dir, $username) {
    global $prefs_cache;

    /* Just in case we've got a slash into the user name */
    $username = str_replace( '/', '.', $username );

    $filename = getHashedFile($username, $data_dir, "$username.pref");

    /* Open the file for writing, or else display an error to the user. */
    if(!$file = @fopen($filename. '.tmp', 'w')) {
        include_once( '../functions/display_messages.php' );
        logout_error( sprintf( _("Preference file, %s, could not be opened. Contact your system administrator to resolve this issue."), "$filename.tmp") );
        exit;
    }

    foreach ($prefs_cache as $Key => $Value) {
        if (isset($Value)) {
            $tmpwrite = @fwrite($file, $Key . '=' . $Value . "\n");
            if ($tmpwrite == -1) {
               logout_error( sprintf( _("Preference file, %s, could not be written. Contact your system administrator to resolve this issue.") , $filename . '.tmp') );
               exit;
            }
        }
    }
    fclose($file);
    @copy($filename . '.tmp',$filename);
    @unlink($filename . '.tmp');
    chmod($filename, 0600);
    sqsession_register($prefs_cache , 'prefs_cache');
}

/**
 * Remove a preference for the current user.
 */
function removePref($data_dir, $username, $string) {
    global $prefs_cache;

    cachePrefValues($data_dir, $username);
 
    if (isset($prefs_cache[$string])) {
        unset($prefs_cache[$string]);
    }

    savePrefValues($data_dir, $username);
}

/**
 * Set a there preference $string to $value.
 */
function setPref($data_dir, $username, $string, $value) {
    global $prefs_cache;

    cachePrefValues($data_dir, $username);
    if (isset($prefs_cache[$string]) && ($prefs_cache[$string] == $value)) {
        return;
    }

    if ($value === '') {
        removePref($data_dir, $username, $string);
        return;
    }

    $prefs_cache[$string] = $value;
    savePrefValues($data_dir, $username);
}

/**
 * Check for a preferences file. If one can not be found, create it.
 */
function checkForPrefs($data_dir, $username, $filename = '') {

    /* Just in case we've got a slash into the user name */
    $username = str_replace( '/', '.', $username );
	
    /* First, make sure we have the filename. */
    if ($filename == '') {
        $filename = getHashedFile($username, $data_dir, "$username.pref");
    }

    /* Then, check if the file exists. */
    if (!@file_exists($filename) ) {
        /* First, check the $data_dir for the default preference file. */
        $default_pref = $data_dir . 'default_pref';

        /* If it is not there, check the internal data directory. */
        if (!@file_exists($default_pref)) {
            $default_pref = '../data/default_pref';
        }

        /* Otherwise, report an error. */
        $errTitle = sprintf( _("Error opening %s"), $default_pref );
        if (!is_readable($default_pref)) {
            $errString = $errTitle . "<br>\n" .
                         _("Default preference file not found or not readable!") . "<br>\n" .
                         _("Please contact your system administrator and report this error.") . "<br>\n";
            include_once( '../functions/display_messages.php' );
            logout_error( $errString, $errTitle );
            exit;
        } else if (!@copy($default_pref, $filename)) {
            $uid = 'httpd';
            if (function_exists('posix_getuid')){
                $user_data = posix_getpwuid(posix_getuid());
                $uid = $user_data['name'];
            }
            $errString = $errTitle . '<br>' .
                       _("Could not create initial preference file!") . "<br>\n" .
                       sprintf( _("%s should be writable by user %s"), $data_dir, $uid ) .
                       "<br>\n" . _("Please contact your system administrator and report this error.") . "<br>\n";
            include_once( '../functions/display_messages.php' );
            logout_error( $errString, $errTitle );
            exit;
        }
    }
}

/**
 * Write the User Signature.
 */
function setSig($data_dir, $username, $number, $value) {

    /* Just in case we've got a slash into the user name */
    $username = str_replace( '/', '.', $username );

    $filename = getHashedFile($username, $data_dir, "$username.si$number");
    /* Open the file for writing, or else display an error to the user. */
    if(!$file = @fopen("$filename.tmp", 'w')) {
        include_once( '../functions/display_messages.php' );
        logout_error( sprintf( _("Signature file, %s, could not be opened. Contact your system administrator to resolve this issue."), "$filename.tmp") );
        exit;
    }
    $tmpwrite = @fwrite($file, $value);
    if ($tmpwrite == -1) {
       include_once( '../functions/display_messages.php' );
       logout_error( sprintf( _("Signature file, %s, could not be written. Contact your system administrator to resolve this issue.") , $filename . '.tmp'));
       exit;
    }
    fclose($file);
    copy("$filename.tmp","$filename");
    unlink("$filename.tmp");
}

/**
 * Get the signature.
 */
function getSig($data_dir, $username, $number) {

    /* Just in case we've got a slash into the user name */
    $username = str_replace( '/', '.', $username );
    
    $filename = getHashedFile($username, $data_dir, "$username.si$number");
    $sig = '';
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        /* Open the file, or else display an error to the user. */
        if(!$file = @fopen($filename, 'r')) {
            include_once( '../functions/display_messages.php' );
            logout_error( sprintf( _("Signature file, %s, could not be opened. Contact your system administrator to resolve this issue."), $filename) );
            exit;
        }
        while (!feof($file)) {
            $sig .= fgets($file, 1024);
        }
        fclose($file);
    }
    return $sig;
}
