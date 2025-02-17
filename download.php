<?php

session_start();
if (!isset($_SESSION["role"]) || strtoupper($_SESSION['role']) != 'LOGISTICS' && strtoupper($_SESSION['role']) != 'D/CEO' && strtoupper($_SESSION['role']) != 'CEO') {
    header('Location: login.php');
    exit();
}

// INCLUDING CONNECTION
include 'connection.php';

if (isset($_GET['pdf_id'])) {
    require('fpdf/fpdf.php');

    // SELECTING DATA FROM DATABASE
    $sql = 'SELECT * FROM `quick_action` WHERE action_id = ?';
    $statement = $db->prepare($sql);
    $statement->bind_param('i', $_GET['pdf_id']);
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
    $quickPdf->Cell(0, 10, 'Quick Fuel Request Report', 0, 1, 'C');
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

    addDataRow($quickPdf, 'Mission Header:', $data['head_mission']);
    addDataRow($quickPdf, 'Driver:', $data['driver']);
    addDataRow($quickPdf, 'Plate Number:', $data['plate_no']);
    addDataRow($quickPdf, 'Liters of Fuel:', $data['fuel'] . 'L');
    addDataRow($quickPdf, 'Total Price:', $data['price'] . ' RWF');
    addDataRow($quickPdf, 'From:', $data['origin']);
    addDataRow($quickPdf, 'To:', $data['destination']);
    addDataRow($quickPdf, 'Description:', $data['description'], true);

    $quickPdf->Ln(20);
    $quickPdf->Cell(95, 10, 'Issued by: ', 0, 0, 'L');
    $quickPdf->Cell(95, 10, 'Informed: ', 0, 1, 'L');

    $quickPdf->Cell(95, 10, $data['prepared_by'], 0, 0, 'L');
    $quickPdf->Cell(95, 10, 'Lt Col. Alexandre KARASIRA', 0, 1, 'L');

    $quickPdf->Cell(95, 10, 'H/LOGISTIC APTC', 0, 0, 'L');
    $quickPdf->Cell(95, 10, 'D/CEO & DAF APTC', 0, 1, 'L');

    $quickPdf->Output();
}
