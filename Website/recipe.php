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
    header('Location: login.html');
    exit;
}

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
<body>
  <header>

    <div id="logo">
      <a href="index.html"><img src="images/logo/logo.png" width="50" height="50">
    </a>
    </div>

    <div class="simpleSearch">
      <form action="/action_page.php">
        <input type="text" placeholder="What do you want to eat today?" name="search";>
        <button type="submit">Go</button>
      </form>
    </div>

    <nav>
      <ul>
        <li><a href="#">All Recipes</a></li>
        <li class="dropdown">
            <a href="javascript:void(0)" class="dropbtn">
                <img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fi.pinimg.com%2Foriginals%2Fc7%2Fab%2Fcd%2Fc7abcd3ce378191a3dddfa4cdb2be46f.png&f=1&nofb=1" alt="authorization icon" width="30">
            </a>
            <div class="dropdown-content" id="myDropdown">
                <a href="#">Your Profile</a>
                <a href="login.html">Log in</a>
                <a href="register.html">Register</a>
            </div>
        </li>
    </ul>
    </nav>
    <div class="dropdown">

  </header>
  <div class="container">
      <div class="main">

        <div class="recipe-image">
            <img class="image-link" src="<?php echo "/flavourfinds/Website" .
                htmlspecialchars(
                    $recipeDetails["picture_url"]
                ); ?>" alt="<?php echo htmlspecialchars(
    $recipeDetails["title"]
); ?>">
        </div>
         <div class="recipe-info">
            <h2 class="title"><?php echo htmlspecialchars(
                $recipeDetails["title"] ?? "Recipe Title"
            ); ?></h2>
            <div class="favourites" id="favouritesDiv">
                <img src="images/<?php echo $isFavourite ? 'heart' : 'whiteheart'; ?>.png" alt="heart icon" id="favouriteIcon" data-user-id="<?php echo $_SESSION['user_id']; ?>" data-recipe-id="<?php echo $recipeId; ?>" style="cursor:pointer;">
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
                    <img src="images/clock.png" alt="clock icon">
                    <p class="num-minutes"><?php echo htmlspecialchars(
                        $recipeDetails["preparation_time"] ?? "0"
                    ); ?> </p>
                </div>
                <div class="people">
                    <img src="images/man.png" alt="man icon">
                    <p class="num-people"><?php echo htmlspecialchars(
                        $recipeDetails["nr_served"] ?? "N/A"
                    ); ?> people</p>
                </div>
              </div>

              <div class="rating">
                  <p class="star-rating">
                        <?php
                        // Display filled stars
                        for ($i = 0; $i < floor($averageRating); $i++) {
                            echo '<img class="star" src="images/star.png" alt="Full Star">'; 
                        }
                        // Display half star where applicable
                        if (floor($averageRating) < $averageRating) {
                            echo '<img class="star" src="images/halfstar.png" alt="Half Star">';
                            $i++; // Increment to avoid an extra empty star
                        }
                        // Display empty stars
                        for ($i; $i < 5; $i++) {
                            echo '<img class="star" src="images/emptystar.png" alt="Empty Star">'; 
                        }
                        ?>
                  </p>
              </div>
            </div>
            <div class="ingredients">
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
        </div>



         <div class="method">
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
        </div>


   <div class="rating-box">
              <h2>Rate this recipe!</h2>
              <!-- <select class= "rating" name="rating" id="rating">
          <option value="1">1 star</option>
          <option value="2">2 stars</option>
          <option value="3">3 stars</option>
          <option value="4">4 stars</option>
          <option value="5">5 stars</option>
        </select> -->

              <p class="star-rating">
                  <i class="my-star star-1" data-star="1"></i>
                  <i class="my-star star-2" data-star="2"></i>
                  <i class="my-star star-3" data-star="3"></i>
                  <i class="my-star star-4" data-star="4"></i>
                  <i class="my-star star-5" data-star="5"></i>
              </p>
              <input type="number" readonly id="output">
              <button type="submit">Rate!</button>
          </div>


        <div class="tips">
            <h2>Tips</h2>
            <ul>
                <?php foreach ($tips as $tip): ?>
                    <li><?php echo htmlspecialchars(
                        $tip["tip_description"]
                    ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
      </div>
      <footer class="landing-footer">

      </footer>
  </div>

  <script src="main.js"></script>
</body>
</html>
