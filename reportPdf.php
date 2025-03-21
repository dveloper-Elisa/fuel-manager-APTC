<?php
require_once './fpdf/fpdf.php'; // Include FPDF library

// Database connection
include "./connection.php";


/**
 * GENERATING PDF OF OPERATION FUEL
 */
if (isset($_GET['down-operation'])) {
    $id = (int)$_GET['down-operation'];

    // SELECTING DATA FROM DATABASE
    $sql = 'SELECT * FROM `operation` WHERE id = ?';
    $statement = $db->prepare($sql);
    $statement->bind_param('i', $id);
    $statement->execute();
    $result = $statement->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        die('No record found.');
    }

    $quickPdf = new FPDF();
    $quickPdf->AddPage();
    $quickPdf->SetMargins(20, 20, 20);
    $quickPdf->SetFont('Times', '', 12);
    $quickPdf->Image('./img/image.png', 40, 10, 120);
    $quickPdf->Ln(30);
    $quickPdf->Cell(0, 10, 'Date:   ' . date('Y-m-d', strtotime($data['created_at'])), 0, 0, 'R');
    $quickPdf->Ln(20);

    // Title
    $quickPdf->SetFont('Times', 'B', 14);
    $quickPdf->Cell(0, 10, 'Operation Fuel Request Report', 0, 1, 'C');
    $quickPdf->Ln(8);

    // Formatting the document
    $quickPdf->SetFont('Times', '', 12);

    function addDataRow($pdf, $label, $value, $wrap = false)
    {
        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(50, 10, $label, 0, 0, 'L');
        $pdf->SetFont('Times', 'B', 12);

        if ($wrap) {
            $pdf->Ln(10);
            $pdf->MultiCell(0, 8, $value, 0, 'L');
        } else {
            $pdf->Cell(0, 10, $value, 0, 1, 'L');
        }
    }

    addDataRow($quickPdf, 'Fuel:', $data['fuel']);
    addDataRow($quickPdf, 'Litter:', $data['litter']);
    addDataRow($quickPdf, 'Required Date:', $data['required_date']);
    addDataRow($quickPdf, 'Description:', $data['description'], true);

    $quickPdf->Ln(20);
    $quickPdf->Cell(95, 10, 'Requested by: ', 0, 0, 'L');
    $quickPdf->Cell(95, 10, 'Approved By: ', 0, 1, 'L');

    $quickPdf->Cell(95, 10, $data['prepared_by'], 0, 0, 'L');
    $quickPdf->Cell(95, 10, 'Lt Col. Alexandre KARASIRA', 0, 1, 'L');

    $quickPdf->Cell(95, 10, 'H/LOGISTIC APTC', 0, 0, 'L');
    $quickPdf->Cell(95, 10, 'D/CEO & DAF APTC', 0, 1, 'L');

    $quickPdf->Output();
}

/**
 * GENERATING PDF FOR OPERATION REPORT
 * 
 */
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
$pdf->Cell(40, 10, 'Plate No', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'From', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'To', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Date', 1, 0, 'C', true);
$pdf->Cell(80, 10, 'Description', 1, 1, 'C', true);

$pdf->SetFont('Times', '', 13);
$pdf->SetTextColor(0);

// Table rows
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, htmlspecialchars($row['driver']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($row['op_car']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($row['op_from']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($row['op_to']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($row['date']), 1);
    $pdf->MultiCell(80, 10, htmlspecialchars($row['description']), 1);
}

// Output PDF
$pdf->Output();

die();
