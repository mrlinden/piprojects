<?php
#error_reporting(E_ERROR | E_PARSE);

$cmd = $argv[1];
#$address = '127.0.0.1';
$address = '10.254.1.1';
$port = 50000;
$errorState = 0;
$result = 0;


function socket_connect_timeout($host, $port, $timeout=100){
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    /**
     * Set the send and receive timeouts super low so that socket_connect
     * will return to us quickly. We then loop and check the real timeout
     * and check the socket error to decide if its conected yet or not.
     */
    $connect_timeval = array(
        "sec"=>0,
        "usec" => 100
    );
    socket_set_option(
        $socket,
        SOL_SOCKET,
        SO_SNDTIMEO,
        $connect_timeval
    );
    socket_set_option(
        $socket,
        SOL_SOCKET,
        SO_RCVTIMEO,
        $connect_timeval
    );
    $now = microtime(true);
    /**
     * Loop calling socket_connect. As long as the error is 115 (in progress)
     * or 114 (already called) and our timeout has not been reached, keep
     * trying.
     */
    $err = null;
    $socket_connected = false;
    do{
        socket_clear_error($socket);
        $socket_connected = @socket_connect($socket, $host, $port);
        $err = socket_last_error($socket);
        $elapsed = (microtime(true) - $now) * 1000;
    }
    while (($err === 115 || $err === 114) && $elapsed < $timeout);
    /**
     * For some reason, socket_connect can return true even when it is
     * not connected. Make sure it returned true the last error is zero
     */
    $socket_connected = $socket_connected && $err === 0;
    if($socket_connected){
        /**
         * Set keep alive on so the other side does not drop us
         */
        socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);
        /**
         * set the real send/receive timeouts here now that we are connected
         */
        $timeval = array(
            "sec" => 0,
            "usec" => 0
        );
        if($timeout >= 1000){
            $ts_seconds = $timeout / 1000;
            $timeval["sec"] = floor($ts_seconds);
            $timeval["usec"] = ($ts_seconds - $timeval["sec"]) * 1000000;
        } else {
            $timeval["usec"] = $timeout * 1000;
        }
        socket_set_option(
            $socket,
            SOL_SOCKET,
            SO_SNDTIMEO,
            $timeval
        );
        socket_set_option(
            $socket,
            SOL_SOCKET,
            SO_RCVTIMEO,
            $timeval
        );
    } else {
        $elapsed = round($elapsed, 4);
        if(!is_null($err) && $err !== 0 && $err !== 114 && $err !== 115){
            $message = "Failed to connect to $host:$port. ($err: ".socket_strerror($err)."; after {$elapsed}ms)";
        } else {
            $message = "Failed to connect to $host:$port. (timed out after {$elapsed}ms)";
        }
        throw new Exception($message);
    }
    return $socket;
}


echo "Do run";

try{
    $sock = socket_connect_timeout($address, $port, 10);
    echo "Connected!!!";
} catch(Exception $e){
    echo $e->getMessage()."\n";
    $sock = null;
    $errorState = 100;
}

var_dump($sock);
echo "\n";



if ($sock === false) {
    $errorState = 1;
}

if ($errorState == 0) {
    echo "Do send cmd";
    socket_write($sock, $cmd, strlen($cmd));
    $buf = '';
    $nrTries = 10;
    while ($nrTries > 0)
    {
        while ($buf != "")
        {
            $buf = socket_read($sock, 2048, PHP_NORMAL_READ);
            echo "buf ". $buf . "\n";
            $result .= $buf;
        }
        sleep(1);
    }

    #if (false !== ($bytes = socket_recv($sock, $buf, 2048, MSG_WAITALL))) {
    #    $result = $buf;
    #    echo "Read $bytes bytes from socket_recv(). Closing socket...";
    #}
    socket_close($sock);
}
if ($errorState == 0) {
    echo "Ok " . $result;
} else {
    echo "Fail " . $errorState . ":" . socket_strerror(socket_last_error($sock));
}






?>
