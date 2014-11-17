<?php
class Usuario extends TRecord
{
    const TABLENAME  = 'usuario';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';
    
    
    /**
     * Autenticate the user
     * @param $login User login
     * @param $password User password
     */
    static public function autenticate($login, $password)
    {
        $user = self::newFromLogin($login);
        
        if ($user instanceof Usuario) {
            if (isset($user->{'password'}) AND ($user->{'password'} == $password)) {
                return true;
            } else {
                throw new Exception('Senha incorreta');
            }
        } else {
            throw new Exception('Usuário não encontrado');
        }
    }
    
    /**
     * Retorna uma instância de usuário a partir do login
     * @param $login Login de usuário
     */
    static public function newFromLogin($login)
    {
        $repository = new TRepository('Usuario');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('login', '=', $login));
        $objects = $repository->load($criteria);
        if (isset($objects[0])) {
            return $objects[0];
        }
    }
    
    public function get_role()
    {
        $role = new Papel($this->id_papel);
        return $role;
    }
}

?>