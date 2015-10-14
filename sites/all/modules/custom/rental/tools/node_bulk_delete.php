<?php

module_load_include('inc', 'node', 'node.pages');
$f = fopen( 'php://stdin', 'r' );
$type=fgets( $f );
fclose( $f );
$type=str_replace("\n","",$type);
$query = new EntityFieldQuery();
$query
 ->entityCondition('entity_type', 'node')
 ->entityCondition('bundle', $type)
 ->propertyCondition('status', 1);
$rResult = $query->execute();
$nids = array_keys($rResult['node']);
print count($nids)."\n";
$i=0;
foreach($nids as $nid){
	$node=node_load($nid);
	//print $node->title.":".$node->type."\n";
	node_delete($nid);
	$i=$i+1;
	if($i % 100==0){
		print $i."\n";
	}
}

?>
