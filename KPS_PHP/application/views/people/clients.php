<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_199'));

tpl_assign("footer_for_layout", '<script>
$(document).ready(function(){
	$(\'#showOnlyFilter\').on(\'change\', function() {
		
		var location = "'.get_page_base_url('people/clients').'";
		var show_only = $("#showOnlyFilter").val();

		if (show_only != "") {
			location = location + "?show_company=" + show_only;
		}
		
		window.location.href = location;
		return false;

	});
});
</script>
');	

$show_company = input_get_request('show_company');

?>
<p><div class="row">
	<div class="col-md-6">
		<select class="form-control custom-fixed-width-select" id="showOnlyFilter">
			<option value="" selected="selected"><?php echo lang('c_227'); ?></option>
			<?php if(isset($client_companies) && is_array($client_companies) && count($client_companies)) : 
			foreach($client_companies as $client_company) : ?>
			<option value="<?php echo $client_company->getId(); ?>"<?php echo ($show_company == $client_company->getId() ? ' selected="selected"' : ''); ?>><?php echo $client_company->getName(); ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php if(isset($without_company_clients) && is_array($without_company_clients) && count($without_company_clients)) : ?>
			<option value="-1"<?php echo ($show_company == -1 ? ' selected="selected"' : ''); ?>>People without company</option>
			<?php endif; ?>
		</select>            
	</div>
	<div class="col-md-6 text-right">
		<a href="javascript:void();" data-url="<?php echo get_page_base_url('people/add/client'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal" >+ <?php echo lang('c_228'); ?></a>
		<a href="javascript:void();" data-url="<?php echo get_page_base_url('companies/add'); ?>" class="btn btn-primary custom-m-3"  data-toggle="commonmodal">+ <?php echo lang('c_229'); ?></a>
	</div>
</div></p>

<?php if(isset($client_companies) && is_array($client_companies) && count($client_companies)) : 
foreach($client_companies as $client_company) : 
if($show_company == '' || $show_company == $client_company->getId()) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-address-book"></i>
	  <h3 class="box-title"><?php echo $client_company->getName(); ?></h3>
		<div class="pull-right">
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($client_company->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_230'); ?></a></li>
					<li class="divider"></li>
					<li><a href="javascript:void();" data-url="<?php echo get_archive_action_url($client_company, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_33'); ?></a>
					<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($client_company, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="box-body">
	
	<?php $company_clients = $client_company->getUsers(false, true);
	if(isset($company_clients) && is_array($company_clients) && count($company_clients)) : ?>
	
		<div class="table-responsive">
		<table class="table table-striped table-bordered clients" width="100%">
		<thead>
			<th width="25%"><?php echo lang('c_141'); ?></th>
			<th><?php echo lang('c_1'); ?></th>
			<th><?php echo lang('c_78'); ?></th>
			<th>&mdash;</th>
		</thead>
	
		<tbody>
		
		<?php foreach($company_clients as $company_client) : ?>
			
			<tr>
		
				<td><b><?php echo $company_client->getName(); ?></b>
				<?php if ($company_client->getNotes() != "") : ?>
					<p class="custom-mt-5"><span class="more"><?php echo $company_client->getNotes(); ?></span></p>
				<?php endif; ?>
				</td>
				<td class="hidden-xs"><?php echo $company_client->getEmail(); ?></td>
				<td class="hidden-xs"><?php echo ($company_client->getPhoneNumber() != "" ? $company_client->getPhoneNumber() : "&mdash;"); ?></td>
				
				<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($company_client->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_231'); ?></a></li>
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($company_client->getAddToProjectURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_223'); ?></a></li>
							<li class="divider"></li>
							<li><a href="javascript:void();" data-url="<?php echo get_archive_action_url($company_client, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_33'); ?></a>
							<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($company_client, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
							
						</ul>
					</div>
				</div>
								
				</td>

			</tr>

		<?php
		endforeach; ?>
		
		</tbody></table></div>
	
	<?php else : ?>
	<?php echo lang('e_2'); ?>
	<?php endif; ?>

	</div>
	
</div>

<?php endif;
endforeach; 
endif; ?>

<?php if(isset($without_company_clients) && is_array($without_company_clients) && count($without_company_clients)) : 
if($show_company == '' || $show_company == -1) : ?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-address-book"></i>
	  <h3 class="box-title"><?php echo lang('c_232'); ?></h3>
	</div>
	
	<div class="box-body">
		
		<div class="table-responsive">
		<table class="table table-striped table-bordered clients" width="100%">
		<thead>
			<th width="25%"><?php echo lang('c_141'); ?></th>
			<th><?php echo lang('c_1'); ?></th>
			<th><?php echo lang('c_78'); ?></th>
			<th>&mdash;</th>
		</thead>
		<tbody>
		
		<?php foreach($without_company_clients as $without_company_client) : ?>
			
			<tr>

				<td><b><?php echo $without_company_client->getName(); ?></b>
				<?php if ($without_company_client->getNotes() != "") : ?>
					<p class="custom-mt-5"><span class="more"><?php echo $without_company_client->getNotes(); ?></span></p>
				<?php endif; ?>
				</td>
				<td class="hidden-xs"><?php echo $without_company_client->getEmail(); ?></td>
				<td class="hidden-xs"><?php echo ($without_company_client->getPhoneNumber() != "" ? $without_company_client->getPhoneNumber() : "&mdash;"); ?></td>
				
				<td>
				<div class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
						<ul class="dropdown-menu pull-right" role="menu">
							
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($without_company_client->getEditURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_231'); ?></a></li>
							<li><a href="javascript:void();" data-url="<?php echo get_page_base_url($without_company_client->getAddToProjectURL()); ?>" data-toggle="commonmodal"><?php echo lang('c_223'); ?></a></li>
							<li class="divider"></li>
							<li><a href="javascript:void();" data-url="<?php echo get_archive_action_url($without_company_client, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_33'); ?></a>
							<li><a href="javascript:void();" data-url="<?php echo get_trash_action_url($without_company_client, 'move'); ?>" data-toggle="commonmodal"><?php echo lang('c_31'); ?></a></li>
							
						</ul>
					</div>
				</div>
								
				</td>

			</tr>

		<?php
		endforeach; ?>
		
		</tbody></table></div>

	</div>
	
</div>

<?php endif; 
endif; ?>
