<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit JSON Data</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <?php
        session_start();
        // Function to validate math problem
        function validateMathProblem($num1, $num2, $userInput) {
            $sum = $num1 + $num2;
            return intval($userInput) === $sum;
        }

        if (isset($_POST['resetButton'])) {
            
            $_SESSION['resetToken'] = 1;
    
            // Check if the user is not logged in
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: ../data/login.php");
                exit;
            }
            if (!isset($_POST['confirmReset']) || $_POST['confirmReset'] !== 'on') {
                echo "<script>alert('Please confirm the reset by checking the box.');</script>";
            } else {
                $num1 = intval($_POST['num1']);
                $num2 = intval($_POST['num2']);
                $userInput = intval($_POST['userInput']);
        
                if (!validateMathProblem($num1, $num2, $userInput)) {
                    $resetToken=''; //clearing reset token to prevent accidental factory reset
                    echo "<script>alert('Incorrect answer. Factory reset cancelled.'); window.location.href = '../data/admin.php';</script>";
                } else {
                    // Redirect to factory_reset.php only if conditions are met
                    $factoryResetURL = 'factory_reset.php';
                    header("Location: $factoryResetURL");
                    exit;
                }
            }
        }

        // Generate random numbers for the math problem
        $num1 = rand(0, 50);
        $num2 = rand(0, 50);
    ?>

    <h1 class="page-title">Factory Reset</h1>
    <div class="container">
    <center><button id="toggleButton" class="toggle-button">Toggle Instructions</button></center>
    <div class="overlay" id="popupOverlay">
            <div class="popup">
                <div class="popup-content">
                        <p><b>INSTRUCTIONS FOR FACTORY RESET:</b></p>
                        <ol>
                            <li>Check the box below to confirm you want to wipe all site data.</li>    
                            <li>Solve the simple math problem confirming you want to wipe all site data.</li>
                            <li>Click on the 'Factory Reset' button.</li>
                            <li>Click 'Confirm' and allow the site to be reset to factory defaults.</li>
                        </ol>
                        <i>(Note: Upon completion, you will be required to re-initialize the site)</i>
                        <center><button id="closeButton">Close</button></center>    
                    </div>
                </div>                                           
        </div>
        <script>
            document.getElementById('toggleButton').addEventListener('click', function() {
                var overlay = document.getElementById('popupOverlay');
                overlay.style.display = 'block';
            });

            document.getElementById('closeButton').addEventListener('click', function() {
                var overlay = document.getElementById('popupOverlay');
                overlay.style.display = 'none';
            });
        </script>
        <form method="post" action="" accept-charset="UTF-8">
            <hr>
            <label for="confirmReset">
                <input type="checkbox" id="confirmReset" name="confirmReset">
                Check this box in order to confirm you want to reset the site to factory defaults. 
            </label>
            <hr>

            <!-- Display the math problem and input field -->
            <input type="hidden" name="num1" value="<?php echo $num1; ?>">
            <input type="hidden" name="num2" value="<?php echo $num2; ?>">
            <label for="userInput">Please solve: <?php echo $num1; ?> + <?php echo $num2; ?> = </label>
            <input type="number" id="userInput" name="userInput" min="0"><br>
            <hr>

            <!--Pass the reset token through the form submission-->
            <input type="submit" name="resetButton" value="Factory Reset">
        </form>
    </div>
    <div class="footer">
        <div class="logout-button">
            <a href="../data/admin.php" class="button">Back to Admin</a>
        </div>
        <div class="user-info">
            Username: <?php echo $_SESSION['username']; ?>
        </div>
    </div>
</body>
</html>