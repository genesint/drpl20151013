<?php
function delete_tenant()
{
    $tid=isset($_GET['tid'])? $_GET['tid']:"";
    if($tid==""){
        drupal_goto('tenants');
        return;
    }
    #get account id
    $query="select nid from node where title=$tid";
    $records = db_query($query);
    $aid=$records->fetchAll()[0]->nid;

    #get incomes, billing and transactions
    $query="select entity_id from field_data_field_account where bundle in ('billing','income','transaction')
    and field_account_target_id='$aid'";
    $records = db_query($query);
    foreach ($records as $record) {
        $node=node_load($record->entity_id);
        #echo $node->nid.'->'.$node->title.'<br>';
        node_delete($record->entity_id);
    }
    #delete account
    #echo $aid.'->'.$tid.'<br>';
    node_delete($aid);
    #delete tenant
    #echo $tid;
    node_delete($tid);

    drupal_goto('tenants');
}
function delete_unit()
{
    $uid=isset($_GET['uid'])? $_GET['uid']:"";
    if($uid==""){
        drupal_goto('units');
        return;
    }
    #get account id
    $query="select nid from node where title=$uid";
    $records = db_query($query);
    $aid=$records->fetchAll()[0]->nid;

    #get incomes, billing and transactions
    $query="select entity_id from field_data_field_account where bundle in ('billing','income','transaction')
    and field_account_target_id='$aid'";
    $records = db_query($query);
    foreach ($records as $record) {
        $node=node_load($record->entity_id);
        #echo $node->nid.'->'.$node->title.'<br>';
        node_delete($record->entity_id);
    }
    #delete account
    #echo $aid.'->'.$tid.'<br>';
    node_delete($aid);
    #delete tenant
    #echo $tid;
    node_delete($uid);

    drupal_goto('units');
}
function delete_unit_type()
{
    $uid=isset($_GET['uid'])? $_GET['uid']:"";
    if($uid==""){
        drupal_goto('unit-types');
        return;
    }
    node_delete($uid);

    drupal_goto('unit-types');
}