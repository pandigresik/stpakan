<div class="row">
    <div class="col-md-12">        
        <div class="col-md-3" id="listTable">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Table
                </div>
                <div class="panel-body">
                <?php 
                    if(!empty($tables)){
                        echo '<ul class="list-group">';
                        foreach($tables as $t){                 
                            echo '<li onclick="Adminer.pilih(this)" class="list-group-item link_span">'.word_wrap($t,25).'</li>';                                            
                        }
                        echo '</ul>';
                    }
                ?>
                </div>
            </div>                        
        </div>
        <div class="col-md-9" id="command_page">
            <?php echo $page ?>
        </div>
    </div>
</div>

<script type="text/javascript" src="assets/js/sinkronisasi/adminer.js"></script>
