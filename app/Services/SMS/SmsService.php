<?php
namespace App\Services\SMS;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
 
use Twilio\Rest\Client;

class SmsService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }
    public function send($phone, $message)
    {

            if (!str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }
            
            $this->client->messages->create(
                $phone,
                array(
                    'from' => env('TWILIO_FROM'),
                    'body' => $message,
                )
            );
    }
}





?>