<?php

class RedisClient {

    private $socket = null;
    
    private string $host;
    
    private int $port;
    
    private float $timeout;

    public function __construct(string $host = '127.0.0.1', int $port = 6379, float $timeout = 2.0) {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    private function connect() {
        if ($this->socket !== null) {
            return;
        }

        $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (!$this->socket) {
            throw new Exception("Không thể kết nối tới máy chủ Redis tại {$this->host}:{$this->port}. Lỗi: {$errstr} ({$errno})");
        }
    }

    public function execute(array $args) {
        $this->connect();

        $cmd = '*' . count($args) . "\r\n";
        foreach ($args as $arg) {
            $cmd .= '$' . strlen($arg) . "\r\n" . $arg . "\r\n";
        }

        fwrite($this->socket, $cmd);

        return $this->readResponse();
    }

    private function readResponse() {
        $line = fgets($this->socket);
        if ($line === false) {
            throw new Exception("Mất kết nối tới máy chủ Redis hoặc hết thời gian chờ đọc socket.");
        }

        $type = $line[0];
        $value = substr($line, 1, -2);

        switch ($type) {
            case '+':
                return $value;
            case '-':
                throw new Exception("Lỗi Redis: " . $value);
            case ':':
                return (int)$value;
            case '$':
                $length = (int)$value;
                if ($length === -1) {
                    return null;
                }
                
                $data = '';
                $remaining = $length;
                while ($remaining > 0) {
                    $chunk = fread($this->socket, min($remaining, 8192));
                    if ($chunk === false || $chunk === '') {
                        throw new Exception("Lỗi khi đọc dữ liệu bulk từ socket Redis.");
                    }
                    $data .= $chunk;
                    $remaining -= strlen($chunk);
                }
                fread($this->socket, 2);
                return $data;
            case '*':
                $count = (int)$value;
                if ($count === -1) {
                    return null;
                }
                $results = [];
                for ($i = 0; $i < $count; $i++) {
                    $results[] = $this->readResponse();
                }
                return $results;
            default:
                throw new Exception("Kiểu RESP Redis không xác định: " . $type);
        }
    }

    public function get(string $key) {
        return $this->execute(['GET', $key]);
    }

    public function set(string $key, string $value) {
        return $this->execute(['SET', $key, $value]);
    }

    public function setex(string $key, int $seconds, string $value) {
        return $this->execute(['SETEX', $key, (string)$seconds, $value]);
    }

    public function del(string $key) {
        return $this->execute(['DEL', $key]);
    }

    public function __destruct() {
        if ($this->socket !== null) {
            fclose($this->socket);
            $this->socket = null;
        }
    }

}
?>
