<?php

/*
 * UDWBase: WOWDB Web Interface
 *
 * © UDW 2009-2011
 *
 * Released under the terms and conditions of the
 * GNU General Public License (http://gnu.org).
 *
 */

$smarty->config_load($conf_file, 'objects');
require_once('includes/page.php');

//$type = $podrazdel;
point_delim($podrazdel, $type, $family);
if($type == '') {
    $type = null;
}

//if (!$data = load_cache(4, intval($type))) {
if (1) {
    unset($data);

    // Подключаемся к ДБ:
    global $DB;
    global $UDWBaseconf;

    $total = $DB->select('
            SELECT count(*) as t
            FROM {?_gameobject_questrelation ?#, } {?_aowow_lock ?#, } ?_gameobject_template g
                {LEFT JOIN (?_locales_gameobject l) ON l.entry=g.entry AND ?d}
            WHERE 
                name != ""
                { AND g.type = ? } 
                { AND g.data0=a.lockID AND g.type=3 AND a.type1=2 AND 1=?} 
                { AND g.data0=a.lockID AND g.type=3 AND a.type2=2 AND 1=?} 
                { AND a.lockproperties1=2 AND 1=?}
                { AND a.lockproperties1=3 AND 1=?}
                { AND a.lockproperties2=1 AND 1=?}
                { AND g.entry = q.?#}
        ',($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($type == -2) ? 'q' : DBSIMPLE_SKIP, (($type == -3) or ($type == -4) or ($type == -5)) ? 'a' : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, ($type > 0) ? $type : DBSIMPLE_SKIP, (($type == -3) or ($type == -4)) ? 1 : DBSIMPLE_SKIP, ($type == -5) ? 1 : DBSIMPLE_SKIP, ($type == -3) ? 1 : DBSIMPLE_SKIP, ($type == -4) ? 1 : DBSIMPLE_SKIP, ($type == -5) ? 1 : DBSIMPLE_SKIP, ($type == -2) ? 'id' : DBSIMPLE_SKIP
    );

    $_page = new Page($total[0]['t'],50);

    // Получаем данные по этому типу объектов
    $rows = $DB->select('
			SELECT g.* {, a.requiredskill1 as ?#} {, a.requiredskill2 as ?#}
				{, l.name_loc?d AS `name_loc`}
			FROM {?_gameobject_questrelation ?#, } {?_aowow_lock ?#, } ?_gameobject_template g
				{LEFT JOIN (?_locales_gameobject l) ON l.entry=g.entry AND ?d}
			WHERE 
				name != ""
				{ AND g.type = ? } 
				{ AND g.data0=a.lockID AND g.type=3 AND a.type1=2 AND 1=?} 
				{ AND g.data0=a.lockID AND g.type=3 AND a.type2=2 AND 1=?} 
				{ AND a.lockproperties1=2 AND 1=?}
				{ AND a.lockproperties1=3 AND 1=?}
				{ AND a.lockproperties2=1 AND 1=?}
				{ AND g.entry = q.?#}
			ORDER by name
			LIMIT '.$_page->firstRow.',50
		', (($type == -3) or ($type == -4)) ? 'skill' : DBSIMPLE_SKIP, ($type == -5) ? 'skill' : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($type == -2) ? 'q' : DBSIMPLE_SKIP, (($type == -3) or ($type == -4) or ($type == -5)) ? 'a' : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, ($type > 0) ? $type : DBSIMPLE_SKIP, (($type == -3) or ($type == -4)) ? 1 : DBSIMPLE_SKIP, ($type == -5) ? 1 : DBSIMPLE_SKIP, ($type == -3) ? 1 : DBSIMPLE_SKIP, ($type == -4) ? 1 : DBSIMPLE_SKIP, ($type == -5) ? 1 : DBSIMPLE_SKIP, ($type == -2) ? 'id' : DBSIMPLE_SKIP
    );


    $i = 0;
    $data = array();
    foreach ($rows as $numRow => $row) {
        $data[$i] = array();
        $data[$i]['entry'] = $row['entry'];
        if (IsSet($row['skill']))
            $data[$i]['skill'] = $row['skill'];
        $data[$i]['name'] = $row['name_loc'] ? $row['name_loc'] : $row['name'];
        $data[$i]['chinese'] = $row['chinese'] ? $row['chinese'] : $row['name'];
        // TODO: Расположение
        $data[$i]['location'] = "[-1]";
        // Тип объекта
        $data[$i]['type'] = (isset($type)) ? $type : $row['type'];
        $i++;
    }
    //save_cache(4, intval($type), $data);
}
global $page;
$page = array(
    'Mapper' => false,
    'Book' => false,
    'Title' => $smarty->get_config_vars('Objects'),
    'tab' => 0,
    'type' => 0,
    'typeid' => 0,
    'path' => '[0, 5,' . $podrazdel . ']'
);
$smarty->assign('page', $page);
$smarty->assign('pagenew', $_page->show());

// Передаем массив данных шаблонизатору
$smarty->assign('data', $data);
// Статистика выполнения mysql запросов
$smarty->assign('mysql', $DB->getStatistics());

$smarty->display('objects.tpl');