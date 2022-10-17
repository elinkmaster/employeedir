<?php 

function curl_get_contents($url)
{
        $ch = curl_init();
        $timeout = 100;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
}

$contents = curl_get_contents('http://dir.elink.corp/cron/attrition');

echo "\n\n" . $contents;

