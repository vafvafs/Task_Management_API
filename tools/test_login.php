<?php
$payload = json_encode(['email' => 'test@example.com', 'password' => 'password']);
$opts = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
        'content' => $payload,
    ],
];
$context = stream_context_create($opts);
$response = file_get_contents('http://127.0.0.1:8000/api/login', false, $context);
$status = null;
if (isset($http_response_header)) {
    foreach ($http_response_header as $h) {
        if (preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $h, $m)) {
            $status = intval($m[1]);
            break;
        }
    }
}
echo "Status: ".$status."\n";
echo "Response:\n".$response."\n";