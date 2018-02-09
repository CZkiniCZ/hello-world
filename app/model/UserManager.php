<?php
namespace App\Model;

use Nette\Database\UniqueConstraintViolationException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;

/**
 * Správce uživatelů redakčního systému.
 * @package App\Model
 */
class UserManager extends BaseManager implements IAuthenticator
{
	/** Konstanty pro manipulaci s modelem. */
	const
		TABLE_NAME = 'user',
		COLUMN_ID = 'user_id',
		COLUMN_NAME = 'username',
                COLUMN_ACTIVE = 'active',
                COLUMN_CODE = 'code',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_ROLE = 'role';

	/**
	 * Přihlásí uživatele do systému.
	 * @param array $credentials       jméno a heslo uživatele
	 * @return Identity identitu přihlášeného uživatele pro další manipulaci
	 * @throws AuthenticationException Jestliže došlo k chybě při prihlašování, např. špatné heslo nebo uživatelské
	 *                                 jméno.
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials; // Extrahuje potřebné parametry.

		// Vykoná dotaz nad databází a vrátí první řádek výsledku nebo false, pokud uživatel neexistuje.
		$user = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

		// Ověření uživatele.
		if (!$user) {
			// Vyhodí výjimku, pokud uživatel neexituje.
			throw new AuthenticationException('Zadané uživatelské jméno neexistuje.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $user[self::COLUMN_PASSWORD_HASH])) { // Ověří heslo.
			// Vyhodí výjimku, pokud je heslo špatně.
			throw new AuthenticationException('Zadané heslo není správně.', self::INVALID_CREDENTIAL);
                } elseif (Passwords::needsRehash($user[self::COLUMN_PASSWORD_HASH])) { // Zjistí, jestli heslo potřebuje rehashovat.
			// Rehashuje heslo.
			$user->update(array(self::COLUMN_PASSWORD_HASH => Passwords::hash($password)));
		}

		// Příprava uživatelských dat.
		$userData = $user->toArray(); // Extrahuje uživatelská data.
		unset($userData[self::COLUMN_PASSWORD_HASH]); // Odstraní položku hesla z uživatelských dat (kvůli bezpečnosti).

		// Vrátí novou identitu přihlášeného uživatele.
		return new Identity($user[self::COLUMN_NAME], $user[self::COLUMN_ID], $user[self::COLUMN_ROLE], $userData);
	}

	/**
	 * Registruje nového uživatele do systému.
	 * @param string $username uživatelské jméno
	 * @param string $password heslo
	 * @throws DuplicateNameException Jestliže uživatel s daným jménem již existuje.
	 */
	public function register($username, $password, $code)
	{
		try {
			// Pokusí se vložit nového uživatele do databáze.
			$this->database->table(self::TABLE_NAME)->insert(array(
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
                            self::COLUMN_CODE => $code,
			));
		} catch (UniqueConstraintViolationException $e) {
			// Vyhodí výjimku, pokud uživatel s daným jménem již existuje.
			throw new DuplicateNameException;
		}
	}
}

/**
 * Výjimka pro duplicitní uživatelské jméno.
 * @package App\Model
 */
class DuplicateNameException extends AuthenticationException
{
	/** Konstruktor s definicích výchozí chybové zprávy. */
	public function __construct()
	{
		parent::__construct();
		$this->message = 'Uživatel s tímto jménem je již zaregistrovaný.';
	}
}
