<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
	<title>Employee Directory</title>
	<link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
	<body>
		<header>
            <div class="header-container">
			<div class="logo-bar">
				<p class="logo">DIRECTORY</p>
				<div class="dropdown">
					<button class="dropbtn">Menu</button>
					<div class="dropdown-content" id="myDropdown">
						<a href="data/admin.php">Admin</a>
						<a href="printable.php" target="_blank">Print Directory</a>
					</div>
				</div>
			</div>
                <div class="intro-wrapper">
                    <h3></h3>
                    <p>This directory provides contact information for easy access to email addresses and phone numbers.</p>                    
					<input id="searchBar" type="text" name="searchBox" placeholder="Filter by name or department...">
                </div>
            </div>
        </header>
		
		<div id="header">
			
		</div>
			<ul id="users" style="list-style: none;">
		<div id="profiles">
			<ul id="users" class="contact-cards"></ul>
		</div>
		<div id="modal"></div>
	</body>
	   <script>
	// Fetch the JSON data
	fetch('data/conf/config.json')
		.then(response => response.json())
		.then(data => {
		// Update the <h2> element with the pageTitle from /data/conf/config.json
		const pageTitle = data.pageTitle;
		const h2Element = document.querySelector('h3');
		h2Element.textContent = pageTitle;
		})
		.catch(error => {
		console.error('Error fetching config.json:', error);
		});

		// Close the dropdown if the user clicks outside of it
		window.onclick = function(event) {
			if (!event.target.matches('.dropbtn')) {
				var dropdowns = document.getElementsByClassName("dropdown-content");
				for (var i = 0; i < dropdowns.length; i++) {
					var openDropdown = dropdowns[i];
					if (openDropdown.style.display === "block") {
						openDropdown.style.display = "none";
					}
				}
			}
		}

	</script>
   <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
   <script type="text/javascript" src="js/app.js"></script>
</html>
