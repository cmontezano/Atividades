<?php
include_once 'lib/adianti/util/TAdiantiLoader.class.php';
spl_autoload_register(array('TAdiantiLoader', 'autoload_web'));
$ini  = parse_ini_file('application.ini');
date_default_timezone_set($ini['timezone']);
define('APPLICATION_NAME', $ini['application']);
define('OS', strtoupper(substr(PHP_OS, 0, 3)));
define('PATH', dirname(__FILE__));

class TApplication extends TCoreApplication
{
    static public function run($debug = FALSE)
    {
        new TSession;
        
        $lang = TSession::getValue('language') ? TSession::getValue('language') : 'en';
        TAdiantiCoreTranslator::setLanguage($lang);
        TApplicationTranslator::setLanguage($lang);
        
        if ($_REQUEST)
        {
            $class = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
            
            if (!TSession::getValue('logged') AND $class !== 'LoginForm')
            {
                echo TPage::getLoadedCSS();
                echo TPage::getLoadedJS();
                new TMessage('error', 'Not logged');
                return;
            }
            parent::run($debug);
        }
    }
}

TApplication::run(TRUE);
?>