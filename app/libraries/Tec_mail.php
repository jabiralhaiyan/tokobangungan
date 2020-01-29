<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
*  ==============================================================================
*  Author   : Mian Saleem
*  Email    : saleem@tecdiary.com
*  Web      : http://tecdiary.com
*  ==============================================================================
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Tec_mail
{

    public function __construct() {
    }

    public function __get($var) {
        return get_instance()->$var;
    }

    public function send_mail($to, $subject, $body, $from = NULL, $from_name = NULL, $attachment = NULL, $cc = NULL, $bcc = NULL) {

        $Settings = $this->site->getSettings();
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        try {
            if ($Settings->protocol == 'mail') {
                $mail->isMail();
            } elseif ($Settings->protocol == 'sendmail') {
                $mail->isSendmail();
            } elseif ($Settings->protocol == 'smtp') {
                $mail->isSMTP();
                $mail->Host = $Settings->smtp_host;
                $mail->SMTPAuth = true;
                $mail->Username = $Settings->smtp_user;
                $mail->Password = $Settings->smtp_pass;
                $mail->SMTPSecure = !empty($Settings->smtp_crypto) ? $Settings->smtp_crypto : false;
                $mail->Port = $Settings->smtp_port;
                // $mail->SMTPDebug = 2;
            } else {
                $mail->isMail();
            }

            if ($from && $from_name) {
                $mail->setFrom($from, $from_name);
                $mail->addReplyTo($from, $from_name);
            } elseif ($from) {
                $mail->setFrom($from, $Settings->site_name);
                $mail->addReplyTo($from, $Settings->site_name);
            } else {
                $mail->setFrom($Settings->default_email, $Settings->site_name);
                $mail->addReplyTo($Settings->default_email, $Settings->site_name);
            }

            $mail->addAddress($to);
            if ($cc) { $mail->addCC($cc); }
            if ($bcc) { $mail->addBCC($bcc); }
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $body;
            if ($attachment) {
                if (is_array($attachment)) {
                    foreach ($attachment as $attach) {
                        $mail->addAttachment($attach);
                    }
                } else {
                    $mail->addAttachment($attachment);
                }
            }

            if (!$mail->send()) {
                log_message('error', $mail->ErrorInfo);
                throw new \Exception($mail->ErrorInfo);
                return FALSE;
            }
            return TRUE;
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            throw new \Exception($e->errorMessage());
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
