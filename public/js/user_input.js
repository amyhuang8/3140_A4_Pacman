function sendForm() {
    // PROCESS: sending POST req. w/ AJAX to server
    $.ajax({
        type: 'POST',
        url: '../index.php',
        data: { action: 'sendForm' },
        dataType: 'json',
        success: function (response) {

            // VARIABLE DECLARATION:
            const name = response["name"];
            const password = response["password"];
          
        },
        error: function (xhr, status) { //error-handling
            console.error("Network Error! Status Code: " + status + " Error: " + xhr.responseText);
        }
    });

}