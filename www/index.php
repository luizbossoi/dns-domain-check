<?php
if(@$_SERVER['REQUEST_METHOD']=='POST') {
	header('Content-type: application/json');
	$domain	= addslashes($_POST['domain']);
	
	// check if DNS entry exists
	$check	= dns_get_record($domain, DNS_A);
	$return	= array('domain'=>$domain, 'host'=>'', 'entry'=>'', 'ip'=>'', 'first_ip'=>'', 'httpCode'=>'&nbsp;');
	if($check) {
		$return['entry']	= json_encode($check);
		foreach($check as $cdns) {
			if(isset($cdns['ip'])) $return['ip'] = $return['ip'] . $cdns['ip'] . ', ';
			if(isset($cdns['host'])) {  if(stripos($return['host'], $cdns['host'])===false) { $return['host'] = $return['host'] . $cdns['host'] . ', '; }}
		}
		$return['ip'] = substr($return['ip'], 0, strlen($return['ip'])-2);
		$return['host'] = substr($return['host'], 0, strlen($return['host'])-2);
		
		$arr_ips = explode(',', $return['ip']);
		$return['first_ip'] = $arr_ips[0];
		
		// get server response code
		$handle = curl_init($domain);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($handle);
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if($httpCode) {
			$return['httpCode'] = 'HTTP ' . $httpCode;
		}
		curl_close($handle);
		
	} else {
		$return['ip'] = 'NXDOMAIN';
	}
	
	print(json_encode($return));
	exit();
}
?>
<html>
	<head>
		<title>Domain analyzer</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	</head>
	<body>
		<span style="font-size:16px;font-family:Arial;padding:10px">Domains for testing, line by line:</span><br><br>
		<div id="container">
			<textarea name="dominios" id="dominios" style="width:100%;height:300px" placeholder="Add domain line by line..."></textarea>
			<br><br>
			<button id="btn_chkdomain" style="width:100%;padding:10px;">Analyze</button>
			<hr>
			<div id="results"></div>
		</div>
		<div id="loader" style="display:none">
			<center><img src="loading.gif"></center>
		</div>

		
		<script>
			var x = 0;
			$("#btn_chkdomain").click(function() { 
				$("#loader").css('display','block');
				$("#results").html('<h3>Results</h3><div class="header">Domain</div><div class="header">Host</div><div class="header">IP Address</div><div class="header">Response</div>');
				var lines = $('#dominios').val().split('\n');
				for(var i = 0;i < lines.length;i++){
					if(lines[i].length>0) {
						x++;
						$.post( "", { domain: lines[i] })
						.done(function( data ) {
							$("#results").append("<div>" + data.domain + "</div><div>" + data.host + "</div><div>" + data.ip + "</div><div>" + data.httpCode + "</div>");
							x--;
						});
					}
				}
				checkFinished();
			});
			
			function checkFinished() {
				if(x==0) { $("#loader").hide(); } else { setTimeout(function() { checkFinished() }, 1000); }
			}
		</script>
		
		<style>
			#container {
				padding: 20px;
			}
			
			#results div.header {
				background-color:#CFCFCF;
			}
			
			#results h3 {
				font-family:Arial
			}
			
			#results div {
				width:calc(25% - 12px);
				float:left;
				border:solid 1px #CFCFCF;
				padding:4px;
				margin:1px;
				font-family:Arial;
				font-size:12px;
			}
		</style>
		
	</body>
</html>
