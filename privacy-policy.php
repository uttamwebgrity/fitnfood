<?php
								include_once("includes/header.php");
								?>
								<div class="inrBnr">
									<?php $db_common->static_page_banner($dynamic_content['page_id']);?>									
								</div>
								
								<div class="bodyContent">
								  <div class="mainDiv2">
								  	<h1><?php echo trim($dynamic_content['title']); ?></h1>
								    <p><?php echo trim($dynamic_content['file_data']); ?></p>
								  </div>
								</div>
								<?php
								include_once("includes/footer.php");
								?>