<?php

function notifications_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";

    $date0 = new DateTime(); # today
    $dateu = $date0->format("Y-m-d H:i:s");

    $query = "
select count(*) as rowCount from field_data_field_start_date a join field_data_field_stop_date b
                  on
                a.entity_id=b.entity_id
                where
                a.field_start_date_value<'$dateu' and b.field_stop_date_value>'$dateu'
                ";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;

    $query = "
select a.entity_id, b.field_stop_date_value from field_data_field_start_date a join field_data_field_stop_date b
                  on
                a.entity_id=b.entity_id
                where
                a.field_start_date_value<'$dateu' and b.field_stop_date_value>'$dateu' order by a.entity_id desc limit $end offset $start
                ";
    $records = db_query($query);
    $olist2 = array();
    foreach ($records as $record) {
        $bllng = node_load($record->entity_id);
        $datex = new DateTime($record->field_stop_date_value);
        $interval = $datex->diff($date0)->days;
        $olist2[$record->entity_id] = $interval;
    }
    /*
   * Output
   */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" =>0,
        "aaData" => array()
    );
    $i=0;
    foreach ($olist2 as $nid => $value) {

        if ($value > 30) continue;
        $i++;
        $node = node_load($nid);
        $mobile=node_load(node_load($node->field_account['und'][0]['target_id'])->title)->field_mobile_number['und']['0']['value'];
        $account=node_load($node->field_account['und'][0]['target_id'])->field_account_name['und']['0']['value'];
        $unit=node_load($node->field_unit['und']['0']['target_id']);
        $row = array();
        $row[] = $node->nid;
        $row[] = $account;
        $row[] = $unit->field_unit_name['und']['0']['value'].": ".$unit->field_unit_code['und']['0']['value'];
        $row[] = $mobile;
        $row[] = $value;
        $output['aaData'][] = $row;
    }
    $output['iTotalRecords'] = $i;
    $output['iTotalDisplayRecords'] = $i;
    echo json_encode($output);
}


