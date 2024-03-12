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

$userLoggedIn = isset($_SESSION['user_id']); //check for if user is logged in, used to control header data

// Initialize an empty array to hold recipe details
$recipeDetails = [];
$ingredients = [];
$steps = [];
$tips = [];

// Check if 'id' is present in the query string and is a number
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $recipeId = intval($_GET["id"]);

    // Create a new database connection
    $conn = getConnection();
$current_script_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'] . $current_script_path . '/';

    // Verify that the database connection was successfully established
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement for execution to call the stored procedure for recipe details
    if (
        $stmt = $conn->prepare(
            "CALL `flavour_finds`.`sp_get_recipe_details`(?)"
        )
    ) {
        // Bind the parameter to the SQL statement
        $stmt->bind_param("i", $recipeId);
        // Execute statements and retrieve the result
        $stmt->execute();
        $result = $stmt->get_result();
        $recipeDetails = $result->fetch_assoc();
        $stmt->close();
    }

    // For ingredients
    if (
        $stmt = $conn->prepare(
            "CALL `flavour_finds`.`sp_get_recipe_ingredients`(?)"
        )
    ) {
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $ingredients[] = $row;
        }
        $stmt->close();
    }

    // For steps
    if (
        $stmt = $conn->prepare("CALL `flavour_finds`.`sp_get_recipe_steps`(?)")
    ) {
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $steps[] = $row;
        }
        $stmt->close();
    }

    // For tips
    if (
        $stmt = $conn->prepare("CALL `flavour_finds`.`sp_get_recipe_tips`(?)")
    ) {
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $tips[] = $row;
        }
        $stmt->close();
    }

    // Prepare the statement for the stored procedure call
    $isFavourite = false;

    if ($user_id && $recipeId) {
        if (
            $stmt = $conn->prepare("CALL `flavour_finds`.`sp_get_user_favourite_recipe`(?, ?)")
        ) {
            $stmt->bind_param("ii", $user_id, $recipeId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $isFavourite = true;
            }
            $stmt->close();
        }
    }

    // For Recipe Average Rating
    // Initialize variable to hold average rating
    $averageRating = 0;

    // SQL statement for execution to call the stored procedure for average rating
    if (
        $stmt = $conn->prepare(
            "CALL `flavour_finds`.`sp_get_average_rating`(?)"
        )
    ) {
        // Bind the parameter to the SQL statement
        $stmt->bind_param("i", $recipeId);
        // Execute the statement and retrieve the average rating
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $averageRating = $row["average_rating"];
        }
        $stmt->close();
    }
    // Convert average to nearest half for star rating display
    $averageRating = round($averageRating * 2) / 2;


    $currentRating = 0; // Default to 0, meaning no rating for current user
    if ($user_id && $recipeId) {
        if ($stmt = $conn->prepare("CALL `flavour_finds`.`sp_get_user_rating`(?, ?)")) {
            $stmt->bind_param("ii", $user_id, $recipeId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $currentRating = $row['rating'];
            }
            $stmt->close();
        }
    }
    // Close database connection
    $conn->close();
} else {
    echo "Invalid recipe ID.";
    exit(); // Exit if the Recipe ID is not valid
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe App</title>
    <!--link to style sheet-->
    <link rel="stylesheet" href="StylesheetRecipeRegisterLogin.css">
   </head>
<body data-user-id="<?= $user_id ?>" data-recipe-id="<?= $recipeId ?>">
<!--header element-->
  <header>
    <div id="logo">
      <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo"></a>
    </div>

    <!--search bar in header-->
    <div class="simpleSearch">
    <form id="search-form" action="search_results.php" role="search">
			<input id="search-bar" type="text" placeholder="What do you want to eat today?" name="search" aria-label="Search";>
        <button type="submit">Go</button>
      </form>
    </div>

    <nav>    <!--logic to show different items on the dropdown menu depending on whether the user is logged in-->
      <ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="search_results.php">All Recipes</a></li>
        <li class="dropdown">
            <a href="javascript:void(0)" class="dropbtn">
                <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
            </a>
            <div class="dropdown-content" id="myDropdown" aria-label="User Menu">
                <a href="user_page.php">Your Profile</a>
                <a href="logout.php">Log Out</a>
            </div>
        </li>
<?php else: ?>
            <li><a href="login.php">Log in</a></li>
            <li><a href="register.php">Register</a></li>
          <?php endif; ?>
    </ul>
    </nav>
      </header>
  <div class="container">
      <div class="main">
        <div class="recipe-image">
            <img class="image-link" src="<?php echo $base_url .
                htmlspecialchars($recipeDetails["picture_url"]); ?>" alt="<?php echo htmlspecialchars($recipeDetails["title"]); ?>">

        </div>
         <section class="recipe-info">
            <h2 class="title"><?php echo htmlspecialchars(
                $recipeDetails["title"] ?? "Recipe Title"
            ); ?></h2>
            <div class="favourites" id="favouritesDiv">
                <img src="images/icons/<?php echo $isFavourite ? 'heart' : 'whiteheart'; ?>.png" alt="heart icon" id="favouriteIcon" data-user-id="<?php echo $_SESSION['user_id']; ?>" data-recipe-id="<?php echo $recipeId; ?>" data-is-favourite="<?php echo $isFavourite ? 'true' : 'false'; ?>" style="cursor:pointer;">
                <p id="favouritesText"><?php echo $isFavourite ? 'Remove from favourites' : 'Add to favourites'; ?></p>
            </div>
            <h5 class="description"><?php echo htmlspecialchars(
                $recipeDetails["description"] ??
                    "Recipe description not available."
            ); ?></h5>

            <h5 class="categories"><?php echo htmlspecialchars(
                implode(", ", $recipeDetails["categories"] ?? [])
            ); ?></h5>
            <div class="time-people">
                <div class="time">
                    <img src="images/icons/clock.png" alt="clock icon">
                    <p class="num-minutes"><?php echo htmlspecialchars(
                        $recipeDetails["preparation_time"] ?? "0"
                    ); ?> </p>
                </div>
                <div class="people">
                    <img src="images/icons/man.png" alt="man icon">
                    <p class="num-people"><?php echo htmlspecialchars(
                        $recipeDetails["nr_served"] ?? "N/A"
                    ); ?> people</p>
                </div>
              </div>

              <div class="average-rating">
                  <h2>Average Rating</h2>
                  <p class="star-rating">
                        <?php
                        // Display filled stars
                        for ($i = 0; $i < floor($averageRating); $i++) {
                            echo '<img class="star" src="images/icons/star.png" alt="Full Star">';
                        }
                        // Display half star where applicable
                        if (floor($averageRating) < $averageRating) {
                            echo '<img class="star" src="images/icons/halfstar.png" alt="Half Star">';
                            $i++; // Increment to avoid an extra empty star
                        }
                        // Display empty stars
                        for ($i; $i < 5; $i++) {
                            echo '<img class="star" src="images/icons/emptystar.png" alt="Empty Star">';
                        }
                        ?>
                  </p>
              </div>
                      </section>
            <section class="ingredients">
            <h2>Ingredients</h2>
            <?php
            // Organizing the ingredients by section.
            $ingredientsBySection = [];
            foreach ($ingredients as $ingredient) {
                $section = $ingredient["ingredient_section"] ?: "Main"; // Default to 'Main' if no ingredient section is available in db
                $ingredientsBySection[$section][] = $ingredient;
            }

            // Looping through each section to display its ingredients.
            foreach ($ingredientsBySection as $section => $ingredientsList) {
                // Display the section heading
                echo "<h3>" .
                    htmlspecialchars($section === "Main" ? "" : $section) .
                    "</h3>";
                echo "<ul>";
                foreach ($ingredientsList as $ingredient) {
                    echo "<li>" .
                        htmlspecialchars($ingredient["full_sentence"]) .
                        "</li>";
                }
                echo "</ul>";
            }
            ?>
        </section>



         <section class="method">
            <h2>Method</h2>
            <ol class="steps">
                <?php foreach ($steps as $step): ?>
                    <li><?php echo htmlspecialchars(
                        $step["step_description"]
                    ); ?>
                        <?php if (
                            isset($step["minutes_needed"]) &&
                            $step["minutes_needed"] > 0
                        ): ?>
                            - Approx. <?php echo htmlspecialchars(
                                $step["minutes_needed"]
                            ); ?> mins
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
                          </section>

    <section class="rating-box">
              <h2>Rate this recipe!</h2>
              <p class="star-rating" id="userRating">
          <?php for($i = 1; $i <= 5; $i++): ?>
          <img src="images/icons/<?= $i <= $currentRating ? 'star' : 'emptystar'; ?>.png" class="star" data-star="<?= $i ?>" alt="<?= $i ?> Star" style="cursor:pointer;">
                <?php endfor; ?>
            </p>
        </section>

        <section class="tips">
            <h2>Tips</h2>
            <!-- If there are not tips to display, the following message will be displayed: -->
            <?php if (empty($tips)): ?>
          <p>There are no tips to display!</p>
           <?php else: ?>
            <ul>
                <?php foreach ($tips as $tip): ?>
                    <li><?php echo htmlspecialchars(
                        $tip["tip_description"]
                    ); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
                  </section>
      </div>
      <footer class="landing-footer">

      </footer>
  </div>

  <script src="main.js"></script>
</body>
</html>
