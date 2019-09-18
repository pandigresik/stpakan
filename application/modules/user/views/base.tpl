<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	<title>{block name=title} POBB {/block}</title>
	<meta charset="utf-8">
	{block name=head}
	    <meta name="viewport" content="width=device-width, initial-scale=10.0">
	    <base href="{base_url()}" />
	{/block}
	</head>
<body>
{block name=menu}{/block}   
{block name=body}{/block}   
</body>

{block name=cssLibrary}
	<link rel="stylesheet" media="all" type="text/css" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" media="screen" type="text/css" href="assets/css/toastr.css">
{/block}
{block name=cssAdditional}{/block}

{block name=js_library}
	<script type="text/javascript" src="assets/js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/toastr.js"></script>
	<script type="text/javascript" src="assets/js/bootbox.js"></script>
	<script type="text/javascript" src="assets/js/common.js"></script>
	<script type="text/javascript" src="assets/js/ajaxSetup.js"></script>
{/block}
{block name=jsAdditional}{/block}

</html>