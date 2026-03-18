<?php

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

$SUPABASE_URL = "https://unqfceucutbdfkurllsa.supabase.co";
$SUPABASE_API_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVucWZjZXVjdXRiZGZrdXJsbHNhIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3MjQ4ODc5MywiZXhwIjoyMDg4MDY0NzkzfQ.ac4jq9STmyLHtRQ0xNO3HPIyNrfD3OtGdTXxI39VmII";

$url = $SUPABASE_URL . "/auth/v1/token?grant_type=password";

$payload = json_encode([
    "email" => $email,
    "password" => $password
]);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "apikey: $SUPABASE_API_KEY"
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);

curl_close($ch);

echo $response;