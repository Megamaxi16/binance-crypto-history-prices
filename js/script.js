

//Troca o status do trigger
$(document).ready(function() {

    var path = window.location.pathname;

    $('.nav-link').each(function() {

        if (this.pathname === path) {
            
            $(this).addClass('active');
        }
    });
});
