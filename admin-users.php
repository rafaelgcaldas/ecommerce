<?php
	use \Hcode\PageAdmin;
	use \Hcode\Model\User;

    // Rota Lista Todos Usuários
	$app->get("/admin/users", function(){
		User::verifyLogin();
		$page = new PageAdmin();

		$users = User::listAll();

		$page->setTpl("users", array(
			"users"=>$users
		));
	});

	// Rota Pagina Inserção de Usuários
	$app->get("/admin/users/create", function(){
		User::verifyLogin();
		$page = new PageAdmin();
		$page->setTpl("users-create");
	});

	// Rota Exclusão de um Usuário
	$app->get("/admin/users/:iduser/delete", function($iduser){
		User::verifyLogin();
		$user = new User();

		$user->get((int)$iduser);
		$user->delete();

		header("Location: /admin/users");
		exit;
	});

	// Rota Página Atualização de um Usuário
	$app->get("/admin/users/:iduser", function($iduser){
		User::verifyLogin();
		$user = new User();
		$page = new PageAdmin();

		$user->get((int)$iduser);
		$page->setTpl("users-update", array(
			"user"=>$user->getValues()
		));
	});

	// Rota Inserção de um Usuário
	$app->post("/admin/users/create", function(){
		User::verifyLogin();
		$user = new User();

		$_POST["inadmin"] = (isset($_POST["inadmin"]))? 1 : 0;
		$_POST["despassword"] = password_hash($_POST["despassword"], PASSWORD_DEFAULT,[
			"cost"=>12
		]);

		$user->setData($_POST);
		$user->save();

		header("Location: /admin/users");
		exit;
	});

	// Rota Atualização de um Usuário
	$app->post("/admin/users/:iduser", function($iduser){
		User::verifyLogin();
		$user = new User();

		$user->get((int)$iduser);
		$_POST["inadmin"] = (isset($_POST["inadmin"]))? 1 : 0;
		$user->setData($_POST);
		$user->update();

		header("Location: /admin/users");
		exit;
	});
?>