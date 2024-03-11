<?php
session_start();

// Import the database connection settings
require_once "../config/dbconfig.php";

// Redirect user to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = getConnection();
$current_script_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . $current_script_path . '/';



// Get all recipes
$recipes = [];
if ($stmt = $conn->prepare("CALL sp_get_recipes()")) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Append the relative path prefix to the picture URL
        $row['picture_url'] = $base_url . ltrim($row['picture_url'], '/');
        $recipes[] = $row;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search page</title>
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
                <!-- Catalog Header for searching -->

                <div class="catalog-content">
                    <ul class="goods-list">
                        <?php foreach ($recipes as $recipe): ?>
                        <li class="goods-item">
                            <div class="product">
                                <div class="product-header">
                                    <img src="<?php echo htmlspecialchars($recipe['picture_url']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                                </div>

                                <div class="product-content">
                                    <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="product-title"><?php echo htmlspecialchars($recipe['title']); ?></a>

                                    <div class="product-btns">
                                        <div class="product-rating">
                                            <span><?php echo htmlspecialchars(round($recipe['average_rating'] * 2) / 2); ?></span>
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
        <div class="container-fluid">Footer</div>
    </footer>
</body>
</html>