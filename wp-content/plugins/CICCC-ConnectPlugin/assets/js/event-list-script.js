jQuery(document).ready(function($) {
    function loadEvents() {
        var $eventList = $('.ciccc-event-list');
        var layout = $eventList.data('layout');
        var numberOfEvents = $eventList.data('number-of-events') || 10;
        var orderBy = $eventList.data('order-by') || 'date';
        var order = $eventList.data('order') || 'DESC';
        var apiUrl = $eventList.data('api-url');

        console.log('Loading events:', { layout, numberOfEvents, orderBy, order, apiUrl });

        // Add loading UI
        $eventList.append('<div class="ciccc-event-loading"><div class="ciccc-event-loading-spinner"></div><p>Loading events...</p></div>');

        $.ajax({
            url: '/wp-json/ciccc-connect/v1/events',
            method: 'GET',
            data: {
                per_page: numberOfEvents,
                orderby: orderBy,
                order: order,
                api_url: apiUrl
            },
            success: function(events) {
                console.log('Received events:', events.length);
                var html = '';
                events.forEach(function(event) {
                    html += createEventCard(event);
                });

                $eventList.html(html);

                // Apply layout after adding the events
                applyLayout($eventList, layout);
            },
            error: function(xhr, status, error) {
                console.error('Error loading events:', error);
                $eventList.html('<p>Error loading events. Please try again later.</p>');
            },
            complete: function() {
                // Remove loading UI
                $eventList.find('.ciccc-event-loading').remove();
            }
        });
    }

    function createEventCard(event) {
        var html = '<div class="ciccc-event-card">';
        html += '<div class="ciccc-event-card-image-container">';
        html += '<img src="' + (event.image_url_event || 'default-image-url.jpg') + '" alt="' + event.name_event + '">';
        html += '</div>';
        html += '<div class="ciccc-event-card-content">';
        html += '<h3>' + event.name_event + '</h3>';
        html += '<div class="ciccc-event-card-details">';
        html += '<p class="event-start">Start: ' + formatDate(event.date_event_start) + '</p>';
        html += '<p class="event-end">End: ' + formatDate(event.date_event_end) + '</p>';
        html += '<p class="event-location">Location: ' + event.location_event + '</p>';
        html += '<p class="event-price">Price: $' + event.price_event + '</p>';
        html += '<p class="event-category">Category: ' + event.category_event + '</p>';
        html += '</div>'; // Close ciccc-event-card-details
        html += '</div>'; // Close ciccc-event-card-content
        html += '</div>'; // Close ciccc-event-card
        return html;
    }

    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleString();
    }

    function applyLayout($eventList, layout) {
        switch (layout) {
            case 'grid':
                applyGridLayout($eventList);
                break;
            case 'list':
                applyListLayout($eventList);
                break;
            case 'slider':
                initSlider($eventList);
                break;
            default:
                console.warn('Unknown layout:', layout);
                applyGridLayout($eventList); // Default to grid
        }
    }

    function applyGridLayout($eventList) {
        var columns = $eventList.data('columns') || 3;
        $eventList.css({
            'display': 'grid',
            'grid-template-columns': 'repeat(' + columns + ', 1fr)',
            'gap': '20px'
        });
    }

    function applyListLayout($eventList) {
        $eventList.css('display', 'block');
        $eventList.find('.ciccc-event-card').css({
            'display': 'flex',
            'margin-bottom': '20px'
        });
    }

    function initSlider($eventList) {
        var slidesToShow = $eventList.data('slides-to-show') || 3;
        var slidesToScroll = $eventList.data('slides-to-scroll') || 1;
        var autoplay = $eventList.data('autoplay') === 'yes';
        var autoplaySpeed = $eventList.data('autoplay-speed') || 3000;
        var pauseOnHover = $eventList.data('pause-on-hover') === 'yes';
        var showArrows = $eventList.data('show-arrows') === 'yes';
        var arrowPosition = $eventList.data('arrow-position') || 'outside';

        // Remove any existing slick initialization
        if ($eventList.hasClass('slick-initialized')) {
            $eventList.slick('unslick');
        }

        // Reset the eventList styles
        $eventList.css({
            'display': '',
            'grid-template-columns': '',
            'gap': ''
        });

        // Apply slider
        $eventList.slick({
            dots: true,
            arrows: showArrows,
            infinite: true,
            speed: 300,
            slidesToShow: slidesToShow,
            slidesToScroll: slidesToScroll,
            autoplay: autoplay,
            autoplaySpeed: autoplaySpeed,
            pauseOnHover: pauseOnHover,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: Math.min(slidesToShow, 2),
                        slidesToScroll: Math.min(slidesToScroll, 2),
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        // Apply arrow position class
        $eventList.addClass('ciccc-event-list-arrow-' + arrowPosition);
    }

    // Initialize the events loading
    loadEvents();

    // Optionally, if you want to refresh the events periodically:
    // setInterval(loadEvents, 60000); // Refresh every minute
});
