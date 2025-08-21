@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Bookings'])

<style>
  .fc .fc-timeline-slot-cushion {      /*Timeline text color*/
   color: rgb(57, 58, 59) !important;
  }
   .fc-addBooking-button {
    background-color: #6f42c1 !important; /*add button color*/
    border-color: #6f42c1 !important;
    color: white !important;
  }
  .fc-toolbar-title {
    font-size: 18%;
    font-weight: bold;
    color: #6f42c1;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  .white_card {
      padding: 20px;
  }
</style>

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="white_card card_height_100 mb_30">
            <div id="calendar"> </div>
        </div>
    </div>
</div>

@include('includes.footer')

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="bookingForm">
        <div class="modal-header">
          <h5 class="modal-title" id="bookingModalTitle">Booking</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="booking_id">
            <div class="common_input mb_15">
                <label>Customer</label>
                <div class="button-group position-relative">
                   <input type="text" class="form-control" id="cbo_customer" placeholder="Search customer..." autocomplete="off" style="max-width: 100%;">
                   <button type="button" id="addCustomerBtn" class="btn position-absolute top-50 end-0 translate-middle-y me-1 btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" style="z-index: 10;" title="Add Customer">
                     <i class="fas fa-plus"></i>
                    </button>
                </div>
                <input type="hidden" id="customer_id">
            </div>
            <div class="common_input mb_15">
                <label>Haircutter</label>
                <input type="text" class="form-control" id="cbo_employee" placeholder="Search employee..." autocomplete="off">
                <input type="hidden" id="employee_id">
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
                <input type="text" class="form-control" id="cbo_service" placeholder="Search service..." autocomplete="off">
                <input type="hidden" id="service_id">
            </div>
            <div class="common_input mb_15">
                <label>Notes</label>
                <textarea class="form-control" id="notes"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnBooking" onclick="saveBooking()">Save Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!--Add Customer Modal-->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <input type="hidden" id="customer_id">
                    <div class="white_card_body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="name" placeholder="Customer Name">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="email" id="email" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="phone" class="contactNo" placeholder="Phone">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="common_input mb_15">
                                    <input type="text" id="address" placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save</button>
                </div>
            </div>
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
     customButtons: {
     addBooking: {
      text: 'Add Booking',
      click: function () {
        $('#bookingForm')[0].reset();
        $('#booking_id').val('');
        $('#customer_id, #employee_id, #service_id').val('');
        $('#status').val('pending');
        $('#notes').val('');
        $('#bookingModalTitle').text('Add Booking');
        $('#btnBooking').text('Save');
        $('#bookingModal').modal('show');
      }
     }
    },
    resourceAreaWidth: '160px',
    headerToolbar: {
      left: 'today prev,next',
      center: 'title',
      right: 'addBooking resourceTimelineDay,resourceTimelineWeek'
    },
    // Custom button text
    buttonText: {
    today: 'TODAY',
    day: 'DAY',
    week: 'WEEK'
    },

    aspectRatio: 1.5,

    slotDuration: '01:00:00',
    slotLabelInterval: '01:00',
    slotMinTime: "07:00:00",
    slotMaxTime: "24:00:00",
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
        // Customer
        $('#customer_id').val(b.customer_id);
        $('#cbo_customer').val(`${b.customer?.name} - ${b.customer?.phone}`);
        // Employee
        $('#employee_id').val(b.employee_id);
        $('#cbo_employee').val(`${b.employee?.employee_id} - ${b.employee?.name}`);
        // Service
        $('#service_id').val(b.service_id);
        $('#cbo_service').val(`${b.service?.description} - Rs.${parseFloat(b.service?.price).toFixed(2)}`);
        $('#booking_date').val(b.booking_date);
        $('#start_time').val(b.start_time);
        $('#end_time').val(b.end_time);
        $('#status').val(b.status);
        $('#notes').val(b.notes);

        // Enable/disable form fields based on status
      if (status !== 'pending') {
        $('#bookingForm select, #bookingForm input, #bookingForm textarea').attr('disabled', true);
        $('#btnBooking').hide();
        $('#addCustomerBtn').hide();
      } else {
        $('#bookingForm select, #bookingForm input, #bookingForm textarea').removeAttr('disabled');
        $('#btnBooking').show();
        $('#addCustomerBtn').show();
      }
        $('#bookingModalTitle').text('Update Booking');
        $('#btnBooking').text('Update');
        $('#bookingModal').modal('show');
      });
    }
  });

  calendar.render();

}
//not disable form fields with add button
$('#bookingModal').on('hidden.bs.modal', function () {
  $('#bookingForm select, #bookingForm input, #bookingForm textarea').removeAttr('disabled');
  $('#btnBooking').show();
  $('#addCustomerBtn').show();
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
        Swal.fire({
            icon: 'success',
            title: 'Booking status updated!',
            showConfirmButton: false,
            timer: 1500
        });
    },
    error: function (xhr) {
        Swal.fire({
            icon: 'error',
            title: xhr.responseJSON?.message || 'Status update failed!',
            showConfirmButton: false,
            timer: 1500
        });
    }
  });
}

function saveBooking() {
    const id = $('#booking_id').val();
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/api/bookings/${id}` : '/api/bookings';

    const bookingDate = $('#booking_date').val();
    const startTime = $('#start_time').val();
    const endTime = $('#end_time').val();

    // Prevent booking past time if today
    const today = new Date().toISOString().split('T')[0];

    if (bookingDate === today) {
        const now = new Date();
        const currentMinutes = now.getHours() * 60 + now.getMinutes();
        const selectedMinutes = parseInt(startTime.split(':')[0]) * 60 + parseInt(startTime.split(':')[1]);

        if (selectedMinutes < currentMinutes) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Time!',
                text: 'You cannot book a past time slot for today.'
            });
            return;
        }
    }

    const data = {
        customer_id: $('#customer_id').val(),
        employee_id: $('#employee_id').val(),
        service_id: $('#service_id').val(),
        booking_date: bookingDate,
        start_time: startTime,
        end_time: endTime,
        status: $('#status').val(),
        notes: $('#notes').val(),
    };

    $.ajax({
        url: url,
        method: method,
        data: data,
        success: function() {
            $('#bookingModal').modal('hide');
            loadCalendar(); // Reload calendar only
            $('#bookingForm')[0].reset();
            $('#booking_id').val('');
            $('#start_time').empty();
            $('#end_time').empty();
            $('#status').val('pending');
            $('#notes').val('');
            Swal.fire({
                icon: 'success',
                title: 'Booking saved successfully!',
                showConfirmButton: false,
                timer: 1500
            });
        },
        error: function(xhr) {
            if (xhr.status === 409 && xhr.responseJSON?.suggested_start_time) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Time slot is already taken',
                    html: `Try:<br>Start: <strong>${xhr.responseJSON.suggested_start_time}</strong><br>End: <strong>${xhr.responseJSON.suggested_end_time}</strong>`,
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: xhr.responseJSON?.message || 'Booking failed!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    });
}

$("#cbo_employee").autocomplete({
    source: function (request, response) {
        if (request.term.length < 1) return;
        $.ajax({
            url: '/api/employees-list',
            dataType: 'json',
            data: { search_key: request.term },
            success: function (data) {
                response(data);
                if (data.length === 1) {
                    $("#cbo_employee").val(data[0].label);
                    $("#employee_id").val(data[0].value);
                }
            }
        });
    },
    minLength: 1,
    appendTo: "#bookingModal",
    focus: function (event, ui) {
        $("#cbo_employee").val(ui.item.label);
        return false;
    },
    select: function (event, ui) {
        $("#cbo_employee").val(ui.item.label);
        $("#employee_id").val(ui.item.value);
        return false;
    }
});

$("#cbo_customer").autocomplete({
    source: function (request, response) {
        if (request.term.length < 1) return;
        $.ajax({
            url: '/api/customer-list',
            dataType: 'json',
            data: { search_key: request.term },
            success: function (data) {
                response(data);
                if (data.length === 1) {
                    $("#cbo_customer").val(data[0].label);
                    $("#customer_id").val(data[0].value);
                }
            }
        });
    },
    minLength: 1,
    appendTo: "#bookingModal",
    focus: function (event, ui) {
        $("#cbo_customer").val(ui.item.label);
        return false;
    },
    select: function (event, ui) {
        $("#cbo_customer").val(ui.item.label);
        $("#customer_id").val(ui.item.value);
        return false;
    }
});

$("#cbo_service").autocomplete({
    source: function (request, response) {
        if (request.term.length < 1) return;
        $.ajax({
            url: '/api/service-list',
            dataType: 'json',
            data: { search_key: request.term },
            success: function (data) {
                response(data);
                if (data.length === 1) {
                    $("#cbo_service").val(data[0].label);
                    $("#service_id").val(data[0].value);
                }
            }
        });
    },
    minLength: 1,
    appendTo: "#bookingModal",
    focus: function (event, ui) {
        $("#cbo_service").val(ui.item.label);
        return false;
    },
    select: function (event, ui) {
        $("#cbo_service").val(ui.item.label);
        $("#service_id").val(ui.item.value);
        return false;
    }
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
        const officeEnd = "23:00";

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

//save customer function
function saveCustomer() {
    const apiUrlCust = '/api/customers';
        const data = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            address: $('#address').val()
        };
        $.ajax({
            url: `${apiUrlCust}`,
            method: 'POST',
            data: data,
            success: function() {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Customer added successfully!',
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Something went wrong!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            }
        });
    }

    function closeModal() {
        const modalElement = document.getElementById('exampleModalCenter');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            $('#customerForm')[0].reset();
            $('#customer_id').val('');
        }
    }

    // Handle Enter key to navigate through form fields
    $(document).on('keydown', '#bookingForm input, #bookingForm select, #bookingForm textarea, #customerForm input, #customerForm select, #customerForm textarea', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const form = $(this).closest('form');
            const focusables = form.find('input, select, textarea, button')
                .filter(':visible:not([readonly]):not([disabled])')
                .not('#addCustomerBtn');

            const index = focusables.index(this);

            if (index > -1 && index + 1 < focusables.length) {
                const next = focusables.eq(index + 1);
                next.focus();

                if (next.is('button') && /save|update/i.test(next.text().trim())) {
                    setTimeout(() => next.click(), 100);
                }
            } else {
                if (form.attr('id') === 'customerForm') {
                    saveCustomer();
                } else if (form.attr('id') === 'bookingForm') {
                    saveBooking();
                }
            }
        }
    });

</script>

