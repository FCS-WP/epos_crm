<?php


namespace EPOS_CRM\Src\Logs;


class EPOS_CRM_logger
{

	public static $logger;
	const LOG_FILENAME = 'epos_crm_log';

	public static function log_message($message)
	{
		if (!class_exists('WC_Logger')) {
			return;
		}

		$logger = wc_get_logger();

		$log_entry = sprintf('==== EPOS CRM Log Start [%s] ==== ', date('d/m/Y H:i:s')) . "\n";
		$log_entry .=  $message . "\n";
		$log_entry .= '==== EPOS CRM Log End====' . "\n\n";

		$logger->debug($log_entry, ['source' => self::LOG_FILENAME]);
	}

	public static function log_response($message, $payload)
	{
		if (!class_exists('WC_Logger')) {
			return;
		}

		$logger = wc_get_logger();

		$log_entry = sprintf('==== EPOS CRM Log Start [%s] ==== ', date('d/m/Y H:i:s')) . "\n";
		$log_entry .=  $message . "\n";
		$log_entry .= wc_print_r($payload, true) . "\n";
		$log_entry .= '==== EPOS CRM Log End====' . "\n\n";


		$logger->debug($log_entry, ['source' => self::LOG_FILENAME]);
	}
}
