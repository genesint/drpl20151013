<?php
module_load_include('inc', 'rental', 'common');
$nid0=$_GET['nid'];
# get account nid
$query="select nid from node where type='account' and title='$nid0'";
$records = db_query($query);
$nid=$records->fetchAll()[0]->nid;

$title="Rent history: ".node_load($nid0)->field_full_name['und']['0']['value'];
drupal_set_title($title);

#billing transactions
$query = "
select a.nid, b.title from
(select nid from node where type='billing') a
left join
(select title from node where type='transaction') b
on
a.nid=b.title
where b.title is null
";
$records = db_query($query);
foreach ($records as $record) {
    $node = node_load($record->nid);
    $title = $record->nid;
    $type = 'transaction';
    $particulars = 'Rent for ' . node_load($node->field_unit['und']['0']['target_id'])->field_unit_name['und']['0']['value'];
    $particulars .=": ".node_load($node->field_unit['und']['0']['target_id'])->field_unit_code['und']['0']['value'];
    $date0 = new DateTime($node->field_start_date['und']['0']['value']);
    $date1 = new DateTime($node->field_stop_date['und']['0']['value']);
    $particulars .= ' from ' . $date0->format('d/m/Y') . ' to ' . $date1->format('d/m/Y');
    $values = array(
        'field_account' => array('target_id', $node->field_account['und']['0']['target_id']),
        'field_particulars' => array('value', $particulars),
        'field_transaction_reference' => array('value', 'B'.$record->nid),
        'field_amount' => array('value', $node->field_amount_due['und']['0']['value'])
    );
    createNode($title, $type, $values);
}

# generate invoice #
$query = "
select nid from node where type='billing' and title=''
";
$records = db_query($query);
foreach ($records as $record) {
    $node = node_load($record->nid);
    $node->title='B'.$record->nid;
    node_save($node);
}
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#mybilling').dataTable({
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
                {"bSearchable": false, "bSortable": false, "aTargets": [8]}
            ],
            "sAjaxSource": "mybilling-grid"
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
                    <li><a href="mybilling?nid=<?php echo $nid0;?>"><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href="export-rent?grid=billing&nid=<?php echo $nid;?>"><i class="fa fa-download fa-fw"></i>Export</a></li>
                    <li><a href="mytransactions?nid=<?php echo $nid;?>"><i class="fa fa-bars fa-fw"></i>Transactions</a></li>
                    <li><a href='mypayments?nid=<?php echo node_load($nid)->title;?>'><i class='fa fa-level-down fa-fw'></i>&nbsp;Payment</a></li>
                    <li><a href="mybilling?nid=<?php echo $nid0;?>#node/add/billing"><i class="fa fa-plus fa-fw"></i>Bill</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="mybilling" width="100%">
        <thead>
        <tr>
            <th>nid</th>
            <th>Invoice no</th>
            <th>Date</th>
            <th>Account</th>
            <th>Unit</th>
            <th>Start date</th>
            <th>Stop date</th>
            <th>Amount</th>
            <th><i class="fa fa-pencil-square-o fa-fw"></i></th>
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
        </tr>
        </tfoot>
    </table>
</div>




