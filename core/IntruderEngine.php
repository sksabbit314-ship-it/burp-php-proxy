<?php
class IntruderEngine {
    public static function bruteForce($baseRequest, $param, $from = 0, $to = 9999) {
        $results = [];
        for ($i = $from; $i <= $to; $i++) {
            $otp = str_pad($i, 4, '0', STR_PAD_LEFT);
            $modified = str_replace($param, $otp, $baseRequest['body']);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseRequest['path']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $modified);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            $results[] = ['otp' => $otp, 'response' => substr($response, 0, 200)];
        }
        return $results;
    }
}
?>
