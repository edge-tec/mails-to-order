<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;
use Exception;

class EmailService {

    public static function isNotificationEnabled(string $notificationKey): bool {
        try {
            $setting = Database::fetch("SELECT setting_value FROM settings WHERE setting_key = ?", ['notify_' . $notificationKey]);
            if ($setting) {
                return (bool) $setting['setting_value'];
            }
        } catch (Exception $e) {
            // Default enabled
        }
        return true;
    }

    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $templateKey = 'generic'): bool {
        if (!self::isNotificationEnabled($templateKey)) {
            return false;
        }

        $mail = new PHPMailer(true);
        $status = 'failed';
        $errorMsg = null;

        try {
            $mailConfig = config('mail');
            
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $mailConfig['host'];
            $mail->SMTPAuth   = !empty($mailConfig['username']);
            $mail->Username   = $mailConfig['username'];
            $mail->Password   = $mailConfig['password'];
            $mail->SMTPSecure = $mailConfig['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $mailConfig['port'];
            $mail->CharSet    = 'UTF-8';

            // Recipients
            $mail->setFrom($mailConfig['from']['address'], $mailConfig['from']['name']);
            $mail->addAddress($toEmail, $toName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = self::wrapInLayout($subject, $htmlBody);
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $htmlBody));

            $mail->send();
            $status = 'sent';
            self::logEmail($toEmail, $subject, $status, null);
            return true;

        } catch (MailException $e) {
            $errorMsg = $mail->ErrorInfo ?: $e->getMessage();
            self::logEmail($toEmail, $subject, 'failed', $errorMsg);
            error_log("Email sending failed to {$toEmail}: " . $errorMsg);
            return false;
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            self::logEmail($toEmail, $subject, 'failed', $errorMsg);
            error_log("Email sending failed to {$toEmail}: " . $errorMsg);
            return false;
        }
    }

    private static function wrapInLayout(string $title, string $content): string {
        $siteName = config('app.name', 'Server Provisioning Portal');
        $siteUrl  = config('app.url', 'http://localhost:8000');
        $year     = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7fb; margin: 0; padding: 0; color: #333333; }
        .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; color: #6366f1; }
        .header p { margin: 5px 0 0 0; font-size: 13px; color: #94a3b8; }
        .content { padding: 35px 30px; line-height: 1.6; font-size: 15px; color: #334155; }
        .btn { display: inline-block; padding: 12px 28px; background-color: #6366f1; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 20px; text-align: center; }
        .credentials-box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #6366f1; border-radius: 6px; padding: 20px; margin: 20px 0; font-family: monospace; font-size: 14px; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .footer a { color: #6366f1; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$siteName}</h1>
            <p>High Performance Server & Cloud Infrastructure</p>
        </div>
        <div class="content">
            {$content}
        </div>
        <div class="footer">
            <p>&copy; {$year} {$siteName}. All rights reserved.</p>
            <p><a href="{$siteUrl}">Visit Dashboard</a> | <a href="{$siteUrl}/contact">Support Center</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private static function logEmail(string $recipient, string $subject, string $status, ?string $error = null): void {
        try {
            Database::insert(
                "INSERT INTO email_logs (recipient, subject, status, error_message, sent_at) VALUES (?, ?, ?, ?, NOW())",
                [$recipient, $subject, $status, $error]
            );
        } catch (Exception $e) {
            // Ignore logging errors
        }
    }

    // Specific Notification Mail Helpers
    public static function sendOrderApprovedWithCredentials(array $user, array $order, array $server): bool {
        $subject = "Your Server Order Has Been Approved - {$order['order_number']}";
        $loginUrl = url('/login');

        $html = <<<HTML
<h2>Hello {$user['name']},</h2>
<p>Great news! Your server order <strong>{$order['order_number']}</strong> has been verified, provisioned, and activated.</p>

<p>Below are your server login credentials:</p>
<div class="credentials-box">
    <strong>Server IP/Host:</strong> {$server['host_ip']}<br>
    <strong>SSH Port:</strong> {$server['ssh_port']}<br>
    <strong>Username:</strong> {$server['username']}<br>
    <strong>Password:</strong> {$server['decrypted_password']}<br>
    <strong>Location:</strong> {$server['location']}<br>
    <strong>Expiration Date:</strong> {$server['expiration_date']}<br>
    <strong>Package:</strong> {$order['package_name']} ({$order['daily_pop']} POP/day)
</div>

<p>You can also log in to your dashboard at any time to view, copy credentials, or check your POP usage metrics.</p>
<p><a href="{$loginUrl}" class="btn">Access Customer Dashboard</a></p>

<p>If you have any questions or require assistance setting up your environment, please contact our support team.</p>
HTML;

        return self::send($user['email'], $user['name'], $subject, $html, 'order_approved');
    }

    public static function sendRegistrationWelcome(array $user): bool {
        $subject = "Welcome to " . config('app.name');
        $loginUrl = url('/login');

        $html = <<<HTML
<h2>Welcome, {$user['name']}!</h2>
<p>Thank you for registering an account with us. You can now browse server packages, place orders, and manage your cloud infrastructure from your user dashboard.</p>
<p><a href="{$loginUrl}" class="btn">Log In to Your Account</a></p>
HTML;

        return self::send($user['email'], $user['name'], $subject, $html, 'user_registration');
    }

    public static function sendOrderSubmitted(array $user, array $order): bool {
        $subject = "Server Order Received - {$order['order_number']}";
        $dashboardUrl = url('/dashboard');

        $html = <<<HTML
<h2>Order Received</h2>
<p>Hello {$user['name']},</p>
<p>We have received your order <strong>{$order['order_number']}</strong> for package <strong>{$order['package_name']}</strong>.</p>
<p>Your payment submission is currently under review by our administration team. Once verified, your server will be automatically provisioned.</p>
<p><a href="{$dashboardUrl}" class="btn">View Order Status</a></p>
HTML;

        return self::send($user['email'], $user['name'], $subject, $html, 'order_submitted');
    }

    public static function sendOrderRejected(array $user, array $order, string $reason): bool {
        $subject = "Update regarding Server Order - {$order['order_number']}";
        $supportUrl = url('/contact');

        $html = <<<HTML
<h2>Order Status Update</h2>
<p>Hello {$user['name']},</p>
<p>Unfortunately, your server order <strong>{$order['order_number']}</strong> could not be approved at this time.</p>
<p><strong>Reason:</strong> {$reason}</p>
<p>Please contact our support team or submit a new payment reference if there was an error.</p>
<p><a href="{$supportUrl}" class="btn">Contact Support</a></p>
HTML;

        return self::send($user['email'], $user['name'], $subject, $html, 'order_rejected');
    }
}
