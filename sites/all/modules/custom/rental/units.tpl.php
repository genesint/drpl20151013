<?php
module_load_include('inc', 'rental', 'common');
# get room status
$date=new DateTime();
$date=$date->format("Y-m-d H:i:s");
$query="
select d.field_unit_target_id from field_data_field_unit d join
(select a.entity_id from field_data_field_start_date a join field_data_field_stop_date b
                  on
                a.entity_id=b.entity_id
                where
                a.field_start_date_value<'$date' and b.field_stop_date_value>'$date') c
on d.entity_id=c.entity_id


                ";
$records = db_query($query);
$status="";
foreach($records as $record){
    $status.="|".$record->field_unit_target_id;
}
#create unit accounts
$query = "
select a.nid, b.title from
(select nid from node where type='unit') a
left join
(select title from node where type='account') b
on
a.nid=b.title
where b.title is null;
";
$records = db_query($query);
foreach ($records as $record) {
    $node=node_load($record->nid);
    $title=$record->nid;
    $type = 'account';
    $values=array(
        'field_account_name'=>array('value',$node->field_unit_name['und'][0]['value'].'-'.$node->field_unit_code['und'][0]['value']),
        'field_account_type'=>array('target_id',1010),
        'field_particulars'=>array('value','Account for expenses incurred on '.$node->field_unit_name['und'][0]['value'].'-'.$node->field_unit_code['und'][0]['value'])
    );
    createNode($title,$type,$values);
}
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#units').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "fnServerParams": function (aoData) {
                aoData.push({"name": "status_values", "value": "<?php echo $status;?>"});
            },
            "processing": true,
            "serverSide": true,
            "searching": true,
            "aoColumnDefs": [
                {"bSearchable": false, "bVisible": true, "bSortable": false, "aTargets": [0]},
                {"bSearchable": false, "bVisible": false, "aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]},
                {"bSearchable": false, "bSortable": false, "aTargets": [3]},
                {"bSearchable": false, "bSortable": false, "aTargets": [4]},
                {"bSearchable": false, "bSortable": false, "aTargets": [5]},
                {"bSearchable": false, "bSortable": false, "aTargets": [6]}
            ],
            "sAjaxSource": "units-grid"
        });
    });
</script>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
    <div class="col-md-3">
        <div id='rental-tool'>
            <div class="btn-group">
                <a class="btn btn-primary" href="#"><i class="fa fa-list-ul fa-fw"></i>&nbsp;Actions</a>
                <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="fa fa-caret-down"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="#overlay=node/add/unit" ><i class="fa fa-plus fa-fw"></i>Add unit</a></li>
                    <li><a href="#overlay=node/add/unit-type" ><i class="fa fa-plus fa-fw"></i>Add unit type</a></li>
                    <li><a href="units" ><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href="export-units"><i class="fa fa-download fa-fw"></i>&nbsp;Export</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="units" width="100%">
        <thead>
        <tr>
            <th>Unit ID</th>
            <th>Title</th>
            <th>Unit</th>
            <th>Water</th>
            <th>Power</th>
            <th>Cost per month</th>
            <th>Menu</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>
                <a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>




