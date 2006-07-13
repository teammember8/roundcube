<?php
   /**
    **  imap_messages.php
    **
    **  This implements functions that manipulate messages
    **
    **  $Id$
    **/

   if (defined ('imap_messages_php'))
       return;
   define ('imap_messages_php', true);

   /******************************************************************************
    **  Copies specified messages to specified folder
    ******************************************************************************/
   function sqimap_messages_copy ($imap_stream, $start, $end, $mailbox) {
      fputs ($imap_stream, "a001 COPY $start:$end \"$mailbox\"\r\n");
      $read = sqimap_read_data ($imap_stream, "a001", true, $response, $message);
   }

   /******************************************************************************
    **  Deletes specified messages and moves them to trash if possible
    ******************************************************************************/
   function sqimap_messages_delete ($imap_stream, $start, $end, $mailbox) {
      global $move_to_trash, $trash_folder, $auto_expunge;

      if (($move_to_trash == true) && (sqimap_mailbox_exists($imap_stream, $trash_folder) && ($mailbox != $trash_folder))) {
         sqimap_messages_copy ($imap_stream, $start, $end, $trash_folder);
         sqimap_messages_flag ($imap_stream, $start, $end, "Deleted");
      } else {
         sqimap_messages_flag ($imap_stream, $start, $end, "Deleted");
      }
   }

   /******************************************************************************
    **  Sets the specified messages with specified flag
    ******************************************************************************/
   function sqimap_messages_flag ($imap_stream, $start, $end, $flag) {
      fputs ($imap_stream, "a001 STORE $start:$end +FLAGS (\\$flag)\r\n");
      $read = sqimap_read_data ($imap_stream, "a001", true, $response, $message);
   }


   /******************************************************************************
    **  Remove specified flag from specified messages
    ******************************************************************************/
   function sqimap_messages_remove_flag ($imap_stream, $start, $end, $flag) {
      fputs ($imap_stream, "a001 STORE $start:$end -FLAGS (\\$flag)\r\n");
      $read = sqimap_read_data ($imap_stream, "a001", true, $response, $message);
   }


   /******************************************************************************
    **  Returns some general header information -- FROM, DATE, and SUBJECT
    ******************************************************************************/
   class small_header {
      var $from = '', $subject = '', $date = '', $to = '', 
          $priority = 0, $message_id = 0, $cc = '';
   }
	 
   function sqimap_get_small_header ($imap_stream, $id, $sent) {
      $res = sqimap_get_small_header_list($imap_stream, array($id), $sent);
      return $res[0];
   }
   
   // Sort the message list and crunch to be as small as possible
   // (overflow could happen, so make it small if possible)
   function sqimap_message_list_squisher($messages_array) {
      sort($messages_array, SORT_NUMERIC);
      $msgs_str = '';
      while ($messages_array) {
         $start = array_shift($messages_array);
	 $end = $start;
	 while (isset($messages_array[0]) && $messages_array[0] == $end + 1)
	    $end = array_shift($messages_array);
	 if ($msgs_str != '')
	    $msgs_str .= ',';
	 $msgs_str .= $start;
	 if ($start != $end)
	    $msgs_str .= ':' . $end;
      }
      
      return $msgs_str;
   }   

   function sqimap_get_small_header_list ($imap_stream, $msg_list, $issent) {
      global $squirrelmail_language, $color;

      /* Get the small headers for each message in $msg_list */

      $maxmsg = sizeof($msg_list);
      $msgs_str = sqimap_message_list_squisher($msg_list);
      $results = array();
      $read_list = array();
      $sizes_list = array();

      /* We need to return the data in the same order as the caller supplied
         in $msg_list, but IMAP servers are free to return responses in
         whatever order they wish... So we need to re-sort manually */

      for ($i = 0; $i < sizeof($msg_list); $i++) {
         $id2index[$msg_list[$i]] = $i;
      }


      $query = "a001 FETCH $msgs_str BODY.PEEK[HEADER.FIELDS (Date To From Cc Subject Message-Id X-Priority Content-Type)]\r\n";
      fputs ($imap_stream, $query);
      $readin_list = sqimap_read_data_list($imap_stream, "a001", true, $response, $message);


      foreach ($readin_list as $r) {
         if (!eregi("^\\* ([0-9]+) FETCH", $r[0], $regs)) {
            set_up_language($squirrelmail_language);
            echo "<br><b><font color=$color[2]>\n";
            echo _("ERROR : Could not complete request.");
            echo "</b><br>\n";
            echo _("Unknown response from IMAP server: ");
            echo $r[0] . "</font><br>\n";
            exit;
         }
         if (! isset($id2index[$regs[1]]) || !count($id2index[$regs[1]])) {
            set_up_language($squirrelmail_language);
            echo "<br><b><font color=$color[2]>\n";
            echo _("ERROR : Could not complete request.");
            echo "</b><br>\n";
            echo _("Unknown message number in reply from server: ");
            echo $regs[1] . "</font><br>\n";
            exit;
         }
         $read_list[$id2index[$regs[1]]] = $r;
      }
      arsort($read_list);
      
      $query = "a002 FETCH $msgs_str RFC822.SIZE\r\n";
      fputs ($imap_stream, $query);
      $sizesin_list = sqimap_read_data_list($imap_stream, "a002", true, $response, $message);
      
      foreach ($sizesin_list as $r) {
         if (!eregi("^\\* ([0-9]+) FETCH", $r[0], $regs)) {
            set_up_language($squirrelmail_language);
            echo "<br><b><font color=$color[2]>\n";
            echo _("ERROR : Could not complete request.");
            echo "</b><br>\n";
            echo _("Unknown response from IMAP server: ");
            echo $r[0] . "</font><br>\n";
            exit;
         }
         if (!count($id2index[$regs[1]])) {
            set_up_language($squirrelmail_language);
            echo "<br><b><font color=$color[2]>\n";
            echo _("ERROR : Could not complete request.");
            echo "</b><br>\n";
            echo _("Unknown messagenumber in reply from server: ");
            echo $regs[1] . "</font><br>\n";
            exit;
         }
         $sizes_list[$id2index[$regs[1]]] = $r;
      }
      arsort($sizes_list);
      
      for ($msgi = 0; $msgi < $maxmsg; $msgi++) {
         $subject = _("(no subject)");
         $from = _("Unknown Sender");
         $priority = 0;
         $messageid = "<>";
         $cc = "";
         $to = "";
         $date = "";
         $type[0] = "";
         $type[1] = "";
         $read = $read_list[$msgi];

         for ($i = 0; $i < count($read); $i++) {
            if (eregi ("^to:(.*)$", $read[$i], $regs)) {
               //$to = sqimap_find_displayable_name(substr($read[$i], 3));
               $to = $regs[1];
            } else if (eregi ("^from:(.*)$", $read[$i], $regs)) {
               //$from = sqimap_find_displayable_name(substr($read[$i], 5));
               $from = $regs[1];
            } else if (eregi ("^x-priority:(.*)$", $read[$i], $regs)) {
               $priority = trim($regs[1]);
            } else if (eregi ("^message-id:(.*)$", $read[$i], $regs)) {
               $messageid = trim($regs[1]);
            } else if (eregi ("^cc:(.*)$", $read[$i], $regs)) {
               $cc = $regs[1];
            } else if (eregi ("^date:(.*)$", $read[$i], $regs)) {
               $date = $regs[1];
            } else if (eregi ("^subject:(.*)$", $read[$i], $regs)) {
               $subject = htmlspecialchars(trim($regs[1]));
               if ($subject == "")
                  $subject = _("(no subject)");
            } else if (eregi ("^content-type:(.*)$", $read[$i], $regs)) {
               $type = strtolower(trim($regs[1]));
               if ($pos = strpos($type, ";"))
                  $type = substr($type, 0, $pos);
               $type = explode("/", $type);
               if (! isset($type[1]))
                   $type[1] = '';
            }
            
         }
         if (trim($date) == "") {
            fputs($imap_stream, "a002 FETCH $msg_list[$msgi] INTERNALDATE\r\n");
            $readdate = sqimap_read_data($imap_stream, "a002", true, $response, $message);
            if (eregi(".*INTERNALDATE \"(.*)\".*", $readdate[0], $regs)) {
               $date_list = explode(" ", trim($regs[1]));
               $date_list[0] = str_replace("-", " ", $date_list[0]);
               $date = implode(" ", $date_list);
            }
         }
         eregi("([0-9]+)[^0-9]*$", $sizes_list[$msgi][0], $regs);
         $size = $regs[1];
         
         $header = new small_header;
         if ($issent == true)
            $header->from = (trim($to) != '')? $to : _("(only Cc/Bcc)");
         else   
            $header->from = $from;

         $header->date = $date;
         $header->subject = $subject;
         $header->to = $to;
         $header->priority = $priority;
         $header->message_id = $messageid;
         $header->cc = $cc;
         $header->size = $size;
         $header->type0 = $type[0];
         $header->type1 = $type[1];

         $result[] = $header;
      }
      return $result;
   }

   /******************************************************************************
    **  Returns the flags for the specified messages 
    ******************************************************************************/
   function sqimap_get_flags ($imap_stream, $i) {
      fputs ($imap_stream, "a001 FETCH $i:$i FLAGS\r\n");
      $read = sqimap_read_data ($imap_stream, "a001", true, $response, $message);
      if (ereg("FLAGS(.*)", $read[0], $regs))
          return explode(" ", trim(ereg_replace('[\\(\\)\\\\]', '', $regs[1])));
      return Array('None');
   }

   function sqimap_get_flags_list ($imap_stream, $msg_list) {
      $msgs_str = sqimap_message_list_squisher($msg_list);
      for ($i = 0; $i < sizeof($msg_list); $i++) {
         $id2index[$msg_list[$i]] = $i;
      }
      fputs ($imap_stream, "a001 FETCH $msgs_str FLAGS\r\n");
      $result_list = sqimap_read_data_list ($imap_stream, "a001", true, $response, $message);
      $result_flags = array();

      for ($i = 0; $i < sizeof($result_list); $i++) {
         if (eregi("^\\* ([0-9]+).*FETCH.*FLAGS(.*)", $result_list[$i][0], $regs)
             && isset($id2index[$regs[1]]) && count($id2index[$regs[1]])) {
            $result_flags[$id2index[$regs[1]]] = explode(" ", trim(ereg_replace('[\\(\\)\\\\]', '', $regs[2])));
         } else {
            set_up_language($squirrelmail_language);
            echo "<br><b><font color=$color[2]>\n";
            echo _("ERROR : Could not complete request.");
            echo "</b><br>\n";
            echo _("Unknown response from IMAP server: ");
            echo $result_list[$i][0] . "</font><br>\n";
            exit;
         }
      }
      arsort($result_flags);
      return $result_flags;
   }

   /******************************************************************************
    **  Returns a message array with all the information about a message.  See
    **  the documentation folder for more information about this array.
    ******************************************************************************/
   function sqimap_get_message ($imap_stream, $id, $mailbox) {
      $header = sqimap_get_message_header($imap_stream, $id, $mailbox);
      return sqimap_get_message_body($imap_stream, $header);
   }

   /******************************************************************************
    **  Wrapper function that reformats the header information.
    ******************************************************************************/
   function sqimap_get_message_header ($imap_stream, $id, $mailbox) {
      fputs ($imap_stream, "a001 FETCH $id:$id BODY[HEADER]\r\n");
      $read = sqimap_read_data ($imap_stream, "a001", true, $response, $message);
     
      $header = sqimap_get_header($imap_stream, $read); 
      $header->id = $id;
      $header->mailbox = $mailbox;

      return $header;
   }

   /******************************************************************************
    **  Wrapper function that returns entity headers for use by decodeMime
    ******************************************************************************/
    /*
   function sqimap_get_entity_header ($imap_stream, &$read, &$type0, &$type1, &$bound, &$encoding, &$charset, &$filename) {
      $header = sqimap_get_header($imap_stream, $read);
      $type0 = $header["TYPE0"]; 
      $type1 = $header["TYPE1"];
      $bound = $header["BOUNDARY"];
      $encoding = $header["ENCODING"];
      $charset = $header["CHARSET"];
      $filename = $header["FILENAME"];
   }
    */

   /******************************************************************************
    **  Queries the IMAP server and gets all header information.
    ******************************************************************************/
   function sqimap_get_header ($imap_stream, $read) {
      global $where, $what;

      $hdr = new msg_header();
      $i = 0;
      // Set up some defaults
      $hdr->type0 = "text";
      $hdr->type1 = "plain";
      $hdr->charset = "us-ascii";

      while ($i < count($read)) {
         if (substr($read[$i], 0, 17) == "MIME-Version: 1.0") {
            $hdr->mime = true;
            $i++;
         }

         /** ENCODING TYPE **/
         else if (substr(strtolower($read[$i]), 0, 26) == "content-transfer-encoding:") {
            $hdr->encoding = strtolower(trim(substr($read[$i], 26)));
            $i++;
         }

         /** CONTENT-TYPE **/
         else if (strtolower(substr($read[$i], 0, 13)) == "content-type:") {
            $cont = strtolower(trim(substr($read[$i], 13)));
            if (strpos($cont, ";"))
               $cont = substr($cont, 0, strpos($cont, ";"));


            if (strpos($cont, "/")) {
               $hdr->type0 = substr($cont, 0, strpos($cont, "/"));
               $hdr->type1 = substr($cont, strpos($cont, "/")+1);
            } else {
               $hdr->type0 = $cont;
            }


            $line = $read[$i];
            $i++;
            while ( (substr(substr($read[$i], 0, strpos($read[$i], " ")), -1) != ":") && (trim($read[$i]) != "") && (trim($read[$i]) != ")")) {
               str_replace("\n", "", $line);
               str_replace("\n", "", $read[$i]);
               $line = "$line $read[$i]";
               $i++;
            }

            /** Detect the boundary of a multipart message **/
            if (eregi('boundary="([^"]+)"', $line, $regs)) {                             
               $hdr->boundary = $regs[1];                                             
            }

            /** Detect the charset **/
            if (strpos(strtolower(trim($line)), "charset=")) {
               $pos = strpos($line, "charset=") + 8;
               $charset = trim($line);
               if (strpos($line, ";", $pos) > 0) {
                  $charset = substr($charset, $pos, strpos($line, ";", $pos)-$pos);
               } else {
                  $charset = substr($charset, $pos);
               }
               $charset = str_replace("\"", "", $charset);
               $hdr->charset = $charset;
            } else {
               $hdr->charset = "us-ascii";
            }

         }

         else if (strtolower(substr($read[$i], 0, 20)) == "content-disposition:") {
            /** Add better dontent-disposition support **/
            
            $line = $read[$i];
            $i++;
            while ( (substr(substr($read[$i], 0, strpos($read[$i], " ")), -1) != ":") && (trim($read[$i]) != "") && (trim($read[$i]) != ")")) {
               str_replace("\n", "", $line);
               str_replace("\n", "", $read[$i]);
               $line = "$line $read[$i]";
               $i++;
            }

            /** Detects filename if any **/
            if (strpos(strtolower(trim($line)), "filename=")) {
               $pos = strpos($line, "filename=") + 9;
               $name = trim($line);
               if (strpos($line, " ", $pos) > 0) {
                  $name = substr($name, $pos, strpos($line, " ", $pos));
               } else {
                  $name = substr($name, $pos);
               }
               $name = str_replace("\"", "", $name);
               $hdr->filename = $name;
            }
         }

         /** REPLY-TO **/
         else if (strtolower(substr($read[$i], 0, 9)) == "reply-to:") {
            $hdr->replyto = trim(substr($read[$i], 9, strlen($read[$i])));
            $i++;
         }

         /** FROM **/
         else if (strtolower(substr($read[$i], 0, 5)) == "from:") {
            $hdr->from = trim(substr($read[$i], 5, strlen($read[$i]) - 6));
            if (! isset($hdr->replyto) || $hdr->replyto == "")
               $hdr->replyto = $hdr->from;
            $i++;
         }
         /** DATE **/
         else if (strtolower(substr($read[$i], 0, 5)) == "date:") {
            $d = substr($read[$i], 5);
            $d = trim($d);
            $d = strtr($d, array('  ' => ' '));
            $d = explode(' ', $d);
            $hdr->date = getTimeStamp($d);
            $i++;
         }
         /** SUBJECT **/
         else if (strtolower(substr($read[$i], 0, 8)) == "subject:") {
            $hdr->subject = trim(substr($read[$i], 8, strlen($read[$i]) - 9));
            if (strlen(Chop($hdr->subject)) == 0)
               $hdr->subject = _("(no subject)");

            if ($where == "SUBJECT") {
               $hdr->subject = eregi_replace($what, "<b>\\0</b>", $hdr->subject);
            }
            $i++;
         }
         /** CC **/
         else if (strtolower(substr($read[$i], 0, 3)) == "cc:") {
            $pos = 0;
            $hdr->cc[$pos] = trim(substr($read[$i], 4));
            $i++;
            while (((substr($read[$i], 0, 1) == " ") || (substr($read[$i], 0, 1) == "\t"))  && (trim($read[$i]) != "")){
               $pos++;
               $hdr->cc[$pos] = trim($read[$i]);
               $i++;
            }
         }
         /** TO **/
         else if (strtolower(substr($read[$i], 0, 3)) == "to:") {
            $pos = 0;
            $hdr->to[$pos] = trim(substr($read[$i], 4));
            $i++;
            while (((substr($read[$i], 0, 1) == " ") || (substr($read[$i], 0, 1) == "\t"))  && (trim($read[$i]) != "")){
               $pos++;
               $hdr->to[$pos] = trim($read[$i]);
               $i++;
            }
         }
         /** MESSAGE ID **/
         else if (strtolower(substr($read[$i], 0, 11)) == "message-id:") {
            $hdr->message_id = trim(substr($read[$i], 11));
            $i++;
         }


         /** ERROR CORRECTION **/
         else if (substr($read[$i], 0, 1) == ")") {
            if (strlen(trim($hdr->subject)) == 0)
                $hdr->subject = _("(no subject)");

            if (strlen(trim($hdr->from)) == 0)
                $hdr->from = _("(unknown sender)");

            if (strlen(trim($hdr->date)) == 0)
                $hdr->date = time();
            $i++;
         }
         else {
            $i++;
         }
      }
      return $hdr;
   }


   /******************************************************************************
    **  Returns the body of a message.
    ******************************************************************************/
   function sqimap_get_message_body ($imap_stream, &$header) {
      $id = $header->id;
      return decodeMime($imap_stream, $header);
   }


   /******************************************************************************
    **  Returns an array with the body structure 
    ******************************************************************************/
?>