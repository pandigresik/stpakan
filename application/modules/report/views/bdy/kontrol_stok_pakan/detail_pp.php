<div class="row">
    <div class="row">`
		<a style="position:absolute" class="tu-float-btn tu-float-btn-right tu-table-next" data-current="1" data-max="<?php echo $maxPage ?>" data-min="1" onclick="KSP.next(this)">
		    <i class="glyphicon glyphicon-circle-arrow-right my-float"></i>
		</a>
        <a style="position:absolute" class="tu-float-btn tu-float-btn-left tu-table-prev" data-current="1" data-max="<?php echo $maxPage ?>" data-min="1" onclick="KSP.prev(this)">
			<i class="glyphicon glyphicon-circle-arrow-left my-float"></i>
		</a>
	</div>
<div class="row container">
	<div class="col-md-12">
		Tanggal DOC In : <?php echo tglIndonesia($tgldocin,'-',' ') ?>	
	</div>

</div>
<div class="col-md-12">
	<?php echo $page1 ?>
	<?php echo $page2 ?>
	<?php echo $page3 ?>
	<?php echo $page4 ?>
</div>
</div>