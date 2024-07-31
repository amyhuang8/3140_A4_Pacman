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

            // PROCESS: checking for insertion error
            if (!isSuccess) { //error

                // VARIABLE DECLARATION:
                const errorMsg = response["errorMsg"];

                // PROCESS: checking if username already exists in database
                if (errorMsg.includes("Duplicate entry")) {

                    // OUTPUT:
                    window.alert("This username is taken!");

                }

            } else { //send user to game
                window.location.href = "http://localhost:4000/run_game.php";
            }

        },
        error: function (xhr, status) { // error-handling
            console.error("Network Error! Status Code: " + status + " Error: " + xhr.responseText);
        }
    });
}
