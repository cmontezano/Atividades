<?php
class UserList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // security check
        if (TSession::getValue('logged') !== TRUE)
        {
            throw new Exception('Não está logado');
        }
        
        // security check
        TTransaction::open('atividades');
        if ((Usuario::newFromLogin(TSession::getValue('login'))->papel->mnemonico !== 'OPERADOR'))
        {
            throw new Exception('Permissão negada');
        }
        TTransaction::close();
        
        // defines the database
        parent::setDatabase('atividades');
        
        // defines the active record
        parent::setActiveRecord('Usuario');
        
        // defines the filter field
        parent::setFilterField('name');
        
        // creates the form
        $this->form = new TForm('form_search_User');
        $this->form->class = 'tform';
        
        $table = new TTable;
        $table->width = '100%';
        $this->form->add($table);
        
        $table->addRowSet(new TLabel('Usuários'), '')->class = 'tformtitle';
        
        // create the form fields
        $filter = new TEntry('name');
        $filter->setSize(400);
        $filter->setValue(TSession::getValue('User_name'));
        
        // add a row for the filter field
        $row=$table->addRow();
        $row->addCell(new TLabel('Nome' . ': '));
        $row->addCell($filter);
        
        // create two action buttons to the form
        $find_button = new TButton('find');
        $new_button  = new TButton('new');
        // define the button actions
        $find_button->setAction(new TAction(array($this, 'onSearch')), 'Encontrar');
        $find_button->setImage('ico_find.png');
        
        $new_button->setAction(new TAction(array('UserForm', 'onEdit')), 'Novo');
        $new_button->setImage('ico_new.png');
        
        $table->addRowSet('', array($find_button, $new_button))->class = 'tformaction';
        
        // define wich are the form fields
        $this->form->setFields(array($filter, $find_button, $new_button));
        
        // creates a DataGrid
        $this->datagrid = new TQuickGrid;
        $this->datagrid->setHeight(320);

        // creates the datagrid columns
        $this->datagrid->addQuickColumn('ID', 'id', 'right', 50, new TAction(array($this, 'onReload')), array('order', 'id'));
        $this->datagrid->addQuickColumn('Nome', 'name', 'left', 200, new TAction(array($this, 'onReload')), array('order', 'name'));
        $this->datagrid->addQuickColumn('Papel', 'papel_id', 'left', 100, new TAction(array($this, 'onReload')), array('order', 'papel'));

        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // creates the page structure using a vbox
        $container = new TVBox;
        $container->add($this->form);
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        // add the vbox inside the page
        parent::add($container);
    }
}
?>