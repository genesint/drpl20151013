<?php
function createNode($title, $type, $values)
{
    global $user;
    $node = new stdClass();
    $node->title = $title;
    $node->type = $type;
    node_object_prepare($node); // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().
    $node->language = LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled
    $node->uid = $user->uid;
    $node->status = 1; //(1 or 0): published or not
    $node->promote = 0; //(1 or 0): promoted to front page
    $node->comment = 0; // 0 = comments disabled, 1 = read only, 2 = read/write
    foreach ($values as $key => $value) {
        $node->{$key}['und'][0][$value[0]] = $value[1];
    }
    $node = node_submit($node); // Prepare node for saving
    node_save($node);
}

function searchbytitle($title, $type)
{
    $query = "select nid from node where title='$title' and type='$type'";
    $result = db_query($query);
    $records = $result->fetchAll();
    if (count($records) > 0) {
        return $records[0]->nid;
    }
    return -1;
}

function count_months($m0, $m1,$y0,$y1)
{

    $months = array();
    for ($y = $y0; $y < $y1 + 1; $y++) {
        for ($m = 1; $m < 13; $m++) {
            if($y==$y0 and $m<$m0 ) continue;
            if($y==$y0 and $m>$m1 ) continue;
            $months[]=$m."-".$y;
        }
    }
    return count($months);

}

function loadcfg()
{

    if (request_path() != 'starter-license' and request_path() != 'user') {

        $r_output = file_get_contents("rental.txt");
        $r_output = str_replace("\n", "", $r_output);
        $o_output = array();
        exec('converter.exe ' . $r_output, $o_output);
        $o_output = explode(" ", $o_output[0]);
        if ($o_output[1] == "0") {
            drupal_goto('starter-license', array());
        }
    }
}