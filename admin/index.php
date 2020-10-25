<?php

// $Id: main.php,v 1.9 2003/03/16 22:39:48 w4z004 Exp $
// Anciennement /system/admin/main.php
//  ------------------------------------------------------------------------ //
// Author: Bruno barthez                                          //
// ------------------------------------------------------------------------- //

require dirname(__DIR__, 3) . '/include/cp_header.php';
xoops_cp_header();
$dbname = XOOPS_DB_NAME;

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}

function createDir($dir)
{
    if (!file_exists($dir)) {
        echo "Creating directory : $dir <BR>";

        mkdir($dir, 0755);

        $findexhtml = fopen($dir . '/index.html', 'w+b');

        //		$findexhtml = fopen ($dir, "a");

        fwrite($findexhtml, ' <script>history.go(-1);</script>');

        fclose($findexhtml);
    } else {
        echo "Directory existant : $dir <BR>";
    }
}
if (!(isset($op))) {
    $op = '';
}

switch ($op) {
case 'createClass':
    include 'form_generer_class.php';
    break;
case 'genererclass':
        require XOOPS_ROOT_PATH . '/header.php';
        if ('genererform' == $op) {
            $GLOBALS['xoopsOption']['template_main'] = 'Formumaire.html';
        } else {
            $GLOBALS['xoopsOption']['template_main'] = 'ClassSystem.html';
        }
// nom de la table
        $temp = mb_strstr($table_id, '_');
        $table_short_id = mb_substr($temp, 1);
        $xoopsTpl->assign('table_id', $table_short_id);
// nom des attributs (les colonnes)
        $fields = mysql_list_fields($dbname, $table_id);
        $columns = mysqli_num_fields($fields);
        $primary_id = '';
        for ($i = 0; $i < $columns; $i++) {
            $name = $GLOBALS['xoopsDB']->getFieldName($fields, $i);

            $typesql = '%s';

            switch (mysql_field_type($fields, $i)) {
                case 'int':
                    $type = 'XOBJ_DTYPE_INT,null,false,10';
                    $data = '$' . $name;
                    $typesql = '%u';
                    break;
                case 'date':
                    $type = 'XOBJ_DTYPE_TXTBOX,null,false';
                    $data = '$now';
                    $typesql = '%s';
                    break;
                default:
                    $type = 'XOBJ_DTYPE_TXTBOX, null, false';
                    $data = '$this->db->quoteString($' . $name . ')';
                    $typesql = '%s';
            }

            [$nulle, $key, $signe, $incerm] = explode(' ', mysql_field_flags($fields, $i));

            if ('primary_key' == $key) {
                $primary_id = $name;

                $typeformat_key = mysql_field_type($fields, $i);
            }

            $arr_fields[] = [
                'field_name' => $name,
'field_type' => $type,
'data' => $data,
'field_typeformat' => $typesql,
'field_len' => mysql_field_len($fields, $i),
            ];
        }
        $xoopsTpl->assign('module_name', $module_name);
        $xoopsTpl->assign('primary_id', $primary_id);
        $xoopsTpl->assign('typeformat_key', $typeformat_key);
        for ($i = 0; $i < $columns; $i++) {
            $nom = $arr_fields[$i]['field_name'];

            $field[$i]['field_name'] = $nom;

            $field[$i]['field_type'] = $arr_fields[$i]['field_type'];

            $field[$i]['data'] = $arr_fields[$i]['data'];

            $field[$i]['field_typeformat'] = $arr_fields[$i]['field_typeformat'];

            $xoopsTpl->append('fields', $field[$i]);
        }

        $contentfile = $xoopsTpl->fetch('db:' . $xoopsOption['template_main']);
        $module_path = XOOPS_ROOT_PATH . '/modules/' . $module_id;
        if ('genererform' == $op) {
            // cas du formulaire

            $fpxv = fopen($module_path . "/index_$table_short_id.php", 'w+b');
        } else {
            $fpxv = fopen($module_path . "/class/$table_short_id.php", 'w+b');
        }
        fwrite($fpxv, $contentfile);
        fclose($fpxv);
        redirect_header('index.php?op=createClass', 1, _CC_GEN_CLASS_OK . $module_path . "/class/$table_short_id.php");
        break;
default:
    $result = mysql_list_tables($dbname);

    if (!$result) {
        echo "Error : BD Listing\n";

        echo 'Error MySQL : ' . $GLOBALS['xoopsDB']->error();
    }

    echo '<table border="1" width="100%"><tr class ="odd" >';
//	echo '<td><a href="'.XOOPS_URL.'/modules/classgenerator/admin/index.php?op=createModuleForm">'._CC_GENERER_MOD.'</a></td>';
    echo '<td><a href="' . XOOPS_URL . '/modules/classgenerator/admin/index.php?op=createClass">' . _CC_GENERER_CLASS . '</a></td>';
//	echo '<td><a href="'.XOOPS_URL.'/modules/classgenerator/admin/index.php?op=createForm">'._CC_GENERER_FORM.'</a></td>';
    echo '</tr></table>';
    echo '<br><br>';
    echo '<table border="1" >';
    echo '<tr><th>' . _CC_LIST_TABLE . '</th></tr>';
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
        echo '<tr>';

        echo "<td><b>$row[0]</b></td>";

        echo '<tr><td>';

        echo '<table>';

        $fields = mysql_list_fields($dbname, $row[0]);

        $columns = mysqli_num_fields($fields);

        for ($i = 0; $i < $columns; $i++) {
            echo '<tr>';

            echo '<td>' . mysql_field_name($fields, $i) . '</td>';

            echo '<td>' . mysql_field_type($fields, $i) . '</td>';

            echo '<td>' . mysql_field_len($fields, $i) . '</td>';

            echo '<td>' . mysql_field_flags($fields, $i) . '</td>';

            echo '</tr>';
        }

        echo '</table>';

        echo '</td></tr>';
    }
    echo '</table>';

    xoops_cp_footer();
    break;
}
