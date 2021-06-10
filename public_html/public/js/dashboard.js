function myFunction(button) {
    var home = document.getElementById("home");
    var themes = document.getElementById("themes");
    var backups = document.getElementById("backups");
    var schedules = document.getElementById("schedules");
    var note = document.getElementById("note");
    var homelink = document.getElementById("homelink");
    var themeslink = document.getElementById("themeslink");
    var backupslink = document.getElementById("backupslink");
    var scheduleslink = document.getElementById("scheduleslink");

    switch (button) {
        case 'home':
            home.style.display = "block";
            themes.style.display = "none";
            backups.style.display = "none";
            schedules.style.display = "none";
            note.style.display = "none";
            homelink.className = "nav-link text-warning";
            themeslink.className = "nav-link text-white";
            backupslink.className = "nav-link text-white";
            scheduleslink.className = "nav-link text-white";
            break;
        case 'themes':
            home.style.display = "none";
            themes.style.display = "block";
            backups.style.display = "none";
            schedules.style.display = "none";
            note.style.display = "block";
            homelink.className = "nav-link text-white";
            themeslink.className = "nav-link text-warning";
            backupslink.className = "nav-link text-white";
            scheduleslink.className = "nav-link text-white";
            break;
        case 'backups':
            home.style.display = "none";
            themes.style.display = "none";
            backups.style.display = "block";
            schedules.style.display = "none";
            note.style.display = "block";
            homelink.className = "nav-link text-white";
            themeslink.className = "nav-link text-white";
            backupslink.className = "nav-link text-warning";
            scheduleslink.className = "nav-link text-white";
            break;
        case 'schedules':
            home.style.display = "none";
            themes.style.display = "none";
            backups.style.display = "none";
            schedules.style.display = "block";
            note.style.display = "block";
            homelink.className = "nav-link text-white";
            themeslink.className = "nav-link text-white";
            backupslink.className = "nav-link text-white";
            scheduleslink.className = "nav-link text-warning";
            break;
    }
}

setTimeout(function () {
    document.getElementById('flash').style.display = 'none'
}, 3000);

$(document).ready(function () {
    $(".btn").click(function () {
        $(this).html(
            '<i class="fa fa-circle-o-notch fa-spin"></i> Please, wait a minute...'
        );
    });
});
