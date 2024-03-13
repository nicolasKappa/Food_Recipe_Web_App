<?php
session_start();

// Redirect user to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize session variables for search and sorting if not set
if (!isset($_SESSION['sort_by'])) {
    $_SESSION['sort_by'] = ""; 
}
if (!isset($_SESSION['selected_category_id'])) {
    $_SESSION['selected_category_id'] = 0; // Default category ID, "All Categories"
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
        $row['picture_url'] = $base_url . ltrim($row['picture_url'], '/');
        $favoriteRecipes[] = $row;
    }
    $stmt->close();
}

$userFullName = ''; // Initialize variable to store user's full name

// Get user's full name
if ($stmt = $conn->prepare("CALL sp_get_user_full_name(?)")) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $userFullName = $row['full_name'];
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
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body>
<style>
    @import url("user_page_search_results.css");
</style>>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-logo">
                    <a href="./index.php" class="logo">
                        <img src="images/logo/logo.png" width="50" height="50" alt="logo">
                    </a>
                </div>
<!-- Search bar: user can search recipes here -->
                <form action="search_results.php" method="get" class="header-form">
                    <input type="search" name="search" placeholder="What do you want to eat today?" id="search-input">
                    <button type="submit">Search</button>
                </form>

                <nav>
                    <ul class="header-nav">
                        <li class="header-nav-item">
<!-- Link to redirect to the common page with recipes -->
                            <a href="search_results.php">All recipes</a>
                        </li>
                        <li class="header-nav-item dropdown">
                            <a href="javascript:void(0)" class="dropbtn" onclick="myFunction()">
                                <img src="images/icons/auth-icon.png" alt="User Profile" width="30">
                            </a>
<!-- Dropdown menu button for logout -->
                            <div class="dropdown-content" id="myDropdown" aria label="user search">
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
<!-- User's name is displayed ensuring the user is logged in in their profile -->
                <header class="catalog-header">
                    <div class="catalog-title">
                        <h1>Hi, <?php echo htmlspecialchars($userFullName); ?>!</h1>
                        <h2>Your favorite recipes</h2>
                    </div>
                </header>
<!-- Here cards with chosen recipes are displayed -->
                <div class="catalog-content">
                    <ul class="goods-list">
                        <?php foreach ($favoriteRecipes as $recipe): ?>
                        <li class="goods-item">
                            <div class="product">
<!-- Photo of the dish -->
                                <div class="product-header">
                                    <img src="<?php echo htmlspecialchars($recipe['picture_url']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                                </div>

                                <div class="product-content">
                                    <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="product-title"><?php echo htmlspecialchars($recipe['title']); ?></a>
<!-- Recipe's rating is displayed here -->
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

        </div>
    </footer>
     <script>
        window.onload = function() {
            document.getElementById('search-input').focus();
        };
    </script>
</body>
</html>
