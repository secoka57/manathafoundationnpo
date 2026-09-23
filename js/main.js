(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();


    // Initiate the wowjs
    new WOW().init();


    // Fixed Navbar
    $(window).scroll(function () {
        if ($(window).width() < 992) {
            if ($(this).scrollTop() > 45) {
                $('.fixed-top').addClass('shadow');
            } else {
                $('.fixed-top').removeClass('shadow');
            }
        } else {
            if ($(this).scrollTop() > 45) {
                $('.fixed-top').addClass('shadow').css('top', -45);
            } else {
                $('.fixed-top').removeClass('shadow').css('top', 0);
            }
        }
    });


    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
        return false;
    });


    // Causes progress
    $('.causes-progress').waypoint(function () {
        $('.progress .progress-bar').each(function () {
            $(this).css("width", $(this).attr("aria-valuenow") + '%');
        });
    }, { offset: '80%' });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: false,
        smartSpeed: 1000,
        center: true,
        dots: false,
        loop: true,
        nav: true,
        navText: [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            }
        }
    });

    // Custom Amount Toggle for Donation Form
    $('input[name="amount"]').on('change', function () {
        if ($(this).attr('id') === 'amtCustom') {
            $('#customAmountContainer').slideDown().find('input').focus();
        } else {
            $('#customAmountContainer').slideUp();
        }
    });

    // Check for URL parameters (success/error messages from PHP processing)
    function checkFormStatus() {
        var urlParams = new URLSearchParams(window.location.search);
        var success = urlParams.get('success');
        var error = urlParams.get('error');
        var $responseElement = $('#contactFormResponse, #volunteerFormResponse');

        if (success === '1') {
            $responseElement.html('<div class="alert alert-success mt-3">Thank you! Your message has been sent successfully. We will get back to you soon.</div>');
            // Scroll to the response message
            $('html, body').animate({
                scrollTop: $responseElement.offset().top - 100
            }, 500);
        } else if (error) {
            var errorMessage = 'There was an error processing your submission. Please try again.';
            if (error.includes('invalid_email')) errorMessage = 'Please enter a valid email address.';
            if (error.includes('name_required')) errorMessage = 'Please enter your name.';
            if (error.includes('subject_required')) errorMessage = 'Please enter a subject.';
            if (error.includes('message_required')) errorMessage = 'Please enter your message.';
            if (error.includes('phone_required')) errorMessage = 'Please enter your phone number.';
            if (error.includes('consent_required')) errorMessage = 'You must consent to allow us to process your information.';

            $responseElement.html('<div class="alert alert-danger mt-3">' + errorMessage + '</div>');
            // Scroll to the response message
            $('html, body').animate({
                scrollTop: $responseElement.offset().top - 100
            }, 500);
        }
    }

    // Run form status check on page load
    checkFormStatus();

    // Contact, Volunteer & Donation Form Submissions
    $('form[action*="process-contact.php"], form[action*="process-volunteer.php"]').on('submit', function (event) {
        var $form = $(this);
        var isDonationForm = $form.attr('id') === 'donationForm' || $form.hasClass('donation-form');
        var submitBtn = $form.find('button[type="submit"]');
        var originalBtnHtml = submitBtn.html();

        // Let the form submit normally to PHP - no preventDefault needed for server-side processing
        // Update button state to loading
        var loadingText = isDonationForm ? 'Submitting Pledge & Redirecting to PayPal...' : 'Sending...';

        // Legacy form handling for any remaining formsubmit.co forms
        $('form[action*="formsubmit.co"]').on('submit', function (event) {
            var $form = $(this);
            var isDonationForm = $form.attr('id') === 'donationForm' || $form.hasClass('donation-form');
            var submitBtn = $form.find('button[type="submit"]');
            var originalBtnHtml = submitBtn.html();

            event.preventDefault();

            // Update button state to loading
            var loadingText = isDonationForm ? 'Submitting Pledge & Redirecting to PayPal...' : 'Sending...';
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + loadingText);

            var formData = new FormData($form[0]);

            fetch('https://formsubmit.co/ajax/manathafoundation@gmail.com', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);

                    var successHtml = '<div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mt-3" role="alert">' +
                        '<i class="fa fa-heart text-danger me-2 fs-5"></i>' +
                        '<strong>Thank you for your submission!</strong> Your message has been received by <strong>manathafoundation@gmail.com</strong>. An email of appreciation and confirmation has been sent to your inbox. Our team will get back to you as soon as possible.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';

                    var responseContainer = $form.find('#volunteerFormResponse, #donationFormResponse, #contactFormResponse');
                    if (responseContainer.length) {
                        responseContainer.html(successHtml);
                    } else {
                        $form.append(successHtml);
                    }

                    $form[0].reset();

                    if (isDonationForm) {
                        // Automatically redirect donor directly to PayPal checkout system
                        setTimeout(function () {
                            window.location.href = 'https://www.paypal.com/ncp/payment/6RWGAXXT5JLLG';
                        }, 800);
                    }
                })
                .catch(function (error) {
                    if (isDonationForm) {
                        window.location.href = 'https://www.paypal.com/ncp/payment/6RWGAXXT5JLLG';
                    } else {
                        $form.off('submit').submit();
                    }
                });
        });

        // Fallback handler for forms with mailto or data attributes
        $('form.contact-form, form[data-email], form[data-mailto-form]').on('submit', function (event) {
            var $form = $(this);
            if ($form.attr('action') && $form.attr('action').indexOf('formsubmit.co') !== -1) {
                return;
            }
            var email = $form.data('email') || 'manathafoundation@gmail.com';
            var subject = $form.data('subject') || 'Website enquiry';

            if ($form.attr('action') && $form.attr('action') !== '#') {
                return;
            }

            event.preventDefault();

            var body = $form.serializeArray().map(function (field) {
                return field.name + ': ' + field.value;
            }).join('\n');

            window.location.href = 'mailto:' + email + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
            $form[0].reset();
        });

    })(jQuery);