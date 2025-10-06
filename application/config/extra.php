<?php
	$config['title'][1] = 'SALMAN'; 
	$config['title'][2] = 'COLLECTION';

	$config['role']['ADMIN'] 		= 'ADMIN';
	$config['role']['PURCHASE'] 	= 'PURCHASE';
	$config['role']['SALES'] 		= 'SALES';
	// $config['role']['SUPER ADMIN'] 	= 'SUPER ADMIN';

	$config['status'][1]= 'ACTIVE';
	$config['status'][2]= 'INACTIVE';

	$config['drcr']['DR']= 'DR';
	$config['drcr']['CR']= 'CR';
	$config['DEFAULT_ACCOUNT']= [1,2,3,4,5];

	$config['group']['CUSTOMER']= 'CUSTOMER';
	$config['group']['GENERAL'] = 'GENERAL';
	$config['group']['SUPPLIER']= 'SUPPLIER';

	$config['payment_mode']['CASH'] 		= '1. CASH';
	$config['payment_mode']['ONLINE'] 		= '2. ONLINE';
	$config['payment_mode']['WALLET'] 		= '3. WALLET';
	$config['payment_mode']['CARD']	        = '4. CARD';
	$config['payment_mode']['CREDIT'] 		= '5. CREDIT';
	
	$config['pagination']['query_string_segment'] 	= 'offset';
	$config['pagination']['page_query_string'] 		= true;
	$config['pagination']['total_rows'] 			= TOTAL_ROWS;
	$config['pagination']['per_page'] 				= PER_PAGE;

	$config['pagination']['full_tag_open'] 			= '
														<nav aria-label="Page navigation example">
															<ul class="pagination justify-content-center">
													  ';

	$config['pagination']['prev_tag_open']			= '
																<li class="page-item">
													  ';
	
	$config['pagination']['prev_link']				= '
																		<span aria-hidden="true"><i class="fa fa-backward"></i></span>
																		<span class="sr-only">PREVIOUS</span>
													  ';

	$config['pagination']['prev_tag_close'] 		= '
																</li>
													  ';
	

	$config['pagination']['first_tag_open']			= '
																<li class="page-item">
													  ';
	
	$config['pagination']['first_link']				= '
																		<span aria-hidden="true"><i class="fa fa-step-backward"></i></span>
																		<span class="sr-only">FIRST</span>
													  ';

	$config['pagination']['first_tag_close'] 		= '
																</li>
													  ';													  						
	

	$config['pagination']['last_tag_open']			= '
																<li class="page-item">
													  ';
	
	$config['pagination']['last_link']				= '
																		<span aria-hidden="true"><i class="fa fa-step-forward"></i></span>
																		<span class="sr-only">LAST</span>
													  ';

	$config['pagination']['last_tag_close'] 		= '
																</li>
													  ';													  						

	
	$config['pagination']['next_tag_open']			= '
																<li class="page-item">
													  ';
	
	$config['pagination']['next_link']				= '
																		<span aria-hidden="true"><i class="fa fa-forward"></i></span>
																		<span class="sr-only">NEXT</span>
													  ';

	$config['pagination']['next_tag_close'] 		= '
																</li>
													  ';													  					

	$config['pagination']['num_tag_open']			= '
																<li class="page-item">
													  ';
	
	$config['pagination']['num_tag_close']			= '
																</li>
													  ';												  							

	$config['pagination']['cur_tag_open']			= '
																<li class="page-item active">
																	<span class="page-link">
													  ';
	
	$config['pagination']['cur_tag_close']			= '
																	</span>
																</li>
													  ';															  							  

	$config['pagination']['full_tag_close'] 		= '
															</ul>
														</nav>
													  ';
?>