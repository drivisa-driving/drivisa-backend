<?php

namespace Modules\Core\Services;

class SendGridMailable
{

    /**
     * @throws \SendGrid\Mail\TypeException
     */
    public function sendMail(
        $templateId,
        $emailAddress,
        $userName,
        $subject,
        array $data
    )
    {

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), 'Drivisa');
        $email->addTo(trim($emailAddress), $userName);
        $email->setSubject($subject . " - Drivisa");
        $email->setTemplateId(
            new \SendGrid\Mail\TemplateId($templateId)
        );

        $templateData = [
            'sender_email' => env('MAIL_FROM_ADDRESS')
        ];

        $templateData += $data;

        // === Here comes the dynamic template data! ===
        $email->addDynamicTemplateDatas($templateData);

        $sendgrid = new \SendGrid(env('MAIL_PASSWORD'));
        return $sendgrid->send($email);
    }
}
