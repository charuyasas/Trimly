// Vendor JS imports - Order can be important
import './vendor/jquery1-3.4.1.min.js';
import './vendor/popper1.min.js';
import './vendor/bootstrap1.min.js';
import './vendor/metisMenu.js';

// jQuery dependent plugins and other general libraries
import './vendor/count_up/jquery.waypoints.min.js';
import './vendor/count_up/jquery.counterup.min.js'; // Depends on waypoints
import './vendor/niceselect/js/jquery.nice-select.min.js';
import './vendor/owl_carousel/js/owl.carousel.min.js';
import './vendor/datatable/js/jquery.dataTables.min.js'; // jQuery plugin
import './vendor/progressbar/jquery.barfiller.js'; // jQuery plugin

// DataTables extensions (likely depend on jquery.dataTables)
import './vendor/datatable/js/dataTables.responsive.min.js';
import './vendor/datatable/js/dataTables.buttons.min.js';
import './vendor/datatable/js/buttons.flash.min.js';
import './vendor/datatable/js/jszip.min.js'; // For excel/zip export
import './vendor/datatable/js/pdfmake.min.js'; // For PDF export
import './vendor/datatable/js/vfs_fonts.js'; // For PDF export
import './vendor/datatable/js/buttons.html5.min.js'; // For HTML5 export buttons
import './vendor/datatable/js/buttons.print.min.js'; // For print button

// Datepicker
import './vendor/datepicker/datepicker.js';
import './vendor/datepicker/datepicker.en.js'; // Language file, load after main datepicker
import './vendor/datepicker/datepicker.custom.js'; // Customizations

// Charting libraries
import './vendor/chartlist/Chart.min.js'; // Generic Chart.js (from chartlist)
import './vendor/chart.min.js'; // Generic Chart.js (from direct js path - ensure this is not a duplicate or resolve if it is)
import './vendor/chartjs/roundedBar.min.js'; // Chart.js plugin

// Other utilities and plugins
import './vendor/tagsinput/tagsinput.js';
import './vendor/text_editor/summernote-bs4.js';
import './vendor/am_chart/amcharts.js'; // amCharts (older version it seems)

// Scrollbar
import './vendor/scroll/perfect-scrollbar.min.js';
import './vendor/scroll/scrollable-custom.js'; // Depends on perfect-scrollbar

// Vector Maps
import './vendor/vectormap-home/vectormap-2.0.2.min.js';
import './vendor/vectormap-home/vectormap-world-mill-en.js'; // Map data

// Apex Charts
import './vendor/apex_chart/apex-chart2.js';
import './vendor/apex_chart/apex_dashboard.js'; // Specific dashboard setup for Apex

// amCharts v4 (more modern amCharts if used)
import './vendor/chart_am/core.js';
import './vendor/chart_am/charts.js';
import './vendor/chart_am/animated.js';
import './vendor/chart_am/kelly.js'; // Theme
import './vendor/chart_am/chart-custom.js'; // Custom amChart v4 setup

// Theme specific initializations - should typically be last or near last
import './vendor/dashboard_init.js';
import './vendor/custom.js';

// Laravel's bootstrap.js
import './bootstrap.js';
