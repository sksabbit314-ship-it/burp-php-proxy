<?php
class ProxyServer {
    private $port = 8080;
    private $socket;

    public function start() {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->socket, '0.0.0.0', $this->port);
        socket_listen($this->socket);

        while (true) {
            $client = @socket_accept($this->socket);
            if ($client) {
                $request = socket_read($client, 8192);
                $this->logRequest($request);
                socket_close($client);
            }
        }
    }

    private function logRequest($request) {
        $log = json_decode(@file_get_contents('storage/requests.json'), true) ?? [];
        $log[] = ['time' => date('Y-m-d H:i:s'), 'raw' => $request];
        if (count($log) > 200) array_shift($log);
        file_put_contents('storage/requests.json', json_encode($log, JSON_PRETTY_PRINT));
    }
}
?>
