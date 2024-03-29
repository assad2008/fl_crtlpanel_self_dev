<?php
/**
* @file apns.php
* @synopsis  apple APNS class
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2012-09-17 11:27:59
*/
!defined('PATH_ADMIN') && exit('Forbidden');
	class PHPAPNS
	{
		const ENVIRONMENT_PRODUCTION = 0;
		const ENVIRONMENT_SANDBOX = 1;
		const DEVICE_BINARY_SIZE = 32;
		const CONNECT_RETRY_INTERVAL = 1000000;
		const SOCKET_SELECT_TIMEOUT = 1000000;
		const COMMAND_PUSH = 1;
		const STATUS_CODE_INTERNAL_ERROR = 999;
		const ERROR_RESPONSE_SIZE = 6;
		const ERROR_RESPONSE_COMMAND = 8;
		const PAYLOAD_MAXIMUM_SIZE = 256;
		const APPLE_RESERVED_NAMESPACE = 'aps';
		protected $_environment;
		protected $_providerCertificateFile;
		protected $_rootCertificationAuthorityFile;
		protected $_connectTimeout;
		protected $_connectRetryTimes = 3;
		protected $_connectRetryInterval;
		protected $_socketSelectTimeout;
		protected $_hSocket;
		protected $_deviceTokens = array();
		protected $_text;
		protected $_badge;
		protected $_sound;
		protected $_customProperties;
		protected $_expiryValue = 604800;
		protected $_customIdentifier;
		protected $_autoAdjustLongPayload = true;
		protected $asurls = array('ssl://gateway.push.apple.com:2195','ssl://gateway.sandbox.push.apple.com:2195');
		protected $_errorResponseMessages = array
							(
								0	=> 'No errors encountered',
								1 => 'Processing error',
								2 => 'Missing device token',
								3 => 'Missing topic',
								4 => 'Missing payload',
								5 => 'Invalid token size',
								6 => 'Invalid topic size',
								7 => 'Invalid payload size',
								8 => 'Invalid token',
								self::STATUS_CODE_INTERNAL_ERROR => 'Internal error'
							);
		
		function __construct($environment,$providerCertificateFile)
		{
			if($environment != self::ENVIRONMENT_PRODUCTION && $environment != self::ENVIRONMENT_SANDBOX) 
			{
				throw new Exception(
					"Invalid environment '{$environment}'"
				);
			}
			$this->_environment = $environment;

			if(!is_readable($providerCertificateFile)) 
			{
				throw new Exception(
					"Unable to read certificate file '{$providerCertificateFile}'"
				);
			}
			$this->_providerCertificateFile = $providerCertificateFile;

			$this->_connectTimeout = @ini_get("default_socket_timeout");
			$this->_connectRetryInterval = self::CONNECT_RETRY_INTERVAL;
			$this->_socketSelectTimeout = self::SOCKET_SELECT_TIMEOUT;
		}

		public function setRCA($rootCertificationAuthorityFile)
		{
			if(!is_readable($rootCertificationAuthorityFile)) 
			{
				throw new Exception(
					"Unable to read Certificate Authority file '{$rootCertificationAuthorityFile}'"
				);
			}
			$this->_rootCertificationAuthorityFile = $rootCertificationAuthorityFile;
		}

		public function getRCA()
		{
			return $this->_rootCertificationAuthorityFile;
		}

		protected function _connect()
		{
			$sURL = $this->asurls[$this->_environment];
			$streamContext = stream_context_create(
				array
					(
						'ssl' => array
						(
							'verify_peer' => isset($this->_rootCertificationAuthorityFile),
							'cafile' => $this->_rootCertificationAuthorityFile,
							'local_cert' => $this->_providerCertificateFile
						)
					)
				);
			$this->_hSocket = @stream_socket_client($sURL,$nError,$sError,$this->_connectTimeout,STREAM_CLIENT_CONNECT, $streamContext);
			if (!$this->_hSocket) 
			{
				throw new Exception
				(
					"Unable to connect to '{$sURL}': {$sError} ({$nError})"
				);
			}
			stream_set_blocking($this->_hSocket, 0);
			stream_set_write_buffer($this->_hSocket, 0);
			return true;
		}

		public function connect()
		{
			$bConnected = false;
			$retry = 0;
			while(!$bConnected) 
			{
				try 
				{
					$bConnected = $this->_connect();
				}catch (Exception $e) 
				{
					if ($nRetry >= $this->_connectRetryTimes) 
					{
						throw $e;
					}else 
					{
						usleep($this->_nConnectRetryInterval);
					}
				}
				$retry++;
			}
		}

		public function disconnect()
		{
			if (is_resource($this->_hSocket)) 
			{
				return fclose($this->_hSocket);
			}
			return false;
		}

		protected function getBinaryNotification($deviceToken, $payload, $messageID = 0, $Expire = 604800)
		{
			$tokenLength = strlen($deviceToken);
			$payloadLength = strlen($payload);

			$ret  = pack('CNNnH*', self::COMMAND_PUSH, $messageID, $Expire > 0 ? time() + $Expire : 0, self::DEVICE_BINARY_SIZE, $deviceToken);
			$ret .= pack('n', $payloadLength);
			$ret .= $payload;
			return $ret;
		}

		protected function readErrorMessage()
		{
			$errorResponse = @fread($this->_hSocket, self::ERROR_RESPONSE_SIZE);
			if ($errorResponse === false || strlen($errorResponse) != self::ERROR_RESPONSE_SIZE) 
			{
				return;
			}
			$errorResponse = $this->parseErrorMessage($errorResponse);
			if (!is_array($errorResponse) || empty($errorResponse)) 
			{
				return;
			}
			if (!isset($errorResponse['command'], $errorResponse['statusCode'], $errorResponse['identifier'])) 
			{
				return;
			}
			if ($errorResponse['command'] != self::ERROR_RESPONSE_COMMAND) 
			{
				return;
			}
			$errorResponse['timeline'] = time();
			$errorResponse['statusMessage'] = 'None (unknown)';
			if (isset($this->_aErrorResponseMessages[$errorResponse['statusCode']])) 
			{
				$errorResponse['statusMessage'] = $this->_errorResponseMessages[$errorResponse['statusCode']];
			}
			return $errorResponse;
		}

		protected function parseErrorMessage($errorMessage)
		{
			return unpack('Ccommand/CstatusCode/Nidentifier', $errorMessage);
		}

		public function send()
		{
			if (!$this->_hSocket) 
			{
				throw new Exception
				(
					'Not connected to Push Notification Service'
				);
			}
			$sendCount = $this->getDTNumber();
			$messagePayload = $this->getPayload();
			foreach($this->_deviceTokens AS $key => $value)
			{
				$apnsMessage = $this->getBinaryNotification($value, $messagePayload, $messageID = 0, $Expire = 604800);
				$nLen = strlen($apnsMessage);
				$aErrorMessage = null;
				if ($nLen !== ($nWritten = (int)@fwrite($this->_hSocket, $apnsMessage))) 
				{
					$aErrorMessage = array
					(
						'identifier' => $key,
						'statusCode' => self::STATUS_CODE_INTERNAL_ERROR,
						'statusMessage' => sprintf('%s (%d bytes written instead of %d bytes)',$this->_errorResponseMessages[self::STATUS_CODE_INTERNAL_ERROR], $nWritten, $nLen)
					);
				}
			}
		}


		public function addDT($deviceToken)
		{
			if (!preg_match('~^[a-f0-9]{64}$~i', $deviceToken)) 
			{
				throw new Exception
				(
					"Invalid device token '{$deviceToken}'"
				);
			}
			$this->_deviceTokens[] = $deviceToken;
		}		
		
		public function getDTNumber()
		{
			return count($this->_deviceTokens);
		}

		public function setText($text)
		{
			$this->_text = $text;
		}

		public function getText()
		{
			return $this->_text;
		}

		public function setBadge($badge)
		{
			if (!is_int($badge)) 
			{
				throw new Exception
				(
					"Invalid badge number '{$badge}'"
				);
			}
			$this->_badge = $badge;
		}

		public function getBadge()
		{
			return $this->_badge;
		}

		public function setSound($sound = 'default')
		{
			$this->_sound = $sound;
		}

		public function getSound()
		{
			return $this->_sound;
		}

		public function setCP($name, $value)
		{
			if ($name == self::APPLE_RESERVED_NAMESPACE) 
			{
				throw new Exception
				(
					"Property name '" . self::APPLE_RESERVED_NAMESPACE . "' can not be used for custom property."
				);
			}
			$this->_customProperties[trim($name)] = $value;
		}

		protected function _getPayload()
		{
			$aPayload[self::APPLE_RESERVED_NAMESPACE] = array();

			if (isset($this->_text)) 
			{
				$aPayload[self::APPLE_RESERVED_NAMESPACE]['alert'] = (string)$this->_text;
			}
			if (isset($this->_badge) && $this->_badge > 0) 
			{
				$aPayload[self::APPLE_RESERVED_NAMESPACE]['badge'] = (int)$this->_badge;
			}
			if (isset($this->_sound)) 
			{
				$aPayload[self::APPLE_RESERVED_NAMESPACE]['sound'] = (string)$this->_sound;
			}

			if (is_array($this->_customProperties)) 
			{
				foreach($this->_customProperties as $propertyName => $propertyValue) 
				{
					$aPayload[$propertyName] = $propertyValue;
				}
			}
			return $aPayload;
		}

		public function setExpiry($expiryValue)
		{
			if (!is_int($expiryValue)) 
			{
				throw new Exception
				(
					"Invalid seconds number '{$expiryValue}'"
				);
			}
			$this->_expiryValue = $expiryValue;
		}

		public function getExpiry()
		{
			return $this->_expiryValue;
		}

		public function setCustomIdentifier($customIdentifier)
		{
			$this->_customIdentifier = $customIdentifier;
		}

		public function getCustomIdentifier()
		{
			return $this->_customIdentifier;
		}		

		public function getPayload()
		{
			include_once(PATH_APPLICATION . '/pm_json.class.php');
			$JSON = new JSON;
			$sJSONPayload = str_replace
			(
				'"' . self::APPLE_RESERVED_NAMESPACE . '":[]',
				'"' . self::APPLE_RESERVED_NAMESPACE . '":{}',
				$JSON->encode($this->_getPayload())
			);
			$nJSONPayloadLen = strlen($sJSONPayload);

			if ($nJSONPayloadLen > self::PAYLOAD_MAXIMUM_SIZE)
			{
				if ($this->_autoAdjustLongPayload) 
				{
					$maxTextLen = $textLen = strlen($this->_text) - ($nJSONPayloadLen - self::PAYLOAD_MAXIMUM_SIZE);
					if ($nMaxTextLen > 0)
					{
						while (strlen($this->_text = mb_substr($this->_text, 0, --$textLen, 'UTF-8')) > $maxTextLen);
						return $this->getPayload();
					}else
					{
						throw new Exception
						(
							"JSON Payload is too long: {$nJSONPayloadLen} bytes. Maximum size is " .
							self::PAYLOAD_MAXIMUM_SIZE . " bytes. The message text can not be auto-adjusted."
						);
					}
				}else
				{
					throw new Exception
					(
						"JSON Payload is too long: {$nJSONPayloadLen} bytes. Maximum size is " .
						self::PAYLOAD_MAXIMUM_SIZE . " bytes"
					);
				}
			}
			return $sJSONPayload;
		}	
	}

?>
