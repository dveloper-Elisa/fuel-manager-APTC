<?php
require_once './fpdf/fpdf.php'; // Include FPDF library

// Database connection
include "./connection.php";


// Fetch data
$result = $db->query("SELECT * FROM operation_report ORDER BY date DESC");
if (!$result) {
    die("Query failed: " . $db->error);
}

// Create PDF class
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Times', 'B', 16);
        $this->Image('./img/image.png', 80, 10, 120);
        $this->Ln(30);
        $this->Cell(0, 10, 'Operation Fuel Report', 0, 1, 'C');
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Times', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Create PDF document
$pdf = new PDF();
$pdf->SetMargins(10, 5, 10);
$pdf->AddPage('l');
$pdf->SetFont('Times', 'B', 13);

// Table header
$pdf->SetFillColor(76, 175, 80);
$pdf->SetTextColor(255);
$pdf->Cell(40, 10, 'Driver', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'From', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'To', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Date', 1, 0, 'C', true);
$pdf->Cell(80, 10, 'Description', 1, 1, 'C', true);

$pdf->SetFont('Times', '', 13);
$pdf->SetTextColor(0);

// Table rows
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, htmlspecialchars($row['driver']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($row['op_from']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($row['op_to']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($row['date']), 1);
    $pdf->Cell(80, 10, htmlspecialchars($row['description']), 1, 1);
}

// Output PDF
$pdf->Output();

die();
