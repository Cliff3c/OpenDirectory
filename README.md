# Open Directory System

This project named **Open Directory** is designed to facilitate efficient management of employee data within your organization. The system offers various functionalities to add, view, modify, and delete employee records, ensuring streamlined administrative operations. Everything is managed via JSON files in order to provide simplicity and portability while being a lightweight directory. The administrative functionality of the site was designed in order to make it easy for someone to manage employee data from a central location while ensuring formatting and data integrity remained in tact all while providing a visually pleasing experience for anyone viewing the Directory index page.  

## Features

### View Employee Directory
- **Description:** Access a comprehensive directory displaying all employee details.
- **Features:** Provides an overview of employees including name, position, and contact information.

### Add Employee
- **Description:** Add new employees to the database.
- **Process:** Users input employee details (name, contact information, department) and apply changes through the 'Apply Updates' page.

### Delete Employee
- **Description:** Permanently remove an existing employee from the database.
- **Process:** Users select an employee for deletion; changes are applied through the 'Apply Updates' page.

### Modify Employee
- **Description:** Edit employee information.
- **Process:** Users make necessary edits and save updated information through the 'Apply Updates' page.

### Apply Updates
- **Description:** Initiate updates for live site employee data.
- **Features:** Ensures changes made to employee information are reflected on the public-facing website.

### Factory Reset
- **Description:** Reset the entire site to factory defaults.
- **Process:** Users confirm intent, solve a simple math problem, and execute the reset operation.

### Logout
- **Description:** Securely log out from the Admin Page.

## Instructions

1. **Setup:** Ensure proper configuration and database connections.
2. **Navigation:** Use the menu to access different functionalities via the admin page.
3. **Operations:** Follow specific prompts and instructions for adding, modifying, deleting, or applying updates to employee data.
4. **Reset:** Exercise caution when performing a factory reset; re-initialize the site post-reset.

## Usage

1. **Access:** Log in to the Admin Page to begin managing employee data.
2. **Navigate:** Use the menu to access desired functionalities.
3. **Apply Changes:** Utilize 'Apply Updates' to ensure changes reflect on the live site. (Note: you may need to clear browser cache to see changes applied).
4. **Manage Users:** Users with 'super_admin' permissions can utilize the 'Manage Users' functionality to change passwords, add new users, delete users, and view a list of current users.
5. **Index Page:** Main page that displays cards and modal windows populated with employee data. The menu allows for access to the admin portal login page and a printable report of employee phone numbers. (Note: the printable report is dynamic, so you will need to adjust your print settings to get the output printed how you like.)

## Folder Structure
- **/** Main site with index page.
- **/css/** Style configurations.
- **/data/** All site data.
- **/data/backups/** Folder used for generating site backup files.
- **/data/conf/** Contains configuration data.
- **/data/employees/** Contains individual files for each employees data.
- **/data/functions/** Contains functions reused throughout the site.
- **/data/help/** Contains site documentation.
- **/data/temp/** Folder for hosting employee data being reviewed.
- **/data/temp/images** Temporary location for image hosting.
- **/data/temp/images/large/** Temporary location for image hosting.
- **/data/temp/images/med/** Temporary location for image hosting.
- **/data/temp/images/thumb/** Temporary location for image hosting
- **/data/users/** Contains admin user account info.



## PHP Files
- **index.php:** Public page displaying employee cards and modal windows with contact information.
- **printable.php:** Dynamically created report containing employee names and phone numbers that can be printed.
- **data/admin.php:** Administrative menu that allows the user to navigate the site functionality for employee data management.
- **data/backup.php:** Facilitates the backup, restore, and reset functionalities.
- **data/check_reviewstatus.php:** Prevents a user from not confirming or rejecting adding a new employee to the system.
- **data/delete.php:** Facilitates with removing employee data and images.
- **data/deleteuser.php:** Removes the employee from the system after confirming with the user.
- **data/departments.php:** Gathers all departments from site configuration to display to users via settings.
- **data/edit.php:** Allows user to select an employee, check a field that needs changing, and enables them to update those field(s).
- **data/editpic.php:** Enables user to change an existing employees picture.
- **data/factory_reset.php:** Enables 'super_admin' user to wipe all data from the site and forces initialization.
- **data/functions/generate_unique_id.php:** Responsible for genering unique ID's for employees used for employee data and image filenames.
- **data/functions/get_init_status.php:** Pulls initialization status from config and won't allow any logons until the site is initialized.
- **data/functions/resizeImage.php:** Creates multiple sizes of employee images including large, medium, and thumbnail. 
- **data/help.php:** Provides both a level overview of the site and links to details.php for a detailed overview.
- **data/help/details.php:** Provides a detailed overview of each menu option from admin.php.
- **data/initialize.php:** Initialization script that forces the initial setup of the site where a root password is  set, page title is set, and at least 1 department is created.
- **data/json_gen.php:** Facilitates with adding a new employee to the site and forces sanitization of data entries ensuring a uniform look to the site.
- **data/login.php:** Login page for administrative users. Users can log in to manage employee data and super_users have the addtional ability to manage admin users.
- **data/logout.php:** Allows the user to log out and end their current session and is located as the footer on admin.php (session times out after 1 hour of inactivity forcing a logout).
- **data/move_files.php:** Responsible for moving files from a temporary location to the live site once data is confirmed for adding a new employee.
- **data/pageTitle.php:** Gather page title from site configuration for the settings page and allows for the user to update the title if necessary.
- **data/reset.php:** Form that facilitates user with confirming a site reset.
- **data/review.php:** Page that forces the user to review new employee data before applying to the live site.
- **data/settings.php:** Manages user settings and configurations that include the page title, name prefix titles, and department names.
- **data/titles.php:** Gathers all prefix titles from site configuration to display to users via settings.
- **data/unset_review.php:** Clears employee review status once the addition of a new employee has been approved or rejected.
- **data/update_data.php:** Updates live site data by pulling each individuals' employee data and aggregating it into a single file.
- **data/update_pageTitle.php:** Updates the page title when the user changes is via settings.
- **data/user_management.php:** Allows users to change their own passowrd. Users with 'super_admin' privileges can change any users password. The can also register, delete, and view users.
- **data/view_pic.php:** Handles image previews for various pages.
- **data/view.php:** Handles viewing of employee data for various pages.

## Javascript Files
- **app.js:** Java code responsible for generating employee cards and modal windows on the index page.

## Dependencies

- **admin.css:** Style configurations for the admin interface.
- **details.css:** Style configurations for the help sections detailed overview.
- **normalize.css:** Style configurations for the index page.
- **report.css:** Style configurations for the printable contacts report accessible via the index page.
- **settings.css:** Style configurations for the settings page.
- **style.css:** Style configurations for the index page.
- **jquery-3.6.0.min.js:** jQuery library for enhanced functionality.

## Contributions

Contributions, feedback, and suggestions are welcome! Fork the repository, make changes, and submit a pull request.

## Credits

This system was developed by Cliff Cazes.

## License
MIT License

Copyright (c) [2023]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
