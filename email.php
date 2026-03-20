<?php
session_start();
error_reporting(E_ALL);
date_default_timezone_set("Asia/Colombo");

require_once __DIR__ . "/php-mailer/PHPMailer.php";
require_once __DIR__ . "/php-mailer/SMTP.php";
require_once __DIR__ . "/php-mailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db_host = 'localhost';
$db_name = 'phyroze';
$db_user = 'root';
$db_pass = '';

$owner_email = 'nousadnousad021@gmail.com';
$smtp_user   = 'nousadnousad021@gmail.com';
$smtp_pass   = 'oyrkewsefktnvdgh';

function redirect($status) {
    header("Location: ./index.php?$status=1");
    exit;
}

function scoreLead($budget, $bizType, $audience) {
    $score = 0;
    if ($budget === 'enterprise') $score += 40;
    elseif ($budget === 'growth') $score += 25;
    else $score += 10;
    if (in_array($bizType, ['ecommerce', 'startup', 'realestate'])) $score += 30;
    elseif (in_array($bizType, ['service', 'health'])) $score += 20;
    else $score += 10;
    if ($audience === 'tech') $score += 15;
    else $score += 5;
    return $score;
}

function sendOwnerEmail($data, $smtpUser, $smtpPass, $ownerEmail) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom($smtpUser, 'Nousad Lead System');
    $mail->addAddress($ownerEmail);
    $mail->isHTML(true);
    $mail->Subject = "🔥 New Lead [{$data['budget']} | Score {$data['score']}] — {$data['name']}";

    $scoreColor = $data['score'] >= 60 ? '#16a34a' : ($data['score'] >= 35 ? '#d97706' : '#dc2626');
    $scoreLabel = $data['score'] >= 60 ? 'HOT' : ($data['score'] >= 35 ? 'WARM' : 'COLD');

    $mail->Body = "
    <div style='font-family:sans-serif;max-width:560px;margin:0 auto;background:#f9fafb;padding:24px;border-radius:12px'>
      <div style='background:#0284c7;color:#fff;padding:20px 24px;border-radius:8px 8px 0 0;text-align:center'>
        <h2 style='margin:0;font-size:20px'>🚀 New Lead Received</h2>
        <p style='margin:4px 0 0;opacity:.85;font-size:13px'>" . date('d M Y, H:i') . "</p>
      </div>
      <div style='background:#fff;padding:24px;border-radius:0 0 8px 8px'>
        <div style='text-align:center;margin-bottom:20px'>
          <span style='background:{$scoreColor};color:#fff;font-size:13px;font-weight:700;padding:4px 16px;border-radius:20px'>{$scoreLabel} LEAD — Score: {$data['score']}/100</span>
        </div>
        <table style='width:100%;border-collapse:collapse;font-size:14px'>
          <tr style='border-bottom:1px solid #f3f4f6'><td style='padding:10px 0;color:#6b7280;width:35%'>Name</td><td style='padding:10px 0;font-weight:600'>" . htmlspecialchars($data['name']) . "</td></tr>
          <tr style='border-bottom:1px solid #f3f4f6'><td style='padding:10px 0;color:#6b7280'>Email</td><td style='padding:10px 0;font-weight:600'><a href='mailto:{$data['email']}' style='color:#0284c7'>" . htmlspecialchars($data['email']) . "</a></td></tr>
          <tr style='border-bottom:1px solid #f3f4f6'><td style='padding:10px 0;color:#6b7280'>Phone</td><td style='padding:10px 0;font-weight:600'><a href='tel:{$data['phone']}' style='color:#0284c7'>" . htmlspecialchars($data['phone']) . "</a></td></tr>
          <tr style='border-bottom:1px solid #f3f4f6'><td style='padding:10px 0;color:#6b7280'>Business</td><td style='padding:10px 0;font-weight:600'>" . htmlspecialchars($data['biz_type']) . "</td></tr>
          <tr style='border-bottom:1px solid #f3f4f6'><td style='padding:10px 0;color:#6b7280'>Budget</td><td style='padding:10px 0;font-weight:600'>" . htmlspecialchars($data['budget']) . "</td></tr>
          <tr style='border-bottom:1px solid #f3f4f6'><td style='padding:10px 0;color:#6b7280'>Audience</td><td style='padding:10px 0;font-weight:600'>" . htmlspecialchars($data['audience']) . "</td></tr>
          <tr style='border-bottom:1px solid #f3f4f6'><td style='padding:10px 0;color:#6b7280'>Template</td><td style='padding:10px 0;font-weight:600'>" . htmlspecialchars($data['template_chosen']) . "</td></tr>
          <tr><td style='padding:10px 0;color:#6b7280'>Via</td><td style='padding:10px 0;font-weight:600'>" . htmlspecialchars($data['platform']) . "</td></tr>
        </table>
        <div style='margin-top:20px;display:flex;gap:12px;flex-wrap:wrap'>
          <a href='mailto:" . htmlspecialchars($data['email']) . "' style='background:#0284c7;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;font-size:13px'>Reply via Email</a>
          <a href='https://wa.me/" . preg_replace('/[^0-9]/', '', $data['phone']) . "' style='background:#25D366;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;font-size:13px'>WhatsApp</a>
        </div>
      </div>
    </div>";

    $mail->send();
}

function sendClientConfirmation($data, $smtpUser, $smtpPass) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom($smtpUser, 'Nousad Digital');
    $mail->addAddress($data['email'], $data['name']);
    $mail->isHTML(true);
    $mail->Subject = "We received your enquiry — Nousad Digital";
    $mail->Body    = "
    <div style='font-family:sans-serif;max-width:520px;margin:0 auto;background:#f0f9ff;padding:24px;border-radius:12px'>
      <div style='background:#0284c7;color:#fff;padding:20px 24px;border-radius:8px;text-align:center;margin-bottom:20px'>
        <h2 style='margin:0;font-size:20px'>Thank you, " . htmlspecialchars($data['name']) . "! 🎉</h2>
      </div>
      <p style='font-size:14px;color:#374151;line-height:1.7'>We've received your request for a <strong>" . htmlspecialchars($data['biz_type']) . "</strong> website. Our team will review your details and get back to you within <strong>5 - 6 days</strong>.</p>
      <div style='background:#fff;border-radius:8px;padding:16px;margin:16px 0;border-left:4px solid #0284c7'>
        <p style='margin:0;font-size:13px;color:#6b7280'>Selected template: <strong style='color:#0284c7'>" . htmlspecialchars($data['template_chosen']) . "</strong></p>
      </div>
      <p style='font-size:13px;color:#6b7280;text-align:center;margin-top:20px'>— Nousad Digital Team</p>
    </div>";
    $mail->send();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('error');
}

$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$phone    = trim($_POST['phone']    ?? '');
$biz_type = trim($_POST['biz_type'] ?? '');
$budget   = trim($_POST['budget']   ?? '');
$audience = trim($_POST['audience'] ?? '');
$template = trim($_POST['template_chosen'] ?? 'Not selected');
$platform = trim($_POST['platform'] ?? '');

if (empty($email) || empty($phone)) {
    redirect('error');
}

$name  = $name  ?: 'Not provided';
$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
if (!$email) redirect('error');

$score = scoreLead($budget, $biz_type, $audience);

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("INSERT INTO leads (name, email, phone, biz_type, budget, audience, template_chosen, platform, score) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$name, $email, $phone, $biz_type, $budget, $audience, $template, $platform, $score]);
} catch (PDOException $e) {
}

$data = compact('name','email','phone','biz_type','budget','audience','template_chosen','platform','score');
$data['template_chosen'] = $template;

try {
    sendOwnerEmail($data, $smtp_user, $smtp_pass, $owner_email);
} catch (Exception $e) {
}

try {
    sendClientConfirmation($data, $smtp_user, $smtp_pass);
} catch (Exception $e) {
}

redirect('success');