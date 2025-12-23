<?php
/* member/card.php  —  ultra-modern membership card PDF */
include '../config/db.php';
if (!isset($_SESSION['member_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


require '../tcpdf/tcpdf.php';

/* ---- fetch member ---- */
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $_SESSION['member_id']);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();

/* ---- card dimensions (credit-card size: 85.60 × 53.98 mm) ---- */
$pdf = new TCPDF('L', 'mm', [85.60, 53.98], true, 'UTF-8', false);
$pdf->SetCreator('Membership System');
$pdf->SetAuthor('Your Organization');
$pdf->SetTitle('Membership Card');
$pdf->SetSubject('Digital Membership Card');
$pdf->SetKeywords('membership, card, qr, digital');

/* ---- remove default header/footer ---- */
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

/* ---- background gradient ---- */
$pdf->LinearGradient(0, 0, 85.60, 53.98,
    ['r' => 15, 'g' => 23, 'b' => 42],
    ['r' => 30, 'g' => 41, 'b' => 59],
    coords: [0, 0, 1, 1]
);

/* ---- top accent bar ---- */
$pdf->SetFillColor(99, 102, 241); // #6366f1
$pdf->Rect(0, 0, 85.60, 8, 'F');

/* ---- logo / brand ---- */
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Text(5, 5.5, 'MEMBER');

/* ---- member photo placeholder (circle) ---- */
$pdf->SetFillColor(255, 255, 255, 0.1);
$pdf->Circle(72, 15, 7, 0, 360, 'F');

/* ---- QR code ---- */
$qrData = "https://{$_SERVER['HTTP_HOST']}/verify.php?no=" . urlencode($m['membership_number']);
$style = [
    'border'        => 0,
    'vpadding'      => 0,
    'hpadding'      => 0,
    'fgcolor'       => [0, 0, 0],
    'bgcolor'       => [255, 255, 255],
    'module_width'  => 1,
    'module_height' => 1
];
$pdf->write2DBarcode($qrData, 'QRCODE,H', 58, 28, 20, 20, $style);

/* ---- member details ---- */
$pdf->SetTextColor(226, 232, 240); // #e2e8f0
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Text(5, 18, strtoupper($m['first_name'] . ' ' . $m['last_name']));

$pdf->SetFont('helvetica', '', 8);
$pdf->Text(5, 24, 'Membership No:');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Text(5, 28, $m['membership_number']);

$pdf->SetFont('helvetica', '', 8);
$pdf->Text(5, 34, 'Type:');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Text(5, 38, $m['membership_type']);

/* ---- validity ---- */
$pdf->SetFont('helvetica', '', 7);
$pdf->SetTextColor(148, 163, 184); // #94a3b8
$pdf->Text(5, 48, 'Valid thru: ' . date('m/y', strtotime('+1 year')));

/* ---- subtle watermark ---- */
$pdf->SetTextColor(255, 255, 255, 0.05);
$pdf->SetFont('helvetica', 'B', 40);
$pdf->Text(25, 35, 'MEMBER', false, false, true, false, '', 0, '', 0, false, 'C');

/* ---- output ---- */
$pdf->Output("membership_card_{$m['membership_number']}.pdf", "I");