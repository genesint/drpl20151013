<?php

function mytransactions_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $nid=$_GET['nid'];
    $query = "select entity_id from field_data_field_account where bundle='transaction' and field_account_target_id=$nid";
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
    $balance=0;
    $paccount_type=node_load(node_load($nid)->field_account_type['und'][0]['target_id'])->title;
    foreach ($records as $record) {
        $node = node_load($record->entity_id);
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
        $row[] = '<a href="#overlay=node/'.$node->title.'/edit" ><i class="fa fa-pencil-square-o fa-fw"></i></a>';
        $row[] = '<a href="#overlay=node/'.$node->title.'/delete" ><i class="fa fa-trash fa-fw"></i></a>';
        if($ptype=='B'){
            $row[] = '<a href="invoice?nid='.$node->title.'"><i class="fa fa-print fa-fw"></i></a>';
        }
        if($ptype=='I'){
            $row[] = '<a href="receipt?nid='.$node->title.'"><i class="fa fa-print fa-fw"></i></a>';
        }
        $output['aaData'][] = $row;
        $rowCount++;
        $balance+=$node->field_amount['und'][0]['value'];
    }
    if(preg_match('/Tenant/',$paccount_type)){
        $output['aaData'][] =array("","","","","","","Outstanding Balance",number_format($balance,  2 ,  "." ,  "," ),"","","");
    }
    $output["iTotalRecords"] = $rowCount;
    $output["iTotalDisplayRecords"] = $rowCount;
    echo json_encode($output);
}


