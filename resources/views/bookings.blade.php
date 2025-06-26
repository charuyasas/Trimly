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
                <input type="date" id="booking_date" class="form-control" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
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

$(function () {
    loadCalendar();
});

function loadCalendar() {
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

  const status = info.event.extendedProps.status;
  info.el.style.backgroundColor = statusColors[status] || '#6c757d';
  info.el.style.color = 'white';
  info.el.style.borderRadius = '4px';
  info.el.style.position = 'relative';
  info.el.style.overflow = 'visible';

  if (status === 'completed') return;

  // Tooltip container
  const tooltip = document.createElement('div');
  tooltip.classList.add('status-tooltip');
  tooltip.style.position = 'absolute';
  tooltip.style.top = '100%';
  tooltip.style.left = '50%';
  tooltip.style.transform = 'translate(-50%, -5%)';
  tooltip.style.background = '#fff';
  tooltip.style.padding = '6px 10px';
  tooltip.style.borderRadius = '6px';
  tooltip.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.12)';
  tooltip.style.display = 'none';
  tooltip.style.zIndex = '9999';
  tooltip.style.whiteSpace = 'nowrap';
  tooltip.style.gap = '6px';
  tooltip.style.border = '1px solid #e2e2e2';
  tooltip.style.alignItems = 'center';
  tooltip.style.fontSize = '12px';

  const bookingId = info.event.id;

  const createButton = (text, value, color) => {
    const btn = document.createElement('button');
    btn.innerText = text;
    btn.style.marginRight = '5px';
    btn.style.padding = '4px 10px';
    btn.style.fontSize = '11px';
    btn.style.border = 'none';
    btn.style.borderRadius = '4px';
    btn.style.backgroundColor = color;
    btn.style.color = '#fff';
    btn.style.cursor = 'pointer';
    btn.style.transition = 'background 0.2s ease';

    btn.onclick = (e) => {
      e.stopPropagation();
      updateBookingStatus(bookingId, value);
    };

    return btn;
  };

  if (status === 'pending') {
    tooltip.appendChild(createButton('Confirm', 'confirmed', '#198754'));
    tooltip.appendChild(createButton('Cancel', 'cancelled', '#dc3545'));
    tooltip.appendChild(createButton('Complete', 'completed', '#0d6efd'));
  } else if (status === 'confirmed') {
    tooltip.appendChild(createButton('Cancel', 'cancelled', '#dc3545'));
    tooltip.appendChild(createButton('Complete', 'completed', '#0d6efd'));
  }

  //Attach tooltip below label
  info.el.appendChild(tooltip);

  // Show/hide on hover
  info.el.addEventListener('mouseenter', () => {
    tooltip.style.display = 'inline-flex';
  });

  info.el.addEventListener('mouseleave', () => {
    tooltip.style.display = 'none';
  });
},
    // Add event click handler to load modal
    eventClick: function (info) {
      const bookingId = info.event.id;
      const status = info.event.extendedProps.status;

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

        // Enable/disable form fields based on status
      if (status !== 'pending') {
        $('#bookingForm select, #bookingForm input, #bookingForm textarea').attr('disabled', true);
        $('#bookingForm button[type="submit"]').hide();
      } else {
        $('#bookingForm select, #bookingForm input, #bookingForm textarea').removeAttr('disabled');
        $('#bookingForm button[type="submit"]').show();
      }

        $('#bookingModal').modal('show');
      });
    }
  });

  calendar.render();

}
//not disable form fields with add button
$('#bookingModal').on('hidden.bs.modal', function () {
  $('#bookingForm select, #bookingForm input, #bookingForm textarea').removeAttr('disabled');
  $('#bookingForm button[type="submit"]').show();
});


function updateBookingStatus(id, newStatus) {
  $.ajax({
    url: `/api/bookings/${id}`,
    type: 'PUT',
    data: {
      status: newStatus
    },
    success: function () {
    //   calendar.refetchEvents();
       loadCalendar(); // Reload calendar to reflect changes
    },
    error: function (xhr) {
      alert(xhr.responseJSON?.message || 'Status update failed.');
    }
  });
}

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
         loadCalendar(); // reload calendar only
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

// Update time-picker based on selected date
  document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('booking_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    function updateTimeLimits() {
        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        const isToday = selectedDate.toDateString() === today.toDateString();

        const officeStart = "07:00";
        const officeEnd = "21:00";

        let minTime;

        if (isToday) {
            const now = new Date();
            now.setMinutes(now.getMinutes() + (30 - now.getMinutes() % 30)); // round to next 30 mins
            const h = now.getHours().toString().padStart(2, '0');
            const m = now.getMinutes().toString().padStart(2, '0');
            minTime = `${h}:${m}`;
        } else {
            minTime = officeStart;
        }

        startTimeInput.min = minTime;
        endTimeInput.min = minTime;

        startTimeInput.max = officeEnd;
        endTimeInput.max = officeEnd;
    }

    dateInput.addEventListener('change', updateTimeLimits);
});
dateInput.addEventListener('change', function () {
    startTimeInput.value = '';
    endTimeInput.value = '';
    updateTimeLimits();
});

</script>
