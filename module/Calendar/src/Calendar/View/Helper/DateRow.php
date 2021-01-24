<?php

namespace Calendar\View\Helper;

use DateTime;
use IntlDateFormatter;
use Zend\View\Helper\AbstractHelper;

use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;

class DateRow extends AbstractHelper
{

	public function __invoke(DateTime $date, $colspan, $outerClasses = null)
	{
		$view = $this->getView();

		$dayName = current(preg_split('/,|\s/', $view->dateFormat($date, IntlDateFormatter::FULL)));
		$dateFormat = $view->dateFormat($date, IntlDateFormatter::LONG);

		$day = $date->format('Y-m-d');

		$request = new Request();
		$request->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
		));
		$request->setUri("https://roster.taupoglidingclub.co.nz/api/rosters/day?date=$day");
		$request->setMethod('GET');


		$client = new Client();
		$response = $client->dispatch($request);
		$data = json_decode($response->getBody(), true);

		$roster = '';
		foreach($data as $event) 
			foreach ($event as $key=>$value)
				$roster .= "$value: $key,   ";

		return sprintf('<tr class="calendar-date-row %s"><td colspan="%s"><div class="day-label">%s</div><div class="date-label">%s</div><div class="day-label">%s</div></td></tr>',
			$outerClasses, $colspan, $dayName, $dateFormat, $roster);
	}

}