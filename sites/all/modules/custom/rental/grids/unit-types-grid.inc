<?php

function unit_types_grid()
{

    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $query = "select count(*) as rowCount from node where type='unit_type'";
    $records = db_query($query);
    $rowCount=$records->fetchAll()[0]->rowCount;

    $query = "select nid from node where type='unit_type'  order by nid desc limit $end offset $start";
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
        $actions="<div class='btn-group'>
            <a class='btn btn-primary' href='#'><i class='fa fa-bars fa-fw'></i>&nbsp;Menu</a>
            <a class='btn btn-primary dropdown-toggle' data-toggle='dropdown' href='#'>
                <span class='fa fa-caret-down'></span></a>
            <ul class='dropdown-menu'>
                <li><a href='#overlay=node/{$node->nid}/edit'><i class='fa fa-pencil fa-fw'></i>&nbsp;Edit</a></li>
                <li><a href='#overlay=node/{$node->nid}/edit'><i class='fa fa-pencil fa-fw'></i>&nbsp;Delete</a></li>
            </ul>
        </div>";
        $row[] = $actions;
        $output['aaData'][] = $row;
    }
    echo json_encode($output);
}


