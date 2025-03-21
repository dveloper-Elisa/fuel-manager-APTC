<?php
include "./fpdf/fpdf.php";
session_start();
if (isset($_SESSION['from']) && isset($_SESSION['to'])) {
    $from = $_SESSION['from'];
    $to = $_SESSION['to'];
    echo "Date range: $from to $to";
}

function getDatesReport($from, $to)
{
    include "./connection.php";

    ob_start();

    $result = $db->query("SELECT * FROM operation_report WHERE date BETWEEN '$from' AND '$to' ORDER BY date DESC");
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
    $getPdf = new PDF();
    $getPdf->SetMargins(10, 5, 10);
    $getPdf->AddPage('l');
    $getPdf->SetFont('Times', 'B', 13);

    // Table header
    $getPdf->SetFillColor(76, 175, 80);
    $getPdf->SetTextColor(255);
    $getPdf->Cell(40, 10, 'Driver', 1, 0, 'C', true);
    $getPdf->Cell(40, 10, 'Plate No', 1, 0, 'C', true);
    $getPdf->Cell(40, 10, 'From', 1, 0, 'C', true);
    $getPdf->Cell(40, 10, 'To', 1, 0, 'C', true);
    $getPdf->Cell(40, 10, 'Date', 1, 0, 'C', true);
    $getPdf->Cell(80, 10, 'Description', 1, 1, 'C', true);

    $getPdf->SetFont('Times', '', 13);
    $getPdf->SetTextColor(0);

    // Table rows
    while ($row = $result->fetch_assoc()) {
        $getPdf->Cell(40, 10, htmlspecialchars($row['driver']), 1);
        $getPdf->Cell(40, 10, htmlspecialchars($row['op_car']), 1);
        $getPdf->Cell(40, 10, htmlspecialchars($row['op_from']), 1);
        $getPdf->Cell(40, 10, htmlspecialchars($row['op_to']), 1);
        $getPdf->Cell(40, 10, htmlspecialchars($row['date']), 1);
        $getPdf->MultiCell(80, 10, htmlspecialchars($row['description']), 1);
    }

    ob_end_clean();
    // Output PDF
    $getPdf->Output();
    die();
}

// getDatesReport($from, $to);
