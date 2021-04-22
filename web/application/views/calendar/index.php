<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

tpl_assign("title_for_layout", lang('c_42'));

tpl_assign("header_for_layout", '
<link href="'.base_url('public/assets/vendor/fullcalendar/fullcalendar.css').'" rel="stylesheet">
<link href="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.css').'" rel="stylesheet">
<link href="'.base_url('public/assets/vendor/select2/select2.min.css').'" rel="stylesheet">
<link href="'.base_url('public/assets/vendor/select2/s2-docs.css').'" rel="stylesheet">
');	

$calendar_google_api_key = config_option('calendar_google_api_key', '');
$calendar_google_event_address = config_option('calendar_google_event_address', '');

tpl_assign("footer_for_layout", '
<script type="text/javascript" src="'.base_url('public/assets/vendor/datetimepicker/jquery.datetimepicker.js').'"></script>
<script type="text/javascript" src="'.base_url('public/assets/vendor/fullcalendar/moment.min.js').'"></script>
<script type="text/javascript" src="'.base_url('public/assets/vendor/fullcalendar/fullcalendar.js').'"></script>
<script type="text/javascript" src="'.base_url('public/assets/vendor/fullcalendar/lang-all.js').'"></script>
<script type="text/javascript" src="'.base_url('public/assets/vendor/select2/select2.full.js').'"></script>

<script type="text/javascript">
(function ($) {
	"use strict"; 
	$(document).ready(function() {   
		$("#fullcalendar").fullCalendar({
		lang: "'.get_site_language_shortcode().'",
		header:{
			left:   "month,agendaWeek,agendaDay",
			center: "title",
			right:  "today prev,next"
		},
		'.($calendar_google_api_key != "" && $calendar_google_event_address != "" ? 
		'googleCalendarApiKey: "'.$calendar_google_api_key.'",
		eventSources: [
			{
				googleCalendarId: "'.$calendar_google_event_address.'",
				className: "google-event",
			}
		],' : '').'
		events: [
				'.(isset($events_list) ? $events_list : '').'  
				],
		eventRender: function(event, element) {
			element.attr("title", event.description);
			"google-event" == event.source.className[0] && element.attr("target", "_blank");
			"true" == event.modal && element.attr("data-toggle", "commonmodal");
			if ("" != event.description) {
			element.attr("title", event.description);
			var tooltip = event.description;
			$(element).attr("data-original-title", tooltip);
			$(element).tooltip({container:"body", trigger:"hover", delay:{show:300, hide:50}});
			}
		},
		eventClick: function(event) {
		'. (logged_user()->isOwner() || logged_user()->isAdmin() ? '
			if (event.url && "true" == event.modal) {
			NProgress.start();
			var url = event.url;
			0 === url.indexOf("#") ? $("#common-modal").modal("open") : $.get(url, function(a) {
				$("#common-modal").modal();
				$("#common-modal").html(a);
			}).done(function() {
				NProgress.done();
			});
			return false;
			}' : '').'
		}
		});
	});
})(jQuery);	
</script>
');	

?>

<?php if(logged_user()->isOwner() || logged_user()->isAdmin()) : ?>
<p><a href="javascript:void();" data-url="<?php echo get_page_base_url('calendar/create'); ?>" class="btn btn-success custom-m-3" data-toggle="commonmodal">+ <?php echo lang('c_43'); ?></a></p>
<?php endif; ?>

<div class="box box-solid">

	<div class="box-header with-border">
	  <i class="fa fa-calendar"></i>
	  <h3 class="box-title"><?php echo lang('c_42'); ?></h3>
	</div>
	
	<div class="box-body no-padding">

		<div class="row">
		
			<div class="col-md-12">
				<div id="fullcalendar"></div>
			</div>
		
		</div>
	
	</div>
	
</div>