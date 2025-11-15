<footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Smart Gen Softech</span>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Smart Gen <a href="#" target="_blank">Softech</a></span>
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- Global Loading Spinner -->
<div id="globalSpinner" class="position-fixed w-100 h-100 d-none" style="background: rgba(0,0,0,0.3); z-index: 9999;top:0;">
    <div class="position-absolute  translate-middle text-center" style="left: 50%;top: 50%;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        </div>
        <div class="text-white mt-2">Please wait...</div>
    </div>
</div>


    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
    <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="../assets/vendors/chart.js/Chart.min.js"></script>
    <script src="../assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="../assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../assets/js/off-canvas.js"></script>
    <script src="../assets/js/hoverable-collapse.js"></script>
    <script src="../assets/js/misc.js"></script>
    <script src="../assets/js/settings.js"></script>
    <script src="../assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="../assets/js/dashboard.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- End custom js for this page -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
$(document).ready(function() {
    $('.collapse').collapse('hide');

    $('.collapse').on('show.bs.collapse', function() {
        $('.collapse').not(this).collapse('hide');
    });
});
</script>
<style>
      #globalSpinner.show {
        display: block;
    }
</style>
<script>
const Spinner = {
    show: function() {
        $('#globalSpinner').removeClass('d-none').addClass('globalSpinner');
    },
    hide: function() {
        $('#globalSpinner').removeClass('d-flex').addClass('d-none');
    }
};

// Add global AJAX event handlers
$(document).ajaxStart(function() {
    Spinner.show();
});

$(document).ajaxStop(function() {
    Spinner.hide();
});
</script>
  </body>