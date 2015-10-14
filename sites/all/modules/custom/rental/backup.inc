<?php
function backup_perform()
{
    $query = "select nid from node where type='variable' and title='Version'";
    $records = db_query($query);
    $node=node_load($records->fetchAll()[0]->nid);
    $date = new DateTime();
    $node->field_value['und'][0]['value']="b". $date->format('YmdHis');
    node_save($node);
    $source = getcwd() . "/sites/default/files/.ht.sqlite";
    $destination = getcwd() . "/sites/default/files/backups/". $node->field_value['und'][0]['value'] . ".sqlite";
    $result = copy($source, $destination);
    if ($result) {
        $query=array(
            'query'=>array('op'=>'backup')
        );
        drupal_goto('backups',$query);
    } else {
        print "Error: couldn't copy $source to $destination!\n";
    }
}
function backup()
{
    $query = "select nid from node where type='variable' and title='Version'";
    $records = db_query($query);
    $node=node_load($records->fetchAll()[0]->nid);
    $date = new DateTime();
    $node->field_source['und'][0]['value'] = $node->field_value['und'][0]['value'];
    $node->field_value['und'][0]['value']="b". $date->format('YmdHis');
    node_save($node);
    $source = getcwd() . "/sites/default/files/.ht.sqlite";
    $destination = getcwd() . "/sites/default/files/backups/". $node->field_value['und'][0]['value'] . ".sqlite";
    $result = copy($source, $destination);
    if ($result) {
        1;
    } else {
        print "Error: couldn't copy $source to $destination!\n";
    }
}

function restore_backup()
{
    $filename=$_GET['file'];
    $source = getcwd() . "/sites/default/files/backups/". $filename ;
    $destination = getcwd() . "/sites/default/files/.ht.sqlite";
    backup();
    $result = copy($source, $destination);
    if ($result) {
        $query=array(
            'query'=>array('op'=>'restore')
        );
        drupal_goto('backups', $query);
    } else {
        print "Error: couldn't copy $source to $destination!\n";
    }

}