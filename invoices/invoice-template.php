<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Invoice</title>
  <style>
    body {
      font-family: sans-serif;
      margin: 0;
      padding: 10px;
      font-size: 12px;
      color: #333;
    }

    .container {
      max-width: 700px;
      margin: auto;
      border: 1px solid #ccc;
      padding: 15px;
    }

    .header,
    .footer {
      text-align: center;
      margin-bottom: 15px;
    }

    .header img {
      max-height: 40px;
      margin-bottom: 5px;
    }

    .section-title {
      font-weight: bold;
      font-size: 13px;
      margin-top: 15px;
      margin-bottom: 5px;
      border-bottom: 1px solid #ddd;
      padding-bottom: 3px;
    }

    .summary,
    .table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 8px;
    }

    .summary td {
      padding: 4px 3px;
    }

    .table th,
    .table td {
      border: 1px solid #ddd;
      padding: 4px 5px;
    }

    .table th {
      background: #f5f5f5;
    }

    .qr {
      text-align: center;
      margin-top: 15px;
    }

    .qr img {
      height: 80px;
    }

    .footer {
      font-size: 11px;
      color: #888;
      border-top: 1px solid #eee;
      padding-top: 5px;
      margin-top: 15px;
    }

    a {
      color: #555;
      text-decoration: none;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <img src="<?= $logoUrl ?>" alt="Logo">
      <div><strong>Villa Rosal</strong></div>
      <div><?= $businessAddress ?> · <?= $businessPhone ?></div>
      <div><?= $businessEmail ?></div>
    </div>

    <!-- Invoice Info -->
    <div class="section-title">Invoice</div>
    <table class="summary">
      <tr>
        <td><strong>Invoice #:</strong></td>
        <td><?= $invoiceNumber ?></td>
      </tr>
      <tr>
        <td><strong>Booking Code:</strong></td>
        <td><?= $bookingData['booking_code'] ?></td>
      </tr>
      <tr>
        <td><strong>Status:</strong></td>
        <td><?= $paymentData['status'] ?> via <?= strtoupper($paymentData['payment_method']) ?></td>
      </tr>
      <tr>
        <td><strong>Date Issued:</strong></td>
        <td><?= date('F j, Y g:i A', strtotime($paymentData['created_at'] ?? date('Y-m-d H:i:s'))) ?></td>
      </tr>
    </table>

    <!-- Customer Info -->
    <div class="section-title">Customer</div>
    <table class="summary">
      <tr>
        <td><strong>Name:</strong></td>
        <td><?= $customerName ?></td>
      </tr>
      <tr>
        <td><strong>Email:</strong></td>
        <td><?= $customerEmail ?></td>
      </tr>
      <tr>
        <td><strong>Contact:</strong></td>
        <td><?= $customerPhone ?></td>
      </tr>
    </table>

    <!-- Booking -->
    <div class="section-title">Booking</div>
    <table class="summary">
      <tr>
        <td><strong>Room:</strong></td>
        <td><?= $roomName ?? 'Room N/A' ?></td>
      </tr>
      <tr>
        <td><strong>Check-in:</strong></td>
        <td><?= $bookingData['check_in_date'] ?> @ <?= $bookingData['check_in_time'] ?></td>
      </tr>
      <tr>
        <td><strong>Check-out:</strong></td>
        <td><?= $bookingData['check_out_date'] ?? '—' ?></td>
      </tr>
      <tr>
        <td><strong>Guests:</strong></td>
        <td><?= $bookingData['adults'] ?> Adults, <?= $bookingData['children'] ?> Children</td>
      </tr>
    </table>

    <!-- Payment -->
    <div class="section-title">Payment Summary</div>
    <table class="table">
      <thead>
        <tr>
          <th>Description</th>
          <th>Qty</th>
          <th>Unit Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= $roomName ?></td>
          <td><?= $paymentData['qty'] ?? 1 ?></td>
          <td><?= number_format($paymentData['base_price'], 2) ?> pesos</td>
          <td><?= number_format($paymentData['base_price'] * ($paymentData['qty'] ?? 1), 2) ?> pesos</td>
        </tr>
        <tr>
          <td colspan="3" style="text-align:right;"><strong>Processing Fee</strong></td>
          <td><?= number_format($paymentData['fee'], 2) ?> pesos</td>
        </tr>
        <tr>
          <td colspan="3" style="text-align:right;"><strong>Total Amount</strong></td>
          <td><strong><?= number_format($paymentData['amount'], 2) ?> pesos</strong></td>
        </tr>
      </tbody>
    </table>

    <!-- QR -->
    <div class="qr">
      <p style="margin-bottom: 5px;">Scan to confirm booking</p>
      <img src="<?= $qrUrl ?>" alt="QR Code">
      <div style="font-size: 11px; color: #666; margin-top: 3px;">Xendit Invoice ID: <?= $paymentData['xendit_invoice_id'] ?? '—' ?></div>
    </div>

    <!-- Footer -->
    <div class="footer">
      Thank you for booking with Villa Rosal 🌿<br>
      Questions? Email <a href="mailto:<?= $businessEmail ?>"><?= $businessEmail ?></a>
    </div>
  </div>
</body>

</html>