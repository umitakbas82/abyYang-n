<?php
// Hataları görelim (Canlıda kapatabilirsin)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// --- PHPMailer ---
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Hatalı istek."]);
    exit;
}

// --- FORM VERİLERİ (GÜVENLİK FİRMASI İÇİN DÜZENLENDİ) ---
$ad_soyad = strip_tags(trim($_POST["ad_soyad"] ?? '')); // HTML name="ad_soyad"
$telefon  = strip_tags(trim($_POST["telefon"] ?? ''));  // HTML name="telefon"
$email    = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL); // HTML name="email"
$mesaj    = strip_tags(trim($_POST["mesaj"] ?? ''));    // HTML name="mesaj"

// Zorunlu alan kontrolü
if (empty($ad_soyad) || empty($email) || empty($telefon)) {
    echo json_encode(["status" => "error", "message" => "Lütfen İsim, Telefon ve E-posta alanlarını doldurun."]);
    exit;
}

$mail = new PHPMailer(true);

try {
    // --- SMTP AYARLARI (BURALARI ABY GÜVENLİK MAİLİNE GÖRE DOLDUR) ---
    $mail->isSMTP();
    $mail->Host       = 'mail.abysecurity.com'; // ÖRNEK: Hosting mail sunucun
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@abyyangin.com'; // ÖRNEK: Gönderen mail
    $mail->Password   = 'mail_sifresi_buraya';  // ÖRNEK: Mail şifresi
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    // --- KİMDEN ---
    $mail->setFrom('info@abysecurity.com', 'ABY Güvenlik Web');

    // --- KİME GİDECEK? ---
    $mail->addAddress('info@abysecurity.com'); // Kendine gönder
    // $mail->addAddress('baska_mail@gmail.com'); // İstersen yedeği buraya gönder

    // --- CEVAPLA DİYİNCE MÜŞTERİ ÇIKSIN ---
    $mail->addReplyTo($email, $ad_soyad);

    // --- MAİL İÇERİĞİ ---
    $mail->isHTML(true);
    $mail->Subject = "Yeni Talep: $ad_soyad (Web Sitesi)";

    $icerik  = "<h3 style='color:#D4AF37;'>ABY Güvenlik - Yeni İletişim Formu</h3>";
    $icerik .= "<p>Web sitesinden yeni bir mesaj aldınız.</p>";
    
    // Tablo görünümü
    $icerik .= "<table cellpadding='10' cellspacing='0' border='1' style='border-color:#eee; width:100%; max-width:600px;'>";
    $icerik .= "<tr><td style='background:#f9f9f9;'><strong>Ad Soyad:</strong></td><td>{$ad_soyad}</td></tr>";
    $icerik .= "<tr><td style='background:#f9f9f9;'><strong>Telefon:</strong></td><td><a href='tel:{$telefon}'>{$telefon}</a></td></tr>";
    $icerik .= "<tr><td style='background:#f9f9f9;'><strong>E-Posta:</strong></td><td>{$email}</td></tr>";
    $icerik .= "</table>";

    if (!empty($mesaj)) {
        $icerik .= "<div style='margin-top:20px; padding:15px; background:#f1f1f1; border-left:4px solid #D4AF37;'>";
        $icerik .= "<strong>Mesaj:</strong><br>" . nl2br($mesaj);
        $icerik .= "</div>";
    }

    $mail->Body = $icerik;

    // --- GÖNDER ---
    $mail->send();

    echo json_encode(["status" => "success", "message" => "Talebiniz alınmıştır, en kısa sürede dönüş yapacağız."]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mail hatası: " . $mail->ErrorInfo]);
}
?>