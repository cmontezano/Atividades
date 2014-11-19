<?php
class Project extends TRecord
{
    const TABLENAME = 'projeto';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';
    
    public function clear_parts()
    {
        $this->atividades = array();
    }
    
    public function addAtividade($atividade)
    {
        $this->atividades[] = $atividade;
    }
    
    public function getAtividades()
    {
        return $this->atividades;
    }
    
    public function load($id)
    {
        $atividades_rep  = new TRepository('Atividade');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('projeto_id', '=', $id));
        
        // load the Author aggregates
        $atividades = $atividades_rep->load($criteria);
        if ($atividades)
        {
            foreach ($atividades as $atividade)
            {
                $ativ = new Atividade($atividade->id);
                $this->addAtividade($ativ);
            }
        }
        
        // load the object itself
        return parent::load($id);
    }
}