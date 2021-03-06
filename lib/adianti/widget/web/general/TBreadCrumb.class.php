<?php
/**
 * BreadCrumb
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @author     Nataniel Rabaioli
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TBreadCrumb extends TElement
{
    protected static $homeController;
    protected $container;
    protected $items;
    
    /**
     * Handle paths from a XML file
     * @param $xml_file path for the file
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->{'id'} = 'div_breadcrumbs';
        
        $this->container = new TElement('ol');
        $this->container->{'class'} = 'breadcrumbs';
        parent::add( $this->container );
    }
    
    /**
     * Add the home icon
     */
    public function addHome()
    {
        $li = new TElement('li');
        
        $a = new TElement('a');
        $a->{'class'} = 'bread';
        $a->generator = 'adianti';
        
        if (self::$homeController)
        {
            $a->{'href'} = 'engine.php?class='.self::$homeController;
        }
        else
        {
            $a->{'href'} = 'engine.php';
        }
        
        $a->{'title'} = 'Home';
        
        $span = new TElement('span');
        $span->add( 'h' );
        $li->add( $a );
        $a->add( $span );
        $this->container->add( $li );
    }
    
    /**
     * Add an item
     * @param $path Path to be shown
     * @param $last If the item is the last one
     */
    public function addItem($path, $last = FALSE)
    {
        $li = new TElement('li');
        $this->container->add( $li );
        
        $span = new TElement('span');
        $span->add( $path );
        
        $this->items[$path] = $span;
        if( $last )
        {
            $li->add( $span );
        }
        else
        {
            $a = new TElement('a');
            
            $li->add( $a );
            $a->add( $span );
        }
            
    }
    
    /**
     * Mark one breadcrumb item as selected
     */
    public function select($path)
    {
        foreach ($this->items as $key => $span)
        {
            if ($key == $path)
            {
                $span->{'class'} = 'selected';
            }
            else
            {
                $span->{'class'} = '';
            }
        }
    }
    
    /**
     * Define the home controller
     * @param $class Home controller class
     */
    public static function setHomeController($className)
    {
        self::$homeController = $className;
    }
    
    /**
     * Show the breadcrumb
     */
    public function show()
    {
        TPage::include_css('lib/adianti/include/tbreadcrumb/tbreadcrumb.css');
        parent::show();
    }
}
?>