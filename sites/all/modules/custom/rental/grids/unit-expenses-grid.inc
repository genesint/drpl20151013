<?php

function unit_expenses_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $nid=$_GET['nid'];
    $query = "select count(*) as rowCount from field_data_field_account2 where bundle='expense' and field_account2_target_id=$nid";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;

    $query = "select entity_id from field_data_field_account2 where bundle='expense' and field_account2_target_id=$nid order by entity_id desc limit $end offset $start ";
    $records = db_query($query);
    /*
   * Output
   */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" =>0,
        "aaData" => array()
    );
    foreach ($records as $record) {
        $node = node_load($record->entity_id);
        $row = array();
        $row[] = $node->nid;
        $row[] = $node->title;
        $date=new DateTime($node->field_date['und'][0]['value']);
        $row[] = $date->format('d/m/Y');
        $row[] = $node->field_reference['und'][0]['value'];
        $row[] = $node->field_particulars['und'][0]['value'];
        $row[] = node_load($node->field_account2['und'][0]['target_id'])->field_account_name['und'][0]['value'];
        $row[] = number_format($node->field_amount_spent['und'][0]['value'],  2 ,  "." ,  "," );
        $output['aaData'][] = $row;
    }
    echo json_encode($output);
}


