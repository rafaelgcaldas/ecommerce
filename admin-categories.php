<?php

	use \Hcode\PageAdmin;
	use \Hcode\Model\User;
	use \Hcode\Model\Category;

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
?>