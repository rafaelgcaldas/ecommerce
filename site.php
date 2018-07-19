<?php

	use \Hcode\Page;
	use \Hcode\Model\Product;
	use \Hcode\Model\Category;

    // Rota Home Page Site
	$app->get("/", function() {
		$products = Product::listAll();
		$page = new Page();
		$page->setTpl("index",[
			"products"=>Product::checkList($products)
		]);
	});

	$app->get("/categories/:idCategory", function($idCategory) {
		$category = new Category();
		$page = new Page();

		$category->get((int)$idCategory);
		$page->setTpl("category",[
			"category"=>$category->getValues(),
			"products"=>Product::checkList($category->getProducts())
		]);
	});
?>