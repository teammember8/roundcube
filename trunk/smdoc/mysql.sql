#
# Default data for WTF v0.19+
#
# This is the default data for the Wiki Type Framework, you will need to
# execute this SQL on your database to be able to run WTF.
#
# This script includes data for the Root user, the Anonymous user, and
# the default front, nothing found, recent changes, and thing list pages.
#
# The Root password for the system is set as "root", you should log in to
# the site as root and change this password before making your site live.
#

#
# Table structure for table 'tblObject'
# sectionid added by SquirrelMail development.
#

CREATE TABLE `tblObject` (
  `objectid` int(11) NOT NULL default '0',
  `version` int(10) unsigned NOT NULL default '1',
  `classid` int(11) NOT NULL default '0',
  `workspaceid` int(11) NOT NULL default '0',
  `object` text,
  `title` varchar(255) NOT NULL default '',
  `updatorDatetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `sectionid` int(10) NOT NULL default'0',
  PRIMARY KEY  (`objectid`,`version`,`classid`,`workspaceid`),
  KEY `idxtblObjecttitle` (`title`),
  KEY `idxtblObjectupdatorDatetime` (`updatorDatetime`),
  KEY `idxtblObjectsectionid` (`sectionid`)
) TYPE=MyISAM;

#
# Default data for table 'tblObject'
#
INSERT INTO `tblObject` VALUES("385153371","1","-1919691191","0","O:4:\"user\":24:{s:7:\"classid\";s:11:\"-1919691191\";s:8:\"objectid\";s:9:\"385153371\";s:5:\"title\";s:4:\"Root\";s:7:\"version\";s:1:\"1\";s:11:\"workspaceid\";s:1:\"0\";s:9:\"creatorid\";s:9:\"385153371\";s:11:\"creatorName\";s:4:\"Root\";s:13:\"creatorHomeid\";s:9:\"385153371\";s:15:\"creatorDatetime\";s:19:\"2002-06-10 19:51:34\";s:9:\"updatorid\";s:9:\"385153371\";s:11:\"updatorName\";s:4:\"Root\";s:13:\"updatorHomeid\";s:9:\"385153371\";s:15:\"updatorDatetime\";s:19:\"2002-07-06 15:24:28\";s:9:\"viewGroup\";s:8:\"Everyone\";s:9:\"editGroup\";s:6:\"Author\";s:11:\"deleteGroup\";s:4:\"Gods\";s:10:\"adminGroup\";s:4:\"Gods\";s:7:\"indexes\";a:6:{s:8:\"objectid\";s:12:\"INT NOT NULL\";s:7:\"version\";s:31:\"INT UNSIGNED NOT NULL DEFAULT 1\";s:7:\"classid\";s:22:\"INT NOT NULL DEFAULT 0\";s:11:\"workspaceid\";s:22:\"INT NOT NULL DEFAULT 0\";s:5:\"title\";s:20:\"VARCHAR(50) NOT NULL\";s:15:\"updatorDatetime\";s:17:\"DATETIME NOT NULL\";}s:8:\"password\";s:32:\"81951160429224af01daa764f7427b80\";s:6:\"homeid\";s:9:\"385153371\";s:5:\"email\";s:19:\"root@wtf.peej.co.uk\";s:4:\"skin\";s:6:\"sqmail\";s:6:\"groups\";a:4:{i:0;s:8:\"Everyone\";i:1;s:7:\"Editors\";i:2;s:4:\"Gods\";i:3;s:6:\"Author\";}s:10:\"homeObject\";s:4:\"home\";}","Root","2002-07-06 15:24:28","0");
INSERT INTO `tblObject` VALUES("385153371","1","1909853392","0","O:4:\"home\":21:{s:7:\"classid\";s:10:\"1909853392\";s:8:\"objectid\";s:9:\"385153371\";s:5:\"title\";s:4:\"Root\";s:7:\"version\";s:1:\"1\";s:11:\"workspaceid\";s:1:\"0\";s:9:\"creatorid\";s:9:\"385153371\";s:11:\"creatorName\";s:4:\"Root\";s:13:\"creatorHomeid\";s:9:\"385153371\";s:15:\"creatorDatetime\";s:19:\"2002-06-09 13:45:15\";s:9:\"updatorid\";s:9:\"385153371\";s:11:\"updatorName\";s:4:\"Root\";s:13:\"updatorHomeid\";s:9:\"385153371\";s:15:\"updatorDatetime\";s:19:\"2002-06-09 13:45:15\";s:9:\"viewGroup\";s:8:\"Everyone\";s:9:\"editGroup\";s:6:\"Author\";s:11:\"deleteGroup\";s:4:\"Gods\";s:10:\"adminGroup\";s:4:\"Gods\";s:7:\"indexes\";a:6:{s:8:\"objectid\";s:12:\"INT NOT NULL\";s:7:\"version\";s:31:\"INT UNSIGNED NOT NULL DEFAULT 1\";s:7:\"classid\";s:22:\"INT NOT NULL DEFAULT 0\";s:11:\"workspaceid\";s:22:\"INT NOT NULL DEFAULT 0\";s:5:\"title\";s:20:\"VARCHAR(50) NOT NULL\";s:15:\"updatorDatetime\";s:17:\"DATETIME NOT NULL\";}s:7:\"content\";s:43:\"<p>This space intentionally left blank.</p>\";s:12:\"contentIsXML\";s:1:\"1\";s:10:\"attributes\";s:3:\"\n\t\t\";}","Root","2002-06-09 13:45:15","0");
INSERT INTO `tblObject` VALUES("-1672260811","1","-1919691191","0","O:4:\"user\":24:{s:7:\"classid\";s:11:\"-1919691191\";s:8:\"objectid\";s:11:\"-1672260811\";s:5:\"title\";s:14:\"Anonymous User\";s:7:\"version\";s:1:\"1\";s:11:\"workspaceid\";s:1:\"0\";s:9:\"creatorid\";s:11:\"-1672260811\";s:11:\"creatorName\";s:14:\"Anonymous User\";s:13:\"creatorHomeid\";s:11:\"-1672260810\";s:15:\"creatorDatetime\";s:19:\"2002-06-09 13:45:15\";s:9:\"updatorid\";s:11:\"-1672260811\";s:11:\"updatorName\";s:14:\"Anonymous User\";s:13:\"updatorHomeid\";s:11:\"-1672260811\";s:15:\"updatorDatetime\";s:19:\"2002-07-14 11:26:59\";s:9:\"viewGroup\";s:8:\"Everyone\";s:9:\"editGroup\";s:4:\"Gods\";s:11:\"deleteGroup\";s:4:\"Gods\";s:10:\"adminGroup\";s:4:\"Gods\";s:7:\"indexes\";a:6:{s:8:\"objectid\";s:12:\"INT NOT NULL\";s:7:\"version\";s:31:\"INT UNSIGNED NOT NULL DEFAULT 1\";s:7:\"classid\";s:22:\"INT NOT NULL DEFAULT 0\";s:11:\"workspaceid\";s:22:\"INT NOT NULL DEFAULT 0\";s:5:\"title\";s:20:\"VARCHAR(50) NOT NULL\";s:15:\"updatorDatetime\";s:17:\"DATETIME NOT NULL\";}s:8:\"password\";s:32:\"b31c2837488ccd85c090bc74aa192e05\";s:6:\"homeid\";s:11:\"-1672260811\";s:5:\"email\";s:0:\"\";s:4:\"skin\";s:6:\"sqmail\";s:6:\"groups\";a:1:{i:0;s:8:\"Everyone\";}s:10:\"homeObject\";s:4:\"home\";}","Anonymous User","2002-07-14 11:26:59","0");
INSERT INTO `tblObject` VALUES("-1672260811","1","1909853392","0","O:4:\"home\":21:{s:7:\"classid\";s:10:\"1909853392\";s:8:\"objectid\";s:11:\"-1672260811\";s:5:\"title\";s:14:\"Anonymous User\";s:7:\"version\";s:1:\"1\";s:11:\"workspaceid\";s:1:\"0\";s:9:\"creatorid\";s:11:\"-1672260811\";s:11:\"creatorName\";s:14:\"Anonymous User\";s:13:\"creatorHomeid\";s:11:\"-1672260811\";s:15:\"creatorDatetime\";s:19:\"2002-06-09 13:45:15\";s:9:\"updatorid\";s:11:\"-1672260811\";s:11:\"updatorName\";s:14:\"Anonymous User\";s:13:\"updatorHomeid\";s:11:\"-1672260811\";s:15:\"updatorDatetime\";s:19:\"2002-06-09 13:45:15\";s:9:\"viewGroup\";s:8:\"Everyone\";s:9:\"editGroup\";s:4:\"Gods\";s:11:\"deleteGroup\";s:4:\"Gods\";s:10:\"adminGroup\";s:4:\"Gods\";s:7:\"indexes\";a:6:{s:8:\"objectid\";s:12:\"INT NOT NULL\";s:7:\"version\";s:31:\"INT UNSIGNED NOT NULL DEFAULT 1\";s:7:\"classid\";s:22:\"INT NOT NULL DEFAULT 0\";s:11:\"workspaceid\";s:22:\"INT NOT NULL DEFAULT 0\";s:5:\"title\";s:20:\"VARCHAR(50) NOT NULL\";s:15:\"updatorDatetime\";s:17:\"DATETIME NOT NULL\";}s:7:\"content\";s:43:\"<p>This space intentionally left blank.</p>\";s:12:\"contentIsXML\";s:1:\"1\";s:10:\"attributes\";s:3:\"\n\t\t\";}","Anonymous User","2002-06-09 13:45:15","0");
INSERT INTO `tblObject` VALUES("-1561770585","4","-20631383","0","O:7:\"content\":21:{s:7:\"classid\";s:9:\"-20631383\";s:8:\"objectid\";s:11:\"-1561770585\";s:5:\"title\";s:19:\"Wiki Type Framework\";s:7:\"version\";s:1:\"4\";s:11:\"workspaceid\";s:1:\"0\";s:9:\"creatorid\";s:9:\"385153371\";s:11:\"creatorName\";s:4:\"Root\";s:13:\"creatorHomeid\";s:9:\"385153371\";s:15:\"creatorDatetime\";s:18:\"2002-06-10 0:02:26\";s:9:\"updatorid\";s:9:\"385153371\";s:11:\"updatorName\";s:4:\"Root\";s:13:\"updatorHomeid\";s:9:\"385153371\";s:15:\"updatorDatetime\";s:19:\"2002-07-06 18:16:38\";s:9:\"viewGroup\";s:8:\"Everyone\";s:9:\"editGroup\";s:7:\"Editors\";s:11:\"deleteGroup\";s:4:\"Gods\";s:10:\"adminGroup\";s:4:\"Gods\";s:7:\"indexes\";a:6:{s:8:\"objectid\";s:12:\"INT NOT NULL\";s:7:\"version\";s:31:\"INT UNSIGNED NOT NULL DEFAULT 1\";s:7:\"classid\";s:22:\"INT NOT NULL DEFAULT 0\";s:11:\"workspaceid\";s:22:\"INT NOT NULL DEFAULT 0\";s:5:\"title\";s:20:\"VARCHAR(50) NOT NULL\";s:15:\"updatorDatetime\";s:17:\"DATETIME NOT NULL\";}s:7:\"content\";s:42:\"<p>Welcome to the Wiki Type Framework.</p>\";s:12:\"contentIsXML\";s:1:\"1\";s:10:\"attributes\";s:3:\"\n\t\t\";}","Wiki Type Framework","2002-07-06 18:16:38","0");
INSERT INTO `tblObject` VALUES("-941827936","1","-20631383","0","O:7:\"content\":21:{s:7:\"classid\";s:9:\"-20631383\";s:8:\"objectid\";s:10:\"-941827936\";s:5:\"title\";s:13:\"Nothing Found\";s:7:\"version\";s:1:\"1\";s:11:\"workspaceid\";s:1:\"0\";s:9:\"creatorid\";s:9:\"385153371\";s:11:\"creatorName\";s:4:\"Root\";s:13:\"creatorHomeid\";s:9:\"385153371\";s:15:\"creatorDatetime\";s:19:\"2002-07-03 12:24:21\";s:9:\"updatorid\";s:9:\"385153371\";s:11:\"updatorName\";s:4:\"Root\";s:13:\"updatorHomeid\";s:9:\"385153371\";s:15:\"updatorDatetime\";s:19:\"2002-07-06 15:04:40\";s:9:\"viewGroup\";s:8:\"Everyone\";s:9:\"editGroup\";s:8:\"Everyone\";s:11:\"deleteGroup\";s:7:\"Editors\";s:10:\"adminGroup\";s:4:\"Gods\";s:7:\"indexes\";a:6:{s:8:\"objectid\";s:12:\"INT NOT NULL\";s:7:\"version\";s:31:\"INT UNSIGNED NOT NULL DEFAULT 1\";s:7:\"classid\";s:22:\"INT NOT NULL DEFAULT 0\";s:11:\"workspaceid\";s:22:\"INT NOT NULL DEFAULT 0\";s:5:\"title\";s:20:\"VARCHAR(50) NOT NULL\";s:15:\"updatorDatetime\";s:17:\"DATETIME NOT NULL\";}s:7:\"content\";s:393:\"<?php\n//header(\"Status: 404 Not Found\");\nif (isset($wtf->thingtitle)) {\n\techo \'<p>Sorry, nothing was found for the page \"\', $wtf->thingtitle, \'\".</p>\';\n\techo \'<p>To create a new page called \"\', $wtf->thingtitle, \'\", <a href=\"\', THINGURI, \'wikipage&amp;title=\', $wtf->thingtitle, \'\">click here</a>.</p>\';\n} else {\n\techo \'<p>Sorry, nothing was found for the page #\', $wtf->thingid, \'.</p>\';\n}\n?>\";s:12:\"contentIsXML\";s:1:\"1\";s:10:\"attributes\";s:3:\"\n\t\t\";}","Nothing Found","2002-07-06 15:04:40","0");
INSERT INTO `tblObject` VALUES("1577244130","3","-20631383","0","O:7:\"content\":21:{s:7:\"classid\";s:9:\"-20631383\";s:8:\"objectid\";s:10:\"1577244130\";s:5:\"title\";s:10:\"Thing List\";s:7:\"version\";s:1:\"3\";s:11:\"workspaceid\";s:1:\"0\";s:9:\"creatorid\";s:9:\"385153371\";s:11:\"creatorName\";s:4:\"Root\";s:13:\"creatorHomeid\";s:9:\"385153371\";s:15:\"creatorDatetime\";s:18:\"2002-06-10 0:02:26\";s:9:\"updatorid\";s:9:\"385153371\";s:11:\"updatorName\";s:4:\"Root\";s:13:\"updatorHomeid\";s:9:\"385153371\";s:15:\"updatorDatetime\";s:19:\"2002-06-17 16:29:55\";s:9:\"viewGroup\";s:8:\"Everyone\";s:9:\"editGroup\";s:7:\"Editors\";s:11:\"deleteGroup\";s:4:\"Gods\";s:10:\"adminGroup\";s:4:\"Gods\";s:7:\"indexes\";a:6:{s:8:\"objectid\";s:12:\"INT NOT NULL\";s:7:\"version\";s:31:\"INT UNSIGNED NOT NULL DEFAULT 1\";s:7:\"classid\";s:22:\"INT NOT NULL DEFAULT 0\";s:11:\"workspaceid\";s:22:\"INT NOT NULL DEFAULT 0\";s:5:\"title\";s:20:\"VARCHAR(50) NOT NULL\";s:15:\"updatorDatetime\";s:17:\"DATETIME NOT NULL\";}s:7:\"content\";s:1072:\"<?php\nif ($wtf->user->workspaceid == 0) {\n\t$where = array(\'workspaceid = 0\');\n} else {\n\t$where = array(\'(workspaceid = 0 OR workspaceid = \'.$wtf->user->workspaceid.\')\');\n}\n$query = DBSelect($conn, OBJECTTABLE, NULL, array(\"DISTINCT objectid, title, classid\"), $where, NULL, array(\"classid, title\"), NULL);\n$lastClass = FALSE;\n$recordNum = getAffectedRows();\nif ($recordNum > 0) {\n\tfor ($foo = 1; $foo <= $recordNum; $foo++) {\n\t\t$record = getRecord($query);\n\t\tif ($lastClass != $record[\"classid\"]) {\n\t\t\techo \"<br/>\";\n\t\t\t$lastClass = $record[\"classid\"];\n\t\t}\n\t\t$classid = intval($record[\"classid\"]);\n\t\tif (isset($HARDCLASS[$classid])) {\n\t\t\techo \"<a href=\\\"\", THINGIDURI.$record[\"objectid\"], \"&amp;class=\", $HARDCLASS[$classid], \"\\\">\", $record[\"title\"], \"</a> (\", $HARDCLASS[$classid], \")<br />\\n\";\n\t\t} else {\n\t\t\techo \"<a href=\\\"\", THINGIDURI.$record[\"objectid\"], \"\\\">\", $record[\"title\"], \"</a><br />\\n\";\n\t\t}\n\t}\n}\necho \"<br/>\";\nforeach($HARDTHING as $thingid => $hardThing) {\n\techo \"<a href=\\\"\", THINGIDURI.$thingid, \"\\\">\", $hardThing[\"title\"], \"</a> (hardthing)<br/>\\n\";\n}\n?>\";s:12:\"contentIsXML\";s:1:\"1\";s:10:\"attributes\";s:3:\"\n\t\t\";}","Thing List","2002-06-17 16:29:55","0");
INSERT INTO `tblObject` VALUES("-1854783249","2","-20631383","0","O:7:\"content\":21:{s:7:\"classid\";s:9:\"-20631383\";s:8:\"objectid\";s:11:\"-1854783249\";s:5:\"title\";s:14:\"Recent Changes\";s:7:\"version\";s:1:\"2\";s:11:\"workspaceid\";s:1:\"0\";s:9:\"creatorid\";s:9:\"385153371\";s:11:\"creatorName\";s:4:\"Root\";s:13:\"creatorHomeid\";s:9:\"385153371\";s:15:\"creatorDatetime\";s:19:\"2002-06-12 14:23:29\";s:9:\"updatorid\";s:9:\"385153371\";s:11:\"updatorName\";s:4:\"Root\";s:13:\"updatorHomeid\";s:9:\"385153371\";s:15:\"updatorDatetime\";s:19:\"2002-06-15 21:05:50\";s:9:\"viewGroup\";s:8:\"Everyone\";s:9:\"editGroup\";s:8:\"Everyone\";s:11:\"deleteGroup\";s:7:\"Editors\";s:10:\"adminGroup\";s:4:\"Gods\";s:7:\"indexes\";a:6:{s:8:\"objectid\";s:12:\"INT NOT NULL\";s:7:\"version\";s:31:\"INT UNSIGNED NOT NULL DEFAULT 1\";s:7:\"classid\";s:22:\"INT NOT NULL DEFAULT 0\";s:11:\"workspaceid\";s:22:\"INT NOT NULL DEFAULT 0\";s:5:\"title\";s:20:\"VARCHAR(50) NOT NULL\";s:15:\"updatorDatetime\";s:17:\"DATETIME NOT NULL\";}s:7:\"content\";s:1142:\"<?php\n$days = 7;\n$query = DBSelect($conn, \'tblObject\', NULL, array(\n\t\'objectid\',\n\t\'title\',\n\t\'classid\',\n\t\'updatorDatetime\'\n), array(\n\t\'updatorDatetime > \"\'.date(DATABASEDATE, time() - ($days * 86400)).\'\"\'\n), NULL, array(\'updatorDatetime DESC\'), NULL);\nif ($query) {\n\t$numberOfThings = returnedRows($query);\n\t$date = FALSE;\n\tfor ($foo = 1; $foo <= $numberOfThings; $foo++) {\n\t\t$record = getRecord($query);\n\t\t$thisDate = date(\'j F, Y\', dbdate2unixtime($record[\'updatorDatetime\']));\n\t\tif ($date != $thisDate) {\n\t\t\tif ($date) {\n\t\t\t\techo \'</ul>\';\n\t\t\t}\n\t\t\t$date = $thisDate;\n\t\t\techo \'<subtitle>\', $date, \'</subtitle>\';\n\t\t\techo \'<ul>\';\n\t\t}\n\t\t$classid = intval($record[\'classid\']);\n\t\tif (isset($HARDCLASS[$classid])) {\n\t\t\techo \'<li><a href=\"\', THINGIDURI, $record[\'objectid\'], \'&amp;class=\', $HARDCLASS[$classid], \'\">\', $record[\'title\'], \'</a> \', date(\'g:i a\', dbdate2unixtime($record[\'updatorDatetime\'])), \'</li>\';\n\t\t} else {\n\t\t\techo \'<li><a href=\"\', THINGIDURI.$record[\'objectid\'], \'\">\', $record[\'title\'], \'</a> \', date(\'g:i a\', dbdate2unixtime($record[\'updatorDatetime\'])), \'</li>\';\n\t\t}\n\t\tif ($foo == $numberOfThings) {\n\t\t\techo \'</ul>\';\n\t\t}\n\t}\n}\n?>\";s:12:\"contentIsXML\";s:1:\"1\";s:10:\"attributes\";s:3:\"\n\t\t\";}","Recent Changes","2002-06-15 21:05:50","0");
