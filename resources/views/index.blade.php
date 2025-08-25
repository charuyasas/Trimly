@include('includes.header')
@include('includes.sidebar', ['pageTitle' => 'Dashboard'])

    <!--/ menu  -->
    <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <div class="row ">
                <div class="col-xl-8">
                    <div class="white_card mb_30 card_height_100">
                        <div class="white_card_header">
                            <div class="row align-items-center justify-content-between flex-wrap">
                                <div class="col-lg-4">
                                    <div class="main-title">
                                        <h3 class="m-0">Daily Sale</h3>
                                    </div>
                                </div>
                                <div class="col-lg-8 text-end d-flex justify-content-end gap-2">
                                    <!-- New week selector -->
                                    <input type="week" id="weekSelect" class="form-control max-width-220" />
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div id="daily_sales_chart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 ">
                    <div class="white_card card_height_100 mb_30 user_crm_wrapper">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="single_crm">
                                    <div class="crm_head d-flex align-items-center justify-content-between" >
                                        <div class="thumb">
                                            <img src="{{ asset('assets/img/crm/businessman.svg') }}" alt="">
                                        </div>
                                        <i class="fas fa-ellipsis-h f_s_11 white_text"></i>
                                    </div>
                                    <div class="crm_body">
                                        <h4 id="employeeCount"></h4>
                                        <p>Employees</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="single_crm ">
                                    <div class="crm_head crm_bg_1 d-flex align-items-center justify-content-between" >
                                        <div class="thumb">
                                            <img src="{{ asset('assets/img/crm/customer.svg') }}" alt="">
                                        </div>
                                        <i class="fas fa-ellipsis-h f_s_11 white_text"></i>
                                    </div>
                                    <div class="crm_body">
                                        <h4 id="serviceCount"></h4>
                                        <p>Services</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="single_crm">
                                    <div class="crm_head crm_bg_2 d-flex align-items-center justify-content-between" >
                                        <div class="thumb">
                                            <img src="{{ asset('assets/img/crm/infographic.svg') }}" alt="">
                                        </div>
                                        <i class="fas fa-ellipsis-h f_s_11 white_text"></i>
                                    </div>
                                    <div class="crm_body">
                                        <h4 id="customerCount"></h4>
                                        <p>Customers</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="single_crm">
                                    <div class="crm_head crm_bg_3 d-flex align-items-center justify-content-between" >
                                        <div class="thumb">
                                            <img src="{{ asset('assets/img/crm/sqr.svg') }}" alt="">
                                        </div>
                                        <i class="fas fa-ellipsis-h f_s_11 white_text"></i>
                                    </div>
                                    <div class="crm_body">
                                        <h4 id="itemCount"></h4>
                                        <p>Products</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="row align-items-center">
                                <div class="col-lg-4">
                                    <div class="main-title">
                                        <h3 class="m-0">Today Bookings</h3>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="row justify-content-end">
                                        <div class="col-lg-8 d-flex justify-content-end">
                                            <div class="serach_field-area theme_bg d-flex align-items-center">
                                                <div class="search_inner">
                                                    <form action="#">
                                                        <div class="search_field">
                                                            <input type="text" placeholder="Search content here..." class="searchBox" data-target="bookingTable">
                                                        </div>
                                                        <button type="submit"> <img src="{{ asset('assets/img/icon/icon_search.svg') }}" alt=""> </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body ">
                            <table class="table lms_table_active">
                                <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Employee</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Services</th>
                                </tr>
                                </thead>
                                <tbody id="bookingTable">
                                <!-- Dynamic Rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</section>
<!-- main content part end -->


@include('includes.footer')

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const weekInput = document.getElementById("weekSelect");
        const chartContainer = document.querySelector("#daily_sales_chart");
        let chart;

        function fetchDailySales(startDate, endDate) {
            fetch(`/api/dashboard/daily-sales?start=${startDate}&end=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    const labels = data.map(row => row.day);
                    const totals = data.map(row => row.total);

                    const options = {
                        chart: { height: 339, type: "bar", toolbar: { show: false } },
                        plotOptions: { bar: { columnWidth: "40%", borderRadius: 6 } },
                        colors: ["#9767FD"],
                        series: [{ name: "Sales", data: totals }],
                        xaxis: { categories: labels },
                        yaxis: { title: { text: "Amount (Rs)" } },
                        dataLabels: { enabled: true },
                        tooltip: {
                            y: {
                                formatter: function (val) { return "Rs " + val.toFixed(2); }
                            }
                        },
                        grid: { borderColor: "#f1f1f1" },
                    };

                    // Destroy previous chart before creating a new one
                    if (chart) chart.destroy();
                    chart = new ApexCharts(chartContainer, options);
                    chart.render();
                });
        }

        // Initialize with current week
        function loadCurrentWeek() {
            const today = new Date();
            const dayOfWeek = today.getDay();
            const monday = new Date(today);
            monday.setDate(today.getDate() - dayOfWeek + 1);
            const sunday = new Date(monday);
            sunday.setDate(monday.getDate() + 6);

            weekInput.value = getWeekString(monday);
            fetchDailySales(formatDate(monday), formatDate(sunday));
        }

        // Convert date to yyyy-mm-dd
        function formatDate(date) {
            return date.toISOString().slice(0,10);
        }

        // Convert date to input[type="week"] value
        function getWeekString(date) {
            const year = date.getFullYear();
            const oneJan = new Date(year,0,1);
            const numberOfDays = Math.floor((date - oneJan) / (24*60*60*1000));
            const week = Math.ceil((date.getDay() + 1 + numberOfDays) / 7);
            return `${year}-W${week.toString().padStart(2,'0')}`;
        }

        // On week change
        weekInput.addEventListener("change", function() {
            const [year, week] = this.value.split("-W");
            const firstDay = new Date(year, 0, (week - 1) * 7 + 1);
            const lastDay = new Date(firstDay);
            lastDay.setDate(firstDay.getDate() + 6);

            fetchDailySales(formatDate(firstDay), formatDate(lastDay));
        });

        function loadBookingDetails(){
            $.get('/api/loadTodayBookings', function(data) {
                let table = $('.lms_table_active').DataTable();
                table.clear();

                let rowID = 1;
                data.forEach(booking => {
                    let services = (booking.services_collection || [])
                        .map(s => s.description + ' (Rs.' + parseFloat(s.price).toFixed(2) + ')')
                        .join(', ');

                    table.row.add([
                        rowID,
                        booking.start_time+ '-' +booking.end_time,
                        booking.employee.name,
                        booking.customer.name,
                        services
                    ]);
                    rowID++;
                });

                table.draw();
            });
        }

        function loadSummaryDetails(){
            $.get('/api/loadSummaryCounts', function(data) {
                $('#employeeCount').text(data.employeeCount);
                $('#customerCount').text(data.customerCount);
                $('#itemCount').text(data.itemCount);
                $('#serviceCount').text(data.serviceCount);
            });
        }

        loadCurrentWeek();
        loadBookingDetails();
        loadSummaryDetails();
    });
</script>


