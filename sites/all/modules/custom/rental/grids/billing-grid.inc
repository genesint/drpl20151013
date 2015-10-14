<?php

function billing_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $offset=$start;
    $limit=$end;
    $query = "select count(*) as rowCount from node where type='billing' ";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;
    $query = "select nid from node where type='billing' order by nid desc limit $end offset $start";
    $records = db_query($query);
    /*
   * Output
   */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $rowCount,
        "iTotalDisplayRecords" =>$rowCount,
        "aaData" => array()
    );
    foreach ($records as $record) {
        $node = node_load($record->nid);
        $row = array();
        $row[] = $node->nid;
        $row[] = $node->title;
        $date=new DateTime('@'.$node->created);
        #$date=new DateTime($node->field_payment_date['und'][0]['value']);
        $row[] = $date->format('d/m/Y');
        $link='<a href="mytransactions?nid='.$node->field_account['und'][0]['target_id'].'" >'.node_load($node->field_account['und'][0]['target_id'])->field_account_name['und'][0]['value'].'</i></a>';
        $row[] = $link;
        $row[] = node_load($node->field_unit['und'][0]['target_id'])->field_unit_code['und'][0]['value'];
        $date=new DateTime($node->field_start_date['und'][0]['value']);
        $row[] = $date->format('d/m/Y');
        $date=new DateTime($node->field_stop_date['und'][0]['value']);
        $row[] =  $date->format('d/m/Y');
        $row[] = number_format($node->field_amount_due['und'][0]['value'],  2 ,  "." ,  "," );
        $row[] = '<a href="#overlay=node/'.$node->nid.'/edit" ><i class="fa fa-pencil-square-o fa-fw"></i></a>';
        $row[] = '<a href="#overlay=node/'.$node->nid.'/delete" ><i class="fa fa-trash fa-fw"></i></a>';
        $row[] = '<a href="invoice?nid='.$node->nid.'"><i class="fa fa-print fa-fw"></i></a>';
        $output['aaData'][] = $row;
    }
    echo json_encode($output);
}


