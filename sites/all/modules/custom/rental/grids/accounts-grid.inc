<?php

function accounts_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $query = "select count(*) as rowCount from node where type='account'";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;

    $query = "select nid from node where type='account' order by nid asc limit $end offset $start";
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
        $link='<a href="mytransactions?nid='.$node->nid.'" >'.$node->field_account_name['und'][0]['value'].'</a>';
        $row[] = $link;
        $account_type=node_load($node->field_account_type['und'][0]['target_id'])->title;
        $row[] = $account_type;
        $row[] = $node->field_particulars['und'][0]['value'];
        if(!in_array($account_type, array('Tenant','Unit','Unit Group'))){
            $row[] = '<a href="#overlay=node/'.$node->nid.'/edit" ><i class="fa fa-pencil-square-o fa-fw"></i></a>';
            $row[] = '<a href="#overlay=node/'.$node->nid.'/delete" ><i class="fa fa-trash fa-fw"></i></a>';
        }
        else{
            $row[] = '';
            $row[] = '';
        }
        $output['aaData'][] = $row;
    }
    echo json_encode($output);
}


