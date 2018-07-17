<?php 
	session_start();
	require_once("vendor/autoload.php");

	use \Slim\Slim;
	use \Hcode\Page;
	use \Hcode\PageAdmin;
	use \Hcode\Model\User;
	use \Hcode\Model\Category;

	$app = new Slim();

	$app->config("debug", true);

	// Rota Home Page Site
	$app->get("/", function() {
		$page = new Page();
		$page->setTpl("index");
	});

	// Rota Home Page Admin
	$app->get("/admin", function() {
		User::verifyLogin();
		$page = new PageAdmin();
		$page->setTpl("index");
	});

	// Rota Page Login
	$app->get("/admin/login", function(){
		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);
		$page->setTpl("Login");
	});

	// Rota método POST Login
	$app->post("/admin/login", function(){
		User::login($_POST["login"], $_POST["password"]);
		header("Location: /admin");
		exit;
	});

	// Rota Logout
	$app->get("/admin/logout", function(){
		User::logout();
		header("Location: /admin/login");
		exit;
	});

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

	// Rota Page Forgot
	$app->get("/admin/forgot/", function(){
		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);
		$page->setTpl("forgot");
	});

	$app->post("/admin/forgot", function(){
		$user = User::getForgot($_POST["email"]);

		header("Location: /admin/forgot/sent");
		exit;
	});

	$app->get("/admin/forgot/sent", function(){
		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);
		$page->setTpl("forgot-sent");
	});

	$app->get("/admin/forgot/reset", function(){

		$user = User::validForgotDecrypt($_GET["code"]);

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false	
		]);
		$page->setTpl("forgot-reset", array(
			"name"=>$user["desperson"],
			"code"=>$_GET["code"]
		));
	});

	$app->post("/admin/forgot/reset", function(){
		$forgot = User::validForgotDecrypt($_POST["code"]);
		User::setForgotUsed($forgot["idrecovery"]);

		$user = new User();
		$user->get((int)$forgot["iduser"]);
		$password = password_hash($_POST["password"], PASSWORD_DEFAULT,[
			"cost"=>12
		]);
		$user->setPassword($password);

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false	
		]);
		$page->setTpl("forgot-reset-success");
	});

	$app->get("/admin/categories", function(){
		User::verifyLogin();
		$page = new PageAdmin();

		$categories = Category::listAll();

		$page->setTpl("categories", [
			"categories"=>$categories
		]);
	});

	$app->get("/admin/categories/create", function(){
		User::verifyLogin();
		$page = new PageAdmin();

		$page->setTpl("categories-create");
	});

	$app->post("/admin/categories/create", function(){
		User::verifyLogin();
		$category = new Category();

		$category->setData($_POST);
		$category->save();

		header("Location: /admin/categories");
		exit;
	});

	$app->get("/admin/categories/:idCategory/delete", function($idCategory){
		User::verifyLogin();
		$category = new Category();

		$category->get((int)$idCategory);
		$category->delete();

		header("Location: /admin/categories");
		exit;
	});

	$app->get("/admin/categories/:idCategory", function($idCategory){
		User::verifyLogin();
		$category = new Category();
		$page = new PageAdmin();

		$category->get((int)$idCategory);
		$page->setTpl("categories-update", array(
			"category"=>$category->getValues()
		));
	});

	$app->post("/admin/categories/:idCategory", function($idCategory){
		User::verifyLogin();
		$category = new Category();

		$category->get((int)$idCategory);
		$category->setData($_POST);
		$category->save();

		header("Location: /admin/categories");
		exit;
	});

	$app->run();

?>