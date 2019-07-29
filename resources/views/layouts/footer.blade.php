
  	<!-- Popper JS -->
    <script type="text/javascript" src="{{ asset('/js/popper-1.14.3.min.js') }}"></script>

    <!-- Bootstrap Datepicker JS -->
    <script type="text/javascript" src="{{ asset('/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script type="text/javascript" src="{{ asset('/js/bootstrap.min.js') }}"></script>

    <!-- Feather JS -->
    <script type="text/javascript" src="{{ asset('/js/feather.min.js') }}"></script>

    <!-- Select2 JS -->
    <script type="text/javascript" src="{{ asset('/js/select2_4.0.6-rc.0.min.js') }}"></script>

    <!-- DataTables-1.10.18\js\jquery.dataTables.min.js -->
    <!-- <script type="text/javascript" src="{{ asset('/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"> -->

    <!-- Chart.js -->
    <script type="text/javascript" src="{{ asset('/js/chart.js/dist/Chart.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('/js/demo/chart-pie-demo.js') }}"></script>



    </script>


	<script type="text/javascript">
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
	</script>

	<script type="text/javascript">
		feather.replace()
	</script>

	<script type="text/javascript">
		$(document).ready(function () {

      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

			$('#ordate').datepicker({
				format: "mm/dd/yyyy",
				autoclose: true
			});

      // Every time a modal is shown, if it has an autofocus element, focus on it.
      $('#change_password').on('shown.bs.modal', function() {
        $('#old_password').focus();
      });


      $('#save_password').on('click', function(e){
        e.preventDefault();

        var _token = CSRF_TOKEN;
        var old_password = $('#old_password').val();
        var new_password = $('#new_password').val();
        var confirm_new_password = $('#confirm_new_password').val();
        var user_id = $('#user_id_value').val();

        if (old_password == '' || new_password == '' || confirm_new_password == '') {
          console.log('please complete the details above .');

        } else if (new_password != confirm_new_password) {
          console.log('new password did not match');

        } else {
          // console.log(_token);
          // console.log(user_id);
          // console.log('passed');
          savePassword(_token, old_password, new_password, confirm_new_password, user_id);
        }
        // console.log('change password clicked');
      });


      function savePassword(_t, opass, npass, cnpass, uid){
        $.ajax({
          type: "POST",
          url: "/settings/change-password",
          data: { _token: _t, old_password: opass, new_password: npass, confirm_new_password: cnpass, user_id: uid },
          dataType: "JSON",
          success: function(data){
            console.log(data);
            var data = data.data;
            var is_saving = data.is_saving;

             $('#change_password').modal('toggle');

            // if (is_saving == 'true') {
            //   console.log('your password has been saved .');
            //
            // } else {
            //   console.log('your current password did not match with the password you provided .');
            // }
          }
        });
      }

		});
	</script>


	<script>
		$(document).ready(function () {
			$('#or_date').datepicker({
				format: "mm/dd/yyyy",
				autoclose: true
			});
		});
	</script>
