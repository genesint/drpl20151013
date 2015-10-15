<?php


function backup_delete()
{
    $filename=$_GET['file'];
    $source = getcwd() . "/sites/default/files/backups/". $filename ;
    $result = unlink($source);
    if ($result) {
        $query=array(
            'query'=>array('op'=>'default')
        );
        drupal_goto('backups', $query);
    } else {
        print "Error: couldn't delete $source!\n";
    }

}