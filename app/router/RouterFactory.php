<?php
namespace App;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Routovací továrnička.
 * Řídí routování v celé aplikaci.
 * @package App
 */
class RouterFactory
{
	/**
	 * Vytváří router pro aplikaci.
	 * @return RouteList výsledný router pro aplikaci
	 */
	public static function createRouter()
	{
		$router = new RouteList();
                 $router[] = new Route('[<action>/][<url>]', array(
			'presenter' => 'Core:CV',
			'action' => array(
				Route::VALUE => 'cveditor',
				Route::FILTER_TABLE => array(
					// řetězec v URL => akce presenteru
					'cv-editor' => 'editor',
                                    'cv' => 'default',
                                    'cv-info' => 'cveditor'
                                      
				),
				Route::FILTER_STRICT => true
			),
                    'url' => null,
		));
                

		return $router;
	}
}
