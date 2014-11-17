<?php
class MemberList extends TStandarList
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    
    /**
     * Class constructor
     * Cretaes the page, the form and the listing
     */
     public function __construct()
     {
         parent::__construct();
         
         // security check
         if (TSession::getValue('logged') !== true)
         {
             throw new Exception('Você não está logado!');
         }
         
         // security check
         TTransaction::open('atividades');
         if (Usuario::newFromLogin(TSession->getValue('login'))->get_role()->mnemonico !== 'OPERADOR') {
             throw new Exception('Permissão negada');
         }
         TTransaction::close();
         
         // defines the database
         parent::setDatabase('atividades');
         
         // defines the active record
         parent::setActiveRecord('Funcionario');
         
         // defines the filter field
         parent::setFilterField('name');
         
         // creates the form
         $this->form = new TForm('form_search_member');
         $this->form->class = 'tform';
         
         $table = new TTable;
         $table->width = '100%';
         $this->form->add($table);
         
         $table->addRowSet(new TLabel('Funcionário'), '')->class = 'tformtitle';
         
         // create the form fileds
         $filter = new TEntry('name');
         $filter->setSize(400);
         $filter->setValue(TSession::getValue('Member_name'));
         
         // add a row for the filter field
         $row = $table->addRow();
         $row->addCell(new TLabel('Nome') . ': '));
         $row->addCell($filter);
         
         // create action button to the form
         $find_button = new TButton('find');
         // define the button action
         $find_button->setAction(new TAction(array($this, 'onSearch')), 'Procurar');
         $find_button->setImage('ico_find.png');
         
         $table->addRowSet('', array($find_button))->class = 'tformaction';
         
         // define which are the form fields
         $this->form->setFields(array($filter, $find_button));
         
         // creates a Datagrid
         $this->datagrid = new TQuickGrid();
         $this->dataGrid->setHeight(320);
         
         // creates the datagrid columns
         $this->datagrid->addQuickColumn('Codigo', 'id', 'right', 50, new TAction(array($this, 'onReload'), array('order', 'id')));
     }
}
?>