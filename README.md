# Flix PHP Framework

* Framework em desenvolvimento

## Select

Criando um select diretamente

````php
use Flix\FW\Components\Select;

(new Select())
->create(['id' => 'mysel'])
->addItem(["id" => "1", "name" => "SIM"])
->addItem(["id" => "0", "name" => "NÃO"])
->addItem(["id" => "2", "name" => "TALVEZ", "selected"=>true])
->render();
````

Buscando dados do banco e exibindo no select

````php
use Flix\FW\Database\Dataset\Dataset;
use Flix\FW\Components\Select;

$ds = new Dataset();

$usuarios = $ds->table('usuario')
               ->order('nome asc')
               ->get();

$select = new Select();
$select->create(['id'=>'usuario_id', 'class'=>"form-control"]);
foreach ($usuarios as $usuario) {
    $select->addItem([
        "id"=>$usuario->id, 
        "name"=>$usuario->nome, 
        "selected"=>($usuario->id==1?true:false)
    ]);
}
$select->render();
````

## Autenticação do usuário

````php
use Flix\FW\Auth\User;

# Configuração que pode ser gravada em outro arquivo e só importar 
$conf = [
    "storage"       => [
        "name"     => "userId",
        "settings" => ["encryped"],
        "table"    => "usuario",
        "columns"  => ["id", "nome", "email", "administrador as admin"]
    ],
    "encryptionkey" => "minha key para criptgrafia",//or false
    "redirect"      => [
        "uri" => "/login"
    ],
];

# Checagem do usuário
$user = (new User($db, $conf))->check();

# Resultado
echo $user->id . '<br>';
echo $user->nome . '<br>';
echo $user->email . '<br>';
echo $user->admin ? 'true' : 'false' . '<br>';
````