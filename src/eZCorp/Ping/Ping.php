<?php

namespace eZCorp\Ping\Ping;

class Ping
{

    private $_services = array();

    /**
     * @param string $service
     * @param string $ip
     * @param string $port
     * @param string $timeout
     */
    public function add(string $service, string $ip, string $port, string $timeout)
    {
        if (empty($ip)) {
            $ip = "localhost";
        }
        $this->_services[$service] = array("port" => $port, "service" => $service, "ip" => $ip, "timeout" => $timeout);
    }

    /**
     * @param string $service
     * @param string $ip
     * @param string $port
     * @param string $timeout
     * @return array
     */
    public function execute(string $service = "", string $ip = "", string $port = "", string $timeout = "")
    {
        $answer = array();

        if (empty($service)) {
            $services = $this->_services;
        } else {
            $services = array($service => array("port" => $port, "service" => $service, "ip" => $ip, "timeout" => $timeout));
        }

        foreach ($services as $service) {
            $status = "Offline";
            $time = 'timeout';

            $starttime = microtime(true);
            $fp = @fsockopen($service['ip'], $service['port'], $errno, $errstr, $service['timeout']);
            $stoptime = microtime(true);

            if ($fp) {
                $time = strval(floor(($stoptime - $starttime) * 1000)) . " ms";
                $status = "Online";
                fclose($fp);
            }
            $answer[$service['service']] = array("port" => $service['port'], "service" => $service['service'], "ip" => $service['ip'], "status" => $status, "response" => $time);
        }
        return $answer;
    }
}