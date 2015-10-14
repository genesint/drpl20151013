<?php
$nid=$_GET['nid'];
$node=node_load($nid);
$date=new DateTime('@'.$node->created);
?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <table class=" table table-bordered">
            <tr>
                <th>Invoice</th>
                <th colspan="2">
                    <div style="float:right">
                        <?php echo variable_get('site_name', '');?>
                        <br>P. O. Box 696
                        <br>Jinja-Uganda
                        <br>+256 701460096
                        <br>+256 772826636
                        <br>+256 755826636
                    </div>
                </th>
            </tr>
            <tr>
                <th></th>
                <th>Date:&nbsp;<?php echo $date->format('d/m/Y');?></th>
                <th>No:&nbsp;<?php echo $node->title;?></th>
            </tr>
            <tr>
                <td>Bill to:</td>
                <td colspan="2"><?php echo node_load($node->field_account['und']['0']['target_id'])->field_account_name['und']['0']['value'];?></td>

            </tr>
            <tr>
                <td>Amount:</td>
                <td colspan="2">UGX <?php echo number_format($node->field_amount_due['und']['0']['value'],  2 ,  "." ,  "," );?></td>

            </tr>
            <tr>
                <td>For :</td>
                <td colspan="2">Rental services:<?php echo node_load($node->field_unit['und'][0]['target_id'])->field_unit_code['und'][0]['value']; ?></td>

            </tr>
        </table>
    </div>
    <div class="col-md-2"><a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a></div>
</div>