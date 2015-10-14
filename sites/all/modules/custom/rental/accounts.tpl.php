<?php
module_load_include('inc', 'rental', 'common');
$query="
select nid from node where type='account' and title=''
";
$records = db_query($query);
foreach ($records as $record) {
    $node=node_load($record->nid);
    $node->title=$record->nid;
    node_save($node);
}

?>
<script>
    jQuery(document).ready(function () {
        jQuery('#accounts').dataTable({
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
                {"bSearchable": false, "bSortable": false, "aTargets": [0]},
                {"bSearchable": false, "bSortable": false, "aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]},
                {"bSearchable": false, "bSortable": false, "aTargets": [3]},
                {"bSearchable": false, "bSortable": false, "aTargets": [4]},
                {"bSearchable": false, "bSortable": false, "aTargets": [5]},
                {"bSearchable": false, "bSortable": false, "aTargets": [6]}
            ],
            "sAjaxSource": "accounts-grid"
        });
    });
</script>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
    <div class="col-md-3">
        <div id='rental-tool'>
            <div class="btn-group">
                <a class="btn btn-primary" href="#"><i class="fa fa-tasks fa-fw"></i>&nbsp;Actions</a>
                <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="fa fa-caret-down"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="#overlay=node/add/account"><i class="fa fa-plus fa-fw"></i>Account</a></li>
                    <li><a href="accounts"><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                    <li><a href="expenses"><i class="fa fa-money fa-fw"></i>&nbsp;Expenses</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="accounts" width="100%">
        <thead>
        <tr>
            <th>Account ID</th>
            <th>ID</th>
            <th>Account name</th>
            <th>Account type</th>
            <th>Particulars</th>
            <th><i class="fa fa-pencil-square-o fa-fw"></i></th>
            <th><i class="fa fa-trash fa-fw"></i></th>
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




