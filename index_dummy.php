<!DOCTYPE html>
<html>
<head>
	<title>ASE Controls</title>
	<script type="text/javascript" src="js/jquery.js"></script>
	<style type="text/css">
		.inputs input {
			padding: 5px;
			margin: 5px;
			width: 50%;
		}
	</style>
</head>
<body>

	<div class="values">
		<p>/home/jon/.CLion12/system/cmake/generated/82e833ec/82e833ec/Debug/ASE-WS2015-16</p>
		<p>/home/jon/Desktop/uni/ASE-WS2015-16/ASE-WS2015-16/testimages/fablab.png</p>
		<p>/home/jon/Desktop/out_from_php.txt</p>
	</div>

	<div class="inputs">
		<div>
			<input type="text" id="exec_path_input" placeholder="exec path">
		</div>
		<div>
			<input type="text" id="img_path_input" placeholder="img path">
		</div>
		<div>
			<input type="text" id="output_path_input" placeholder="output file path">
		</div>
	</div>

	<button id="run_program">Run Program</button>

<script>
	jQuery(document).ready(function($) {
		$("#run_program").on('click', runProgram);
	});		
	
	function runProgram(e) {
		$.ajax({
			url: 'exec_script.php',
			type: 'GET',
			data: 
				{
					exec_path: $("#exec_path_input").val(),
					img: $("#img_path_input").val(),
					toFile: $("#output_path_input").val()
				},
			success: function(e) {console.log(e);},
			error: function(e) {console.log(e);}
		});
	}
</script>
</body>
</html>