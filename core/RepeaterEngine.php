<?php
class RepeaterEngine {
    public static function send($requestData) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestData['path']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestData['method']);
        
        $headers = [];
        foreach ($requestData['headers'] as $key => $val) {
            $headers[] = "$key: $val";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData['body'] ?? '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
?>
