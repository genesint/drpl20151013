<?php
module_load_include('inc', 'rental', 'common');
#create unit group accounts
$query = "
select a.nid, b.title from
(select nid from node where type='unit_group') a
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
        'field_account_name'=>array('value',$node->title),
        'field_account_type'=>array('target_id',1700),
        'field_particulars'=>array('value','Account for expenses incurred on '.$node->title)
    );
    createNode($title,$type,$values);
}
?>

<script>
    jQuery(document).ready(function () {
        jQuery('#unit-groups').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "aoColumnDefs": [
                {"bSearchable": false,  "bVisible": false,"bSortable": false, "aTargets": [0]},
                {"bSearchable": false, "bVisible": true, "bSortable": false,"aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]}
            ],
            "sAjaxSource": "unit-groups-grid"
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
                    <li><a href="#overlay=node/add/unit-group" ><i class="fa fa-plus fa-fw"></i>Add unit group</a></li>
                    <li><a href="unit-types" ><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href="units"><i class="fa fa-users fa-fw"></i>&nbsp;Units</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic" style="width:50%;margin-left:auto;margin-right:auto">
    <table class="display  table table-bordered" id="unit-groups" width="100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Menu</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th>
                <a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>




