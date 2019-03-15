<?php
namespace App\Http\Controllers;

trait CurlTrait 
{
	protected function curlCall($url) {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $output = curl_exec($ch); 
        curl_close($ch);   

        return $output;
	}
}