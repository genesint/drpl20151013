<?php
module_load_include('inc', 'rental', 'common');
#expense transactions
$query = "
select a.nid, b.title from
(select nid from node where type='expense') a
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
        'field_account'=>array('target_id',$node->field_account2['und']['0']['target_id']),
        'field_particulars'=>array('value',$node->field_particulars['und']['0']['value'].', REF:'.$node->field_reference['und']['0']['value']),

        'field_transaction_reference' => array('value', 'E'.$record->nid),
        'field_amount'=>array('value',$node->field_amount_spent['und']['0']['value'])
    );
    createNode($title,$type,$values);
}
# generate voucher #
$query = "
select nid from node where type='expense' and title=''
";
$records = db_query($query);
foreach ($records as $record) {
    $node = node_load($record->nid);
    $node->title='E'.$record->nid;
    node_save($node);
}

#update transactions for changed expenses
$query = "
          select
          a.nid, a.title, b.field_amount_value, c.field_amount_spent_value from node a
          join field_data_field_amount b on a.nid=b.entity_id
          join field_data_field_amount_spent c on a.title=c.entity_id
          where abs(b.field_amount_value  - c.field_amount_spent_value)>0
";
$records = db_query($query);
foreach ($records as $record) {
    $trn = node_load($record->nid);
    $bln = node_load($record->title);
    $trn->field_amount['und']['0']['value'] = $bln->field_amount_spent['und']['0']['value'];
    $particulars = $bln->field_particulars['und']['0']['value'] . ', REF:' . $bln->field_reference['und']['0']['value'];
    $trn->field_particulars['und']['0']['value'] = $particulars;
    node_save($trn);
}

# Delete transactions which have been removed
$query = "select a.nid, a.title from node a left join node b on a.title=b.nid where a.type='transaction' and b.nid is null";
$records = db_query($query);
foreach ($records as $record) {
    $nid=$record->nid;
    node_delete($nid);
}
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#expenses').dataTable({
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
            "sAjaxSource": "expenses-grid"
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
                    <li><a href="#overlay=node/add/expense" ><i class="fa fa-plus fa-fw"></i>Add expense</a></li>
                    <li><a href="expenses" ><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href=""><i class="fa fa-download fa-fw"></i>Export</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                    <li><a href="accounts"><i class="fa fa-list fa-fw"></i>&nbsp;Accounts</a></li>
                    <li><a href="units"><i class="fa fa-building fa-fw"></i>&nbsp;Units</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="expenses" width="100%">
        <thead>
        <tr>
            <th>nid</th>
            <th>Voucher #</th>
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




