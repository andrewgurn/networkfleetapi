<?php

/*
	Class: NetworkFleet
	Purpose: Manage API calls to Verizon's NetworkFleet service through cURL
	Author: Andrew Weitzel
	Github URL: https://github.com/andrewgurn/networkfleetapi
*/

class NetworkFleetAPI
{
	var $user;
	var $secret;
	var $tokenArray;
	var $token;
	var $tokenType = 'Bearer';
	var $refreshToken;
	var $defaultHost = 'api.networkfleet.com';
	var $defaultAuthHost = 'auth.networkfleet.com';
	/*
		The line below tells the API to return JSON data.  If you'd prefer XML data instead, replace it with:
		var $defaultContentType = 'application/vnd.networkfleet.api-v1+xml';
		
	*/
	var $defaultContentType = 'application/vnd.networkfleet.api-v1+json';

	function __construct($userIn, $secretIn, $tokenIn = '', $refreshTokenIn = '') 
	{
		$this->user = $userIn;
		$this->secret = $secretIn;
	
		//If we don't have a token already, create a new connection
		if($tokenIn == "")
		{
			createConnection($userIn, $secretIn);
		}
		//If we already have a token, populate everything that would otherwise be populated by createConnection() 
		else
		{
			$this->token = $tokenIn;
			$this->tokenArray['access_token'] = $tokenIn;
			$this->refreshToken = $refreshTokenIn;
			$this->tokenArray['refreshToken'] = $refreshTokenIn;
			$this->tokenArray['tokenType'] = $tokenType;
		}
	}
	
	function createConnection($userIn, $secretIn)
	{
		$host = $this->defaultAuthHost;
		$method = 'POST';
		$path = '/token';
		$url = "https://$host$path";
		$data = "grant_type=password&username=$userIn&password=$secretIn";

		$options = array( CURLOPT_HTTPHEADER => array("Authorization: Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW", "Content-Type: application/x-www-form-urlencoded"),
				  CURLOPT_POSTFIELDS => $data,
				  CURLOPT_HEADER => false,
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_SSL_VERIFYPEER => false);
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$json = curl_exec($curl);
		curl_close($curl);
		$this->tokenArray = json_decode($json, true);
		$this->token = $this->tokenArray ["access_token"];
		$this->tokenType = $this->tokenArray ["token_type"];
		$this->refreshToken = $this->tokenArray ["refresh_token"];
	}
	
	function refreshToken($refreshTokenIn)
	{
		$host = $this->defaultAuthHost;
		$method = 'POST';
		$path = '/token';
		$url = "https://$host$path";
		$data = "grant_type=refresh_token&refresh_token=$refreshTokenIn";

		$options = array( CURLOPT_HTTPHEADER => array("Authorization: Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW", "Content-Type: application/x-www-form-urlencoded"),
				  CURLOPT_POSTFIELDS => $data,
				  CURLOPT_HEADER => false,
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_SSL_VERIFYPEER => false);
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$json = curl_exec($curl);
		curl_close($curl);
		$this->tokenArray = json_decode($json, true);
		$this->token = $this->tokenArray ["access_token"];
		$this->tokenType = $this->tokenArray ["token_type"];
		$this->refreshToken = $this->tokenArray ["refresh_token"];
	}

	function getQuery($path, $method, $queryParameters = "")
	{
		$host = $this->defaultHost;
		
		if($method == 'GET' && $queryParameters != '')
		{
			$path .= $queryParameters;
		}
		
		$url = "https://$host$path";
		
		$options = array( CURLOPT_HTTPHEADER => array(
								"Authorization: ".$this->tokenType." ".$this->token." "
								, "Content-Type:".$this->defaultContentType
								, "Accept: ".$this->defaultContentType
							),
				  CURLOPT_HEADER => false,
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_SSL_VERIFYPEER => false);
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$json = curl_exec($curl);
		
		return $json;
	}
	
	function getTokenArray()
	{
		return $this->tokenArray;
	}

	function testCall()
	{	
		return $this->getQuery('/test', 'GET', '');
	}
	
	function getVehicleLocations()
	{
		return $this->getQuery('/locations', 'GET','');
	}

	function getLandmarkCategories()
	{
		return $this->getQuery('/landmarkCategories', 'GET', '');
	}

} //end class

?>
