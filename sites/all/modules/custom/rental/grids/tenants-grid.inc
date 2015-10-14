<?php

function tenants_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $offset=$start;
    $limit=$end;
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $query = "select  a.field_account_target_id as aid, SUM(b.field_amount_value) as amount from
              field_data_field_account a join field_data_field_amount b on a.entity_id=b.entity_id
              where a.bundle='transaction' and b.bundle='transaction'
              group by a.field_account_target_id
              ";
    $records = db_query($query);
    $oamounts=array();
    foreach($records as $record){
        $oamounts[$record->aid]=$record->amount;
    }
# get number tenants
    $query = "select count(*) as rowCount from node a
              join node b on a.nid=b.title
              where a.type='tenant' and a.title like '%$skey%'";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;

    # get tenants
    $query = "select a.nid, b.nid as aid from node a
              join node b on a.nid=b.title
              where a.type='tenant'  and a.title like '%$skey%' order by a.nid asc limit $limit offset $offset";
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
        #$row[] = 'put photo here';
        $row[] = $node->field_full_name['und'][0]['value'];
        $row[] = $node->field_origin['und'][0]['value'];
        $row[] = $node->field_mobile_number['und'][0]['value'];
        $row[] = $node->field_number_of_family_members['und'][0]['value'];
        $row[]= number_format($oamounts[$record->aid],  2 ,  "." ,  "," );
        $view="<div class='btn-group'>
            <a class='btn btn-primary' href='#'><i class='fa fa-list-ul fa-fw'></i>&nbsp;View</a>
            <a class='btn btn-primary dropdown-toggle' data-toggle='dropdown' href='#'>
                <span class='fa fa-caret-down'></span></a>
            <ul class='dropdown-menu'>
                <li><a href='mybilling?nid={$node->nid}'><i class='fa fa-level-up fa-fw'></i>&nbsp;Rent</a></li>
                <li><a href='mypayments?nid={$node->nid}'><i class='fa fa-level-down fa-fw'></i>&nbsp;Payments</a></li>
                <li><a href='mytransactions?nid={$record->aid}'><i class='fa fa-bars fa-fw'></i>Transactions</a></li>
            </ul>
        </div>";
        $row[]=$view;
        $actions="<div class='btn-group'>
            <a class='btn btn-primary' href='#'><i class='fa fa-tasks fa-fw'></i>&nbsp;Actions</a>
            <a class='btn btn-primary dropdown-toggle' data-toggle='dropdown' href='#'>
                <span class='fa fa-caret-down'></span></a>
            <ul class='dropdown-menu'>
                <li><a href='#overlay=node/{$node->nid}/edit'><i class='fa fa-pencil fa-fw'></i>&nbsp;Edit</a></li>
                <li><a href='delete-tenant?tid={$node->nid}'><i class='fa fa-trash-o fa-fw'></i>&nbsp;Delete</a></li>
                <li><a href='mybilling?nid={$node->nid}#overlay=node/add/billing'><i class='fa fa-level-up fa-fw'></i>&nbsp;Bill</a></li>
                <li><a href='mypayments?nid={$node->nid}#overlay=node/add/income'><i class='fa fa-level-down fa-fw'></i>&nbsp;Payment</a></li>
            </ul>
        </div>";
        $row[] = $actions;
        $output['aaData'][] = $row;
    }
    echo json_encode($output);
}


