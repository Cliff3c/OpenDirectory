<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Printable Report</title>
    <link rel="stylesheet" type="text/css" href="css/report.css">
</head>
<body>
    <h1 class="page-title" id="reportTitle"></h1>
    <?php
        $currentDate = date("F j, Y");
        echo "<center><h5 class='subtitle'>As of $currentDate</h5></center>";
    ?>
    <div class="employee-container" id="employeeContainer">
        <script>
            // Function to fetch employee data from employees.json
            async function fetchEmployeeData() {
                try {
                    const response = await fetch('data/employees.json');
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error fetching employee data:', error);
                    return [];
                }
            }

            function generateDepartmentHTML(department, employees) {
                let departmentHTML = `<h2 class="page-title">${department}</h2>`; // Include the department name
                departmentHTML += `<table class="employee-table">
                                        <thead>
                                            <tr>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Phone</th>
                                                <th>Cell</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                employees
                .sort((a, b) => a.name.first.localeCompare(b.name.first)) // Sort employees by first name
                .forEach(employee => {
                    departmentHTML += `<tr>
                                            <td>${employee.name.first || ''}</td>
                                            <td>${employee.name.last || ''}</td>
                                            <td>${employee.phone || ''}</td>
                                            <td>${employee.cell !== 'No Mobile Phone' ? employee.cell || '' : ''}</td>
                                        </tr>`;
                });
                departmentHTML += `</tbody></table>`;
                return departmentHTML;
            }

            async function fetchReportTitle() {
                try {
                    const response = await fetch('data/conf/config.json');
                    const data = await response.json();
                    return data.pageTitle;
                } catch (error) {
                    console.error('Error fetching report title:', error);
                    return 'Default Title'; // Set a default title in case of an error
                }
            }

            async function generateReport() {
                const reportTitle = await fetchReportTitle();
                const reportTitleElement = document.getElementById('reportTitle');
                reportTitleElement.textContent = reportTitle;
                
                const employeesData = await fetchEmployeeData();
                const dataArray = Array.isArray(employeesData.results) ? employeesData.results : [];

                const departmentsSet = new Set(dataArray.map(employee => employee.department));
                const sortedDepartments = Array.from(departmentsSet).sort((a, b) => a.localeCompare(b));

                const totalDepartments = sortedDepartments.length;
                const halfLength = Math.ceil(totalDepartments / 2);

                const columns = {
                    column1: {
                        departments: [],
                        totalEmployees: 0
                    },
                    column2: {
                        departments: [],
                        totalEmployees: 0
                    }
                };

                sortedDepartments.forEach((department, index) => {
                    const employeesInDepartment = dataArray.filter(employee => employee.department === department);
                    const departmentHTML = generateDepartmentHTML(department, employeesInDepartment);

                    const targetColumn = columns.column1.totalEmployees <= columns.column2.totalEmployees ? 'column1' : 'column2';
                    columns[targetColumn].departments.push(departmentHTML);
                    columns[targetColumn].totalEmployees += employeesInDepartment.length;
                });

                const columnsHTML = `
                    <div class="column">${columns.column1.departments.join('')}</div>
                    <div class="column">${columns.column2.departments.join('')}</div>
                `;

                const employeeContainer = document.getElementById('employeeContainer');
                employeeContainer.innerHTML = columnsHTML;

                calculateColumnLayout(); // Call the layout calculation after rendering
            }






            // Generate the report on page load
            window.onload = async () => {
                await generateReport();
                calculateColumnLayout(); // Call the column layout calculation after generating the report
            };

            // Function to calculate column layout based on printable page
            function calculateColumnLayout() {
                const employeeContainer = document.getElementById('employeeContainer');
                const containerHeight = employeeContainer.scrollHeight; // Consider using scrollHeight for accurate content height
                const pageHeight = 792; // Height of an 8.5x11" page in pixels (assuming portrait)

                // If content height exceeds the page height, split into columns
                if (containerHeight > pageHeight) {
                    employeeContainer.style.columnCount = '2';
                } else {
                    employeeContainer.style.columnCount = '1';
                }
            }

            // Call the function initially and on window resize
            calculateColumnLayout();
            window.addEventListener('resize', calculateColumnLayout);
        </script>
    </div>
</body>
</html>
