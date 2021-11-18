<?php

function sendNotification(array $tokens, String $titulo, String $body) {
    $SERVER_API_KEY = 'AAAAYu3fFhE:APA91bESm3NEUerhW-CuXsIEbIiAQot7xTBRCeOakCvjevgwm1fEPlcTocXARJlbJtvow5qTrFKemuuG9VBdyK450jR3EM1YwVaFLgBBQCWZNxQ8tQ1l_bRPLjdZSVT-lQpkGukDjSud';
    
    $data = [
        'registration_ids' => $tokens,
        'notification' => [
            'title' => $titulo,
            'body' => $body,
            'sound' => 'default'
        ]
    ];
    $dataString = json_encode($data);

    $headers = [
        'Authorization: key='.$SERVER_API_KEY,
        'Content-type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    $response = curl_exec($ch);
    return $response;
}