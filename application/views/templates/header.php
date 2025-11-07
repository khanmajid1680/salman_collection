<?php
  $user_id  	= $_SESSION['user_id'];
  $role     	= $_SESSION['user_role'];
  $name     	= $_SESSION['user_fullname'];
  $uname    	= $_SESSION['user_name'];
  $branch   	= $_SESSION['user_branch'];
  $fin_year 	= $_SESSION['fin_year'];
  $start_year 	= $_SESSION['start_year'];
  $end_year 	= $_SESSION['end_year'];
  $title		= $this->config->item('title');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	  	<title><?php echo $title[1].' '. $title[2]; ?></title>
	  	
	  	<!-- Tell the browser to be responsive to screen width -->
	  	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	  	<!-- Bootstrap 4 -->
	  	<link rel="stylesheet" href="<?php echo assets('plugins/bootstrap/css/bootstrap.min.css')?>">

	  	<!-- Font Awesome -->
	  	<link rel="stylesheet" href="<?php echo assets('plugins/font-awesome/css/font-awesome.min.css')?>">

	  	<!--Toastify-->
	  	<link rel="stylesheet" href="<?php echo assets('plugins/toastify/css/toastify.min.css'); ?>" media="screen,projection" />

	  	<!-- Date Picker -->
  		<link rel="stylesheet" href="<?php echo assets('plugins/datepicker/css/bootstrap-datepicker.css')?>">

		<!-- Toggle Switch -->
  		<link rel="stylesheet" href="<?php echo assets('plugins/toggle-switch/css/toggle.min.css')?>">

  		<!-- Select2 -->
  		<link rel="stylesheet" href="<?php echo assets('plugins/select2/css/select2.min.css')?>">
	
		<!-- Google Font -->
		<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
			
		<!-- custom style sheet -->
		<link rel="stylesheet" href="<?php echo assets('dist/css/bootstrap.css?v=2')?>">
		<link rel="stylesheet" href="<?php echo assets('dist/css/common.css')?>">
		<link rel="stylesheet" href="<?php echo assets('dist/css/floating.css')?>">
		<link rel="stylesheet" href="<?php echo assets('dist/css/loader.css')?>">
		<link rel="stylesheet" href="<?php echo assets('dist/css/select2.css')?>">
	</head>
	<body class="wrapper blur">
		<!-- Modal  -->
		<div class="modal fade" id="popup_modal_sm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	  		<div class="modal-dialog" role="document">
	    		<div class="modal-content">
		      		<div class="modal-header">
		        		<div class="modal-title modal-title-sm" id="exampleModalLongTitle"></div>
	        			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          				<span aria-hidden="true">&times;</span>
	        			</button>
	      			</div>
      				<div class="my-2 modal-body modal-body-sm"></div>
	      			<div class="modal-footer modal-footer-sm"></div>
	    		</div>
	  		</div>
		</div>
		<div class="modal fade" id="popup_modal_lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  		<div class="modal-dialog modal-lg">
		    	<div class="modal-content">
		    		<div class="modal-header">
			        	<div class="modal-title modal-title-lg" id="exampleModalLongTitle"></div>
			        	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          		<span aria-hidden="true">&times;</span>
			        	</button>
		      		</div>
		      		<div class="my-2 modal-body modal-body-lg"></div>
		      		<div class="modal-footer modal-footer-lg"></div>
		    	</div>
		  	</div>
		</div>
		<header class="sticky-top">
			<nav class="navbar navbar-expand-lg navbar-dark">
				<button class="navbar-toggler hamburger_button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    		<!-- <span class="navbar-toggler-icon"></span> -->
	    		<div class="hamburger_icon"><span></span><span></span><span></span></div>
	  		</button>
	  		<div class="d-flex flex-column">
	  			<a class="navbar-brand d-flex flex-wrap flex-column" href="<?php echo base_url('/home'); ?>">
	    			<span class="border-bottom text-white font-weight-bold font-italic text-center" style="font-size: 10px;">
			  			<span><?php echo $title[1]; ?>&nbsp;<?php echo $title[2]; ?></span>
		      		</span>
		      		<span class="text-white font-italic text-center" style="font-size: 12px;">
		  					<span class="text-white text-center font-italic" style="font-size: 12px;"><?php echo $fin_year ?></span>
			  				<input type="hidden" id="start_year" value="<?php echo $start_year ?>">
			  				<input type="hidden" id="end_year" value="<?php echo $end_year ?>">
		      		</span>	
	  			</a>
	  		</div>
	  		<div class="d-block d-sm-block d-md-block d-lg-none">
			    	<a class="p-2 rounded neu_flat_secondary text-secondary" href="<?php echo base_url('login/logout')?>" data-toggle="tooltip" data-placement="bottom" title="Logout">
		      		<i class="fa fa-sign-out"></i>
		      	</a>
			    </div>
		    </div>
		    <div class="collapse navbar-collapse scroll" id="navbarSupportedContent">
		    	<ul class="navbar-nav navbar-nav-mobile" >
		    		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
			  				<li class="nav-item dropdown position-static" id="master">
					      	<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    	      MASTER
					        </a>
				        	<div class="dropdown-menu w-100" aria-labelledby="navbarDropdown">
				        		<div class="d-flex flex-wrap justify-content-center">
				        			<div class="col-12 col-sm-12 col-md-4 col-lg-4">
				        				<a id="CUSTOMER" class="dropdown-item my-2" href="<?php echo base_url('master/account?action=view&type=CUSTOMER'); ?>">
					          			CUSTOMER
					          		</a>
					          		<a id="GENERAL" class="dropdown-item my-2" href="<?php echo base_url('master/account?action=view&type=GENERAL'); ?>">
					          			GENERAL
					          		</a>
					          		<a id="SUPPLIER" class="dropdown-item my-2" href="<?php echo base_url('master/account?action=view&type=SUPPLIER'); ?>">
					          			SUPPLIER
					          		</a>
					          		<a id="transport" class="dropdown-item my-2" href="<?php echo base_url('master/transport?action=view'); ?>">
					          			TRANSPORT
					          		</a>
				        			</div>
				        			<div class="col-12 col-sm-12 col-md-4 col-lg-4">
				        				<a id="city" class="dropdown-item my-2" href="<?php echo base_url('master/city?action=view'); ?>">
					          			CITY
					          		</a>
					          		<a id="country" class="dropdown-item my-2" href="<?php echo base_url('master/country?action=view'); ?>">
					          			COUNTRY
					          		</a>
					          		<a id="state" class="dropdown-item my-2" href="<?php echo base_url('master/state?action=view'); ?>">
					          			STATE
					          		</a>
				        			</div>
				        			<div class="col-12 col-sm-12 col-md-4 col-lg-4">
				        				<a id="branch" class="dropdown-item my-2" href="<?php echo base_url('master/branch?action=view'); ?>">
					          			BRANCH
					          		</a>
				        				<a id="person" class="dropdown-item my-2" href="<?php echo base_url('master/person?action=view'); ?>">
					          			SALES PERSON
					          		</a>
				        				<a id="user" class="dropdown-item my-2" href="<?php echo base_url('master/user?action=view'); ?>">
					          			USER
					          		</a>
				        			</div>
				        		</div>
				        	</div>
			      		</li>
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == PURCHASE): ?>
			  				<li class="nav-item dropdown position-static" id="item_description">
					        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    	      ITEM DESCRIPTION
					        </a>
				        	<div class="dropdown-menu w-100" aria-labelledby="navbarDropdown">
				        		<div class="d-flex flex-wrap justify-content-center text-center">
				        			<div class="col-12 col-sm-12 col-md-6 col-lg-4">
				        				<a id="age" class="dropdown-item my-2" href="<?php echo base_url('master/age?action=view'); ?>">
					          			AGE GROUP
					          		</a>
					          		<a id="brand" class="dropdown-item my-2" href="<?php echo base_url('master/brand?action=view'); ?>">
					          			BRAND
					          		</a>
				        			</div>
				        			<div class="col-12 col-sm-12 col-md-6 col-lg-4">
						          		<a id="design" class="dropdown-item my-2" href="<?php echo base_url('master/design?action=view'); ?>">
						          			DESIGN
						          		</a>
						          		<a id="style" class="dropdown-item my-2" href="<?php echo base_url('master/style?action=view'); ?>">
						          			STYLE
						          		</a>
						          		
				        			</div>
				        			<div class="col-12 col-sm-12 col-md-6 col-lg-4">
						          		<a id="style" class="dropdown-item my-2" href="<?php echo base_url('master/hsn?action=view'); ?>">
						          			HSN
						          		</a>
				        			</div>
				        		</div>
				        	</div>
				      	</li>
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == PURCHASE): ?>
			  				<li class="nav-item" id="purchase">
                  <a class="nav-link" href="<?php echo base_url('purchase?action=view')?>">
                    PURCHASE
                  </a>
                </li>
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == PURCHASE): ?>
			  				<li class="nav-item" id="purchase_return">
                  <a class="nav-link" href="<?php echo base_url('purchase_return?action=view')?>">
                    PURCHASE RETURN
                  </a>
                </li>
	  			<?php endif; ?>
	  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == SALES): ?>
	  				<li class="nav-item" id="sales">
                  <a class="nav-link" href="<?php echo base_url('sales?action=view')?>">
                    SALES
                  </a>
                </li>
	  			<?php endif; ?>

	  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == SALES): ?>
	  				<li class="nav-item" id="approval">
                  <a class="nav-link" href="<?php echo base_url('approval?action=view')?>">
                    APPROVAL/CREDIT
                  </a>
                </li>
	  			<?php endif; ?>

	  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == SALES): ?>
	  				<li class="nav-item" id="sales_return">
                  <a class="nav-link" href="<?php echo base_url('sales_return?action=view')?>">
                    SALES RETURN
                  </a>
                </li>
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == SALES): ?>
			  				<li class="nav-item" id="outward">
                  <a class="nav-link" href="<?php echo base_url('outward?action=view')?>">
                    OUTWARD
                  </a>
                </li>
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == SALES): ?>
			  				<li class="nav-item dropdown position-static" id="grn">
						        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    	      INWARD <span class="badge badge-light grn_pending"></span>
						        </a>
					        	<div class="dropdown-menu w-100" aria-labelledby="navbarDropdown">
					        		<div class="d-flex flex-wrap justify-content-center text-center">
					        			<div class="col-12 col-sm-12 col-md-6 col-lg-6">
					        				<a id="grn_pending" class="dropdown-item my-2" href="<?php echo base_url('grn/pending?action=view'); ?>">
						          			PENDING <span class="badge badge-warning grn_pending"></span>
						          		</a>
					        			</div>
					        			<div class="col-12 col-sm-12 col-md-6 col-lg-6">
					        				<a id="grn_view" class="dropdown-item my-2" href="<?php echo base_url('grn?action=view'); ?>">
						          			VIEW
						          		</a>
					        			</div>
					        		</div>
					        	</div>
				      		</li>
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == PURCHASE || $role == SALES): ?>
			  				<li class="nav-item dropdown position-static" id="voucher">
						        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    	      VOUCHER <span class="badge badge-light receipt_pending"></span>
						        </a>
					        	<div class="dropdown-menu w-100" aria-labelledby="navbarDropdown">
					        		<div class="d-flex flex-wrap justify-content-center text-center">
					        			<div class="col-12 col-sm-12 col-md-3 col-lg-3">
					        				<a id="payment" class="dropdown-item my-2" href="<?php echo base_url('voucher/payment?action=view'); ?>">
					          				PAYMENT
						          		</a>
					        			</div>
					        			<div class="col-12 col-sm-12 col-md-3 col-lg-3">
					        				<a id="receipt" class="dropdown-item my-2" href="<?php echo base_url('voucher/receipt?action=view'); ?>">
						          			RECEIPT
						          		</a>
					        			</div>
					        			<div class="col-12 col-sm-12 col-md-3 col-lg-3">
					        				<a id="branch_payment" class="dropdown-item my-2" href="<?php echo base_url('voucher/branch_payment?action=view'); ?>">
					          				BRANCH PAYMENT
						          		</a>
					        			</div>
					        			<div class="col-12 col-sm-12 col-md-3 col-lg-3">
					        				<a id="branch_receipt" class="dropdown-item my-2" href="<?php echo base_url('voucher/branch_receipt?action=view'); ?>">
					          				BRANCH RECEIPT <span class="badge badge-warning receipt_pending"></span>
						          			</a>
					        			</div>
					        		</div>
					        	</div>
				      		</li>
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
			  				<!-- <li class="nav-item" id="message">
			                  <a class="nav-link" href="<?php echo base_url('message?action=view')?>">
			                    SMS
			                  </a>
			                </li> -->
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
			  				<!-- <li class="nav-item" id="invoice">
			                  <a class="nav-link" href="<?php echo base_url('invoice?action=view')?>">
			                    CA REPORT
			                  </a>
			                </li> -->
			  			<?php endif; ?>
			  			<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == PURCHASE || $role == SALES): ?>
			  				<li class="nav-item dropdown position-static" id="report">
						        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    	      REPORT
						        </a>
					        	<div class="dropdown-menu w-100" aria-labelledby="navbarDropdown">
					        			<div class="d-flex flex-wrap justify-content-center">
						        			<div class="col-12 col-sm-12 col-md-4 col-lg-4">
						        				<a id="balance_stock" class="dropdown-item my-2" href="<?php echo base_url('report/balance_stock?action=view'); ?>">
							          			BALANCE STOCK
							          		</a>
							          		<a id="barcode_history" class="dropdown-item my-2" href="<?php echo base_url('report/barcode_history?action=view'); ?>">
							          			BARCODE HISTORY
							          		</a>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == PURCHASE || $role == SALES): ?>
							          		<a id="barcode_stock" class="dropdown-item my-2" href="<?php echo base_url('report/barcode_stock?action=view'); ?>">
							          			BARCODE STOCK
							          		</a>
							          		<?php endif; ?>
							          		<a id="vip_stock" class="dropdown-item my-2" href="<?php echo base_url('report/vip_stock?action=view'); ?>">
							          			BARCODE(VIP) STOCK
							          		</a>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="best_person" class="dropdown-item my-2" href="<?php echo base_url('report/best_person?action=view'); ?>">
							          			BEST SALES PERSON
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
								          		<a id="customer_ledger" class="dropdown-item my-2" href="<?php echo base_url('report/customer_ledger?action=view'); ?>">
								          			CUSTOMER LEDGER
								          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="customer_outstanding" class="dropdown-item my-2" href="<?php echo base_url('report/customer_outstanding?action=view'); ?>">
							          			CUSTOMER OUTSTANDING
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="ca_report" class="dropdown-item my-2" href="<?php echo base_url('report/ca_report?action=view'); ?>">CA REPORT</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
								          		<a id="trial_report" class="dropdown-item my-2" href="<?php echo base_url('report/trial_report?action=view'); ?>">
								          			ALTER REPORT
								          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
								          		<a id="daily_profit" class="dropdown-item my-2" href="<?php echo base_url('report/daily_profit?action=view'); ?>">
								          			BILL WISE PROFIT
								          		</a>
							          		<?php endif; ?>
						        			</div>
						        			<div class="col-12 col-sm-12 col-md-4 col-lg-4">
						        				<?php if($role == SUPER_ADMIN || $role == ADMIN || $role == SALES): ?>
							          		<a id="daily_transaction" class="dropdown-item my-2" href="<?php echo base_url('report/daily_transaction?action=view'); ?>">
							          			DAILY TRANSACTION
							          		</a>
							          		<?php endif; ?>
						        				<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="expense" class="dropdown-item my-2" href="<?php echo base_url('report/expense?action=view'); ?>">
							          			EXPENSE REPORT
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="general_ledger" class="dropdown-item my-2" href="<?php echo base_url('report/general_ledger?action=view'); ?>">
							          			GENERAL LEDGER
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="general_outstanding" class="dropdown-item my-2" href="<?php echo base_url('report/general_outstanding?action=view'); ?>">
							          			GENERAL OUTSTANDING
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="max_supplier_sale" class="dropdown-item my-2" href="<?php echo base_url('report/max_supplier_sale?action=view'); ?>">
							          			MAXIMUM VENDOR SALE
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="monthly_profit" class="dropdown-item my-2" href="<?php echo base_url('report/monthly_profit?action=view'); ?>">
							          			MONTHLY PROFIT
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="monthly_summary" class="dropdown-item my-2" href="<?php echo base_url('report/monthly_summary?action=view'); ?>">
							          			MONTHLY SUMMARY
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="non_moving" class="dropdown-item my-2" href="<?php echo base_url('report/non_moving?action=view'); ?>">
							          			NON MOVING STOCK
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="token_report_profit" class="dropdown-item my-2" href="<?php echo base_url('report/token_report_profit?action=view'); ?>">
							          			TOKEN SALE REPORT
							          		</a>
							          		<?php endif; ?>

						        			</div>
						        			<div class="col-12 col-sm-12 col-md-4 col-lg-4">
						        				<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="payment_reminder" class="dropdown-item my-2" href="<?php echo base_url('report/payment_reminder?action=view'); ?>">
							          			PAYMENT REMINDER
							          		</a>
							          		<?php endif; ?>
						        			<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
								          		<a id="purchase_summary" class="dropdown-item my-2" href="<?php echo base_url('report/purchase_summary?action=view'); ?>">
								          			PURCHASE SUMMARY
								          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
								          		<a id="purchase_summary_itemwise" class="dropdown-item my-2" href="<?php echo base_url('report/purchase_summary_itemwise?action=view'); ?>">
								          			PURCHASE SUMMARY ( ITEMWISE )
								          		</a>
							          		<?php endif; ?>

							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="purchase_return_summary" class="dropdown-item my-2" href="<?php echo base_url('report/purchase_return_summary?action=view'); ?>">
							          			PURCHASE RETURN SUMMARY
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="sales_summary" class="dropdown-item my-2" href="<?php echo base_url('report/sales_summary?action=view'); ?>">
							          			SALES SUMMARY
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
								          		<a id="sales_summary_itemwise" class="dropdown-item my-2" href="<?php echo base_url('report/sales_summary_itemwise?action=view'); ?>">
								          			SALES SUMMARY ( ITEMWISE )
								          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="sales_return_summary" class="dropdown-item my-2" href="<?php echo base_url('report/sales_return_summary?action=view'); ?>">
							          			SALES RETURN SUMMARY
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="supplier_ledger" class="dropdown-item my-2" href="<?php echo base_url('report/supplier_ledger?action=view'); ?>">
							          			SUPPLIER LEDGER
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="supplier_outstanding" class="dropdown-item my-2" href="<?php echo base_url('report/supplier_outstanding?action=view'); ?>">
							          			SUPPLIER OUTSTANDING
							          		</a>
							          		<?php endif; ?>
							          		<?php if($role == SUPER_ADMIN || $role == ADMIN): ?>
							          		<a id="today_sale" class="dropdown-item my-2" href="<?php echo base_url('report/today_sale?action=view'); ?>">
							          			WHAT SOLD TODAY
							          		</a>
							          		<?php endif; ?>
						        			</div>
						        		</div>
					        	</div>
				      		</li>
			  			<?php endif; ?>
		    		<li class="d-block d-sm-block d-md-block d-lg-none nav-item" id="profile">
            	<div class="d-flex flex-column">
				    			<span class="text-white font-weight-bold font-italic text-center border-bottom" style="font-size: 12px;">
					      		<?php echo strtoupper($uname); ?>
				      		</span>
				      		<span class="text-white font-italic text-center" style="font-size: 10px;">
					      		<?php echo strtoupper($branch); ?>
				      		</span>	
					    </div>
            </li>
		    	</ul>
		    </div>
		    <div class="d-none d-sm-none d-md-none d-lg-block">
		    	<div class="d-flex">
			    	<div class="d-flex flex-column ml-3 mr-2">
			    		<span class=" text-white font-weight-bold font-italic text-center border-bottom" style="font-size: 12px;">
				      		<?php echo strtoupper($uname); ?>
			      		</span>
			      		<span class=" text-white font-italic text-center" style="font-size: 10px;">
				      		<?php echo strtoupper($branch); ?>
			      		</span>	
			    	</div>
			    	<a class="mx-2 p-2 rounded neu_flat_secondary text-secondary" href="<?php echo base_url('login/logout')?>" data-toggle="tooltip" data-placement="bottom" title="Logout">
		      		<i class="fa fa-sign-out"></i>
		      	</a>
			    </div>
		    </div>
			</nav>
		</header>
		<main>