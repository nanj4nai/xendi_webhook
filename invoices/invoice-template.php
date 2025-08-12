<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
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
      <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" />
      <div><strong>Villa Rosal</strong></div>
      <div><?= htmlspecialchars($businessAddress) ?> · <?= htmlspecialchars($businessPhone) ?></div>
      <div><?= htmlspecialchars($businessEmail) ?></div>
    </div>

    <!-- Invoice Info -->
    <div class="section-title">Invoice</div>
    <table class="summary">
      <tr>
        <td><strong>Invoice #:</strong></td>
        <td><?= htmlspecialchars($paymentData['xendit_invoice_id'] ?? '—') ?></td>
      </tr>
      <tr>
        <td><strong>Booking Code:</strong></td>
        <td><?= htmlspecialchars($bookingData['booking_code'] ?? '—') ?></td>
      </tr>
      <tr>
        <td><strong>Status:</strong></td>
        <td><?= htmlspecialchars($paymentData['status'] ?? 'pending') ?> via <?= strtoupper(htmlspecialchars($paymentData['payment_method'] ?? 'unknown')) ?></td>
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
        <td><?= htmlspecialchars($customerName ?? 'Guest') ?></td>
      </tr>
      <tr>
        <td><strong>Email:</strong></td>
        <td><?= htmlspecialchars($customerEmail ?? '—') ?></td>
      </tr>
      <tr>
        <td><strong>Contact:</strong></td>
        <td><?= htmlspecialchars($customerPhone ?? '—') ?></td>
      </tr>
    </table>

    <!-- Booking -->
    <div class="section-title">Booking</div>
    <table class="summary">
      <tr>
        <td><strong>Room:</strong></td>
        <td><?= htmlspecialchars($roomName ?? 'Room N/A') ?></td>
      </tr>
      <tr>
        <td><strong>Check-in:</strong></td>
        <td><?= htmlspecialchars($checkInFormatted ?? '—') ?> @ <?= htmlspecialchars($checkInTime ?? '—') ?></td>
      </tr>
      <tr>
        <td><strong>Check-out:</strong></td>
        <td><?= htmlspecialchars($checkOutFormatted ?? '—') ?> @ <?= htmlspecialchars($checkOutTime ?? '—') ?></td>
      </tr>
      <tr>
        <td><strong>Guests:</strong></td>
        <td><?= (int)($bookingData['adults'] ?? 0) ?> Adults, <?= (int)($bookingData['children'] ?? 0) ?> Children</td>
      </tr>
    </table>

    <!-- Payment -->
    <div class="section-title">Payment Summary</div>
    <table class="table">
      <thead>
        <tr>
          <th>Description</th>
          <th>Qty</th>
          <th>Nights</th>
          <th>Unit Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= htmlspecialchars($roomName ?? 'Room') ?></td>
          <td><?= (int)($qty ?? 1) ?></td>
          <td><?= (int)($nights ?? 1) ?></td>
          <td><?= number_format((float)($basePrice ?? 0), 2) ?> pesos</td>
          <td><?= number_format((float)($subtotal ?? 0), 2) ?> pesos</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align:right;"><strong>Processing Fee (12%)</strong></td>
          <td><?= number_format((float)($processingFee ?? 0), 2) ?> pesos</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align:right;"><strong>Total Amount</strong></td>
          <td><strong><?= number_format((float)($totalAmount ?? 0), 2) ?> pesos</strong></td>
        </tr>
      </tbody>
    </table>

    <!-- QR -->
    <div class="qr">
      <p style="margin-bottom: 5px;">Scan to confirm booking</p>
      <img src="<?= htmlspecialchars($qrUrl ?? '') ?>" alt="QR Code" />
      <div style="font-size: 11px; color: #666; margin-top: 3px;">Xendit Invoice ID: <?= htmlspecialchars($paymentData['xendit_invoice_id'] ?? '—') ?></div>
    </div>

    <!-- Footer -->
    <div class="footer">
      Thank you for booking with Villa Rosal 🌿<br />
      Questions? Email <a href="mailto:<?= htmlspecialchars($businessEmail ?? '') ?>"><?= htmlspecialchars($businessEmail ?? '') ?></a>
    </div>
  </div>
</body>

</html>
