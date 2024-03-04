<?php

namespace App\CustomClass;

use Doctrine\ORM\Query\Expr\From;
use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mail
{
  private $api_key = '8c84465c9abc886f3931df53f29f4c42';
  private $api_key_secret = '4d83ba8c01bd6a8c05d65e19e009d471';

  public function send($to_email, $to_name, $subject, $content)
  {

    $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
    $body = [
      'Messages' => [
        [
          'From' => [
            'Email' => "bienechristy1994@gmail.com",
            'Name' => "christ"
          ],
          'To' => [
            [
              'Email' => $to_email,
              'Name' => $to_name
            ]
          ],
          'TemplateID' => 5754102,
          'TemplateLanguage' => true,
          'Subject' => $subject,
          'Variables' => [
            "content" => $content
          ]
        ]
      ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();
  }
}
