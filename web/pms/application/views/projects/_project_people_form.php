<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
tpl_assign("title_for_layout", lang('c_254').': <u>'.$project->getName().'</u>'); ?>

<form method="post" action="<?php echo get_page_base_url('projects/manage_people/'.$project->getId()); ?>" id="i_project_users_form" class="form-horizontal">

<div id="projectUsersData" data-users="<?php echo clean_field($project_users_data); ?>"></div>

<div class="form-group">
	<div class="col-md-6">
		<?php echo select_box("companyOptions", null, null, ' id="companyOptions" class="form-control"'); ?>
	</div>
	<div class="col-md-6">
		<?php echo select_box("userOptions", null, null, ' id="userOptions" class="form-control"'); ?>
	</div>
</div>

<div class="form-group">
	<div class="col-md-12">
		<span id="addPeojectUser" class="btn btn-primary pull-right"><?php echo lang('c_223'); ?></span>
	</div>
</div>

<div class="form-group">

	<div class="col-md-12">

		<div class="table-responsive">
		<table class="table table-bordered table-striped">
		<tbody id="projectUsersList"></tbody></table></div>
	
	</div>
	
</div>

<input type="hidden" name="submited" value="submited" />

<div class="form-group text-right">
	<button type="submit" class="btn btn-success" data-loading-text="<?php echo lang('c_276'); ?>" id="i_project_users_submit"><?php echo lang('c_226'); ?></button>
	<a class="btn btn-default" data-dismiss="modal"><?php echo lang('c_37'); ?></a>
</div>

</form>

<script language="javascript">

$(function() {
  $.getJSON("<?php echo get_page_base_url("people/companies_json"); ?>", function(b) {
    var c = '<option value=""><?php echo lang('c_218'); ?></option>';
    $.each(b, function(b, a) {
      c = c + '<option value="' + a.id + '">' + a.name + "</option>";
    });
    c += '<option value="0"><?php echo lang('c_232'); ?></option>';
    $("#companyOptions").html(c);
    $("#userOptions").html('<option value=""><?php echo lang('c_255'); ?></option>');
  });
  $("#companyOptions").change(function() {
    $.getJSON("<?php echo get_page_base_url("people/users_json/"); ?>" + $(this).val(), function(b) {
      var c = '<option value=""><?php echo lang('c_255'); ?></option>';
      $.each(b, function(b, a) {
        c = c + '<option value="' + a.id + '" data-usertype="' + (a.is_member ? "<?php echo lang('c_28'); ?>" : "<?php echo lang('c_29'); ?>") + '">' + a.name + "</option>";
      });
      $("#userOptions").html(c);
      $("#companyOptions option[value='']").attr("disabled", "disabled");
    });
  });
  var a = $("#projectUsersList"), f = $("#projectUsersData"), d = $("#userOptions"), g = $("#addPeojectUser"), e = function() {
    var b = a.data(), c = "", d;
    for (d in b) {
      c += '<tr><td class="text-left"><b>' + b[d].name + '</b></td><td class="text-left">' + b[d].usertype + '</td><td class="text-right"><a href="#" data-delID="' + d + '" class="delMember btn btn-sm btn-danger btn-xs"><?php echo lang('c_50'); ?></a></td></tr>';
    }
    "" == c ? a.html("<tr><td><?php echo lang('c_256'); ?></td></tr>") : a.html(c);
  };
  $(document).on("click", "#projectUsersList a.delMember", function() {
    a.removeData($(this).attr("data-delID"));
    e();
    return !1;
  });
  g.on("click", function() {
    var b = d.find("option:selected");
    "" == d.val() ? alert("<?php echo lang('c_257'); ?>") : a.data(d.val()) ? alert("<?php echo lang('c_258'); ?>") : a.data(d.val(), {id:b.val(), usertype:b.data("usertype"), name:b.text()});
    e();
  });
  a.data(f.data("users"));
  e();
});

</script>