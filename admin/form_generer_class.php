<?php

// $Id: index.php,v 1.6 2003/03/10 19:26:46 okazu Exp $
//  ----------------------------------------------------------------- //
// Author: Bruno Barthez	                                            //
// Project:  Vet		                                                  //
// ------------------------------------------------------------------ //

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    $form_CC = new XoopsThemeForm(_CC_GENERER_CLASS, 'Class', 'index.php');
    $f_choix_module = new XoopsFormSelect(_CC_MODULE_ID, 'module_id', '');
    $moduleHandler = xoops_getHandler('module');
    $installed_mods = $moduleHandler->getObjects();
    $listed_mods = [];
    $count = 0;
    foreach ($installed_mods as $module) {
        $f_choix_module->addOption($module->getVar('dirname', 'E'), $module->getVar('dirname', 'E'));
    }
    $f_choix_table = new XoopsFormSelect(_CC_TABLE_ID, 'table_id', '');
    $result = mysql_list_tables(XOOPS_DB_NAME);

    if (!$result) {
        echo "Erreur : impossible de lister les bases de données\n";

        echo 'Erreur MySQL : ' . $GLOBALS['xoopsDB']->error();

        break;
    }

    while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
        $f_choix_table->addOption($row[0], $row[0]);
    }
    $form_CC->addElement($f_choix_module);
    $form_CC->addElement($f_choix_table);
    $form_CC->addElement($op_hidden);
    $dbname_hidden = new XoopsFormHidden('dbname', $dbname);
    $form_CC->addElement($dbname_hidden);
    $op_hidden = new XoopsFormHidden('op', 'genererclass');
    $form_CC->addElement($op_hidden);
    $submit_button = new XoopsFormButton('', 'action', _CC_GENERER, 'submit');
    $form_CC->addElement($submit_button);
    $form_CC->display();
