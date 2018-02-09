<?php

/*  _____ _______         _                      _
 * |_   _|__   __|       | |                    | |
 *   | |    | |_ __   ___| |___      _____  _ __| | __  ___ ____
 *   | |    | | '_ \ / _ \ __\ \ /\ / / _ \| '__| |/ / / __|_  /
 *  _| |_   | | | | |  __/ |_ \ V  V / (_) | |  |   < | (__ / /
 * |_____|  |_|_| |_|\___|\__| \_/\_/ \___/|_|  |_|\_(_)___/___|
 *                                _
 *              ___ ___ ___ _____|_|_ _ _____
 *             | . |  _| -_|     | | | |     |  LICENCE
 *             |  _|_| |___|_|_|_|_|___|_|_|_|
 *             |_|
 *
 * IT ZPRAVODAJSTVÍ  <>  PROGRAMOVÁNÍ  <>  HW A SW  <>  KOMUNITA
 *
 * Tento zdrojový kód je součástí výukových seriálů na
 * IT sociální síti WWW.ITNETWORK.CZ
 *
 * Kód spadá pod licenci prémiového obsahu a vznikl díky podpoře
 * našich členů. Je určen pouze pro osobní užití a nesmí být šířen.
 */

namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Tracy\ILogger;

/**
 * Error presenter pro vykreslení různých chyb na stránce.
 * @package App\Presenters
 */
class ErrorPresenter extends BasePresenter
{
	/** @var ILogger Instance třídy pro logování. */
	private $logger;

	/**
	 * Konstruktor tohoto presenteru.
	 * @param ILogger $logger třída pro logování
	 */
	public function __construct(ILogger $logger)
	{
		parent::__construct();
		$this->logger = $logger;
	}

	/**
	 * Vykresluje podobu stránky s HTTP chybou.
	 * @param BadRequestException $exception výjimka, díky které byl tento presenter zavolán
	 * @throws AbortException Jestliže došlo k selhání při vykreslení stránky s chybou. (Proběhne nouzové vykreslení chyby 500.)
	 */
	public function renderDefault($exception)
	{
		$serverError = false;
		// Pokud jde o chybu v dotazu.
		if ($exception instanceof BadRequestException) {
			// Zapisuje zprávu do access.log.
			$this->logger->log("HTTP code {$exception->getCode()}: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'access');
		} else { // Jinak je to chyba serveru.
			$this->setView('500'); // Načítá template 500.latte.
			$this->logger->log($exception, ILogger::EXCEPTION); // Loguje výjimku.
			$serverError = true;
		}

		// Pokud jde o AJAXový dotaz, pošle chybu v payloadu, což je asynchroní odpoveď na AJAX dotaz.
		if ($this->isAjax()) {
			$this->payload->error = true;
			$this->terminate();
		} elseif (!$serverError) { // Jinak pokud to není chyba serveru.
			$this->redirect(':Core:Page:', 'chyba'); // Přesměruj na vlastní chybovou stránku.
		}
		// Jinak se vykreslí defaultní chyba serveru (500).
	}
}
