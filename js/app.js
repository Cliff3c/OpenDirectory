$.ajax({
    url: 'data/employees.json?' + Math.random(), // Add the cache-busting query parameter
    dataType: 'json',
    success: function (data) {
        let users = data.results;
		
		// Sort the users array alphabetically by full name
		users.sort((a, b) => {
		  const nameA = (a.name.first + ' ' + a.name.last).toUpperCase();
		  const nameB = (b.name.first + ' ' + b.name.last).toUpperCase();
		  return nameA.localeCompare(nameB);
		});

        // Create the HTML for displaying employee information
        var cards = ``;
        var i = 0;

        // Iterate over all employee profiles and build cards for each
        $.each(users, function (index, value) {
            cards += `<li class="card" id="${i++}">
                <img class="image" src="${users[index].picture.large}">
                <div class="info">
                    <p class="fullName propper-noun"><i>${users[index].name.first} ${users[index].name.last}</i></p>
                    <p><i>${users[index].email}</i></p>
                    <p><i>${users[index].phone}</i></p>
                    <p><i>${users[index].cell}</i></p>
                    <p class="propper-noun"><i>${users[index].department}</i></p>
                </div>
            </li>`;
        });

        cards += "</ul>";
        $("#profiles").html(cards);

        // Create an overlay for the modal window
        var overlay = $('<div id="overlay">');
        $("body").append(overlay);

        // Function to update the modal with employee information
        function updateModal(cardToShow) {
            $("#modal").html(`
                <div class="modal-image">
                    <img src="${users[cardToShow].picture.large}">
                </div>
                <div class="modal-info">
                    <p class="propper-noun fullName">${users[cardToShow].name.title} ${users[cardToShow].name.first} ${users[cardToShow].name.last}</p>
                    <p><a href="mailto:${users[cardToShow].email}">${users[cardToShow].email}</a></p>
                    <p>Phone: ${users[cardToShow].phone}</p>
                    <p>Mobile: ${users[cardToShow].cell}</p>
                    <p>Department: ${users[cardToShow].department}</p>
                </div>
            `);
        }

		// Create a function to fetch profiles with a unique query parameter
		function fetchProfiles() {
			// Generate a random query parameter
			var randomQueryParam = 'nocache=' + Math.random();
			// Build the URL with the query parameter
			var profilesURL = 'data/employees.json?' + randomQueryParam;

			// Fetch the profiles with the unique query parameter
			fetch(profilesURL)
				.then(response => response.json())
				.then(data => {
					// Process the data and update your HTML
					let users = data.results;
					// Sort the users array alphabetically by full name
					users.sort((a, b) => {
						const nameA = (a.name.first + ' ' + a.name.last).toUpperCase();
						const nameB = (b.name.first + ' ' + b.name.last).toUpperCase();
						return nameA.localeCompare(nameB);
					});

					// Create the HTML for displaying employee information
					var cards = ``;
					var i = 0;

					// Iterate over all employee profiles and build cards for each
					$.each(users, function (index, value) {
						cards += `<li class="card" id="${i++}">
							<img class="image" src="${users[index].picture.large}">
							<div class="info">
								<p class="fullName propper-noun"><i>${users[index].name.first} ${users[index].name.last}</i></p>
								<p><i>${users[index].email}</i></p>
								<p><i>${users[index].phone}</i></p>
								<p><i>${users[index].cell}</i></p>
								<p class="propper-noun"><i>${users[index].department}</i></p>
							</div>
						</li>`;
					});

					cards += "</ul>";
					$("#profiles").html(cards);

					// ... Rest of your code ...
				})
				.catch(error => {
					console.error('Error fetching profiles:', error);
				});
		}
		
        // Handle click on employee card
        $('.card').on("click", function (e) {
            var thisCard = parseInt($(this).attr('id'));
            updateModal(thisCard);
            $("#modal").slideDown();
            overlay.show();

            // Handle modal navigation (next and previous)
            $("#modal").on('click', '#next', function () {
                if (thisCard !== users.length - 1) {
                    thisCard++;
                    updateModal(thisCard);
                } else {
                    thisCard = 0;
                    updateModal(thisCard);
                }
            });

            $("#modal").on('click', '#prev', function () {
                if (thisCard !== 0) {
                    thisCard--;
                    updateModal(thisCard);
                } else {
                    thisCard = users.length - 1;
                    updateModal(thisCard);
                }
            });

            // Handle modal navigation with arrow keys
            $(document).keyup(function (e) {
                if (e.keyCode == 39) {
                    if (thisCard !== users.length - 1) {
                        thisCard++;
                        updateModal(thisCard);
                    } else {
                        thisCard = 0;
                        updateModal(thisCard);
                    }
                }
            });

            $(document).keyup(function (e) {
                if (e.keyCode == 37) {
                    if (thisCard !== 0) {
                        thisCard--;
                        updateModal(thisCard);
                    } else {
                        thisCard = users.length - 1;
                        updateModal(thisCard);
                    }
                }
            });
        });

        // Handle closing the modal
        overlay.click(function () {
            overlay.hide();
            $("#modal").hide();
        });

        $(document).keyup(function (e) {
            if (e.keyCode == 27) {
                overlay.hide();
                $("#modal").hide();
            }
        });

        // Handle searching for employees by name or username
		var search = function () {
			var filter = $('#searchBar').val().toLowerCase();
			var li = $("#profiles li");

			for (i = 0; i < li.length; i++) {
				var searchItemsName = li[i].getElementsByTagName("i")[0];
				var searchItemsUser = li[i].getElementsByTagName("i")[1];
				var searchItemsDepartment = li[i].getElementsByClassName("propper-noun")[1]; // Department is inside a <p> with class "propper-noun"

				// Debugging: Display department values
				//console.log("Department Value: " + searchItemsDepartment.textContent);

				
				// Check if the filter matches any of the fields or the department
				if (
					searchItemsName.textContent.toLowerCase().indexOf(filter) > -1 ||
					searchItemsUser.textContent.toLowerCase().indexOf(filter) > -1 ||
					(searchItemsDepartment && searchItemsDepartment.textContent.toLowerCase().indexOf(filter) > -1)
				) {
					li[i].style.display = "";
				} else {
					li[i].style.display = "none";
				}
			}
		}


        $('#searchBar').on('keyup', search);
    }
});
