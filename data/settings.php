<?php
    // Start the session
    session_start();

    // Check if the user is not logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // User is not logged in; redirect to the login page or display an access denied message
        header("Location: ../data/login.php"); // Redirect to the login page                  
        exit; // Terminate script to ensure the redirect takes effect
    }

    // Check if user is a super_admin - if not, redirect back to admin.php
    if ($_SESSION['user_role'] !== 'super_admin'){
        header("Location: ../data/admin.php");
        exit;
    }

    

    // Function to load config data from the config.json file
    function loadConfigData() {
        $configDataFile = '../data/conf/config.json';
        if (file_exists($configDataFile)) {
            $configData = json_decode(file_get_contents($configDataFile), true);
            if ($configData !== null) {
                return $configData;
            }
        }
        return []; // Return an empty array if there's an issue with loading the data
    }

    // Load user data at the beginning of the script
    $configData = loadConfigData();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <link rel="stylesheet" type="text/css" href="../css/settings.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<h1 class="page-title">Settings</h1>
<div class="container small">
    <center>
    <?php
        if ($_SESSION['user_role'] === 'super_admin'):
            $initializationStatus = $configData['initialize_status'];
            $pageTitle = $configData['pageTitle'];

            $statusClass = $initializationStatus == 1 ? "success" : "error";
            $statusText = $initializationStatus == 1 ? "Initialization complete!" : "Initial setup not complete";

            if (isset($configData['pageTitle']) && !empty($configData['pageTitle'])) {
                $pageTitle = $configData['pageTitle'];
                $pageTitleClass = "success";
            } else {
                $pageTitle = 'Page title not set. Please udpdate setting.'; // Provide a default title
                $pageTitleClass = "error";
            }
            ?>

        <div class="modal-buttons">
            <!-- Button to open Initialization Status modal -->
            <button class="modal-buttons" onclick="openModal('initStatusModal')">View Initialization Status</button>
        </div>

        <!-- Initialization Status modal -->
        <div id="initStatusModal" class="modal" onclick="closeModal('initStatusModal')">
            <div class="modal-content" onclick="event.stopPropagation();">
                <h2>Initialization Status</h2>
                <div class="status <?= $statusClass ?>"><?= $statusText ?></div>
                <button class="button" onclick="closeModal('initStatusModal')">Close</button>
            </div>
        </div>

        <div class="modal-buttons">
            <!-- Button to open Page Title modal -->
            <button class="modal-buttons" onclick="openModal('pageTitleModal')">View Page Title</button>
        </div>

        <!-- Page Title modal -->
        <div id="pageTitleModal" class="modal" onclick="closeModal('pageTitleModal')">
            <div class="modal-content" onclick="event.stopPropagation();">
                <h2>Current Page Title</h2>
                <div class="status <?= $statusClass ?>">
                    <div class="pageTitleDisplay"><?= $pageTitle ?></div> <!-- Element to display updated title -->
                </div>
                <div class="status <?= $statusClass ?>">
                    <div class="add-title-section">
                        <input type="text" id="pageTitleInput" placeholder="Create a new page title...">
                        <button class="button2" onclick="updatePageTitle()">Save</button>
                    </div>
                </div>
                <button class="button" onclick="closeModal('pageTitleModal')">Close</button>
            </div>
        </div>
        
        <script>
            function updatePageTitle() {
                const newTitle = document.getElementById('pageTitleInput').value;

                // Make an AJAX request to update the JSON file using jQuery
                $.post('update_pageTitle.php', { pageTitle: newTitle }, function (response) {
                    console.log('Server response:', response);

                    if (response.message) {
                        // Handle a successful response
                        console.log(response.message);
                        // Close the modal or update the UI as needed
                        closeModal('pageTitleModal');
                        // Fetch and display the updated page title
                        fetchUpdatedPageTitle();
                    } else if (response.error) {
                        // Handle an error response
                        console.error('An error occurred while updating the data:', response.error);
                        alert('An error occurred while updating the data. Check the server logs for details.');
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    // Handle AJAX failure
                    console.error('AJAX request failed:', textStatus, errorThrown);
                    alert('AJAX request failed. Check the server logs for details.');
                });
            }

            // Function to fetch and display the updated page title
            function fetchUpdatedPageTitle() {
                $.ajax({
                    url: 'pageTitle.php',
                    dataType: 'json',
                    success: function (data) {
                        const updatedTitle = data.title;
                        document.querySelector('.pageTitleDisplay').innerText = updatedTitle;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching updated page title:', textStatus, errorThrown);
                    },
                    complete: function () {
                        setTimeout(fetchUpdatedPageTitle, 2500); // Initiates the next fetch after 2.5 seconds
                    }
                });
            }

            // Call the function to start fetching the updated page title
            fetchUpdatedPageTitle();
        </script>

        <?php
            // Fetch the latest titles from the config.json file
            $configData = json_decode(file_get_contents('conf' . DIRECTORY_SEPARATOR . 'config.json'), true);
            $titles = $configData['organization_Settings']['titles'];
            ?>

        <div class="modal-buttons">
            <!-- Button to open Titles modal -->
            <button class="modal-buttons" onclick="openModal('titlesModal')">View Titles</button>
        </div>

        <div id="titlesModal" class="modal" onclick="closeModal('titlesModal')">
        <div class="modal-content" onclick="event.stopPropagation();">
                <h2>Prefix Titles</h2>
                <table class="settings-table">
                    <thead>
                        <tr>
                            <th>JSON Key</th>
                            <th>Title</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($titles as $titleKey => $titleValue): ?>
                            <tr>
                                <td><?= $titleKey ?></td>
                                <td><?= $titleValue ?></td>
                                <td>
                                    <button class="button2" onclick="removeTitle('title_${titleKey}', '${titleValue}')">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="add-title-section">
                    <input type="text" id="newTitle" placeholder="New Title" oninput="validateInput(this)">
                    <button class="button2" onclick="addTitle()">Add</button>
                </div>
                <button class="button" onclick="closeModal('titlesModal')">Close</button>
                <script>
                    // Initialize the titles array with the existing titles
                    let titles = [];

                    // Function to update the table in the modal
                    function updateTitlesTable() {
                        const tableBody = $('#titlesModal .settings-table tbody');
                        tableBody.empty(); // Clear the current table rows

                        // Loop through the titles array and create new table rows
                        $.each(titles, function (titleKey, titleValue) {
                            const newRow = $('<tr>');
                            newRow.html(`
                                <td>${titleKey}</td>
                                <td>${titleValue}</td>
                                <td>
                                    <button class="button2" onclick="removeTitle('title_${titleKey}', '${titleValue}')">Remove</button>
                                </td>
                            `);
                            tableBody.append(newRow);
                        });
                    }

                    // Function to fetch titles from the server
                    function fetchTitles() {
                        $.ajax({
                            url: 'titles.php',
                            dataType: 'json',
                            success: function (data) {
                                titles = Array.isArray(data.titles) ? data.titles : Object.values(data.titles);
                                updateTitlesTable(); // Update the table in the modal
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.error('Error fetching titles:', textStatus, errorThrown);
                                // Handle the error or retry as needed
                            },
                            complete: function () {
                                setTimeout(fetchTitles, 2500); // Initiate the next fetch after 2.5 seconds
                            }
                        });
                    }

                    // Initialize the real-time updates
                    $(document).ready(function () {
                        fetchTitles();
                    });
        
                    // Function to add a new title
                    function addTitle() {
                        const newTitle = document.getElementById('newTitle').value;
                        if (newTitle.trim() === '') {
                            alert('Title cannot be empty');
                            return;
                        }

                        // Convert the titles object to an array
                        const titlesArray = Object.values(titles);

                        // Add the new title to the titles array
                        titlesArray.push(newTitle);

                        // Update the JSON data
                        const jsonData = JSON.stringify({ titles: titles }, null, 2);

                        // Make an AJAX request to update the JSON file using jQuery
                        $.post('update_titles.php', { jsonData: jsonData, action: 'add_title', newTitle: newTitle }, function (response) {
                            console.log('Server response:', response);

                            if (response.message) {
                                // Handle a successful response
                                console.log(response.message);
                                // Close the modal
                                closeModal('titlesModal');
                                // Update the UI to reflect the new titles
                                updateTitlesTable();
                            } else if (response.error) {
                                // Handle an error response
                                console.error('An error occurred while updating the data:', response.error);
                                alert('An error occurred while updating the data. Check the server logs for details.');
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            // Handle AJAX failure
                            console.error('AJAX request failed:', textStatus, errorThrown);
                            alert('AJAX request failed. Check the server logs for details.');
                        });

                        // Clear the input field
                        document.getElementById('newTitle').value = '';
                    }

                    // Function to remove a title
                    function removeTitle(titleKey, titleValue) {
                        if (confirm(`Are you sure you want to remove the title "${titleValue}"?`)) {
                            // Find the title in the titles array
                            const index = titles.indexOf(titleValue);

                            console.log('Numeric Key:', titleKey);
                            console.log('Titles:', titles);

                            if (index !== -1) {
                                // Remove the title
                                titles.splice(index, 1);
                                
                                // Reorder the titles with consecutive numeric keys
                                const updatedTitles = {};
                                titles.forEach((title, index) => {
                                    updatedTitles[`title_${index}`] = title;
                                });

                                // Update the JSON data with updatedTitles instead of titles
                                const jsonData = JSON.stringify({ titles: updatedTitles }, null, 2);

                                // Make an AJAX request to update the JSON file using jQuery
                                $.post('update_titles.php', { jsonData: jsonData, action: 'remove_title', titleKey: titleKey }, function (response) {
                                    console.log('Server response:', response);

                                    if (response.message) {
                                        // Handle a successful response
                                        console.log(response.message);
                                        // Update the UI to reflect the removed title
                                        updateTitlesTable();
                                    } else if (response.error) {
                                        // Handle an error response
                                        console.error('An error occurred while updating the data:', response.error);
                                        alert('An error occurred while updating the data. Check the server logs for details.');
                                    }
                                }).fail(function (jqXHR, textStatus, errorThrown) {
                                    // Handle AJAX failure
                                    console.error('AJAX request failed:', textStatus, errorThrown);
                                    alert('AJAX request failed. Check the server logs for details.');
                                });
                            } else {
                                console.log('Title not found in the array.');
                            }
                        }
                    }
                </script>

            </div>
        </div>

        <?php
        // Add a button and modal for Departments
        $departments = $configData['organization_Settings']['Departments'];
        ?>
        <div class="modal-buttons">
            <!-- Button to open Departments modal -->
            <button class="modal-buttons" onclick="openModal('departmentsModal')">View Departments</button>
        </div>

        <div id="departmentsModal" class="modal" onclick="closeModal('departmentsModal')">
        <div class="modal-content" onclick="event.stopPropagation();">
            <h2>Departments</h2>
            <table class="settings-table">
                <thead>
                    <tr>
                        <th>JSON Key</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $departmentKey => $departmentValue): ?>
                        <tr>
                            <td><?= $departmentKey ?></td>
                            <td><?= $departmentValue ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="add-title-section">
                    <input type="text" id="newDepartment" placeholder="New Department" oninput="validateInput(this)">
                    <button class="button2" onclick="addDepartment()">Add</button>
            </div>


            <button class="button" onclick="closeModal('departmentsModal')">Close</button>
                <script>
                    // Initialize the departments array with the existing departments
                    let departments = [];

                    // Function to update the table in the modal
                    function updateDepartmentsTable() {
                        const tableBody = $('#departmentsModal .settings-table tbody');
                        tableBody.empty(); // Clear the current table rows

                        // Loop through the departments array and create new table rows
                        $.each(departments, function (departmentKey, departmentValue) {
                            const newRow = $('<tr>');
                            newRow.html(`
                                <td>${departmentKey}</td>
                                <td>${departmentValue}</td>
                                <td>
                                    <button class="button2" onclick="removeDepartment('department_${departmentKey}', '${departmentValue}')">Remove</button>
                                </td>
                            `);
                            tableBody.append(newRow);
                        });
                    }

                    // Function to fetch departments from the server
                    function fetchDepartments() {
                        $.ajax({
                            url: 'departments.php',
                            dataType: 'json',
                            success: function (data) {
                                departments = Array.isArray(data.Departments) ? data.Departments : Object.values(data.Departments);
                                updateDepartmentsTable(); // Update the table in the modal
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.error('Error fetching departments:', textStatus, errorThrown);
                                // Handle the error or retry as needed
                            },
                            complete: function () {
                                setTimeout(fetchDepartments, 2500); // Initiate the next fetch after 2.5 seconds
                            }
                        });
                    }

                    // Initialize the real-time updates
                    $(document).ready(function () {
                        fetchDepartments();
                    });

                    // Function to add a new department
                    function addDepartment() {
                        const newDepartment = document.getElementById('newDepartment').value;
                        if (newDepartment.trim() === '') {
                            alert('Department cannot be empty');
                            return;
                        }

                        // Convert the departments object to an array
                        const departmentsArray = Object.values(departments);

                        // Add the new department to the departments array
                        departmentsArray.push(newDepartment);

                        // Update the JSON data
                        const jsonData = JSON.stringify({ Departments: departments }, null, 2);

                        // Make an AJAX request to update the JSON file using jQuery
                        $.post('update_departments.php', { jsonData: jsonData, action: 'add_department', newDepartment: newDepartment }, function (response) {
                            console.log('Server response:', response);

                            if (response.message) {
                                // Handle a successful response
                                console.log(response.message);
                                // Close the modal
                                closeModal('departmentsModal');
                                // Update the UI to reflect the new departments
                                updateDepartmentsTable();
                            } else if (response.error) {
                                // Handle an error response
                                console.error('An error occurred while updating the data:', response.error);
                                alert('An error occurred while updating the data. Check the server logs for details.');
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            // Handle AJAX failure
                            console.error('AJAX request failed:', textStatus, errorThrown);
                            alert('AJAX request failed. Check the server logs for details.');
                        });

                        // Clear the input field
                        document.getElementById('newDepartment').value = '';
                    }

                    // Function to remove a department
                    function removeDepartment(departmentKey, departmentValue) {
                        if (confirm(`Are you sure you want to remove the department "${departmentValue}"?`)) {
                            // Find the department in the departments array
                            const index = departments.indexOf(departmentValue);

                            console.log('Numeric Key:', departmentKey);
                            console.log('Departments:', departments);

                            if (index !== -1) {
                                // Remove the department
                                departments.splice(index, 1);

                                // Reorder the departments with consecutive numeric keys
                                const updatedDepartments = {};
                                departments.forEach((department, index) => {
                                    updatedDepartments[`department_${index}`] = department;
                                });

                                // Update the JSON data with updatedDepartments instead of departments
                                const jsonData = JSON.stringify({ Departments: updatedDepartments }, null, 2);

                                // Make an AJAX request to update the JSON file using jQuery
                                $.post('update_departments.php', { jsonData: jsonData, action: 'remove_department', departmentKey: departmentKey }, function (response) {
                                    console.log('Server response:', response);

                                    if (response.message) {
                                        // Handle a successful response
                                        console.log(response.message);
                                        // Update the UI to reflect the removed department
                                        updateDepartmentsTable();
                                    } else if (response.error) {
                                        // Handle an error response
                                        console.error('An error occurred while updating the data:', response.error);
                                        alert('An error occurred while updating the data. Check the server logs for details.');
                                    }
                                }).fail(function (jqXHR, textStatus, errorThrown) {
                                    // Handle AJAX failure
                                    console.error('AJAX request failed:', textStatus, errorThrown);
                                    alert('AJAX request failed. Check the server logs for details.');
                                });
                            } else {
                                console.log('Department not found in the array.');
                            }
                        }
                    }

                </script>
            </div>
        </div>
    <?php endif; ?>
</center>
</div>

<div class="footer">
    <div class="logout-button">
        <a href="../data/admin.php" class="button">Back to Admin</a>
    </div>
    <div class="user-info">
        Username: <?= $_SESSION['username'] ?>
    </div>
</div>

<script>
    // Function to open a modal by ID
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    // Function to close a modal by ID
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    function validateInput(input) {
        // Remove special characters from the input value
        input.value = input.value.replace(/[^\w\s.-]/gi, '');
    }
</script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configPath = '../data/conf/config.json';
    $configData = json_decode(file_get_contents($configPath), true);

    if ($configData !== null) {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add_title') {
                // Handle adding a new title
                if (isset($_POST['newTitle'])) {
                    $newTitle = $_POST['newTitle'];

                    // Add the new title with a numeric index
                    $configData['organization_Settings']['titles'][] = $newTitle;

                    // Save the changes to the JSON file
                    $success = file_put_contents($configPath, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

                    // Check if saving was successful and respond accordingly
                    if ($success !== false) {
                        // Respond with a success message and the updated titles
                        echo json_encode(['message' => 'Data updated successfully', 'titles' => $configData['organization_Settings']['titles']]);
                    } else {
                        echo json_encode(['error' => 'An error occurred while saving the data']);
                    }
                }
            } elseif ($_POST['action'] === 'remove_title') {
                // Handle removing a title
                if (isset($_POST['titleKey'])) {
                    $titleKeyToRemove = $_POST['titleKey'];

                    // Find the title index in the titles array
                    $index = array_search($titleKeyToRemove, $configData['organization_Settings']['titles']);

                    if ($index !== false) {
                        // Remove the title
                        array_splice($configData['organization_Settings']['titles'], $index, 1);

                        // Save the changes to the JSON file
                        $success = file_put_contents($configPath, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

                        // Check if saving was successful and respond accordingly
                        if ($success !== false) {
                            echo json_encode(['message' => 'Data updated successfully', 'titles' => $configData['organization_Settings']['titles']]);
                        } else {
                            echo json_encode(['error' => 'An error occurred while saving the data']);
                        }
                    } else {
                        echo json_encode(['error' => 'Title not found in the array']);
                    }
                } else {
                    echo json_encode(['error' => 'Invalid request']);
                }
            } else {
                // Handle invalid JSON data
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON data']);
            }
        } else {
            echo json_encode(['error' => 'Method not allowed']);
        }
    }
}
?>