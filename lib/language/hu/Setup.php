<?php

namespace Distvan\Language\Hu;

use Distvan\Language\Base;

/**
 * Class Setup
 *
 * @package Distvan\Language\Hu
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Setup extends Base
{
    protected $_lang = array(
        'blog_installation' => 'Blog Telepítés',
        'step1_instruction' => 'Állítsd be a PHP konfigurációt a következők szerint.',
        'step2_instruction' => 'Állítsd be a következő PHP kiterjesztéseket.',
        'step3_instruction' => 'Állítsd be a jogosultságokat a következő könyvtárra.',
        'php_settings' => 'PHP Beállítások',
        'current_settings' => 'Jelenlegi beállítások',
        'required_settings' => 'Elvárt beállítások',
        'extension_settings' => 'Kiegészítők beállításai',
        'status' => 'Állapot',
        'php_version' => 'PHP Verzió',
        'on' => 'Bekapcsolva',
        'off' => 'Kikapcsolva',
        'writable' => 'Írható',
        'missing' => 'Hiányzik',
        'directory' => 'Könyvtár',
        'blog_author' => 'Bejegyzések szerzőjének neve',
        'blog_admin_name' => 'Admin felhasználó',
        'blog_admin_password' => 'Admin jelszó',
        'blog_admin_password_again' => 'Jelszó megerősítése',
        'install' => 'Telepít',
        'error_invalid_author' => 'A szerző nevének 5 és 25 karakter közötti hosszúságúnak kell lennie!',
        'error_invalid_admin' => 'Az admin nevének 5 és 10 karakter közötti hosszúságúnak kell lennie!',
        'error_invalid_password' => 'A jelszónak legalább 6 karekteresnek kell lennie és számot és betűt is kell tartalmaznia!',
        'error_invalid_password_again' => 'A beírt jelszó nem egyezik!',
    );
}