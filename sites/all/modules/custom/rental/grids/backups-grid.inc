<?php

function backups_grid()
{
    global $base_url;
    $start = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : "0";
    $end = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : "0";
    $skey = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
    $bdir=getcwd()."/sites/default/files/backups/";
    $files=scandir($bdir);
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
    foreach ($files as $ofile) {
        if(in_array($ofile,array(".",".."))) continue;

        $row = array();
        $row[] =0;
        $row[] =  '<a href="restore-backup?file='.$ofile.'" ><i class="fa fa-history fa-fw"></i></a>';
        $row[] = $ofile;
        $row[] = filesize($bdir.$ofile);
        $db = new SQLite3($bdir.$ofile);
        $stmt = $db->prepare("SELECT field_source_value FROM field_data_field_source;");
        $result = $stmt->execute();
        $source = $result->fetchArray();
        $row[]=$source['field_source_value'];
        $date=new DateTime('@'.fileatime($bdir.$ofile));
        $date->setTimeZone(new DateTimeZone('Africa/Kampala'));
        $row[] =  $date->format('d/m/Y H:i:s')." Africa/Kampala";
        $row[] =  '<a href="'.$base_url.'/sites/default/files/backups/'.$ofile.'" ><i class="fa fa-download fa-fw"></i></a>';

        $row[] =  '<a href="backup-delete?file='.$ofile.'" ><i class="fa fa-trash fa-fw"></i></a>';
        $output['aaData'][] = $row;
        $rowCount++;
    }
    $output["iTotalRecords"] = $rowCount;
    $output["iTotalDisplayRecords"] = $rowCount;
    echo json_encode($output);
}


