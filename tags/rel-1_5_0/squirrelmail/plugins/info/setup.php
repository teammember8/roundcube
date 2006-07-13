<?php

/** setup file for the IMAP server info plugin
 * Copyright (c) 1999-2003 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *   
 * by: Jason Munro jason@stdbev.com
 * 
 * $Id$
 * @package plugins
 * @subpackage info
 */

/** @ignore */
require_once (SM_PATH . 'plugins/info/functions.php');

function squirrelmail_plugin_init_info() {
    global $squirrelmail_plugin_hooks;
    $squirrelmail_plugin_hooks['optpage_register_block']['info'] = 'info_opt';
}

function info_opt() {
    global $optpage_blocks;

    $optpage_blocks[] = array(
        'name' => _("IMAP server information"),
        'url'  => '../plugins/info/options.php',
        'desc' => _("Run some test IMAP commands, displaying both the command and the result. These tests use the Squirrelmail IMAP commands and your current Squirrelmail configuration. Custom command strings can be used."),
        'js'   => false
    );
}

?>