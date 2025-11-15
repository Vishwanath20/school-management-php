$(document).ready(function() {
    // Owl Carousel for Testimonials
    $('.testimonial-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            }
        },
        navText: [
            "<i class='fas fa-chevron-left'></i>",
            "<i class='fas fa-chevron-right'></i>"
        ]
    });

    // Counter Up Animation
    $('.counter').counterUp({
        delay: 10,
        time: 1000
    });

    // Sticky Navbar
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.navbar').addClass('navbar-sticky');
            $('#backToTop').addClass('active');
        } else {
            $('.navbar').removeClass('navbar-sticky');
            $('#backToTop').removeClass('active');
        }
    });

    // Smooth Scroll
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 1000);
                return false;
            }
        }
    });

    // Back to Top Button
    $('#backToTop').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    // Initialize Bootstrap Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Mobile Menu Toggle
    $('.navbar-toggler').click(function() {
        $(this).toggleClass('active');
    });

    // Animate on Scroll
    AOS.init({
        duration: 1000,
        once: true
    });

    // Form Validation
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        // Hide any existing alerts
        $('#successAlert, #errorAlert').hide();
        
        // Get form data
        const formData = {
            source:$('input[name="source"]').val(),
            name: $('input[name="name"]').val(),
            email: $('input[name="email"]').val(),
            phone: $('input[name="phone"]').val(),
            course_interest: $('select[name="course_interest"]').val(),
            message: $('textarea[name="message"]').val()
        };
        
        // Submit form using AJAX
        $.ajax({
            url: 'api/leadsgenerate/process_contact.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#successAlert').fadeIn();
                    $('#contactForm')[0].reset();
                } else {
                    $('#errorAlert').text(response.message || 'Something went wrong!').fadeIn();
                }
            },
            error: function() {
                $('#errorAlert').fadeIn();
            }
        });
    });
// Newsletter Form Submission
$('#newsletterForm').on('submit', function(e) {
    e.preventDefault();
    
    const email = $(this).find('input[name="email"]').val();
    
    // Hide previous alerts
    $('#newsletterSuccess, #newsletterError').hide();
    
    $.ajax({
        url: 'api/leadsgenerate/process_newsletter.php',
        type: 'POST',
        data: { email: email },
        success: function(response) {
            if (response.success) {
                $('#newsletterSuccess').fadeIn();
                $('#newsletterForm')[0].reset();
            } else {
                $('#newsletterError').text(response.message || 'Failed to subscribe!').fadeIn();
            }
        },
        error: function() {
            $('#newsletterError').text('Something went wrong!').fadeIn();
        }
    });
});
    // Course Tab Navigation
    $('.course-tab').click(function() {
        $('.course-tab').removeClass('active');
        $(this).addClass('active');
        
        var target = $(this).data('target');
        $('.course-content').removeClass('active');
        $(target).addClass('active');
    });
});