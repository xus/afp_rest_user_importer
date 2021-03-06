<?php
/* Copyright (c) 1998-2017 ILIAS open source, Extended GPL, see docs/LICENSE */
//include_once 'class.crm_rest_api.php';


/**
 * REST reader.
 * @author Jesus Lopez <lopez@leifos.com>
 */
class ilAfPImportRestReader
{
	protected $session_id;
	protected $rest_base_url;
	protected $last_execution;

	function __construct()
	{
		$this->rest_base_url = ilAfPSettings::getInstance()->getRestUrl();
		$this->connection();
	}

	protected function connection()
	{
		try
		{
			$target = $this->rest_base_url."logon?method=crmLogin&response_type=JSON&username=".ilAfPSettings::getInstance()->getRestUser()."&password=".ilAfPSettings::getInstance()->getRestPassword();

			$response = file_get_contents($target);

			$this->session_id = json_decode($response);

			ilAfPLogger::getLogger()->write("Connection successful session id = ".$this->session_id);

		}
		catch (Exception $e)
		{
			ilAfPLogger::getLogger()->write("Connection Exception: ".$e->getMessage());
		}


	}

	// calls REST crmgetChangedContacts
	function getRestUsers()
	{

		//get the last cron execution timestamp
		$cron_data = ilCronManager::getCronJobData('afpui');
		$last_execution = $cron_data[0]['job_results_ts'];

		//ilAfPLogger::getLogger()->write("Last execution = ".$last_execution);

		/** GET CONTACTS - RETURNS ERROR 500 */
		/*
		$target = $this->rest_base_url."contacts?method=crmgetChangedContacts&response_type=JSON&session_id=".$this->session_id."&timestamp=1464510140";
		*/

		/** GET CONTACTS WITH LIMITS */
		//TODO get the last cron execution and change this hardcoded timestamp
		$target = $this->rest_base_url."contacts?method=crmgetChangedContactsLimit&response_type=JSON&session_id=".$this->session_id."&timestamp=1464510140&count=20&offset=0";
		ilAfPLogger::getLogger()->write("target changed contacts LIMIT = ".$target);
		$response = file_get_contents($target);
		$items = json_decode($response, true);
		
		return $items;

	}


}