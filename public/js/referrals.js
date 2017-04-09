$(document).ready(function() {

    $.noty.defaults.theme = 'relax';
    $.noty.defaults.type  = 'success';
    $.noty.defaults.timeout = 2000;

    $('form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
        }).done(function(res) {
            noty({ type: res.status, text: res.message });
        });
    });

});
