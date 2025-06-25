@include('includes.header')
@include('includes.sidebar')

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex justify-content-between">
                    <div class="page_title_left d-flex align-items-center">
                        <h3 class="f_s_25 f_w_700 dark_text mr_30">Bookings</h3>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Bookings</li>
                        </ol>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookingModal">Add Booking</button>
                </div>
            </div>
        </div>
           <div id="calendar"> </div>
    </div>
</div>

@include('includes.footer')

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="bookingForm">
        <div class="modal-header">
          <h5 class="modal-title">Booking</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="booking_id">
            <div class="common_input mb_15">
                <label>Customer</label>
                <select class="form-select" id="customer_id"></select>
            </div>
            <div class="common_input mb_15">
                <label>Haircutter</label>
                <select class="form-select" id="employee_id"></select>
            </div>
            <div class="common_input mb_15">
                <label>Date</label>
                <input type="date" id="booking_date" class="form-control">
            </div>
            <div class="common_input mb_15">
                <label>Start Time</label>
                <input type="time" id="start_time" class="form-control">
            </div>
            <div class="common_input mb_15">
                <label>End Time</label>
                <input type="time" id="end_time" class="form-control">
            </div>
            <div class="common_input mb_15">
                <label>Service</label>
                <select class="form-select" id="service_id"></select>
            </div>
            <div class="common_input mb_15">
                <label>Status</label>
                <select class="form-select" id="status">
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="common_input mb_15">
                <label>Notes</label>
                <textarea class="form-control" id="notes"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
    initialView: 'resourceTimelineDay',
    headerToolbar: {
      left: 'today prev,next',
      center: 'title',
      right: 'resourceTimelineDay,resourceTimelineWeek'
    },
    aspectRatio: 1.5,

    slotDuration: '01:00:00',
    slotLabelInterval: '01:00',
    slotMinTime: "07:00:00",
    slotMaxTime: "22:00:00",
    slotLabelFormat: {
    hour: 'numeric',
    meridiem: 'short',
    hour12: true
    },

    resourceAreaColumns: [
      {
        field: 'title',
        headerContent: 'Employee'
      }
    ],
    resources: '/api/calendar/employees',
    events: '/api/calendar/bookings',
    eventDidMount: function (info) {
      const statusColors = {
        confirmed: '#198754',
        pending: '#ffc107',
        completed: '#0d6efd',
        cancelled: '#dc3545'
      };
      info.el.style.backgroundColor = statusColors[info.event.extendedProps.status] || '#6c757d';
      info.el.style.color = 'white';
      info.el.style.borderRadius = '4px';
    },

    // Add event click handler to load modal
    eventClick: function (info) {
      const bookingId = info.event.id;

      // Load data from API
      $.get(`/api/bookings/${bookingId}`, function (b) {
        $('#booking_id').val(b.id);
        $('#customer_id').val(b.customer_id);
        $('#employee_id').val(b.employee_id);
        $('#service_id').val(b.service_id);
        $('#booking_date').val(b.booking_date);
        $('#start_time').val(b.start_time);
        $('#end_time').val(b.end_time);
        $('#status').val(b.status);
        $('#notes').val(b.notes);

        $('#bookingModal').modal('show');
      });
    }
  });

  calendar.render();

});


$('#bookingForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#booking_id').val();
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/api/bookings/${id}` : '/api/bookings';

    const data = {
        customer_id: $('#customer_id').val(),
        employee_id: $('#employee_id').val(),
        service_id: $('#service_id').val(),
        booking_date: $('#booking_date').val(),
        start_time: $('#start_time').val(),
        end_time: $('#end_time').val(),
        status: $('#status').val(),
        notes: $('#notes').val(),
    };

    $.ajax({
        url: url,
        method: method,
        data: data,
        success: function() {
         $('#bookingModal').modal('hide');
         location.reload(); // Force full page reload
         // Reset form fields
         $('#bookingForm')[0].reset();
         $('#booking_id').val();
         $('#start_time').empty(); // Clear timeslot dropdowns
         $('#end_time').empty();
         $('#status').val('pending');
         $('#notes').val('');
        },
        error: function(xhr) {
            if (xhr.status === 409 && xhr.responseJSON?.suggested_start_time) {
                alert(`Time slot is already taken.\nTry:\nStart: ${xhr.responseJSON.suggested_start_time}\nEnd: ${xhr.responseJSON.suggested_end_time}`);
            } else {
                alert(xhr.responseJSON?.message || 'Booking failed');
            }
        }
    });
});


// Load dropdowns
$.get('/api/customers', res => {
    res.forEach(c => $('#customer_id').append(`<option value="${c.id}">${c.name}</option>`));
});
$.get('/api/employees', res => {
    res.forEach(e => $('#employee_id').append(`<option value="${e.id}">${e.name}</option>`));
});
$.get('/api/services', res => {
    res.forEach(s => $('#service_id').append(`<option value="${s.id}">${s.description} - Rs.${parseFloat(s.price).toFixed(2)}</option>`));
});


// Reset form when Add Booking button is clicked
  $(document).on('click', '[data-bs-target="#bookingModal"]', function () {
      $('#bookingForm')[0].reset(); // Reset all fields
      $('#booking_id').val('');     // Clear hidden ID
      $('#customer_id').removeData('selected-id'); // Clear selected data
      $('#employee_id').removeData('selected-id');
      $('#service_id').removeData('selected-id');
      $('#notes').val('');
      $('#status').val('pending'); // Reset status
  });

</script>
