<?php

module_load_include('inc', 'rental', 'common');
# auto bill/pay
/*
$query="select a.entity_id, b.field_billing_ref_value from field_data_field_payment_date a left join field_data_field_billing_ref b on a.entity_id=b.field_billing_ref_value where b.field_billing_ref_value is null";
$records = db_query($query);
foreach ($records as $record) {
    $node=node_load($record->entity_id);
    $title='';
    $type = 'income';
    $date0 = new DateTime($node->field_start_date['und']['0']['value']);
    $date1 = new DateTime($node->field_stop_date['und']['0']['value']);
    $particulars = 'Being payment of rent for '.node_load($node->field_unit['und']['0']['target_id'])->field_unit_code['und']['0']['value'].' from ' . $date0->format('d/m/Y') . ' to ' . $date1->format('d/m/Y');
    $values=array(
        'field_reference' => array('value', 'Auto'),
        'field_particulars'=>array('value',$particulars ),
        'field_account'=>array('target_id',$node->field_account['und']['0']['target_id']),
        'field_amount_paid'=>array('value',$node->field_amount_due['und']['0']['value']),
        'field_billing_ref'=>array('value',$record->entity_id)
    );
    createNode($title,$type,$values);
}
*/
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
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#income').dataTable({
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
                {"bSearchable": false, "bSortable": false, "aTargets": [6]},
                {"bSearchable": false, "bSortable": false, "aTargets": [7]},
                {"bSearchable": false, "bSortable": false, "aTargets": [8]},
                {"bSearchable": false, "bSortable": false, "aTargets": [9]}
            ],
            "sAjaxSource": "income-grid"
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
                    <li><a href="#overlay=node/add/income" ><i class="fa fa-plus fa-fw"></i>Add income</a></li>
                    <li><a href="income" ><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href="export-income?grid=income"><i class="fa fa-download fa-fw"></i>Export</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="income" width="100%">
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




