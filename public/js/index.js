/**
 * This function handles the action performed when the user submits the sign-up form.
 */
function sendForm() {

    // VARIABLE DECLARATION: retrieving the submitted form data
    const formData = new FormData(document.getElementById('myForm'));

    $.ajax({
        type: 'POST',
        url: '../index.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {

            // VARIABLE DECLARATION:
            const isSuccess = response["isSuccess"];
            const isAdmin = response["isAdmin"];

            // PROCESS: checking for insertion error
            if (!isSuccess) { //error

                // VARIABLE DECLARATION:
                const errorMsg = response["errorMsg"];

                // PROCESS: checking if username already exists in database
                if (errorMsg.includes("Duplicate entry")) {

                    // OUTPUT:
                    window.alert("This username is taken; please select another one. If you are trying to login as the Administrator, please check your credentials.");

                } else if (errorMsg === "Admin does not exist in database.") {

                    // OUTPUT:
                    window.alert("The admin login does not exist in the database!");

                }

            } else { //send user to game

                // PROCESS: checking for admin login
                if (isAdmin) {
                    window.location.href = "http://localhost:4000/admin.php";
                } else {
                    window.location.href = "http://localhost:4000/run_game.php";
                }

            }

        },
        error: function (xhr, status) { // error-handling
            console.error("Network Error! Status Code: " + status + " Error: " + xhr.responseText);
        }
    });
}

/**
 * This function handles the action performed when the Administrator clears the leaderboard.
 */
function clearLeaderboard() {

    // PROCESS: sending POST req. w/ AJAX to server
    $.ajax({
        type: 'POST',
        url: '../admin.php',
        data: { action: 'clearLeaderboard' },
        dataType: 'json',
        success: function (response) {
            // OUTPUT:
            window.alert(response);
        },
        error: function (xhr, status) { //error-handling
            console.error("Network Error! Status Code: " + status + " Error: " + xhr.responseText);
        }
    });

}
