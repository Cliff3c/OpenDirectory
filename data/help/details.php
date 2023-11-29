<?php
    // Start the session
	session_start();

	// Check if the user is not logged in
	if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
		// User is not logged in; redirect to the login page or display an access denied message
		header("Location: ../data/login.php"); // Redirect to the login page
		exit; // Terminate script to ensure the redirect takes effect
	} else {
		// Check for session activity timeout
		if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
			// If more than 1 hour has passed, destroy the session
			session_unset();
			session_destroy();
			header("Location: ../data/login.php"); // Redirect to login page
			exit;
		} else {
			// Update the last activity timestamp
			$_SESSION['last_activity'] = time();
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Documentation</title>
    <link rel="stylesheet" type="text/css" href="../../css/details.css">    
</head>
<body>
    <header>
        <h1 class=page-title>Documentation</h1>
    </header>
    <center>
    <div  class="container">
    <p><h3>Click a button to view detailed documentation about each administrative option.</h3></p>
        <button class="expand-toggle">Add Employee</button>
        <div class="expandable active">

            <h3>Description</h3>
            <p>The 'Add Employee' page, accessible at "<code>Employee-Directory/data/json_gen.php</code>," serves the purpose of creating JSON data for new employees. It provides a user-friendly interface to input employee details, ensuring standardized and sanitized data integration into the live site.</p>
            <p>The page prompts users to select a prefix title from a dropdown menu populated via '<code>conf/config.json</code>,' editable by 'Super_Admin' users. Fields include First Name, Last Name, Email, Phone, Cell, Department (also derived from '<code>conf/config.json</code>'), and Image Upload.</p>
            <p><b>Note:</b></p>
            <ul>
                <li><b>Phone Field:</b> Automatically formats input and allows only numerical values for consistency.</li>
                <li><b>Cell Field:</b> Offers 'Yes' or 'No' options. Selecting 'No' removes the cell phone input, logging it as 'No Mobile Phone' in the JSON data.</li>
                <li><b>Image Upload:</b> Enables users to upload jpeg or png files, defaulting to 'blank.jpg' if no image is selected.</li>
            </ul>

            <h3>Detailed Technical Information</h3>
            <p>The code resides at "<code>/Employee-Directory/data/json_gen.php</code>." It initiates user authentication and session duration verification on loading. Additionally, it checks ongoing 'employee reviews' to prevent incomplete data entries.</p>
            <p>This page manages file functions within temporary directories, ensuring their clearance after applying or canceling review status. Upon loading, users can toggle instructions for uniform data entry. Dropdown menus fetch data from '<code>data/conf/config.json</code>,' utilizing JavaScript for formatting phone numbers.</p>
            <p>Image resizing occurs and is saved via '<code>data/functions/resizeImage.php</code>.' Each employee receives a unique ID through '<code>data/functions/generate_unique_id.php</code>.'</p>
            <p>Upon form completion and 'Generate JSON' click, the system uploads the photo, generates JSON data, redirects to '<code>data/review.php</code>' with the appended unique_id, and loads 'review.php' after 1.5 seconds.</p>
            <p>In '<code>review.php</code>,' users review the generated JSON data and uploaded pictures. They have the options to 'Apply,' triggering file transfer to the live site via '<code>data/move_files.php</code>.' Another request updates '<code>data/employees.json</code>,' generating employee cards on the index page. Canceling removes the generated JSON data and uploaded pictures.</p>

            <h3>Instructions</h3>
            <ol>
                <li>Click 'Add Employee' on the '<code>admin.php</code>' page.</li>
                <li>Fill out the employee data form.</li>
                <li>Click 'Generate JSON.'</li>
                <li>Allow '<code>review.php</code>' to load.</li>
                <li>Review the data and picture.</li>
                <li>Click 'Apply' or 'Cancel' accordingly.</li>
                <li>Redirect to '<code>admin.php</code>.' Click 'View Employee Directory.'</li>
                <li>Confirm the directory update with the new employee data if applied.</li>
            </ol>

            <h3>Files Associated with 'Add Employee'</h3>
            <ul>
                <li><code>json_gen.php</code></li>
                <li><code>functions/resizeImage.php</code></li>
                <li><code>functions/generate_unique_id.php</code></li>
                <li><code>review.php</code></li>
                <li><code>view.php</code></li>
                <li><code>move_files.php</code></li>
                <li><code>update_data.php</code></li>
            </ul>
            <hr>
        </div>
        <br>
        <button class="expand-toggle">Delete Employee</button>
        <div class="expandable active">
            <h3>Description</h3>
            <div class="expandable active">
                <p>The 'Delete JSON Files' page, located at "<code>Employee-Directory/data/delete_json.php</code>," serves the purpose of removing employee data stored as JSON files.</p>
            </div>
            <h3>Detailed Technical Information</h3>
            <div class="expandable active">
                <p>The PHP code initiates a session, verifies user authentication, and manages the deletion process of JSON data and associated images. The JavaScript section handles instructions for users and loads JSON data for review.</p>
            </div>

            <h3>Instructions</h3>
            <div class="expandable active">
                <ol>
                    <li>Log in using valid credentials.</li>
                    <li>Click on 'Delete JSON Files' in the admin menu.</li>
                    <li>Select an employee from the dropdown list.</li>
                    <li>Review the employee data to confirm your selection.</li>
                    <li>Click on 'Delete JSON and Picture' to remove the employee data.</li>
                    <li>The user data will be deleted from the live site.</li>
                    <li>The page will automatically redirect to the Admin Menu page after a successful deletion.</li>
                </ol>
            </div>

            <h3>Files Associated with 'Delete JSON Files'</h3>
            <div class="expandable active">
                <ul>
                    <li><code>delete_json.php</code></li>
                    <li><code>view.php</code></li>
                    <li><code>update_data.php</code></li>
                </ul>
            </div>
        </div>
        <br>
        <button class="expand-toggle">Modify Employee</button>
        <div class="expandable active">
            <h3>Description</h3>
            <p>The 'Modify Employee' page, located at "<code>Employee-Directory/data/edit.php</code>," enables users to edit JSON data associated with an employee's information.</p>
            
            <h3>Detailed Technical Information</h3>
                <p>
                    <strong>Requirements</strong>
                    <ul>    
                        <li>The Modify Employee Page (edit.php) requires a server environment supporting PHP to function properly. It relies on JSON data storage for employee details and images.</li>
                    </ul>
                    <br>
                    <strong>Implementation Overview</strong> 
                    <ul>
                        <li>Backend: PHP for server-side processing, employing sessions for user authentication. </li>
                        <li>Frontend: HTML, CSS for layout, and jQuery for dynamic functionality (e.g., image upload and selection).</li>
                    </ul>
                    <br>
                    <strong>Functionality</strong> 
                    <ul>
                        <li>Allows authenticated users to modify employee details and pictures</li>
                        <li>Presents a dropdown list of employees fetched from JSON files</li>
                        <li>Users can either select a new picture or set the image to blank for a selected employee</li>
                        <li>Provides instructions and visual cues for each action within the interface</li>
                    </ul>
                    <br>
                    <strong>Notes</strong>: 
                    <ul>
                        <li>Image file types supported: JPEG and PNG</li>
                        <li>For new picture uploads, it's recommended to use images with a 1:1 aspect ratio to prevent distortion during resizing</li>
                        <li>The system updates JSON data dynamically based on user actions, affecting associated image files and metadata</li>
                    </ul>
                </p>

            <h3>Instructions</h3>
            <ol>
                <li>Log in using valid credentials.</li>
                <li>Access the 'Modify Employee' page from the admin menu.</li>
                <li>Select an employee from the dropdown list.</li>
                <li>Review and update the populated fields as necessary.</li>
                <li>For the 'Cell' field:
                    <ol type="i">
                        <li>Check the 'Cell' box and choose 'Has Cell' or 'No Cell.'
                        <li>If updating the employee's cell number, modify the text field below.
                        <li>Selecting 'No Cell' sets the default value to 'No Mobile Phone.'
                    </ol>
                </li>
                <li>Enable the edit option by checking the appropriate field(s).</li>
                <li>After making all necessary changes, click 'Save JSON.'</li>
                <li>Follow the instructions on the 'Apply Updates' page to apply changes to the live site.</li>
            </ol>

            <h3>Files Associated with 'Modify Employee'</h3>
            <ul>
                <li><code>edit.php</code></li>
                <li><code>update_data.php</code></li>
            </ul>
        </div>
        <br>
        
        <button class="expand-toggle">Change Picture Documentation</button>
        <div class="expandable active">
            <h3>Description</h3>
            <p>The "Change Employee Picture" functionality within the editpic.php file facilitates the modification or reset of an employee's profile picture. It allows users to upload new images or set an employee's picture to a default "blank" state.</p>

            <h3>Detailed Technical Information</h3>
            <p>
                <strong>Requirements</strong>: 
                <ul>
                    <li>Session Management: Ensures user authentication before accessing the picture modification feature.</li>
                    <li>Image Manipulation: Supports file uploads and resizing for consistent image sizes and formats.</li>
                </ul>
                <br>
                <strong>Implementation Overview</strong>: 
                <ul>
                    <li>Session Check: Validates user login status before allowing picture modifications.</li>
                    <li>Interface: Provides a user-friendly interface for employee selection and picture management.</li>
                    <li>File Upload: Enables users to upload new images for employee profiles.</li>
                    <li>Blank Image Option: Allows resetting an employee's picture to a default "blank" state.</li>
                    <li>View Existing Picture: Provides a preview of the current employee picture upon selection.</li>
                </ul>  
                <br>
                <strong>Functionality</strong>: 
                <ol>
                    <li>Select Employee: Dropdown list to choose an employee for picture modification.</li>
                    <li>View Existing Picture: Displays the current employee picture for reference.</li>
                    <li>Set Photo to Blank:
                        <ul>
                            <li>Checkbox to indicate setting the image to blank.</li>
                            <li>Disables the new image upload option.</li>
                            <li>Updates the employee's picture to the default blank image and removes existing images.</li>
                        </ul> 
                    </li>
                    <li>Change Picture:
                        <ul>
                            <li>Uploads a new image for the selected employee:</li>
                            <li>Accepts JPG or PNG formats.</li>
                            <li>Resizes the image to maintain consistency across sizes (large, medium, thumbnail).</li>
                            <li>Overwrites the existing image and updates associated JSON data.</li>
                        </ul>
                    </li>
                    <li> Error Handling:
                        <ul>
                            <li>Provides detailed error messages for various scenarios (e.g., invalid file format, update failure).</li>
                        </ul>
                    </li>
                </ol>
                <br>
                <strong>Notes</strong>: 
                <ul>
                    <li>JSON files store employee information, including image references.</li>
                    <li>External script view_pic.php handles image previews.</li>
                    <li>Image resizing handled by functions/resizeImage.php.</li>
                </ul>
            </p>

            <h3>Instructions</h3>
            <p>
                <ol>
                    <li>User Interface:
                        <ul>
                            <li>Choose an employee from the dropdown list to modify their picture.</li>
                            <li>View the current picture upon selection.</li>
                        </ul>
                    </li>
                    <li>Set Photo to Blank:
                        <ul>
                            <li>Check the box to set the image to blank. Note: the option to upload a new image disappears.</li>
                            <li>Click "Change Picture" to apply the blank image and update JSON data.</li>
                            <li>Instructions provided to apply changes to the live site through an "Apply Updates" page.</li>
                        </ul>
                    </li>                        
                    <li>Change Employee Picture:
                        <ul>
                            <li>Click "Browse..." to upload a new image:</li>
                                <ul>
                                    <li>Ensure the picture has a 1:1 ratio to avoid distortion.</li>
                                </ul>
                            <li>Click "Change Picture" to overwrite the old picture with the new one.</li>
                        </ul>
                    </li>
                    <li>Error Handling:
                        <ul>
                            <li>Clear messages guide users through possible issues and necessary actions.</li>
                        </ul>
                    </li>
                </ol>
            </p>

            <h3>Files Associated with `editpic.php`</h3>
            <p>
                <ul>
                    <li><code>editpic.php</code></li>
                    <li><code>view_pic.php</code></li>
                    <li><code>functions/resizeImage.php</code></li>
                </ul>
            </p>
        </div>

        <button class="expand-toggle">Manage Users Documentation</button>
        <div class="expandable active">
            <h3>Description</h3>
            <p>The <code>user_management.php</code> file manages user-related functionalities, including user registration, password changes, deletion, and user role management. It interacts with a JSON file to store user data and utilizes session variables to maintain user states.</p>
        
            <h3>Detailed Technical Information:</h3>
            <ul>
                <li><strong>Session Handling:</strong> Initiates and maintains sessions to manage user login states.</li>
                <li><strong>User Data Management:</strong> Functions like <code>loadUserData()</code> and <code>saveUserData()</code> handle reading from and writing to a JSON file that stores user information.</li>
                <li><strong>User Registration:</strong> Validates and registers new users, hashing passwords before storage.</li>
                <li><strong>Password Management:</strong> Allows changing passwords for users and validates password changes.</li>
                <li><strong>User Deletion (Super Admin):</strong> Permits deletion of users through an AJAX call, confirming deletion through a dialog and updating the UI dynamically.</li>
                <li><strong>User Role Management (Super Admin):</strong> Enables the creation of new users with different roles (User or Super Admin) based on the session's privileges.</li>
                <li><strong>UI Interaction:</strong> Utilizes HTML details and summary elements to create collapsible sections for different functionalities.</li>
            </ul>

            <!-- Instructions -->
            <h3>Instructions:</h3>
            <ol>
                <li><strong>Register User:</strong> Super Admins can register new users by providing a unique username, password, and optionally assigning a user role.</li>
                <li><strong>Delete User:</strong> Super Admins can delete users by selecting them from a dropdown and confirming deletion. The UI updates dynamically without page refresh.</li>
                <li><strong>Change User Password:</strong> Super Admins can change any user's password, while regular users can change their own passwords.</li>
                <li><strong>View Current Users:</strong> Super Admins can view the list of current users along with their assigned user roles.</li>
            </ol>

            <!-- Associated Files -->
            <h3>Files associated with <code>user_management.php</code>:</h3>
            <ul>
                <li><strong>Related PHP Files:</strong>
                    <ul>
                        <li><code>deleteUser.php</code>: Handles server-side deletion of users. Note that <code>root</code> user cannot be deleted from the system.</li>
                    </ul>
                </li>
                <li><strong>Related CSS File:</strong>
                    <ul>
                        <li><code>../css/admin.css</code>: Contains styles for the user management interface.</li>
                    </ul>
                </li>
            </ul>
        </div>

        <button class="expand-toggle">Settings Documentation</button>
        <div class="expandable active">
            <h3>Description</h3>
            <p>
                This PHP script manages user settings and configurations. It interacts with JSON files to update and display
                various settings related to page titles, initialization status, and organizational titles and departments.
            </p>

            <h3>Detailed Technical Information</h3>
            <ul>
                <li>Session Management: Initiates and verifies user sessions.</li>
                <li>Config Data Loading: Loads configuration data from a <code>config.json</code> file.</li>
                <li>Dynamic HTML Generation: Generates dynamic HTML based on user roles (super admin) and fetched configuration
                    data.</li>
                <li>Modal Operations: Manages modals for initialization status, page titles, organizational titles, and
                    departments.</li>
                <li>AJAX Operations: Handles asynchronous requests for updating and fetching data from the server.</li>
                <li>Real-time Updates: Implements real-time updates for titles and departments.</li>
            </ul>

            <h3>Instructions</h3>
            <ol>
                <li><strong>Initialization Status:</strong>
                    <ul>
                        <li>Displays initialization status in a modal.</li>
                        <li>Provides a button to view the initialization status.</li>
                    </ul>
                </li>
                <li><strong>View Page Title:</strong>
                    <ul>
                        <li>Shows the Super Admin user the current page title and allows them to change it if necessary.</li>
                        <li>If the title is being changed, the user must click on the red 'Save' button to apply the update</li>
                        <li>The page title will update dynamically (clearing browser cache may be necessary to view the live update on the index.php page).</li>
                    </ul>
                </li>
                <li><strong>View Titles:</strong>
                    <p>
                        To view titles associated with the organization:
                        <ol>
                            <li>Click on the <strong>'View Titles'</strong> button.</li>
                            <li>A modal window will appear displaying the prefix titles used.</li>
                            <li>You can see the JSON key and the corresponding title.</li>
                            <li>To remove a title, click the <strong>'Remove'</strong> button beside the title.</li>
                            <li>To add a new title, enter the title in the provided input field and click the <strong>'Add'</strong>
                                button.</li>
                        </ol>
                    </p>
                </li>

                <li><strong>View Titles:</strong>
                    <p>
                    To view departments within the organization:
                        <ol>
                            <li>Click on the <strong>'View Departments'</strong> button.</li>
                            <li>A modal window will appear displaying the departments.</li>
                            <li>You can see the JSON key and the corresponding department.</li>
                            <li>To remove a department, click the <strong>'Remove'</strong> button beside the department.</li>
                            <li>To add a new department, enter the department name in the provided input field and click the
                                <strong>'Add'</strong> button.</li>
                        </ol>
                    </p>
                </li>

                <!-- Add more detailed instructions for each functionality -->
            </ol>

            <h3>Files associated with <code>settings.php</code></h3>
            <ul>
                <li><strong>Dependencies:</strong>
                    <ul>
                        <li><code>admin.css</code></li>
                        <li>settings.css</code> for styling.</li>
                        <li><code>jquery-3.6.0.min.js</code> for jQuery library.</li>
                    </ul>
                </li>
                <li><strong>PHP Operations:</strong> for handling AJAX requests.
                    <ul>
                        <li><code>update_pageTitle.php</code></li>
                        <li><code>pageTitle.php</code></li>
                        <li><code>update_titles.php</code></li>
                        <li><code>update_departments.php</code></li>
                    </ul>
                </li>
            </ul>
        </div>

        

        <button class="expand-toggle">Backup / Restore / Reset Documentation</button>
        <div class="expandable active">
            <h3>Description</h3>
            <p>
                This PHP script handles backup and restoration functionalities for a website's crucial data. It provides forms for initiating
                backup and restore operations and includes detailed instructions for users. Sample data is availalbe from here:<strong><a href="../../sample/SampleData.zip">'SampleData.zip'</strong></a>.
            </p>

            <h3>Detailed Technical Information</h3>
            <ul>
                <li>Session Management: Initiates or resumes sessions and tracks user activity.</li>
                <li>Backup Process:
                    <ul>
                        <li>Creates a ZIP archive containing specified files and directories.</li>
                        <li>Initiates download of the generated backup file.</li>
                        <li>Deletes contents from the backup folder after successful download.</li>
                    </ul>
                </li>
                <li>Restore Process:
                    <ul>
                        <li>Allows users to upload a zipped backup file.</li>
                        <li>Validates the uploaded file's structure and contents.</li>
                        <li>Restores data by replacing existing files with verified data from the uploaded backup.</li>
                        <li>Generates resized image copies for different image sizes.</li>
                    </ul>
                </li>
                <li>Factory Reset Process:
                    <ol>
                        <li>Allows user to wipe all site data and restore the system to factory defaults.</li>
                        <li>All user accounts, employee data, and images are deleted.</li>
                        <li>After a factory reset, the user is required to fill out the initialization form.</li>
                        <li>Upon completion of the factor reset, a new root user password, page title, and at least one department is required to be set in order for the site to be accessible.</li>
                    </ol>
                </li>                
            </ul>

            <h3>Instructions</h3>
            <ol>
                <li><strong>Backup Instructions:</strong>
                    <ol>
                        <li>Click on 'Backup Site Data.'</li>
                        <li>Download the generated ZIP archive file.</li>
                        <li>Save the downloaded file in a secure location.</li>
                    </ol>
                </li>
                <li><strong>Restore Instructions:</strong>
                    <ol>
                        <li>Click on 'Restore Site Data.'</li>
                        <li>Select your zipped backup file.</li>
                        <li>Click on the 'Restore' button.</li>
                        <li>Wait for the data to be restored.</li>
                    </ol>
                </li>

                <li><strong>Factory Reset Instructions:</strong>
                    <ol>
                        <li>Check the box to confirm the intention to wipe all site data.</li>
                        <li>Solve the simple math problem provided to confirm the desire for a complete data wipe.</li>
                        <li>Click on the 'Factory Reset' button.</li>
                        <li>Confirm the action and allow the site to be reset to its factory defaults.</li>
                    </ol>

                    <p><i>Note: Upon completion, a re-initialization of the site will be required.</i></p>
                </li>
            </ol>

            <h3>Files associated with <code>backup.php</code></h3>
            <ul>
                <li><strong>HTML/CSS/JavaScript Dependencies:</strong>
                    <ul>
                        <li><code>admin.css</code> for styling.</li>
                        <li><code>settings.css</code> for styling.</li>
                        <li><code>jquery-3.6.0.min.js</code> for jQuery library.</li>
                    </ul>
                </li>
                <li><strong>PHP Functions and Files:</strong>
                    <ul>
                        <li><code>createBackup()</code>: Creates a backup ZIP file containing specified directories and files.</li>
                        <li><code>deleteDirectory($path)</code>: Recursively deletes a directory and its contents.</li>
                        <li><code>reset.php</code> runs the code to perform a factory reset.</li>
                        <li><code>initialize.php</code> which is only accessible upon a factory reset.</li>
                    </ul>
                </li>
                <li><strong>Implementation Details:</strong>
                    <ul>
                        <li>File handling for backup creation, restoration, and validations.</li>
                        <li>Copying verified data from backup to specified locations for the live site.</li>
                        <li>Session handling, logout on inactivity, and user information display.</li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>

    </div>
</center>
    <script>
        const expandButtons = document.querySelectorAll('.expand-toggle');
        expandButtons.forEach(button => {
            button.addEventListener('click', () => {
                button.nextElementSibling.classList.toggle('active');
            });
            button.nextElementSibling.classList.remove('active');
        });
    </script>
    <div class="footer">
        <div class="logout-button">
			<a href="../help.php" class="button">Back to Help</a>
		</div>
		<div class="user-info">
			Username: <?php echo $_SESSION['username']; ?>
		</div>		
	</div>
	</body>
</html>
