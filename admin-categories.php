<?php

	use \Hcode\PageAdmin;
	use \Hcode\Model\User;
	use \Hcode\Model\Category;
	use \Hcode\Model\Product;

    $app->get("/admin/categories", function(){
		User::verifyLogin();

		$search = (isset($_GET["search"])) ? $_GET["search"] : "";
		$page = (isset($_GET["page"])) ? $_GET["page"] : 1;
		
		$categories = Category::listAll();

		if($search != ""){
			$pagination = Category::getPageSearch($search, $page);
		} else {
			$pagination = Category::getPage($page);
		}

		$pages = [];

		for ($x=0; $x < $pagination["pages"]; $x++) { 
			array_push($pages, [
				"href"=>"/admin/users?".http_build_query([
					"page"=>$x + 1,
					"search"=>$search
				]),
				"text"=>$x + 1
			]);
		}

		$page = new PageAdmin();

		$page->setTpl("categories", [
			"categories"=>$pagination["data"],
			"search"=>$search,
			"pages"=>$pages
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

	$app->get("/admin/categories/:idCategory/products", function($idCategory){
		User::verifyLogin();
		$category = new Category();
		$page = new PageAdmin();

		$category->get((int)$idCategory);
		$page->setTpl("categories-products",[
			"category"=>$category->getValues(),
			"productsRelated"=>$category->getProducts(),
			"productsNotRelated"=>$category->getProducts(false)
		]);

	});

	$app->get("/admin/categories/:idCategory/products/:idProduct/add", function($idCategory, $idProduct){
		User::verifyLogin();
		$category = new Category();
		$product = new Product();

		$category->get((int)$idCategory);
		$product->get((int)$idProduct);
		$category->addProduct($product);

		header("Location: /admin/categories/". $idCategory ."/products");
		exit;
	});

	$app->get("/admin/categories/:idCategory/products/:idProduct/remove", function($idCategory, $idProduct){
		User::verifyLogin();
		$category = new Category();
		$product = new Product();

		$category->get((int)$idCategory);
		$product->get((int)$idProduct);
		$category->removeProduct($product);

		header("Location: /admin/categories/". $idCategory ."/products");
		exit;
	});
?>