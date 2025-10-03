<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Logout</title>
    <script>
        // Clear localStorage and redirect to login
        localStorage.removeItem('loggedIn');
        localStorage.removeItem('userRole');
        localStorage.removeItem('username');
        window.location.href = 'login.php';
    </script>
</head>

<body>
    <p>Logging out...</p>
</body>

</html>