<?php

function mypayments_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $nid=$_GET['nid'];
    $query = "
    select a.entity_id from
                field_data_field_account a join field_data_field_amount_paid b
                on a.entity_id=b.entity_id
                where b.bundle='income' and a.field_account_target_id=$nid";
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
        #$date=new DateTime('@'.$node->created);
        $date=new DateTime($node->field_payment_date['und'][0]['value']);
        $row[] = $date->format('d/m/Y');
        $row[] = $node->field_reference['und'][0]['value'];
        $row[] = $node->field_particulars['und'][0]['value'];
        $link='<a href="mytransactions?nid='.$node->field_account['und'][0]['target_id'].'" >'.node_load($node->field_account['und'][0]['target_id'])->field_account_name['und'][0]['value'].'</i></a>';
        $row[] = $link;
        //$row[] = node_load($node->field_account['und'][0]['target_id'])->field_account_name['und'][0]['value'];
        $row[] = number_format($node->field_amount_paid['und'][0]['value'],  2 ,  "." ,  "," );
        $row[] = '<a href="#overlay=node/'.$node->nid.'/edit" ><i class="fa fa-pencil-square-o fa-fw"></i></a>';
        $row[] = '<a href="#overlay=node/'.$node->nid.'/delete" ><i class="fa fa-trash fa-fw"></i></a>';
        $row[] = '<a href="receipt?nid='.$node->nid.'"><i class="fa fa-print fa-fw"></i></a>';
        $output['aaData'][] = $row;
        $rowCount++;
    }
    $output["iTotalRecords"] = $rowCount;
    $output["iTotalDisplayRecords"] = $rowCount;
    echo json_encode($output);
}


