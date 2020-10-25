<?php

// $Id: main.php,v 1.9 2003/03/16 22:39:48 w4z004 Exp $
// Anciennement /system/admin/main.php
//  ------------------------------------------------------------------------ //
// Author: Bruno barthez                                          //
// ------------------------------------------------------------------------- //

require_once XOOPS_ROOT_PATH . '/header.php';
$dbname = XoopsDatabaseFactory::getDatabaseConnection();
$dbname = XOOPS_DB_NAME;

$result = mysql_list_tables($dbname);

if (!$result) {
    echo "Erreur : impossible de lister les bases de données\n";

    echo 'Erreur MySQL : ' . $GLOBALS['xoopsDB']->error();
}

while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
    echo "<td>Table : $row[0]\n<td>";
}

require_once XOOPS_ROOT_PATH . '/footer.php';
