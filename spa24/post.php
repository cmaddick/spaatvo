<?php

	// -------------------------------------
	// USER CONFIGURATION: CHANGE BELOW
	// -------------------------------------

	// Password (must match setting in ATVO)
	$password = "racespotspa";

	// Folder to store data (server must have read/write access)
	$cachedir = "sessions";
	
	// -------------------------------------
	// END OF CONFIGURATION. DO NOT CHANGE BELOW
	// -------------------------------------

	try {
	
		$json = file_get_contents('php://input');
		$data = json_decode($json, true);
		
		if ($data['Password'] == $password) {
		
			$ssid = $data['Ssid'];
			
			// remove password before saving
			unset($data['Password']);

			process_data_update($data, $ssid);

		} else {
			echo "Incorrect password";
		}

	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}

	function process_data_update($update, $ssid) {	
		global $cachedir;

		// strip events from json
		$events = $update['Events'];
		unset($update['Events']);

		// store data
		$filename = $cachedir . "/data.json";
		file_put_contents($filename, json_encode($update));

		// process events further
		process_events_update($events, $ssid);
	}

	function process_events_update($events, $ssid) {
		global $cachedir;

		$filename = $cachedir . "/events_" . $ssid . ".json";
				
		// read current list
		if (file_exists($filename)) {
			$json = file_get_contents($filename);
			$curr_events = json_decode($json, true);
		} else {
			$curr_events = array();
		}

		// add new events
		foreach ($events as $e) {
			array_unshift($curr_events, $e);
		}

		// save
		file_put_contents($filename, json_encode($curr_events));
	}
?>
