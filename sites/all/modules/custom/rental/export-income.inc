<?php

/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';


function download_excel()
{
    $grid = $_GET['grid'];
    $nid = isset($_GET['nid'])? $_GET['nid']:"x";
    $gdef=array(
        'income'=>array(
            array('Receipt #',''),
            array('Date',''),
            array('Reference',''),
            array('Particulars',''),
            array('Account',''),
            array('Amount',''),
        ),

    );
// Create new PHPExcel object

    $objPHPExcel = new PHPExcel();

// Set document properties
    $objPHPExcel->getProperties()->setCreator("Spaces Management System")
        ->setLastModifiedBy("Spaces Management System")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    $row = 1;
    $colwidth = 20;
    for($i=0;$i<count($gdef[$grid]);$i++){

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(97+$i) . $row, $gdef[$grid][$i][0]);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(chr(97+$i) )->setWidth($colwidth);
    }
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:'.chr(97+count($gdef[$grid])-1).'1')->getFont()->setBold(true);


    $query = "select entity_id from field_data_field_account where bundle='income' and field_account_target_id=$nid order by entity_id desc";
    if($nid=='x'){
        $query = "select entity_id from field_data_field_account where bundle='income' order by entity_id desc";
    }
    $records = db_query($query);
    foreach ($records as $record) {
        $row++;
        $node = node_load($record->entity_id);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(97) . $row,$node->title );
        $date=new DateTime('@'.$node->created);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(98) . $row,$date->format('d/m/Y') );
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(99) . $row,$node->field_reference['und'][0]['value'] );
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(100) . $row,$node->field_particulars['und'][0]['value'] );
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(101) . $row,node_load($node->field_account['und'][0]['target_id'])->field_account_name['und'][0]['value'] );
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(102) . $row,$node->field_amount_paid['und'][0]['value'] );

    }
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        )
    );
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:'.chr(97+count($gdef[$grid])-1) . $row)->applyFromArray($styleArray);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('I1:I' . $objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);


    $date = new DateTime();
    $objPHPExcel->getActiveSheet()->setTitle($grid.$date->format('-Y-m-d-His'));


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $filename = $grid . $date->format('-Y-m-d-His') . '.xlsx';
// Redirect output to a clientâs web browser (Excel5)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
}
