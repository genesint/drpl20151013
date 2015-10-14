<?php

function transactions_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $query = "select  count(*) as rowCount from node where type='transaction'";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;


    $query = "select nid from node where type='transaction' order by nid desc limit $end offset $start";
    $records = db_query($query);
    /*
   * Output
   */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $rowCount,
        "iTotalDisplayRecords" => $rowCount,
        "aaData" => array()
    );
    foreach ($records as $record) {
        $node = node_load($record->nid);
        $row = array();
        $row[] = $node->nid;
        $row[] = $node->title;
        $date=new DateTime('@'.$node->created);
        $row[] = $date->format('d/m/Y');
        $ptype=str_replace($node->title,'',$node->field_transaction_reference['und'][0]['value']);
        if($ptype=='B'){
            $row[] = '<a href="invoice?nid='.$node->title.'">'.$node->field_transaction_reference['und'][0]['value'].'</a>';
        }
        if($ptype=='I'){
            $row[] = '<a href="receipt?nid='.$node->title.'">'.$node->field_transaction_reference['und'][0]['value'].'</a>';
        }
        if($ptype=='E'){
            $row[] = '<a href="voucher?nid='.$node->title.'">'.$node->field_transaction_reference['und'][0]['value'].'</a>';
        }
        $row[] = node_load($node->field_account['und'][0]['target_id'])->field_account_name['und'][0]['value'];
        $account_type = node_load($node->field_account['und'][0]['target_id'])->field_account_type['und'][0]['target_id'];
        $row[]=node_load($account_type)->title;
        $row[] = $node->field_particulars['und'][0]['value'];
        $row[] = number_format($node->field_amount['und'][0]['value'],  2 ,  "." ,  "," );
        $output['aaData'][] = $row;
        $rowCount++;
    }
    echo json_encode($output);
}


