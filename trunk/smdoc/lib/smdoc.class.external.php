<?php
/*
 * Copyright (c) 1999-2003 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This file is an addition/modification to the 
 * Framework for Object Orientated Web Development (Foowd).
 *
 * $Id$
 */

/** 
 * ARRAY filled with external resources
 * Each static resource should supply the following:
 *   func - name of function to call to retrieve content
 *   title - title of external resource
 *   group - Group permitted to view external resource (Everyone by default)
 */
if ( !isset($EXTERNAL_RESOURCES) ) $EXTERNAL_RESOURCES = array();

/** METHOD PERMISSIONS **/
setPermission('foowd_external', 'class',  'create', 'Nobody');
setPermission('foowd_external', 'object', 'admin', 'Nobody');
setPermission('foowd_external', 'object', 'revert', 'Nobody');
setPermission('foowd_external', 'object', 'delete', 'Nobody');
setPermission('foowd_external', 'object', 'clone', 'Nobody');
setPermission('foowd_external', 'object', 'permissions', 'Nobody');

/** CLASS DESCRIPTOR **/
setClassMeta('foowd_external', 'Externally Defined Objects');

setConst('EXTERNAL_CLASS_ID', META_FOOWD_EXTERNAL_CLASS_ID);

class foowd_external extends foowd_object {

/*** Constructor ***/

	function foowd_external(
		&$foowd,
		$objectid = NULL
	) {
		global $EXTERNAL_RESOURCES;
        $foowd->track('foowd_external::foowd_external');
		
        $this->__wakeup(); // init meta arrays

		$this->objectid = $objectid;
		$this->classid = EXTERNAL_CLASS_ID;
        $this->workspaceid = 0;
        $this->creatorid = 0;
        $this->creatorName = 'System';
        $this->updatorid = 0;
        $this->updatorName = 'System';
        $this->url = getURI(array('classid' => EXTERNAL_CLASS_ID,
                                  'objectid' => $objectid));
		
		$this->title = htmlspecialchars($EXTERNAL_RESOURCES[$objectid]['title']);

        $this->version = 0;

        $last_modified = time();
        $this->created = $last_modified;
        $this->updated = $last_modified;

        // method permissions
        $view_group = NULL;
        if ( isset($EXTERNAL_RESOURCES[$objectid]['group']) ) 
            $view_group = htmlspecialchars($EXTERNAL_RESOURCES[$objectid]['group']);

		if ($view_group != NULL) $this->permissions['view'] = $view_group;

        $foowd->track();
    }

    function set(&$foowd, $member, $value = NULL) {
        return FALSE;
    }

    function save(&$foowd) {
        return FALSE;
    }

    function delete(&$foowd) {
        return FALSE;
    }

/** METHODS */    
    function method_view(&$foowd) {     
        global $EXTERNAL_RESOURCES;
        $foowd->track('foowd_external::method_view');

        $methodName = $EXTERNAL_RESOURCES[$this->objectid]['func'];
        $foowd->tpl->assign('PAGE_TITLE', $this->title);
        $foowd->tpl->assign('PAGE_TITLE_URL', 
                            '<a href="'.$this->url.'">'.$this->title.'</a>');
        $foowd->tpl->assign_by_ref('CURRENT_OBJECT', $this);

        if (function_exists($methodName)) {
            $methodName(&$foowd);
        } else {
            triggerError('Request for unknown method, '. $methodName 
                         . ', on external resource, ' . $this->title
                         . ' (object id = ' . $this->objectid . ')' );
        }
        $foowd->track();
    }

    function method_history(&$foowd) {
      header('Location: '. $this->url);
    }

    function method_admin(&$foowd) {
      header('Location: '. $this->url);
    }
    
    function method_revert(&$foowd) {
      header('Location: '. $this->url);
    }

    function method_delete(&$foowd) {
      header('Location: '. $this->url);
    }
    
    function method_clone(&$foowd) {
      header('Location: '. $this->url);
    }

} // end static class
?>
