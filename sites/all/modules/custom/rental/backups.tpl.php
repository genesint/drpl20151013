<?php

module_load_include('inc', 'rental', 'common');
$op = isset($_GET['op']) ? $_GET['op'] : 'default';
$query = "select nid from node where type='variable' and title='Version'";
$records = db_query($query);
$node = node_load($records->fetchAll()[0]->nid);
if ($op != 'restore') {
    $date = new DateTime();
    $node->field_value['und'][0]['value'] = "b" . $date->format('YmdHis');
    node_save($node);
}
else{
    $node->field_source['und'][0]['value'] = $node->field_value['und'][0]['value'];
    node_save($node);
}
$current = $node->field_value['und'][0]['value'];
$source = $node->field_source['und'][0]['value'];
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#backups').dataTable({
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
                {"bSearchable": false, "bVisible": false, "aTargets": [0]},
                {"bSearchable": false, "bSortable": false, "aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]},
                {"bSearchable": false, "bSortable": false, "aTargets": [3]},
                {"bSearchable": false, "bSortable": false, "aTargets": [4]},
                {"bSearchable": false, "bSortable": false, "aTargets": [5]},
                {"bSearchable": false, "bSortable": false, "aTargets": [6]}
            ],
            "sAjaxSource": "backups-grid"
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
                    <li><a href="backup-perform"><i class="fa fa-file-archive-o fa-fw"></i> Backup data</a></li>
                    <li><a href="#overlay=node/add/backups"><i class="fa fa-upload fa-fw"></i> Upload a backup</a></li>
                    <li><a href="backups"><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div class="row">
    <div class="col-md-3">Current version: <?php echo $current; ?></div>
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
    <div class="col-md-3">Source version: <?php echo $source; ?></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="backups" width="100%">
        <thead>
        <tr>
            <th>nid</th>
            <th>Restore</th>
            <th>Filename</th>
            <th>Size</th>
            <th>Source</th>
            <th>Upload date</th>
            <th>Download</th>
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
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>




