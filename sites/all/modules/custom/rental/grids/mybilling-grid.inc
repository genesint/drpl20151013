<?php

function mybilling_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $nid=$_GET['nid'];
    # alternative notification list

    $date0 = new DateTime(); # today
    $dateu=$date0->format("Y-m-d H:i:s");
    $query="
select a.entity_id, b.field_stop_date_value from field_data_field_start_date a join field_data_field_stop_date b
                  on
                a.entity_id=b.entity_id
                where
                a.field_start_date_value<'$dateu' and b.field_stop_date_value>'$dateu'
                ";
    $records = db_query($query);
    $olist2=array();
    foreach($records as $record){
        $bllng=node_load($record->entity_id);
        $datex=new DateTime($record->field_stop_date_value);
        $interval = $datex->diff($date0)->days;
        $olist2[$record->entity_id]=$interval;
    }
    $query = "select entity_id from field_data_field_account where bundle='billing' and field_account_target_id=$nid";
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
        $row[] = node_load($node->field_unit['und'][0]['target_id'])->field_unit_name['und'][0]['value'].','.node_load($node->field_unit['und'][0]['target_id'])->field_unit_code['und'][0]['value'];
        $date=new DateTime($node->field_start_date['und'][0]['value']);
        $row[] = $date->format('d/m/Y');
        $date=new DateTime($node->field_stop_date['und'][0]['value']);
        $row[] =  $date->format('d/m/Y');
        $row[] = number_format($node->field_amount_due['und'][0]['value'],  2 ,  "." ,  "," );
        #$row[] = $olist2[$record->entity_id];
        $row[] = '<a href="#overlay=node/'.$node->nid.'/edit" ><i class="fa fa-pencil-square-o fa-fw"></i></a>';
        #$row[] = '<a href="invoice?nid='.$node->nid.'"><i class="fa fa-print fa-fw"></i></a>';
        $output['aaData'][] = $row;
        $rowCount++;
    }
    $output["iTotalRecords"] = $rowCount;
    $output["iTotalDisplayRecords"] = $rowCount;
    echo json_encode($output);
}


