
<script>
    jQuery(document).ready(function () {
        jQuery("#start-date").datepicker({ dateFormat: 'yy-mm-dd' });
        jQuery("#end-date").datepicker({ dateFormat: 'yy-mm-dd' });
    });
</script>
<?php
$date0 = new DateTime(); # today
$date1 = new DateTime();
$date1->modify('last day of previous month'); # last day of previous month
$date2 = new DateTime($date1->format('Y-m-d'));
$date2->modify('last day of previous month'); # last day of the month before the previous month
$date3 = new DateTime($date0->format('Y-m-d'));
$date3->modify('-1 year'); # last year
# Calculation for units
$dateu = $date0->format("Y-m-d H:i:s");
$query = "
select d.field_unit_target_id from field_data_field_unit d join
(select a.entity_id from field_data_field_start_date a join field_data_field_stop_date b
                  on
                a.entity_id=b.entity_id
                where
                a.field_start_date_value<'$dateu' and b.field_stop_date_value>'$dateu') c
on d.entity_id=c.entity_id where d.bundle='billing'
                ";
$records = db_query($query);
$occupied = array();
foreach ($records as $record) {
    $occupied[] = $record->field_unit_target_id;
}
$total = array();
$total['Apartment'] = 0;
$total['Room'] = 0;
$vacant = array();
$vacant['Apartment'] = 0;
$vacant['Room'] = 0;
$query = "select nid from node where type='unit'";
$records = db_query($query);
foreach ($records as $record) {
    $node = node_load($record->nid);
    $total[node_load($node->field_unit_type['und'][0]['target_id'])->title] += 1;
    if (!in_array($record->nid, $occupied)) {
        $vacant[node_load($node->field_unit_type['und'][0]['target_id'])->title] += 1;
    }
}


#number of tenants
$query = "
select count(*) as total from node where type='tenant';
";
$records = db_query($query);
$num_tenants = $records->fetchAll()[0]->total;

# income computations for today
$today000000 = new Datetime($date0->format('Y-m-d') . " 00:00:00");
$today235959 = new Datetime($date0->format('Y-m-d') . " 00:00:00");
$today235959 = $today235959->modify('+86399 second');
$query = "
SELECT SUM(b.field_amount_paid_value) as amount from node a join field_data_field_amount_paid b
on
a.nid=b.entity_id WHERE a.created>=" . $today000000->format('U') . " AND a.created<=" . $today235959->format('U');
$records = db_query($query);
$incometoday = $records->fetchAll()[0]->amount;

$query = "
SELECT SUM(b.field_amount_spent_value) as amount from node a join field_data_field_amount_spent b
on
a.nid=b.entity_id WHERE a.created>=" . $today000000->format('U') . " AND a.created<=" . $today235959->format('U');
$records = db_query($query);
$expensestoday = $records->fetchAll()[0]->amount;

# income computations for yesterday
$yesterday000000 = new Datetime($date0->format('Y-m-d') . " 00:00:00");
$yesterday000000 = $yesterday000000->modify('-86400 second');
$yesterday235959 = new Datetime($date0->format('Y-m-d') . " 00:00:00");
$yesterday235959 = $yesterday235959->modify('-1 second');
$query = "
SELECT SUM(b.field_amount_paid_value) as amount from node a join field_data_field_amount_paid b
on
a.nid=b.entity_id WHERE a.created>=" . $yesterday000000->format('U') . " AND a.created<=" . $yesterday235959->format('U');
$records = db_query($query);
$incomeyesterday = $records->fetchAll()[0]->amount;

$query = "
SELECT SUM(b.field_amount_spent_value) as amount from node a join field_data_field_amount_spent b
on
a.nid=b.entity_id WHERE a.created>=" . $yesterday000000->format('U') . " AND a.created<=" . $yesterday235959->format('U');
$records = db_query($query);
$expensesyesterday = $records->fetchAll()[0]->amount;

# income computations for this month
$monthstart = new Datetime($date0->format('Y-m-01') . " 00:00:00");
$monthend = new Datetime($date0->format('Y-m-d') . " 00:00:00");
$monthend = $monthend->modify('last day of this month');
$monthend = $monthend->modify('+86399 second');
$query = "
SELECT SUM(b.field_amount_paid_value) as amount from node a join field_data_field_amount_paid b
on
a.nid=b.entity_id WHERE a.created>=" . $monthstart->format('U') . " AND a.created<=" . $monthend->format('U');
$records = db_query($query);
$incomemonth = $records->fetchAll()[0]->amount;

$query = "
SELECT SUM(b.field_amount_spent_value) as amount from node a join field_data_field_amount_spent b
on
a.nid=b.entity_id WHERE a.created>=" . $monthstart->format('U') . " AND a.created<=" . $monthend->format('U');
$records = db_query($query);
$expensesmonth = $records->fetchAll()[0]->amount;

# income computations for last month
$lmonthstart = new Datetime($monthstart->format('Y-m-d') . " 00:00:00");
$lmonthstart = $lmonthstart->modify('first day of last month');
$lmonthend = new Datetime($lmonthstart->format('Y-m-d') . " 00:00:00");
$lmonthend = $lmonthend->modify('last day of this month');
$lmonthend = $lmonthend->modify('+86399 second');
$query = "
SELECT SUM(b.field_amount_paid_value) as amount from node a join field_data_field_amount_paid b
on
a.nid=b.entity_id WHERE a.created>=" . $lmonthstart->format('U') . " AND a.created<=" . $lmonthend->format('U');
$records = db_query($query);
$incomelmonth = $records->fetchAll()[0]->amount;

$query = "
SELECT SUM(b.field_amount_spent_value) as amount from node a join field_data_field_amount_spent b
on
a.nid=b.entity_id WHERE a.created>=" . $lmonthstart->format('U') . " AND a.created<=" . $lmonthend->format('U');
$records = db_query($query);
$expenseslmonth = $records->fetchAll()[0]->amount;

# income computations for 2 months back
$l2monthstart = new Datetime($lmonthstart->format('Y-m-d') . " 00:00:00");
$l2monthstart = $l2monthstart->modify('first day of last month');
$l2monthend = new Datetime($l2monthstart->format('Y-m-d') . " 00:00:00");
$l2monthend = $l2monthend->modify('last day of this month');
$l2monthend = $l2monthend->modify('+86399 second');
$query = "
SELECT SUM(b.field_amount_paid_value) as amount from node a join field_data_field_amount_paid b
on
a.nid=b.entity_id WHERE a.created>=" . $l2monthstart->format('U') . " AND a.created<=" . $l2monthend->format('U');
$records = db_query($query);
$incomel2month = $records->fetchAll()[0]->amount;

$query = "
SELECT SUM(b.field_amount_spent_value) as amount from node a join field_data_field_amount_spent b
on
a.nid=b.entity_id WHERE a.created>=" . $l2monthstart->format('U') . " AND a.created<=" . $l2monthend->format('U');
$records = db_query($query);
$expensesl2month = $records->fetchAll()[0]->amount;
# income computations for this year
$yearstart = new Datetime($date0->format('Y-01-01') . " 00:00:00");
$yearend = new Datetime($date0->format('Y-12-31') . " 23:59:59");
$query = "
SELECT SUM(b.field_amount_paid_value) as amount from node a join field_data_field_amount_paid b
on
a.nid=b.entity_id WHERE a.created>=" . $yearstart->format('U') . " AND a.created<=" . $yearend->format('U');
$records = db_query($query);
$incomeyear = $records->fetchAll()[0]->amount;

$query = "
SELECT SUM(b.field_amount_spent_value) as amount from node a join field_data_field_amount_spent b
on
a.nid=b.entity_id WHERE a.created>=" . $yearstart->format('U') . " AND a.created<=" . $yearend->format('U');
$records = db_query($query);
$expensesyear = $records->fetchAll()[0]->amount;
# income computations for last year
$year1end = new Datetime($date0->format('Y-01-01') . " 00:00:00");
$year1end = $year1end->modify('-1 second');
$year1start = new Datetime($year1end->format('Y-01-01') . " 00:00:00");
$query = "
SELECT SUM(b.field_amount_paid_value) as amount from node a join field_data_field_amount_paid b
on
a.nid=b.entity_id WHERE a.created>=" . $year1start->format('U') . " AND a.created<=" . $year1end->format('U');
$records = db_query($query);
$income1year = $records->fetchAll()[0]->amount;
$query = "
SELECT SUM(b.field_amount_spent_value) as amount from node a join field_data_field_amount_spent b
on
a.nid=b.entity_id WHERE a.created>=" . $year1start->format('U') . " AND a.created<=" . $year1end->format('U');
$records = db_query($query);
$expenses1year = $records->fetchAll()[0]->amount;

# estimate outstanding amount of rent
$query = "
SELECT SUM(field_amount_due_value) as amount from field_data_field_amount_due where bundle='billing'
";
$records = db_query($query);
$totalsales = $records->fetchAll()[0]->amount;
$query = "
SELECT SUM(field_amount_paid_value) as amount from field_data_field_amount_paid
";
$records = db_query($query);
$totalincome = $records->fetchAll()[0]->amount;
$outstanding = $totalsales - $totalincome;

#notifications list
$query = "
select a.field_account_target_id, sum(b.field_amount_value) as amount from field_data_field_account a join field_data_field_amount b
on
a.entity_id=b.entity_id  where b.bundle='billing'
group by
a.field_account_target_id
";
$records = db_query($query);
$billing = array();
foreach ($records as $record) {
    $billing[$record->field_account_target_id] = $record->amount;
}
$query = "
select a.field_account_target_id, sum(b.field_amount_paid_value) as amount from field_data_field_account a join field_data_field_amount_paid b
on
a.entity_id=b.entity_id
group by
a.field_account_target_id
";
$records = db_query($query);
$income = array();
foreach ($records as $record) {
    $income[$record->field_account_target_id] = $record->amount;
}
$billing_keys = array_keys($billing);
$olist = array();
foreach ($billing_keys as $key) {
    $node = node_load($key);
    if (node_load($node->field_account_type['und'][0]['target_id'])->title != 'Tenant') continue;
    $bill = $billing[$key];
    if (!empty($income[$key])) {
        $bill = $bill - $income[$key];
    }
    $olist[$key] = $bill;
}
# alternative notification list
$query = "
select a.entity_id, b.field_stop_date_value from field_data_field_start_date a join field_data_field_stop_date b
                  on
                a.entity_id=b.entity_id
                where
                a.field_start_date_value<'$dateu' and b.field_stop_date_value>'$dateu'
                ";
$records = db_query($query);
$olist2 = array();
foreach ($records as $record) {
    $bllng = node_load($record->entity_id);
    $datex = new DateTime($record->field_stop_date_value);
    $interval = $datex->diff($date0)->days;
    $olist2[$record->entity_id] = $interval;
}
?>
<div class="row">
    <div class="col-md-2"><a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a></div>
    <div class="col-md-8"></div>
    <div class="col-md-2">
        <?php
        echo $date0->format('l, F d, Y');
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6 dashboard" id="d0">
        <table class="table table-bordered">
            <tr>
                <th colspan="4"><i class="fa fa-users fa-fw"></i>Tenants</th>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <th>Today</th>
            </tr>
            <tr>
                <td>Number of tenants</td>
                <td><?php echo $num_tenants; ?></td>
            </tr>
        </table>
        <table class="table table-bordered" id="d-units">
            <tr>
                <th colspan="4"><i class="fa fa-tachometer fa-fw"></i>Units</th>
            </tr>

            <tr>
                <th>&nbsp;</th>
                <th>Apartments</th>
                <th>Rooms</th>
            </tr>
            <tr>
                <td>Number of vacant units</td>
                <td><?php echo $vacant['Apartment']; ?></td>
                <td><?php echo $vacant['Room']; ?></td>
            </tr>
            <tr>
                <td>Number of occupied units</td>
                <td><?php echo $total['Apartment'] - $vacant['Apartment']; ?></td>
                <td><?php echo $total['Room'] - $vacant['Room']; ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?php echo $total['Apartment']; ?></td>
                <td><?php echo $total['Room']; ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6 dashboard" id="d1">
        <table class="table table-bordered">
            <tr>
                <th><i class="fa fa-tasks fa-fw"></i>Notifications</th>
            </tr>
            <?php
            foreach ($olist2 as $key => $value) {
                if ($value > 30) continue;
                $node = node_load($key);
                $link = '<a href="mytransactions?nid=' . node_load($node->field_account['und'][0]['target_id'])->title . '" ><i class="fa fa-money fa-fw"></i></a>';
                ?>
                <tr>
                    <td>Tenancy period
                        for <?php
                        $mobile=node_load(node_load($node->field_account['und'][0]['target_id'])->title)->field_mobile_number['und']['0']['value'];
                        echo node_load($node->field_account['und'][0]['target_id'])->field_account_name['und']['0']['value']." ($mobile)"; ?>
                        expires in <?php echo $value; ?> days. <?php echo $link; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12 dashboard">
        <table class="table table-bordered" id="d-finance">
            <tr>
                <th colspan="8"><i class="fa fa-money fa-fw"></i>Finance</th>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <th>Today</th>
                <th>Yesterday</th>
                <th><?php echo $date0->format('F'); ?></th>
                <th><?php echo $date1->format('F'); ?></th>
                <th><?php echo $date2->format('F'); ?></th>
                <th><?php echo $date0->format('Y'); ?></th>
                <th><?php echo $date3->format('Y'); ?></th>
            </tr>
            <tr>
                <td>Income</td>
                <td><?php echo number_format($incometoday, 2, ".", ","); ?></td>
                <td><?php echo number_format($incomeyesterday, 2, ".", ","); ?></td>
                <td><?php echo number_format($incomemonth, 2, ".", ","); ?></td>
                <td><?php echo number_format($incomelmonth, 2, ".", ","); ?></td>
                <td><?php echo number_format($incomel2month, 2, ".", ","); ?></td>
                <td><?php echo number_format($incomeyear, 2, ".", ","); ?></td>
                <td><?php echo number_format($income1year, 2, ".", ","); ?></td>
            </tr>
            <tr>
                <td>Outstanding</td>
                <td><?php echo number_format($outstanding, 2, ".", ","); ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Expenses</td>
                <td><?php echo number_format($expensestoday, 2, ".", ","); ?></td>
                <td><?php echo number_format($expensesyesterday, 2, ".", ","); ?></td>
                <td><?php echo number_format($expensesmonth, 2, ".", ","); ?></td>
                <td><?php echo number_format($expenseslmonth, 2, ".", ","); ?></td>
                <td><?php echo number_format($expensesl2month, 2, ".", ","); ?></td>
                <td><?php echo number_format($expensesyear, 2, ".", ","); ?></td>
                <td><?php echo number_format($expenses1year, 2, ".", ","); ?></td>
            </tr>
        </table>
    </div>
</div>
<?php
$date = new DateTime();
$start_date = isset($_GET['start-date']) ? $_GET['start-date'] . " 00:00:00" : $date->format('Y-m-01 00:00:00');
$end_date = isset($_GET['end-date']) ? $_GET['end-date'] . " 23:59:59" : $date->format('Y-m-d 23:59:59');
$query = "select nid,title from node where type='unit_group' ";
$records = db_query($query);
?>

<h4>Profit and Loss by Unit Group</h4>
<form role="form" class="row form-inline" action="unit-name-profit-loss" method="GET">
    <div class="col-md-3">
        <div class="form-group">
            <label for="start-date">Unit Group:&nbsp;</label>
            <select class="form-control" name="unit-group-id">
                <?php
                foreach ($records as $record) {
                    ?>
                    <option value="<?php echo $record->nid; ?>"><?php echo $record->title; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="start-date">From&nbsp;</label>
            <input type="text" class="form-control" id="start-date" name="start-date"
                   placeholder="<?php echo isset($_GET['start-date']) ? $_GET['start-date'] : $date->format('Y-m-01'); ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="end-date">to&nbsp;</label>
            <input type="text" class="form-control" id="end-date" name="end-date"
                   placeholder="<?php echo isset($_GET['end-date']) ? $_GET['end-date'] : $date->format('Y-m-d'); ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <button type="submit" class="btn btn-default">Go</button>
        </div>
    </div>
</form>