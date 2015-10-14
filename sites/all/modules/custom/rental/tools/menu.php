<?php
global $user;
module_load_include('inc', 'node', 'node.pages');
function addnode($line){
	$line= str_replace ( "\n" , "" , $line );
	$data=explode ( "|" , $line , 15);
	$node = (object) array(
	  'uid' => $user->uid,
	  'name' => $user->name,
	  'type' => 'menu',
	  'language' => 'und',
	);
	
	node_object_prepare($node);

	$fields = field_info_instances('node');
	$form_state = array();
	foreach($fields['menu'] as $field_name => $values) {
	  $form_state['values'][$field_name] = array('und' => array());
	}	
	$form_state['values']['title']=$data[0];
	$form_state['values']['field_title_callback']['und'][0]['value']=$data[1];
	$form_state['values']['field_title_arguments']['und'][0]['value']=$data[2];
	$form_state['values']['field_description']['und'][0]['value']=$data[3];
	$form_state['values']['field_page_callback']['und'][0]['value']=$data[4];
	$form_state['values']['field_page_arguments']['und'][0]['value']=$data[5];
	$form_state['values']['field_access_callback']['und'][0]['value']=$data[6];
	$form_state['values']['field_access_arguments']['und'][0]['value']=$data[7];
	$form_state['values']['field_file']['und'][0]['value']=$data[8];
	$form_state['values']['field_file_path']['und'][0]['value']=$data[9];
	$form_state['values']['field_weight']['und'][0]['value']=$data[10];
	$form_state['values']['field_menu_name']['und'][0]['value']=$data[11];
	$form_state['values']['field_type']['und'][0]['value']=$data[12];
	$form_state['values']['field_url']['und'][0]['value']=$data[13];

	$form_state['values']['op']=t('Save');
	drupal_form_submit('menu_node_form',$form_state, $node);
}
$filename="sites/all/modules/custom/rental/tools/menu.csv";
$filearray = file($filename);
$i=-1;
foreach($filearray as $line){
	$i=$i+1;
	if($i==0) {
		continue;
	}
	
	addnode($line);
}


