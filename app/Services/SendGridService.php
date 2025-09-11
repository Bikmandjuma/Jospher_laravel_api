<?php

namespace App\Services;

use SendGrid\Mail\Mail;

class SendGridService
{
    public static function sendEmail($to, $name, $subject, $body)
    {
        $email = new Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject($subject);
        $email->addTo($to, $name);
        $email->addContent("text/html", $body);

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));

        try {
            $response = $sendgrid->send($email);
            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
