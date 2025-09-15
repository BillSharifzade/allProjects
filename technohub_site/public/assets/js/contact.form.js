/**
 *
 * -----------------------------------------------------------------------------
 *
Template Name: Gratech - IT Service PHP Template
Author:  Gramentheme
Author URI: https://themeforest.net/user/gramentheme/portfolio
Version: 1.0.0
Description: Gratech - IT Service PHP Template
 *
 * -----------------------------------------------------------------------------
 *
 **/

(function ($) {
    'use strict';
    // Get the form.
    var form = $('#contact-form');

    // Get the messages div.
    var formMessages = $('#form-messages');

    // Set up an event listener for the contact form.
    $(form).submit(function (e) {
        // Stop the browser from submitting the form.
        e.preventDefault();

        // Serialize the form data.
        var formData = $(form).serialize();

        // Submit the form using AJAX.
        $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: formData
            })
            .done(function (response) {
                // Make sure that the formMessages div has the 'success' class.
                $(formMessages).removeClass('text-warning fw-bolder mt-2');
                $(formMessages).addClass('text-dark fw-bold mt-3 border py-2 px-3');

                // Set the message text.
                $(formMessages).text(response);

                // Clear the form.
                $('#name, #email,  #subject, #message').val('');
            })
            .fail(function (data) {
                // Make sure that the formMessages div has the 'error' class.
                $(formMessages).addClass('text-warning fw-bolder mt-2');
                $(formMessages).removeClass('text-dark fw-bold mt-3 border py-2 px-3');

                // Set the message text.
                if (data.responseText !== '') {
                    $(formMessages).text(data.responseText);
                } else {
                    $(formMessages).text('Oops! An error occured and your message could not be sent.');
                }
            });
    });

})(jQuery);