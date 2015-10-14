<?php

function unit_history_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $nid=$_GET['nid'];
    $query = "select entity_id from field_data_field_unit where bundle='billing' and field_unit_target_id=$nid";
    $records = db_query($query);
    /*
   * Output
   */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => array()
    );
    $rowCount = 0;
    foreach ($records as $record) {
        $node = node_load($record->entity_id);
        $row = array();
        $row[] = $node->nid;
        $row[] = $node->title;
        $date=new DateTime('@'.$node->created);
        $row[] = $date->format('d/m/Y');
        $row[] = node_load($node->field_account['und'][0]['target_id'])->field_account_name['und'][0]['value'];
        $row[] = node_load($node->field_unit['und'][0]['target_id'])->field_unit_name['und'][0]['value']."/".node_load($node->field_unit['und'][0]['target_id'])->field_unit_code['und'][0]['value'];
        $date=new DateTime($node->field_start_date['und'][0]['value']);
        $row[] = $date->format('d/m/Y');
        $date=new DateTime($node->field_stop_date['und'][0]['value']);
        $row[] =  $date->format('d/m/Y');
        $row[] = number_format($node->field_amount_due['und'][0]['value'],  2 ,  "." ,  "," );
        $row[] = '<a href="#overlay=node/'.$node->nid.'/edit" ><i class="fa fa-pencil-square-o fa-fw"></i></a>';
        $row[] = '<a href="invoice?nid='.$node->nid.'"><i class="fa fa-print fa-fw"></i></a>';
        $output['aaData'][] = $row;
        $rowCount++;
    }
    $output["iTotalRecords"] = $rowCount;
    $output["iTotalDisplayRecords"] = $rowCount;
    echo json_encode($output);
}


