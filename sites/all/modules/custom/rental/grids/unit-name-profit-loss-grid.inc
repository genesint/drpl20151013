<?php

function unit_name_profit_loss_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $unit_group_aid = $_GET['unit-group-aid'];
    $start_date = new DateTime($_GET['start-date']);
    $end_date = new DateTime($_GET['end-date']);
    $eids = array();
    # get all expenses with target_id = $unit_group_aid
    $query = "select a.entity_id from field_data_field_account2 a join node b on a.entity_id=b.nid
              where a.bundle='expense' and a.field_account2_target_id='$unit_group_aid'
              and b.created>={$start_date->format('U')}
              and b.created<={$end_date->format('U')}";
    $records = db_query($query);
    foreach ($records as $record) {
        $eids[]=$record->entity_id;
    }
    $bids = array();
# get all billing with uid as uid; this is income
    $unit_group_id=node_load($unit_group_aid)->title;
    $query = "select a.entity_id from field_data_field_unit a join node b
              on a.entity_id=b.nid
              join field_data_field_unit_group c on c.entity_id=a.field_unit_target_id
              where a.bundle='billing' and c.bundle='unit' and c.field_unit_group_target_id='$unit_group_id'
              and b.created>={$start_date->format('U')}
              and b.created<={$end_date->format('U')}";
    $records = db_query($query);
    foreach ($records as $record) {
        $bids[] = $record->entity_id;
    }
    /*
    * Output
    */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => array()
    );

    $billing=0;
    foreach ($bids as $bid) {
        $node = node_load($bid);
        $row = array();
        $row[] = $node->nid;
        $row[] = $node->title;
        $date = new DateTime('@' . $node->created);
        $row[] = $date->format('d/m/Y');
        $row[] = "Invoice #: ".$node->title;
        $unit_name=node_load($node->field_unit['und'][0]['target_id'])->field_unit_name['und'][0]['value'];
        $unit_code=node_load($node->field_unit['und'][0]['target_id'])->field_unit_code['und'][0]['value'];
        $row[] = "Rental income from $unit_name/$unit_code";
        $type  = node_load(node_load($node->field_account['und'][0]['target_id'])->field_account_type['und'][0]['target_id'])->title;
        $row[] = node_load($node->field_account['und'][0]['target_id'])->field_account_name['und'][0]['value']." ($type)";
        $row[] = number_format($node->field_amount_due['und'][0]['value'], 2, ".", ",");
        $output['aaData'][] = $row;
        $billing+=$node->field_amount_due['und'][0]['value'];
    }
    $output['aaData'][] = array("","","","","","<em>Total income</em>","<em>".number_format($billing, 2, ".", ",")."</em>");
    $expenses=0;
    foreach ($eids as $eid) {
        $node = node_load($eid);
        $row = array();
        $row[] = $node->nid;
        $row[] = $node->title;
        $date = new DateTime('@' . $node->created);
        $row[] = $date->format('d/m/Y');
        $row[] = "Voucher #: ".$node->title;
        $row[] = $node->field_particulars['und'][0]['value'];
        $type  = node_load(node_load($node->field_account2['und'][0]['target_id'])->field_account_type['und'][0]['target_id'])->title;
        $row[] = node_load($node->field_account2['und'][0]['target_id'])->field_account_name['und'][0]['value']." ($type)";
        $row[] = number_format(-$node->field_amount_spent['und'][0]['value'], 2, ".", ",");
        $output['aaData'][] = $row;
        $expenses-=$node->field_amount_spent['und'][0]['value'];
    }
    $output['aaData'][] = array("","","","","","<em>Total expenses</em>","<em>".number_format($expenses, 2, ".", ",")."</em>");
    $output['aaData'][] = array("","","","","","<em>Profit</em>","<em>".number_format($billing+$expenses, 2, ".", ",")."</em>");
    echo json_encode($output);
}


