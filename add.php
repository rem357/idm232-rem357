<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add a Recipe</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 20px; max-width: 600px; }
    input, textarea, select { width: 100%; padding: 8px; margin: 6px 0 12px; }
    label { font-weight: bold; display: block; margin-top: 8px; }
    button { padding: 10px 14px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #004c99; }
    .success { padding: 10px; background: #e6ffe6; border: 1px solid #00b300; margin-bottom: 20px; }
  </style>
</head>
<body>
  <h1>Add a New Recipe</h1>
  <p><a href="index.php">← Back to recipes</a></p>

<?php
// this block runs *after* the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  // pull data out of the form using $_POST (from your lecture)
  $title = trim($_POST["title"]);
  $category = trim($_POST["category"]);
  $description = trim($_POST["description"]);
  $cook_time = (int)$_POST["cook_time"];
  $servings = (int)$_POST["servings"];

  // very basic validation (check if required fields are empty)
  $errors = [];
  if ($title === "") { $errors[] = "Title is required."; }
  if ($category === "") { $errors[] = "Category is required."; }
  if ($cook_time <= 0) { $errors[] = "Cook time must be greater than 0."; }

  // if no errors, show success
  if (empty($errors)) {
    echo "<div class='success'><strong>Recipe added!</strong><br>";
    echo "Title: " . htmlspecialchars($title) . "<br>";
    echo "Category: " . htmlspecialchars($category) . "<br>";
    echo "Description: " . htmlspecialchars($description) . "<br>";
    echo "Cook Time: " . htmlspecialchars($cook_time) . " minutes<br>";
    echo "Servings: " . htmlspecialchars($servings) . "</div>";
  } else {
    // show errors
    foreach ($errors as $e) {
      echo "<p style='color:red;'>⚠️ " . htmlspecialchars($e) . "</p>";
    }
  }
}
?>

  <!-- HTML FORM -->
  <form method="post" action="">
    <label for="title">Recipe Title:</label>
    <input type="text" id="title" name="title" placeholder="e.g. Lemon Pasta" required>

    <label for="category">Category:</label>
    <select id="category" name="category" required>
      <option value="">-- Choose one --</option>
      <option value="Poultry">Poultry</option>
      <option value="Beef">Beef</option>
      <option value="Seafood">Seafood</option>
      <option value="Vegetarian">Vegetarian</option>
    </select>

    <label for="description">Short Description:</label>
    <textarea id="description" name="description" rows="3" placeholder="Quick summary..."></textarea>

    <label for="cook_time">Cook Time (minutes):</label>
    <input type="number" id="cook_time" name="cook_time" min="1" step="1">

    <label for="servings">Servings:</label>
    <input type="number" id="servings" name="servings" min="1" step="1">

    <button type="submit">Add Recipe</button>
  </form>
</body>
</html>
