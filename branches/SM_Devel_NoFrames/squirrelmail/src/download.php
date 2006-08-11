<?php

/**
 * download.php
 *
 * Copyright (c) 1999-2003 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Handles attachment downloads to the users computer.
 * Also allows displaying of attachments when possible.
 *
 * $Id$
 * @package squirrelmail
 */

/** Path for SquirrelMail required files. */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/mime.php');

header('Pragma: ');
header('Cache-Control: cache');

/* globals */
sqgetGlobalVar('key',        $key,          SQ_COOKIE);
sqgetGlobalVar('username',   $username,     SQ_SESSION);
sqgetGlobalVar('onetimepad', $onetimepad,   SQ_SESSION);
sqgetGlobalVar('messages',   $messages,     SQ_SESSION);
sqgetGlobalVar('mailbox',    $mailbox,      SQ_GET);
sqgetGlobalVar('ent_id',     $ent_id,       SQ_GET);
sqgetGlobalVar('absolute_dl',$absolute_dl,  SQ_GET);
if ( sqgetGlobalVar('passed_id', $temp, SQ_GET) ) {
  $passed_id = (int) $temp;
}

/* end globals */

global $uid_support;

$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$mbx_response =  sqimap_mailbox_select($imapConnection, $mailbox);

$message = &$messages[$mbx_response['UIDVALIDITY']]["$passed_id"];
if (!is_object($message)) {
    $message = sqimap_get_message($imapConnection,$passed_id, $mailbox);
}
$subject = $message->rfc822_header->subject;
if ($ent_id) {
    $message = &$message->getEntity($ent_id);
    $header = $message->header;

    if ($message->rfc822_header) {
       $subject = $message->rfc822_header->subject;
    } else {
       $header = $message->header;
    }
    $type0 = $header->type0;
    $type1 = $header->type1;
    $encoding = strtolower($header->encoding);
} else {
    /* raw message */
    $type0 = 'message';
    $type1 = 'rfc822';
    $encoding = 'US-ASCII';
    $header = $message->header;
}

/*
 * lets redefine message as this particular entity that we wish to display.
 * it should hold only the header for this entity.  We need to fetch the body
 * yet before we can display anything.
 */

if (isset($override_type0)) {
    $type0 = $override_type0;
}
if (isset($override_type1)) {
    $type1 = $override_type1;
}
$filename = '';
if (is_object($message->header->disposition)) {
    $filename = $header->disposition->getProperty('filename');
    if (!$filename) {
        $filename = $header->disposition->getProperty('name');
    }
    if (!$filename) {
        $filename = $header->getParameter('name');
    }
} else {
    $filename = $header->getParameter('name');
}

$filename = decodeHeader($filename, true, false);   //Don't want html output
if (strlen($filename) < 1) {
    if ($type1 == 'plain' && $type0 == 'text') {
        $suffix = 'txt';
        $filename = $subject . '.txt';
    } else if ($type1 == 'richtext' && $type0 == 'text') {
        $suffix = 'rtf';
        $filename = $subject . '.rtf';
    } else if ($type1 == 'postscript' && $type0 == 'application') {
        $suffix = 'ps';
        $filename = $subject . '.ps';
    } else if ($type1 == 'rfc822' && $type0 == 'message') {
        $suffix = 'eml';
        $filename = $subject . '.msg';
    } else {
        $suffix = $type1;
    }

    if (strlen($filename) < 1) {
       $filename = 'untitled'.strip_tags($ent_id).'.'.$suffix;
    } else {
       $filename = "$filename.$suffix";
    }
}

/*
 * Note:
 *    The following sections display the attachment in different
 *    ways depending on how they choose.  The first way will download
 *    under any circumstance.  This sets the Content-type to be
 *    applicatin/octet-stream, which should be interpreted by the
 *    browser as "download me".
 *      The second method (view) is used for images or other formats
 *    that should be able to be handled by the browser.  It will
 *    most likely display the attachment inline inside the browser.
 *      And finally, the third one will be used by default.  If it
 *    is displayable (text or html), it will load them up in a text
 *    viewer (built in to squirrelmail).  Otherwise, it sets the
 *    content-type as application/octet-stream
 */
if (isset($absolute_dl) && $absolute_dl) {
    SendDownloadHeaders($type0, $type1, $filename, 1);
} else {
    SendDownloadHeaders($type0, $type1, $filename, 0);
}
/* be aware that any warning caused by download.php will corrupt the
 * attachment in case of ERROR reporting = E_ALL and the output is the screen */
mime_print_body_lines ($imapConnection, $passed_id, $ent_id, $encoding);

?>