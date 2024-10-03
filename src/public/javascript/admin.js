//in vanilla js give cookie name and value
document.cookie = "admin=T70gFw2PXH";

function goToAdminPage(table) {
    var validTables = ["group", "post", "user"];
    if (validTables.includes(table)) {
        window.location.href = "/admin/" + table;
    }
}

// on click go to admin page
document.getElementById("table").addEventListener("click", function () {
    var table = document.getElementById("table").value;
    goToAdminPage(table);
});