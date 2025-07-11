// Preview Password (  Password Protected Entire Site )

 var $ = jQuery;

$(document).ready(function() {
    $('#toggle-password').click(function(){
        var passwordInput = $('.password-input');
        if (passwordInput.attr('type') == 'password') {
            $('#eye-icon').removeClass('dashicons-visibility');
            $('#eye-icon').addClass('dashicons-hidden');
            passwordInput.attr('type', 'text');
        } else {
            $('#eye-icon').removeClass('dashicons-hidden');
            $('#eye-icon').addClass('dashicons-visibility');
            passwordInput.attr('type', 'password');
        }
     });
});
