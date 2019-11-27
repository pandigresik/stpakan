<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<title>{block name=title} Analisis Sampel {/block}</title>
		{block name=head}
		<meta name="viewport" content="width=device-width, initial-scale=10.0">
		<base href="{base_url()}" />
		<link rel="stylesheet" media="screen" type="text/css" href="{base_url('assets/css/composit_sample/ui.fancytree.css')}">
		<link rel="stylesheet" media="screen" type="text/css" href="{base_url('assets/css/analysis_sample/style.css')}">
		<link rel="stylesheet" media="screen" type="text/css" href="{base_url('assets/css/jquery-ui.css')}">
		<link rel="stylesheet" media="screen" type="text/css" href="{base_url('assets/css/bootstrap.min.css')}">
		<link rel="stylesheet" media="screen" type="text/css" href="{base_url('assets/css/toastr.css')}">

		<style media="print">
			.hidden-print, .navbar{
				display: none;
			}
		</style>

		{/block}
		{block name=js_library}
		<script type="text/javascript" src="{base_url('assets/js/jquery-1.9.1.js')}"></script>
		<script type="text/javascript" src="{base_url('assets/js/jquery-ui.js')}"></script>
		<script type="text/javascript" src="{base_url('assets/js/bootstrap.min.js')}"></script>
		<script type="text/javascript" src="{base_url('assets/js/toastr.js')}"></script>
		<script type="text/javascript" src="{base_url('assets/js/jquery-barcode.js')}"></script>
		<script type="text/javascript" src="{base_url('assets/js/composit_sample/jquery.fancytree.js')}"></script>
		<script type="text/javascript" src="{base_url('assets/js/ajaxSetup.js')}"></script>
		{/block}
	</head>
	<body>
		{block name=body}{/block}

	</body>

	{block name=jsAdditional}{/block}
</html>
