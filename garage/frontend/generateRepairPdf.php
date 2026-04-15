<?php
if (isset($_GET['action']) && $_GET['action'] === 'generate_pdf') {
    require_once('../../fpdf/fpdf.php');
    require_once('../../connection.php'); // <- Make sure your DB connection is here

    class PDF extends FPDF
    {
        function Header()
        {
            $this->SetFont('Arial', 'B', 16);
            $this->SetTextColor(77, 124, 15);
            $this->Cell(0, 10, 'Repair Requests Report', 0, 1, 'C');
            $this->SetTextColor(0, 0, 0);
            $this->Ln(5);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s') . ' - Page ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    // ✅ Fetch real data
    $requests = [];
    $query = "SELECT r.service, r.createdAt, s.stf_names AS user
              FROM request_repair r
              JOIN staff s ON r.user_id = s.stf_code
              ORDER BY r.createdAt DESC";

    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = [
                'user' => $row['user'],
                'service' => $row['service'],
                'createdAt' => $row['createdAt']
            ];
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(77, 124, 15);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(15, 10, '#', 1, 0, 'C', true);
    $pdf->Cell(55, 10, 'Requested By', 1, 0, 'C', true);
    $pdf->Cell(80, 10, 'Service', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Date', 1, 1, 'C', true);

    // Table content
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $fill = false;

    foreach ($requests as $index => $request) {
        $pdf->SetFillColor(240, 253, 244);
        $pdf->Cell(15, 8, $index + 1, 1, 0, 'C', $fill);
        $pdf->Cell(55, 8, $request['user'], 1, 0, 'L', $fill);
        $pdf->Cell(80, 8, $request['service'], 1, 0, 'L', $fill);
        $pdf->Cell(40, 8, date('Y-m-d H:i', strtotime($request['createdAt'])), 1, 1, 'C', $fill);
        $fill = !$fill;
    }

    $pdf->Output();
    exit;
}
