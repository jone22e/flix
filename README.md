# Flix PHP Framework

###Select

Criando um select diretamente

````php
(new Select())->create(['id' => 'mysel'])
              ->addItem(["id" => "1", "name" => "SIM"])
              ->addItem(["id" => "0", "name" => "NÃO"])
              ->addItem(["id" => "2", "name" => "TALVEZ", "selected"=>true])
              ->render();
````

Buscando dados do banco e exibindo no select

````php
$ds = new Datasource();

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