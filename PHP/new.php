
<?php
// Display a welcome message
echo "Hello, Ali! Welcome to PHP programming.<br>";

// Define some variables
$name = "Ali";
$age = 25;
$city = "Lahore";

// Display the variables
echo "Name: " . $name . "<br>";
echo "Age: " . $age . "<br>";
echo "City: " . $city . "<br>";

// Perform a simple calculation
$num1 = 10;
$num2 = 20;
$sum = $num1 + $num2;
echo "The sum of $num1 and $num2 is: $sum<br>";

// Create and display an array
$hobbies = array("Reading", "Traveling", "Coding");
echo "Hobbies:<br>";
foreach ($hobbies as $hobby) {
    echo "- " . $hobby . "<br>";
}

// Function to greet the user
function greetUser($name) {
    return "Hello, " . $name . "! Hope you're having a great day.<br>";
}

// Call the function
echo greetUser($name);

// Conditional statement
if ($age > 18) {
    echo "You are an adult.<br>";
} else {
    echo "You are a minor.<br>";
}

// Display the current date and time
echo "Current Date and Time: " . date("Y-m-d H:i:s") . "<br>";

// Add a form for user input
echo '<form method="post">
        <label for="inputName">Enter your name:</label>
        <input type="text" id="inputName" name="inputName">
        <button type="submit">Submit</button>
      </form>';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputName = htmlspecialchars($_POST['inputName']);
    if (!empty($inputName)) {
        echo "You entered: " . $inputName . "<br>";
    } else {
        echo "Please enter a name.<br>";
    }
}
?>
