<?php
session_start();

// Import the database connection settings
require_once "../config/dbconfig.php";
// Import categories for dropdown
require_once "get_categories.php";

// Redirect user to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = getConnection();
$current_script_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . $current_script_path . '/';

// Get search term and other filtering parameters from GET request
$searchTerm = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
$categoryId = filter_input(INPUT_GET, "category_id", FILTER_SANITIZE_NUMBER_INT);

// Store the last sort by after searching
if (isset($_GET['sort_by'])) {
    $_SESSION['sort_by'] = $_GET['sort_by'];
}

// Get the sort option from the session 
$sortBy = isset($_SESSION['sort_by']) ? $_SESSION['sort_by'] : null;


//Get last selected values for dropdowns after searching
$selectedCategoryId = isset($_GET["category_id"]) ? filter_input(INPUT_GET, "category_id", FILTER_SANITIZE_NUMBER_INT) : (isset($_SESSION['selected_category_id']) ? $_SESSION['selected_category_id'] : 0);

// Store the last selected category ID in the session
$_SESSION['selected_category_id'] = $selectedCategoryId;

// Check if categories were retrieved successfully 
if (isset($categories) && !empty($categories)) {
   $category_options = "";
   foreach ($categories as $category) {
       // Check if category from foreach loop should be marked as selected from last search
       $selectedAttr = ($category['category_id'] == $selectedCategoryId) ? "selected" : "";
       $category_options .= "<option value='" . $category['category_id'] . "' " . $selectedAttr . ">" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "</option>";
   }
} else {
   $category_options = "<option value=''>No categories available</option>";
}

// Get all recipes
$recipes = [];
if ($stmt = $conn->prepare("CALL sp_get_recipes(?, ?, ?)")) {
  $stmt->bind_param("sss", $searchTerm, $categoryId, $sortBy);
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
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
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
                <nav>
                    <ul class="header-nav">
                        <li class="header-nav-item">
                            <a href="search_results.php">All recipes</a>
                        </li>
                        <li class="header-nav-item dropdown">
                            <a href="javascript:void(0)" class="dropbtn" onclick="myFunction()">
                                <img src="images/icons/auth-icon.png" alt="User Profile" width="30">
                            </a>
                            <div class="dropdown-content" id="myDropdown" aria-label="user search">
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
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" class="header-form">
                    <input type="search" name="search" placeholder="What do you want to eat today?" id="search-input" value="<?php echo htmlspecialchars($searchTerm); ?>">

                    <select name="category_id" id="category-filter">
                        <option value="0" <?php echo $selectedCategoryId == 0 ? "selected" : ""; ?>>All Categories</option>
                        <?php echo isset($category_options) ? $category_options : ''; ?>
                    </select>

                    <button type="submit">Search</button>

                   <select name="sort_by" id="sort-by">
                        <option value="">Sort By</option>
                        <option value="title_asc" <?php echo $sortBy == "title_asc" ? "selected" : ""; ?>>Title (ASC)</option>
                        <option value="title_desc" <?php echo $sortBy == "title_desc" ? "selected" : ""; ?>>Title (DESC)</option>
                        <option value="nr_served_asc" <?php echo $sortBy == "nr_served_asc" ? "selected" : ""; ?>>Nr Served (ASC)</option>
                        <option value="nr_served_desc" <?php echo $sortBy == "nr_served_desc" ? "selected" : ""; ?>>Nr Served (DESC)</option>
                        <option value="average_rating_asc" <?php echo $sortBy == "average_rating_asc" ? "selected" : ""; ?>>Average Rating (ASC)</option>
                        <option value="average_rating_desc" <?php echo $sortBy == "average_rating_desc" ? "selected" : ""; ?>>Average Rating (DESC)</option>
                    </select>
                </form>
                <br/>

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
    <script src="main.js"></script>
    <script>
        window.onload = function() {
            document.getElementById('search-input').focus();
        };
    </script>
</body>
</html>
