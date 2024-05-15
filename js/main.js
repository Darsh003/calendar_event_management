(function ($) {
    "use strict";

    document.addEventListener('DOMContentLoaded', function () {
        var today = new Date(),
            year = today.getFullYear(),
            month = today.getMonth(),
            monthTag = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            day = today.getDate(),
            days = document.getElementsByTagName('td'),
            selectedDay,
            setDate,
            daysLen = days.length;

        // Call fetchEventsForSelectedDate with today's date
        fetchEventsForSelectedDate(formatDate(today));

        function Calendar(selector, options) {
            this.options = options;
            this.draw();
        }

        Calendar.prototype.draw = function () {
            this.getCookie('selected_day');
            this.getOptions();
            this.drawDays();
            var that = this,
                reset = document.getElementById('reset'),
                pre = document.getElementsByClassName('pre-button'),
                next = document.getElementsByClassName('next-button');

            pre[0].addEventListener('click', function () { that.preMonth(); });
            next[0].addEventListener('click', function () { that.nextMonth(); });
            reset.addEventListener('click', function () { that.reset(); });
            while (daysLen--) {
                days[daysLen].addEventListener('click', function () { that.clickDay(this); });
            }
        };

        Calendar.prototype.drawHeader = function (e) {
            var headDay = document.getElementsByClassName('head-day'),
                headMonth = document.getElementsByClassName('head-month');

            e ? headDay[0].innerHTML = e : headDay[0].innerHTML = day;
            headMonth[0].innerHTML = monthTag[month] + " - " + year;
        };

        Calendar.prototype.drawDays = function () {
            var startDay = new Date(year, month, 1).getDay(),

                nDays = new Date(year, month + 1, 0).getDate(),

                n = startDay;

            for (var k = 0; k < 42; k++) {
                days[k].innerHTML = '';
                days[k].id = '';
                days[k].className = '';
            }

            for (var i = 1; i <= nDays; i++) {
                days[n].innerHTML = i;
                n++;
            }

            for (var j = 0; j < 42; j++) {
                if (days[j].innerHTML === "") {

                    days[j].id = "disabled";

                } else if (j === day + startDay - 1) {
                    if ((this.options && (month === setDate.getMonth()) && (year === setDate.getFullYear())) || (!this.options && (month === today.getMonth()) && (year === today.getFullYear()))) {
                        this.drawHeader(day);
                        days[j].id = "today";
                    }
                }
                if (selectedDay) {
                    if ((j === selectedDay.getDate() + startDay - 1) && (month === selectedDay.getMonth()) && (year === selectedDay.getFullYear())) {
                        days[j].className = "selected";
                        this.drawHeader(selectedDay.getDate());
                    }
                }
            }
        };

        Calendar.prototype.clickDay = function (o) {
            var selected = document.getElementsByClassName("selected"),
                len = selected.length;
            if (len !== 0) {
                selected[0].className = "";
            }
            o.className = "selected";
            selectedDay = new Date(year, month, o.innerHTML);
            this.drawHeader(o.innerHTML);
            this.setCookie('selected_day', 1);

            // Call fetchEventsForSelectedDate with the selected date
            fetchEventsForSelectedDate(formatDate(selectedDay));

        };

        Calendar.prototype.preMonth = function () {
            if (month < 1) {
                month = 11;
                year = year - 1;
            } else {
                month = month - 1;
            }
            this.drawHeader(1);
            this.drawDays();
        };

        Calendar.prototype.nextMonth = function () {
            if (month >= 11) {
                month = 0;
                year = year + 1;
            } else {
                month = month + 1;
            }
            this.drawHeader(1);
            this.drawDays();
        };

        Calendar.prototype.getOptions = function () {
            if (this.options) {
                var sets = this.options.split('-');
                setDate = new Date(sets[0], sets[1] - 1, sets[2]);
                day = setDate.getDate();
                year = setDate.getFullYear();
                month = setDate.getMonth();
            }
        };

        Calendar.prototype.reset = function () {
            month = today.getMonth();
            year = today.getFullYear();
            day = today.getDate();
            this.options = undefined;
            this.drawDays();
        };

        Calendar.prototype.setCookie = function (name, expiredays) {
            if (expiredays) {
                var date = new Date();
                date.setTime(date.getTime() + (expiredays * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
            } else {
                var expires = "";
            }
            document.cookie = name + "=" + selectedDay + expires + "; path=/";
        };

        Calendar.prototype.getCookie = function (name) {
            if (document.cookie.length) {
                var arrCookie = document.cookie.split(';'),
                    nameEQ = name + "=";
                for (var i = 0, cLen = arrCookie.length; i < cLen; i++) {
                    var c = arrCookie[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1, c.length);

                    }
                    if (c.indexOf(nameEQ) === 0) {
                        selectedDay = new Date(c.substring(nameEQ.length, c.length));
                    }
                }
            }
        };

        function openEventDialog() {
            var selectedDate = document.querySelector('.selected').innerText; // Get the selected date
            var modalTitle = document.getElementById('modalLabel');
            modalTitle.innerText = 'Add New Event - ' + selectedDate; // Update the modal title with the selected date
            $('#event_start_date').val(formatDate(selectedDay)); // Set the selected date in the event start date input field
            $('#event_entry_modal').modal('show'); // Show the modal
        }

        // Function to fetch events for the selected date
        function fetchEventsForSelectedDate(selectedDate) {
            $.ajax({
                url: "fetch_events_for_selected_date.php", // PHP script to fetch events for the selected date
                type: "GET",
                dataType: "json",
                data: {
                    selected_date: selectedDate
                },
                success: function (response) {
                    if (response && response.length > 0) {
                        var eventList = $("#selected-date-events");
                        eventList.empty(); // Clear previous events
                        response.forEach(function (event) {
                            var eventHtml = '<div class="card mb-2"><div class="card-body">' +
                                '<h5 class="card-title">' + event.event_name + '</h5>' +
                                '<p class="card-text">Start Date: ' + event.event_start_date + '</p>' +
                                '<p class="card-text">End Date: ' + event.event_end_date + '</p>' +
                                '<button type="button" class="btn btn-primary btn-sm mr-2 edit-event-btn" data-event-id="' + event.event_id + '">Edit</button>' +
                                '<button type="button" class="btn btn-danger btn-sm delete-event-btn" data-event-id="' + event.event_id + '">Delete</button>' +
                                '</div></div>';
                            eventList.append(eventHtml);
                        });
                    } else {
                        $("#selected-date-events").html("<p>No events found for selected date.</p>");
                    }
                },
                error: function (xhr, status) {
                    console.log('Ajax error: ' + xhr.statusText);
                    alert('Error fetching events: ' + status);
                }
            });
        }


        // Function to fetch events from the database
        function fetchEvents() {
            $.ajax({
                url: "fetch_events.php", // PHP script to fetch events
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response && response.length > 0) {
                        var eventList = $("#event-list");
                        eventList.empty(); // Clear previous events
                        response.forEach(function (event) {
                            var eventHtml = '<div class="card mb-2"><div class="card-body">' +
                                '<h5 class="card-title">' + event.event_name + '</h5>' +
                                '<p class="card-text">Start Date: ' + event.event_start_date + '</p>' +
                                '<p class="card-text">End Date: ' + event.event_end_date + '</p>' +
                                '<button type="button" class="btn btn-primary btn-sm mr-2 edit-event-btn" data-event-id="' + event.event_id + '">Edit</button>' +
                                '<button type="button" class="btn btn-danger btn-sm delete-event-btn" data-event-id="' + event.event_id + '">Delete</button>' +
                                '</div></div>';
                            eventList.append(eventHtml);
                        });
                    } else {
                        $("#event-list").html("<p>No events found.</p>");
                    }
                },
                error: function (xhr, status) {
                    console.log('Ajax error: ' + xhr.statusText);
                    alert('Error fetching events: ' + status);
                }
            });
        }

        // Call fetchEvents function when the page loads
        $(document).ready(function () {
            fetchEvents();
        });


        function formatDate(date) {
            var dd = String(date.getDate()).padStart(2, '0');
            var mm = String(date.getMonth() + 1).padStart(2, '0'); // January is 0!
            var yyyy = date.getFullYear();

            return yyyy + '-' + mm + '-' + dd;
        }

        // JavaScript Functions for Edit and Delete
        $(document).on('click', '.edit-event-btn', function () {
            var eventId = $(this).data('event-id');

            // AJAX request to fetch event details
            $.ajax({
                url: "fetch_event_details.php", // PHP script to fetch event details
                type: "GET",
                dataType: "json",
                data: {
                    event_id: eventId
                },
                success: function (response) {
                    if (response.status) {
                        var eventData = response.event;
                        // Populate the edit event modal with event details
                        $('#edit_event_id').val(eventData.event_id);
                        $('#edit_event_name').val(eventData.event_name);
                        $('#edit_event_start_date').val(eventData.event_start_date);
                        $('#edit_event_end_date').val(eventData.event_end_date);
                        $('#edit_event_modal').modal('show'); // Show the edit event modal
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, status) {
                    console.log('Ajax error: ' + xhr.statusText);
                    alert('Error fetching event details: ' + status);
                }
            });
        });

        // Function to handle the submission of the edit event form
        $('#edit_event_form').submit(function (event) {
            event.preventDefault(); // Prevent the default form submission

            // Get form data
            var eventId = $('#edit_event_id').val();
            var eventName = $('#edit_event_name').val();
            var eventStartDate = $('#edit_event_start_date').val();
            var eventEndDate = $('#edit_event_end_date').val();

            // AJAX request to update event
            $.ajax({
                url: "update_event.php", // PHP script to update event details
                type: "POST",
                dataType: "json",
                data: {
                    event_id: eventId,
                    event_name: eventName,
                    event_start_date: eventStartDate,
                    event_end_date: eventEndDate
                },
                success: function (response) {
                    if (response.status) {
                        alert(response.message);
                        $('#edit_event_modal').modal('hide'); // Hide the edit event modal
                        // Refresh page or update event list after successful update
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, status) {
                    console.log('Ajax error: ' + xhr.statusText);
                    alert('Error updating event: ' + status);
                }
            });
        });


        $(document).on('click', '.delete-event-btn', function () {
            var eventId = $(this).data('event-id');
            // Confirm deletion
            if (confirm("Are you sure you want to delete this event?")) {
                // Send AJAX request to delete event
                $.ajax({
                    url: "delete_event.php",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        event_id: eventId// Provide event ID to delete
                    },
                    success: function (response) {
                        if (response.status) {
                            alert(response.msg);
                            // Refresh page or update event list after successful deletion
                            location.reload();
                        } else {
                            alert(response.msg);
                        }
                    },
                    error: function (xhr, status) {
                        console.log('Ajax error: ' + xhr.statusText);
                        alert('Error deleting event: ' + status);
                    }
                });
            }
        });



        var calendar = new Calendar();

        $('#add_event_button').on('click', openEventDialog); // Bind openEventDialog function to the click event of the "Add Event" button

    }, false);

})(jQuery);
