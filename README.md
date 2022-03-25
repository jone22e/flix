# Flix PHP Framework

* Framework em desenvolvimento

## Conexão com banco de dados

````php
use Flix\FW\Database\Datasource\Postgres;

$db = new Postgres(['host'=>'localhost', 'database'=>'mydatabase', 'password'=>'']);
````
## Dataset

````php
use Flix\FW\Database\Dataset\Dataset;

$ds = new Dataset($db);

# Buscar um usuário
$user = $ds->table('usuario')->find($id);

echo $user->nome;

# Listar usuários
$users = $ds->table('usuario')->get();
foreach ($users as $user) {
    echo $user->nome.'<br>';
}

# Listar usuários com filtro
$users = $ds->table('usuario')
            ->where('excluido',0)
            ->where('inativo',0)
            ->order('id asc')
            ->order('nome asc')
            ->get();
foreach ($users as $user) {
    echo $user->nome.'<br>';
}

# Inserir
$ds->table('usuario')->insert([
   'nome' => 'Novo usuario'
]);

# Inserir e pegar o último inserido
$id = $ds->table('usuario')->insertGetId([
   'nome' => 'Novo usuario'
]);

# Atualizar
$ds->table('usuario')->where('id', 1)
                     ->update(['nome' => 'Novo nome']);

# Marcar excluido
$ds->table('usuario')->where('id', 1)->delete();

# Forçar delete
$ds->table('usuario')->where('id', 1)->delete(false);

# Delete sem where unlocker
$ds->table('usuario')->delete(false, true);
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
    "encryptionkey" => "minha key para criptgrafia",// or false
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

## Componentes Visuais

### Select

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
