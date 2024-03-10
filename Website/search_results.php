<?php
// Start or continue user session once logged in
session_start();

// Import the database connection settings
require_once "../config/dbconfig.php";

// Check if the user is logged in, using the session variable set during login
if(isset($_SESSION['user_id'])) {
    // Retrieve user ID from the session
    $user_id = $_SESSION['user_id'];
} else {
    // If the session variable is not set, redirect to the login page
    header('Location: login.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Search page</title>
	<style>
		@import url('https://fonts.cdnfonts.com/css/trebuchet-ms-2');
		@import url("user_page_search_results.css");
	</style>
</head>

<body>
   <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-logo">
                    <a href="./index.php" class="logo">
                        <img src="images/logo/logo.png" width="50" height="50" alt="logo">
                    </a>
                </div>

                <form action="search_results.php" method="get" class="header-form">
                    <input type="search" name="search" placeholder="What do you want to eat today?" id="search-input">
                    <button type="submit">Search</button>
                </form>

                <nav>
                    <ul class="header-nav">
                        <li class="header-nav-item">
                            <a href="search_results.php">All recipes</a>
                        </li>
                        <li class="header-nav-item dropdown">
                            <a href="javascript:void(0)" class="dropbtn" onclick="myFunction()">
                                <img src="images/icons/auth-icon.png" alt="User Profile" width="30">
                            </a>
                            <div class="dropdown-content" id="myDropdown">
                                <a href="user_page.php">Your Profile</a>    
                                <a href="logout.php">Log Out</a>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

	<main>
		<div class="container">
			<section class="catalog">
				<header class="catalog-header catalog-result-header">
					<form action="" class="catalog-result-filter">
						<input type="search" name="catalog-search-input" placeholder="Chicken"
							id="catalog-search-input">

						<select name="filter-by-input" id="filter-by-input">
							<option disabled selected hidden>Filter by...</option>
							<option>Тест 1</option>
							<option>Тест 2</option>
							<option>Тест 3</option>
						</select>
					</form>
				</header>

				<div class="catalog-content">
					<ul class="goods-list">
						<li class="goods-item">
							<div class="product">
								<div class="product-header">
									<img src="images/user-page/product-1.jpg" alt="Fried chicken">
								</div>

								<div class="product-content">
									<a href="#product-1" class="product-title">Fried chicken</a>

									<div class="product-btns">
										<div class="product-rating">
											<span>4</span>
											<img src="images/user-page/star-icon.svg" alt="4">
										</div>
									</div>
								</div>
							</div>
						</li>

						<li class="goods-item">
							<div class="product">
								<div class="product-header">
									<img src="images/user-page/product-2.jpg" alt="Fried chicken">
								</div>

								<div class="product-content">
									<a href="#product-2" class="product-title">Fried chicken</a>

									<div class="product-btns">
										<div class="product-rating">
											<span>4</span>
											<img src="images/user-page/star-icon.svg" alt="4">
										</div>
									</div>
								</div>
							</div>
						</li>

						<li class="goods-item">
							<div class="product">
								<div class="product-header">
									<img src="images/user-page/product-3.jpg" alt="Fried chicken">
								</div>

								<div class="product-content">
									<a href="#product-3" class="product-title">Fried chicken</a>

									<div class="product-btns">
										<div class="product-rating">
											<span>2</span>
											<img src="images/user-page/star-icon.svg" alt="2">
										</div>
									</div>
								</div>
							</div>
						</li>

						<li class="goods-item">
							<div class="product">
								<div class="product-header">
									<img src="images/user-page/product-4.jpg" alt="Fried chicken">
								</div>

								<div class="product-content">
									<a href="#product-4" class="product-title">Fried chicken</a>

									<div class="product-btns">
										<div class="product-rating">
											<span>3</span>
											<img src="images/user-page/star-icon.svg" alt="3">
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</section>
		</div>
	</main>

	<footer class="footer">
		<div class="container-fluid">
			Footer
		</div>
	</footer>
</body>

</html>