<?php
require_once __DIR__ .'/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user_id'];
$stmt = $app_conn->prepare("SELECT username, email, gender, grade, birthday, country, city, school FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $gender, $grade, $birthday, $country, $city, $school);
$stmt->fetch();
?>

<h2>Profile</h2>
<p>Username: <?php echo htmlspecialchars($username); ?></p>
<p>Email: <?php echo htmlspecialchars($email); ?></p>
<p>Gender: <?php echo htmlspecialchars($gender); ?></p>
<p>Grade: <?php echo htmlspecialchars($grade); ?></p>
<p>Birthday: <?php echo htmlspecialchars($birthday); ?></p>
<p>Country: <?php echo htmlspecialchars($country); ?></p>
<p>City: <?php echo htmlspecialchars($city); ?></p>
<p>School: <?php echo htmlspecialchars($school); ?></p>