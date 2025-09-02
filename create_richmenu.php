<?php
$accessToken = "hvkQKkszJlIi9i2oltSW/mwbtjnMWrFFz2GedFvSpnHpCGESTS1iy6voqFzpcfMfJOtdGSSw5VLkddjMi8JkUdqxlPFW5RxCHY98vtatbdloJl4nziEiTavp4xb4rAB1byg0iF/l3fFnSQXEmUNosgdB04t89/1O/w1cDnyilFU=";

$data = [
    "size" => ["width" => 2500, "height" => 843],
    "selected" => true,
    "name" => "richmenu-search",
    "chatBarText" => "เมนูหลัก",
    "areas" => [
        [
            "bounds" => ["x" => 0, "y" => 0, "width" => 2500, "height" => 843],
            "action" => [
                "type" => "postback",
                "data" => "search_nearby_cafe"
            ]
        ]
    ]
];

$ch = curl_init("https://api.line.me/v2/bot/richmenu");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $accessToken
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$result = curl_exec($ch);
curl_close($ch);

echo $result;
