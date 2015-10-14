<?php

module_load_include('inc', 'rental', 'common');

#income transactions
$query = "
select a.nid, b.title from
(select nid from node where type='income') a
left join
(select title from node where type='transaction') b
on
a.nid=b.title
where b.title is null
";
$records = db_query($query);
foreach ($records as $record) {
    $node=node_load($record->nid);
    $title=$record->nid;
    $type = 'transaction';
    $values=array(
        'field_account'=>array('target_id',$node->field_account['und']['0']['target_id']),
        'field_particulars'=>array('value',$node->field_particulars['und']['0']['value'].', REF:'.$node->field_reference['und']['0']['value']),

        'field_transaction_reference' => array('value', 'I'.$record->nid),
        'field_amount'=>array('value',$node->field_amount_paid['und']['0']['value']*-1)
    );
    createNode($title,$type,$values);
}
# generate receipt #
$query = "
select nid from node where type='income' and title=''
";
$records = db_query($query);
foreach ($records as $record) {
    $node = node_load($record->nid);
    $node->title='I'.$record->nid;
    node_save($node);
}
$nid0=$_GET['nid'];
# get account nid
$query="select nid from node where type='account' and title='$nid0'";
$records = db_query($query);
$nid=$records->fetchAll()[0]->nid;

$title="Payment history: ".node_load($nid0)->field_full_name['und']['0']['value'];
drupal_set_title($title);
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#mypayments').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "fnServerParams": function (aoData) {
                aoData.push({"name": "nid", "value": <?php echo $nid;?>});
            },
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
                {"bSearchable": false, "bSortable": false, "aTargets": [6]},
                {"bSearchable": false, "bSortable": false, "aTargets": [7]},
                {"bSearchable": false, "bSortable": false, "aTargets": [8]},
                {"bSearchable": false, "bSortable": false, "aTargets": [9]}
            ],
            "sAjaxSource": "mypayments-grid"
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
                    <li><a href="mypayments?nid=<?php echo $nid0;?>" ><i class="fa fa-refresh fa-fw"></i>&nbsp;Refresh</a></li>
                    <li><a href="export-income?grid=income&nid=<?php echo $nid;?>"><i class="fa fa-download fa-fw"></i>&nbsp;Export</a></li>
                    <li><a href="mytransactions?nid=<?php echo $nid;?>"><i class="fa fa-bars fa-fw"></i>Transactions</a></li>
                    <li><a href='mybilling?nid=<?php echo node_load($nid)->title;?>'><i class='fa fa-level-up fa-fw'></i>&nbsp;Rent</a></li>

                    <li><a href="mypayments?nid=<?php echo $nid0;?>#overlay=node/add/income"><i class="fa fa-plus fa-fw"></i>&nbsp;Add payment</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="mypayments" width="100%">
        <thead>
        <tr>
            <th>nid</th>
            <th>Receipt #</th>
            <th>Date</th>
            <th>Reference</th>
            <th>Particulars</th>
            <th>Account</th>
            <th>Amount</th>
            <th><i class="fa fa-pencil-square-o fa-fw"></i></th>
            <th><i class="fa fa-trash fa-fw"></i></th>
            <th><i class="fa fa-print fa-fw"></i></th>
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
            <th></th>
            <th></th>
            <th>
                <a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>




