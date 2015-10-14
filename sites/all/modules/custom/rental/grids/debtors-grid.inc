<?php

function debtors_grid()
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
    $outstanding="";
    foreach($records as $record){
        $oamounts[$record->aid]=$record->amount;
        if($record->amount>0){
            $outstanding.=$record->aid.",";
        }
    }
    $outstanding="(".$outstanding."-1)";
# get number tenants
    $query = "select count(*) as rowCount from node a
              join node b on a.nid=b.title
              where a.type='tenant' and a.title like '%$skey%'  and b.nid in $outstanding ";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;

    # get tenants
    $query = "select a.nid, b.nid as aid from node a
              join node b on a.nid=b.title
              where a.type='tenant'  and a.title like '%$skey%' and b.nid in $outstanding order by a.nid asc limit $limit offset $offset";
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
        $output['aaData'][] = $row;
    }
    echo json_encode($output);
}


