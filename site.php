<?php

	use \Hcode\Page;
	use \Hcode\Model\Product;

    // Rota Home Page Site
	$app->get("/", function() {
		$products = Product::listAll();
		$page = new Page();
		$page->setTpl("index",[
			"products"=>Product::checkList($products)
		]);
	});
?>