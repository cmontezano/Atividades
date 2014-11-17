<?php
// define the autoloader
include_once 'lib/adianti/util/TAdiantiLoader.class.php';
spl_autoload_register(array('TAdiantiLoader', 'autoload_gtk'));

// preload Gtk components
TAdiantiLoader::preload('gtk');

// read configurations
$ini  = parse_ini_file('application.ini');
date_default_timezone_set($ini['timezone']);
TAdiantiCoreTranslator::setLanguage( $ini['language'] );
TApplicationTranslator::setLanguage( $ini['language'] );

// define constants
define('APPLICATION_NAME', $ini['application']);
define('OS', strtoupper(substr(PHP_OS, 0, 3)));
define('PATH', dirname(__FILE__));
ini_set('php-gtk.codepage', 'UTF8');

class TApplication extends TCoreApplication
{
    protected $content;
    function __construct()
    {
        parent::__construct();
        parent::set_title('Adianti Framework :: Samples');
        $this->content = new GtkFixed;

        $vbox = new GtkVBox;
        $vbox->pack_start(GtkImage::new_from_file('app/images/pageheader-gtk.png'), false, false);
        $MenuBar = TMenuBar::newFromXML('menu.xml');
        
        $vbox->pack_start($MenuBar, false, false);
        $vbox->pack_start($this->content, true, true);
        
        parent::add($vbox);
        parent::show_all();
    }
}

$app = new TApplication;

try
{
    Gtk::Main();
}
catch (Exception $e)
{
    $app->destroy();
    new TExceptionView($e);
    Gtk::main();
}
?>