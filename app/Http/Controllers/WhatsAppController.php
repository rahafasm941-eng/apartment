<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function sendMessage()
    {
        $params = [
            'token' => env('ULTRAMSG_TOKEN'),
            'to'    => '+963959103439',
            'body'  => 'WhatsApp API on UltraMsg.com works good'
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.ultramsg.com/instance155393/messages/chat",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => ["content-type: application/x-www-form-urlencoded"],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        return $err ? "cURL Error #: $err" : $response;
    }
}