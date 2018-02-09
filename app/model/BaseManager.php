<?php


namespace App\Model;

use Nette\Database\Context;
use Nette\Object;
use Nette\Database\Connection;
/**
 * Základní třída modelu pro všechny modely aplikace.
 * Předává přístup k práci s databází.
 * @package App\Model
 */
abstract class BaseManager extends Object
{
	/** @var Context Instance třídy pro práci s databází. */
	protected $database;
        
	/**
	 * Konstruktor s injektovanou třídou pro práci s databází.
	 * @param Context $database automaticky injektovaná třída pro práci s databází

	 */
	public function __construct(Context $database)
	{
		$this->database = $database;

	}
}
