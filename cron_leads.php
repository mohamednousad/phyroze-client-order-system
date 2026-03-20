<?php
error_reporting(E_ALL);
date_default_timezone_set("Asia/Colombo");

require_once __DIR__ . "/php-mailer/PHPMailer.php";
require_once __DIR__ . "/php-mailer/SMTP.php";
require_once __DIR__ . "/php-mailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db_host     = 'localhost';
$db_name     = 'phyroze';
$db_user     = 'root';
$db_pass     = '';
$owner_email = 'nousadnousad021@gmail.com';
$smtp_user   = 'nousadnousad021@gmail.com';
$smtp_pass   = 'oyrkewsefktnvdgh';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("DB connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare("
    SELECT * FROM leads
    WHERE created_at <= NOW() - INTERVAL 5 DAY
    AND score >= 30
    ORDER BY score DESC
    LIMIT 50
");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($leads)) {
    exit("No qualifying leads found.");
}

$hot  = array_filter($leads, fn($l) => $l['score'] >= 60);
$warm = array_filter($leads, fn($l) => $l['score'] >= 35 && $l['score'] < 60);
$cool = array_filter($leads, fn($l) => $l['score'] < 35);

function renderLeadRows($leads) {
    $html = '';
    foreach ($leads as $l) {
        $scoreColor = $l['score'] >= 60 ? '#16a34a' : ($l['score'] >= 35 ? '#d97706' : '#dc2626');
        $html .= "
        <tr style='border-bottom:1px solid #f3f4f6'>
          <td style='padding:10px 8px;font-weight:600'>" . htmlspecialchars($l['name']) . "</td>
          <td style='padding:10px 8px'><a href='mailto:{$l['email']}' style='color:#0284c7'>" . htmlspecialchars($l['email']) . "</a></td>
          <td style='padding:10px 8px'><a href='tel:{$l['phone']}' style='color:#0284c7'>" . htmlspecialchars($l['phone']) . "</a></td>
          <td style='padding:10px 8px'>" . htmlspecialchars($l['biz_type']) . "</td>
          <td style='padding:10px 8px'>" . htmlspecialchars($l['budget']) . "</td>
          <td style='padding:10px 8px'>" . htmlspecialchars($l['template_chosen']) . "</td>
          <td style='padding:10px 8px;text-align:center'><span style='background:{$scoreColor};color:#fff;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700'>{$l['score']}</span></td>
          <td style='padding:10px 8px;font-size:12px;color:#6b7280'>" . date('d M Y', strtotime($l['created_at'])) . "</td>
        </tr>";
    }
    return $html;
}

function renderSection($title, $color, $emoji, $leads) {
    if (empty($leads)) return '';
    $rows = renderLeadRows($leads);
    return "
    <div style='margin-bottom:32px'>
      <h3 style='font-size:16px;font-weight:700;color:{$color};margin-bottom:12px'>{$emoji} {$title} (" . count($leads) . ")</h3>
      <table style='width:100%;border-collapse:collapse;font-size:13px'>
        <thead>
          <tr style='background:#f8fafc;color:#6b7280;font-size:11px;letter-spacing:.5px'>
            <th style='padding:8px;text-align:left'>NAME</th>
            <th style='padding:8px;text-align:left'>EMAIL</th>
            <th style='padding:8px;text-align:left'>PHONE</th>
            <th style='padding:8px;text-align:left'>BUSINESS</th>
            <th style='padding:8px;text-align:left'>BUDGET</th>
            <th style='padding:8px;text-align:left'>TEMPLATE</th>
            <th style='padding:8px;text-align:center'>SCORE</th>
            <th style='padding:8px;text-align:left'>DATE</th>
          </tr>
        </thead>
        <tbody>$rows</tbody>
      </table>
    </div>";
}

$totalLeads = count($leads);
$avgScore   = round(array_sum(array_column($leads, 'score')) / $totalLeads);
$dateRange  = date('d M') . ' – ' . date('d M Y');

$body = "
<div style='font-family:sans-serif;max-width:700px;margin:0 auto;background:#f9fafb;padding:24px;border-radius:12px'>
  <div style='background:linear-gradient(135deg,#0284c7,#0ea5e9);color:#fff;padding:24px 28px;border-radius:10px;margin-bottom:24px'>
    <h1 style='margin:0;font-size:22px'>📊 5-Day Lead Report</h1>
    <p style='margin:6px 0 0;opacity:.9;font-size:13px'>Period: {$dateRange} · {$totalLeads} qualified leads · Avg score: {$avgScore}</p>
  </div>

  <div style='display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:24px'>
    <div style='background:#fff;border-radius:8px;padding:16px;text-align:center;border-top:3px solid #16a34a'>
      <p style='font-size:24px;font-weight:800;color:#16a34a;margin:0'>" . count($hot) . "</p>
      <p style='font-size:12px;color:#6b7280;margin-top:4px'>🔥 Hot Leads</p>
    </div>
    <div style='background:#fff;border-radius:8px;padding:16px;text-align:center;border-top:3px solid #d97706'>
      <p style='font-size:24px;font-weight:800;color:#d97706;margin:0'>" . count($warm) . "</p>
      <p style='font-size:12px;color:#6b7280;margin-top:4px'>🌡 Warm Leads</p>
    </div>
    <div style='background:#fff;border-radius:8px;padding:16px;text-align:center;border-top:3px solid #0284c7'>
      <p style='font-size:24px;font-weight:800;color:#0284c7;margin:0'>{$totalLeads}</p>
      <p style='font-size:12px;color:#6b7280;margin-top:4px'>📋 Total Qualified</p>
    </div>
  </div>

  <div style='background:#fff;padding:20px;border-radius:10px;margin-bottom:16px'>
    " . renderSection('Hot Leads (Score 60+)', '#16a34a', '🔥', $hot) . "
    " . renderSection('Warm Leads (Score 35–59)', '#d97706', '🌡', $warm) . "
    " . renderSection('Follow-up Leads (Score < 35)', '#6b7280', '📋', $cool) . "
  </div>

  <p style='text-align:center;font-size:12px;color:#9ca3af;margin-top:16px'>Generated automatically by cron · " . date('d M Y H:i') . " · Nousad Lead System</p>
</div>";

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = $smtp_user;
$mail->Password   = $smtp_pass;
$mail->SMTPSecure = 'tls';
$mail->Port       = 587;
$mail->setFrom($smtp_user, 'Nousad Lead System');
$mail->addAddress($owner_email);
$mail->isHTML(true);
$mail->Subject = "📊 5-Day Lead Report — {$totalLeads} leads · " . count($hot) . " HOT · " . date('d M Y');
$mail->Body    = $body;

try {
    $mail->send();
    echo "Lead report sent successfully. {$totalLeads} leads included.";
} catch (Exception $e) {
    echo "Mailer error: " . $mail->ErrorInfo;
}
