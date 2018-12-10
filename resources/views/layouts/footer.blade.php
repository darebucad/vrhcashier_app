  
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
    

	<script type="text/javascript">
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
	</script>
	    
	<script>
		feather.replace()
	</script>

	<script>
		$(document).ready(function () {
			$('#ordate').datepicker({
				format: "mm/dd/yyyy",
				autoclose: true
			});
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


  
