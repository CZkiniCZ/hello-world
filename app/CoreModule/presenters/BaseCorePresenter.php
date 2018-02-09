<?php



namespace App\CoreModule\Presenters;


use App\Presenters\BasePresenter;

/**
 * Základní presenter pro všechny ostatní presentery v CoreModule.
 * @package App\CoreModule\Presenters
 */
abstract class BaseCorePresenter extends BasePresenter
{
	/** @var null|string Adresa presenteru pro logování uživatele v rámci CoreModule. */
	protected $loginPresenter = ':Core:User:login';
}
