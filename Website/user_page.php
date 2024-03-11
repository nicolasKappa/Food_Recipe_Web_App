﻿<?php
session_start();

// Redirect user to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once "../config/dbconfig.php";
$conn = getConnection();
$current_script_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . $current_script_path . '/';

// Get user's favorite recipes
$user_id = $_SESSION['user_id'];
$favoriteRecipes = [];
if ($stmt = $conn->prepare("CALL sp_get_user_favourite_recipes(?)")) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Round the average rating to the nearest whole number
        $row['average_rating'] = round($row['average_rating']);
        // Prepend the relative path prefix to the picture URL
        $row['picture_url'] = "/flavourfinds/Website" . $row['picture_url'];
        $favoriteRecipes[] = $row;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User page</title>
    <link rel="stylesheet" href="user_page_search_results.css">
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
                <header class="catalog-header">
                    <div class="catalog-title">
                        <h1>Hi, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h1>
                        <h2>Your favorite recipes</h2>
                    </div>
                </header>

                <div class="catalog-content">
                    <ul class="goods-list">
                        <?php foreach ($favoriteRecipes as $recipe): ?>
                        <li class="goods-item">
                            <div class="product">
                                <div class="product-header">
                                    <img src="<?php echo htmlspecialchars($recipe['picture_url']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                                </div>

                                <div class="product-content">
                                    <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="product-title"><?php echo htmlspecialchars($recipe['title']); ?></a>

                                    <div class="product-btns">
                                        <div class="product-rating">
                                            <span><?php echo htmlspecialchars(round($recipe['average_rating'])); ?></span>
                                            <img src="images/user-page/star-icon.svg" alt="Rating">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
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
