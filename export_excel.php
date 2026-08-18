<?php
require 'db.php';
require 'vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column headers
$headers = ['A1' => 'Payment ID', 'B1' => 'Tenant Name', 'C1' => 'Amount Paid (PHP)', 'D1' => 'Date Paid', 'E1' => 'Status'];
foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Fetch data
$query = "SELECT p.payment_id, t.first_name, p.amount, p.date_paid, s.status_type 
          FROM payment_tbl p 
          JOIN tenant_tbl t ON p.tenant_id = t.tenant_id 
          JOIN status s ON p.status_id = s.status_id 
          WHERE p.status_id = 5 
          ORDER BY p.date_paid DESC";
$result = mysqli_query($con, $query);

// Populate rows
$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNum, $row['payment_id']);
    $sheet->setCellValue('B' . $rowNum, $row['first_name']);
    $sheet->setCellValue('C' . $rowNum, number_format($row['amount'], 2, '.', ''));
    $sheet->setCellValue('D' . $rowNum, $row['date_paid']);
    $sheet->setCellValue('E' . $rowNum, $row['status_type']);
    $rowNum++;
}

// Auto-size columns
foreach (range('A', 'E') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Center align all cells
$sheet->getStyle('A1:E' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:E1')->getFont()->setBold(true); // Make headers bold

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="payment_stats.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
