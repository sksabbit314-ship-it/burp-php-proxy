<?php
class HttpParser {
    public static function parse($raw) {
        $lines = explode("\r\n", $raw);
        $requestLine = explode(" ", $lines[0] ?? 'GET / HTTP/1.1');
        $headers = [];
        $body = '';
        $inBody = false;

        foreach ($lines as $line) {
            if ($inBody) { $body .= $line; continue; }
            if (trim($line) == '') { $inBody = true; continue; }
            if (strpos($line, ': ') !== false) {
                list($key, $val) = explode(': ', $line, 2);
                $headers[$key] = $val;
            }
        }

        return [
            'method' => $requestLine[0] ?? 'GET',
            'path' => $requestLine[1] ?? '/',
            'headers' => $headers,
            'body' => $body
        ];
    }
}
?>
