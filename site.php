<?php

	use \Hcode\Page;

    // Rota Home Page Site
	$app->get("/", function() {
		$page = new Page();
		$page->setTpl("index");
	});
?>