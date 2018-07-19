<?php

	
	
/**
*
* (c) roadthemes.com /Init widgets
*
*/ 
 wp_enqueue_style('importdata-style', plugins_url('css/style.css',__FILE__ )); 
?>
<div class="import-data-content">

	<section class="wrap">
			
			<div class="row" style="padding: 15px">
				
				<section class="content">
					<h1><?php echo ('Import Data Demo')?></h1>
		
					<?php 
						
						if( !empty( $_POST['importSampleData'] ) ){
					
					?>
					
						<div id="errorImportMsg" class="p" style="width:100%;"></div>
						<div id="importWorking">
							<h2 style="color: #1D9126;">The importer is working</h2>
							
							<i>Status: <span id="import-status" style="font-size: 12px;color: maroon;">Preparing for importing...</span></i>
							<div id="importStatus" style="width:0%"></div>
						</div>
					
					    <script type="text/javascript">
					    	
					    	var docTitle = document.title;
					    	var el = document.getElementById('importStatus');
					    	
					    	function istaus( is ){
					    		
					    		var perc = parseInt( is*100 )+'%';
					    		el.style.width = perc;
					    		
					    		if( perc != '100%' ){
					    			el.innerHTML = perc+' Complete';
					    		}	
					    		else{
						    		el.innerHTML = 'Download Completed!  &nbsp;  Initializing Data...';	
					    		}
					    		document.title = el.innerHTML+'  - '+docTitle;
					    	}
					    	
					    	function tstatus( t ){ 
					    		document.getElementById('import-status').innerHTML = t;
					    	}
					    	
					    	function iserror( msg ){
						    	document.getElementById('errorImportMsg').innerHTML += '<div class="alert alert-danger">'+msg+'</div>';
						    	document.getElementById('errorImportMsg').style.display = 'inline-block';
					    	}
					    </script>
					<?php	
								
							include THEME_DIRECTORY.DS.'road_importdata'.DS.'importer.php';		
													
					?>		
						<script type="text/javascript">document.getElementById('importWorking').style.display = 'none';</script>
						<h2 style="color: blue;">The data have imported succesfully</h2>
					<?php	
						}else{
								include THEME_DIRECTORY.DS.'road_importdata'.DS.'data_demo.php';
					?>
					 
						<div class="content-inner">
							<div class="loading"> 
								<div class="image">
									<img src="<?php echo THEME_URI; ?>/road_importdata/images/loading.gif" /> 
								</div>
							</div>
							<?php 
							
							$default_tmp = $array_imports['digital1']['page_name'];
							$home_page_default = get_page_by_title($default_tmp);	
							
							
							
							foreach($array_imports as $key => $val) {
								$home_page = get_page_by_title($val['page_name']);
								if(isset($home_page_default->ID) && $home_page_default->ID) {
									$demo_data = 'install_data';
								} else {
									$demo_data = 'demo_data';
								}							
								if(isset($val['is_default']) && $val['is_default']==1) {
									$demo_data = 'is_default';
								}	
								$frontpage_id = get_option( 'page_on_front' );
								$is_active = '';
								if(isset($home_page->ID) && $frontpage_id == $home_page->ID) {
									$is_active = 'actived';
								}
								
							?>
								<div class="r_item <?php echo $demo_data .' '.$is_active; ?>"  >
									<div class="item-inner">
										<div class="image">
											<img src="<?php echo THEME_URI; ?>/road_importdata/images/<?php echo $val['image'];?>.png" class="pull-right1" />	
											<?php if(isset($home_page->ID)) { ?>
												<label><?php echo ('imported');?></label>
											<?php } ?> 
										</div>	
										<h3 class="name"><?php echo $val['page_name']; ?></h3>
										<form action="" method="post" onsubmit="doSubmit(this)">  
											<?php if (isset($home_page->ID) && $home_page->ID) { ?>
													
													<input type="submit" <?php if($frontpage_id == $home_page->ID) { echo 'disabled="true" value="Activated"';} else { echo 'value="Activate"';} ?>  id="submitbtn_act"  class="btn submit-btn " />
													<input type="hidden" value="2" name="active_demo" />
											<?php  } else {
											?>
													<input type="submit" id="submitbtn" value="import demo" class="btn submit-btn" />
											<?php									
											} ?> 
											<p id="imp-notice">
												
											</p> 
											<input type="hidden" value="1" name="importSampleData" />
											<input type="hidden" value="<?php echo $key; ?>" name="import_data" /> 
										</form>	 
									</div>	
								</div>				
							<?php } ?>
						</div>
					<?php } ?>
						
				</section><!-- /content -->

			</div><!-- /row -->
	
			<div class="row">
	
			
			</div><!-- /row -->

	  </section>
</div>		
<script type="text/javascript">

	var loading = jQuery('.loading');
		loading.hide(); 
	function doSubmit( form ){
	
		var btn = document.getElementById('submitbtn');
		btn.className+=' disable';
		btn.disabled=false;
		btn.value='Importing.....';
		loading.show();
		
		document.getElementById('imp-notice').style.display = 'block';
	}
	
</script>  

 
