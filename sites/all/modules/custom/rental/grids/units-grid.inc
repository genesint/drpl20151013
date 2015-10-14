<?php

function units_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $status_values = isset($_GET['status_values']) ? $_GET['status_values'] : "";
    $query = "select count(*) as rowCount from node where type='unit' and title like '%$skey%'";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;

    $query = "select nid from node where type='unit' and title like '%$skey%' order by nid desc limit $end offset $start";
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
    $stvals = explode("|", $status_values);
    foreach ($records as $record) {
        $node = node_load($record->nid);
        $row = array();
        $row[] = $node->nid;
        $row[] = $node->title;
        $unit = $node->field_unit_name['und'][0]['value'].', '.$node->field_unit_code['und'][0]['value'];
        $unit .=' ('.node_load($node->field_unit_type['und'][0]['target_id'])->title;
        if (in_array($node->nid, $stvals)) {
            $row[] = "$unit, <i class='fa fa-star fa-fw'></i>)";
        } else {
            $row[] =  "$unit, <i class='fa fa-star-o fa-fw'></i>)";

        }
        $row[] = "Meter: ".$node->field_water_meter_number['und'][0]['value']."<br>".
                 "A/C: ".$node->field_water_billing_account_numb['und'][0]['value'];
        $row[] = "Meter: ".$node->field_electricity_meter_number['und'][0]['value']."<br>".
            "A/C: ".$node->field_electricity_account_number['und'][0]['value'];
        $row[] = number_format($node->field_cost_per_month['und'][0]['value'], 2, ".", ",");
        $actions="<div class='btn-group'>
            <a class='btn btn-primary' href='#'><i class='fa fa-bars fa-fw'></i>&nbsp;Menu</a>
            <a class='btn btn-primary dropdown-toggle' data-toggle='dropdown' href='#'>
                <span class='fa fa-caret-down'></span></a>
            <ul class='dropdown-menu'>
                <li><a href='#overlay=node/{$node->nid}/edit'><i class='fa fa-pencil fa-fw'></i>&nbsp;Edit</a></li>
                <li><a href='delete-unit?uid={$node->nid}'><i class='fa fa-trash fa-fw'></i>&nbsp;Delete</a></li>
                <li class='divider'></li>
                <li><a href='unit-history?nid={$node->nid}'><i class='fa fa-history fa-fw'></i>&nbsp;Rental history</a></li>
                <li><a href='unit-expenses?nid={$node->nid}'><i class='fa fa-history fa-fw'></i>&nbsp;Expense history</a></li>
                <li><a href='unit-profit-loss?uid={$node->nid}'><i class='fa fa-question-circle fa-fw'></i>&nbsp;Profit & Loss</a></li>
            </ul>
        </div>";
        $row[] = $actions;
        $output['aaData'][] = $row;
    }
    echo json_encode($output);
}


