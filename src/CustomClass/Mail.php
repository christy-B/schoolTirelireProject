<?php

namespace App\CustomClass;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Mail
{
  private $parameter; 

  public function __construct(ParameterBagInterface $parameter) 
  { 
    $this->parameter = $parameter; 
  }

  public function send($to_email, $to_name, $subject, $content)
  {
    $api_key = $this->parameter->get('api_key');
    $api_key_secret = $this->parameter->get('api_secret'); 

    $mj = new Client($api_key, $api_key_secret, true, ['version' => 'v3.1']);
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
