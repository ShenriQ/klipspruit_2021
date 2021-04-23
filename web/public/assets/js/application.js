(function($) {
    "use strict";

    $.ajaxSetup({ cache: !1 });
    NProgress.configure({ showSpinner: !1 });
    $(document).on("click", '[data-toggle="commonmodal"]', function(a) {
        NProgress.start();
        a.preventDefault();
        var b = $(this).attr("data-url");
        0 === b.indexOf("#") ? $("#common-modal").modal("open") : $.get(b, function(a, c, e) {
            (c = e.getResponseHeader("requestURL")) && "" != c && c != b ? window.location = c + "?ref=" + $.base64.encode(window.location.href) : a.trim() ? ($("#common-modal").modal(), $("#common-modal").html(a)) : window.location.reload();
        }).done(function() {
            NProgress.done();
        }).fail(function(e) {
            console.log(e)
            alert("Page not found.");
            NProgress.done();
        });
    });
    $(".notify").animate({ opacity: 1, right: "10px" }, 800, function() {
        $(".notify").delay(3E3).fadeOut();
    });

    function m_format_error(a) {
        return '<div class="alert alert-danger">' + a + "</div>";
    }

    function serialize_ajax_call(a, b, d) {
        $("#" + d).button("loading");
        $.ajax({
            url: a,
            type: "POST",
            data: b,
            dataType: "json",
            success: function() {
                $("#" + d).button("reset");
            }
        }).done(function(a) {
            if (a.success) {
                window.location.reload();
            } else {
                var c = "";
                if ("string" === typeof a.message) {
                    c += a.message;
                } else {
                    for (var b in a.message) {
                        c += a.message[b] + "<br>";
                    }
                }
                $("#responseModel").html(m_format_error(c));
            }
        }).fail(function(a) {
            //alert(a.responseText);
            $("#responseModel").html(m_format_error("Sorry, this operation could not be completed."));
            $("#" + d).button("reset");
        });
    }

    function formdata_ajax_call(a, b, d, e) {
        $("#" + d).button("loading");
        $.ajax({
            url: a,
            type: "POST",
            data: b,
            processData: !1,
            contentType: !1,
            dataType: "json",
            success: function() {
                $("#" + d).button("reset");
            }
        }).done(function(a) {
            console.log('formdata_ajax_call success', a)
            if (a.success) {
                window.location.reload();
            } else {
                var b = "";
                if ("string" === typeof a.message) {
                    b += a.message;
                } else {
                    for (var c in a.message) {
                        b += a.message[c] + "<br>";
                    }
                }
                $("#" + e).html(m_format_error(b));
            }
        }).fail(function(a) {
            console.log('formdata_ajax_call err', a)
                //alert(a.responseText);
            $("#" + e).html(m_format_error("Sorry, this operation could not be completed."));
            $("#" + d).button("reset");
        });
    }
    $(document).ready(function() {
        $(".more").each(function() {
            var a = $(this).html();
            if (200 < a.length) {
                var b = a.substr(0, 200),
                    a = a.substr(200, a.length - 200),
                    b = b + '<span class="moreellipses">...&nbsp;</span><span class="morecontent"><span>' + a + '</span>&nbsp;&nbsp;<a href="" class="morelink">Read more</a></span>';
                $(this).html(b);
            }
        });
        $(".morelink").on("click", function() {
            $(this).hasClass("less") ? ($(this).removeClass("less"), $(this).html("Read more")) : ($(this).addClass("less"), $(this).html("Close"));
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return !1;
        });
        $(".add-message-file").on("click", function() {
            var a = $(".attach-message-file").length + 1;
            if (5 < a) {
                return alert("You can not add more file attachments. Limit is 5"), !1;
            }
            a = $('<table class="attach-message-file"><tr><td><input name="attachFiles[]" id="message_file' + a + '" type="file"></td><td><a type="button" class="remove-message-file btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></a></td></tr></table>');
            $(".attach-message-file:last").after(a);
            a.fadeIn("slow");
        });
        $(".form-group").on("click", ".remove-message-file", function() {
            $(this).closest(".attach-message-file").remove();
            return !1;
        });
        $(".add-alone-file").on("click", function() {
            var a = $(".attach-alone-file").length + 1;
            if (5 < a) {
                return alert("You can not add more files. Limit is 5"), !1;
            }
            a = $('<table class="attach-alone-file"><tr><td><input name="attachFiles[]" id="alone_file' + a + '" type="file"></td><td><a type="button" class="remove-alone-file btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></a></td></tr></table>');
            $(".attach-alone-file:last").after(a);
            a.fadeIn("slow");
        });
        $(".form-group").on("click", ".remove-alone-file", function() {
            $(this).closest(".attach-alone-file").remove();
            return !1;
        });
    });
    $(document).ready(function() {
        $(document).on("submit", "#i_company_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_company_submit");
        });
        $(document).on("submit", "#i_user_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_user_submit");
        });
        $(document).on("submit", "#i_edit_profile_form", function(a) {
            a.preventDefault();
            formdata_ajax_call($(this).attr("action"), new FormData(this), "i_edit_profile_submit", "responseModel");
        });
        $(document).on("submit", "#i_move_to_trash_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_move_to_trash_submit");
        });
        $(document).on("submit", "#i_move_to_archive_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_move_to_archive_submit");
        });
        $(document).on("submit", "#i_empty_trash_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_empty_trash_submit");
        });
        $(document).on("submit", "#i_project_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_project_submit");
        });
        $(document).on("submit", "#i_complete_project_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_complete_project_submit");
        });
        $(document).on("submit", "#i_close_ticket_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_close_ticket_submit");
        });
        $(document).on("submit", "#i_cancel_invoice_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_cancel_invoice_submit");
        });
        $(document).on("submit", "#i_add_to_project_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_add_to_project_submit");
        });
        $(document).on("submit", "#i_topic_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_topic_submit");
        });
        $(document).on("submit", "#i_task_list_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_task_list_submit");
        });
        $(document).on("submit", "#i_option_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_option_submit");
        });
        $(document).on("submit", "#i_event_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_event_submit");
        });
        $(document).on("submit", "#i_task_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_task_submit");
        });
        $(document).on("submit", "#i_timelog_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_timelog_submit");
        });
        $(document).on("submit", "#i_comment_form", function(a) {
            a.preventDefault();
            formdata_ajax_call($(this).attr("action"), new FormData(this), "i_comment_submit", "responseForm");
        });
        $(document).on("submit", "#i_upload_form", function(a) {
            a.preventDefault();
            formdata_ajax_call($(this).attr("action"), new FormData(this), "i_upload_submit", "responseForm");
        });
        $(document).on("submit", "#i_project_users_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), "submited=submited&project_user_ids=" + JSON.stringify(Object.keys($("#projectUsersList").data())), "i_project_users_submit");
        });
        $(document).on("submit", "#i_invoice_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_invoice_submit");
        });
        $(document).on("submit", "#i_department_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_department_submit");
        });
        $(document).on("submit", "#i_announcement_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_announcement_submit");
        });
        $(document).on("submit", "#i_estimate_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_estimate_submit");
        });
        $(document).on("submit", "#i_ticket_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_ticket_submit");
        });
        $(document).on("submit", "#i_source_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_source_submit");
        });
        $(document).on("submit", "#i_status_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_status_submit");
        });
        $(document).on("submit", "#i_payment_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_payment_submit");
        });
        $(document).on("submit", "#i_leadnotes_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_leadnotes_submit");
        });
        $(document).on("submit", "#i_lead_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_lead_submit");
        });
        $(document).on("submit", "#i_base_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_base_submit");
        });
        $(document).on("submit", "#i_expense_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_expense_submit");
        });
        $(document).on("submit", "#i_test_email_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_test_email_submit");
        });
        $(document).on("submit", "#i_subscription_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_subscription_submit");
        });
        $(document).on("submit", "#i_package_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_package_submit");
        });
        $(document).on("submit", "#i_widget_form", function(a) {
            a.preventDefault();
            formdata_ajax_call($(this).attr("action"), new FormData(this), "i_widget_submit", "responseModel");
        });
        $(document).on("submit", "#i_delete_widget_form", function(a) {
            a.preventDefault();
            serialize_ajax_call($(this).attr("action"), $(this).serialize(), "i_delete_widget_submit");
        });

    });
    $(document).ready(function() {
        $(document).on("change", ".item_amount", function() {
            var a = $(this).attr("id"),
                a = a.replace("amount", ""),
                a = a.replace("_", ""),
                a = parseInt(a),
                b = parseFloat($(this).val()),
                b = convert_number(b);
            $("#amount_" + a).val(b);
            var c = parseFloat($("#quantity_" + a).val()),
                c = convert_number(c);
            $("#quantity_" + a).val(c);
            b = parseFloat(b * c);
            b = b.toFixed(2);
            $("#total_" + a).html("" + b);
            sum_sub_total();
        });
        $(document).on("change", ".item_quantity", function() {
            var a = $(this).attr("id"),
                a = a.replace("quantity", ""),
                a = a.replace("_", ""),
                a = parseInt(a),
                b = parseFloat($("#amount_" + a).val()),
                b = convert_number(b);
            $("#amount_" + a).val(b);
            var c = parseFloat($(this).val()),
                c = convert_number(c);
            $("#quantity_" + a).val(c);
            b = parseFloat(b * c);
            b = b.toFixed(2);
            $("#total_" + a).html("" + b);
            sum_sub_total();
        });
        $(document).on("change", "#tax", function() {
            $("#tax_name").text($("#tax").val());
        });
        $(document).on("change", "#tax2", function() {
            $("#tax2_name").text($("#tax2").val());
        });
        $(document).on("change", "#tax_rate", function() {
            $("#tax_amount").text($("#tax_rate").val() + "%");
            $("#tax_name").text($("#tax").val());
            update_tax_all();
            sum_sub_total();
        });
        $(document).on("change", "#tax_rate2", function() {
            $("#tax2_amount").text($("#tax_rate2").val() + "%");
            $("#tax2_name").text($("#tax2").val());
            update_tax_all();
            sum_sub_total();
        });
        $(document).on("change", "#discount_amount, #discount_amount_type, #discount_type", function() {
            sum_sub_total();
        });
    });
})(jQuery);

function ToggleAll(a, b) {
    checkboxes = document.getElementsByName(b + "[]");
    for (var d = 0, c = checkboxes.length; d < c; d++) {
        checkboxes[d].checked = a ? 'checked="checked"' : "";
    }
}

function update_tax_all() {
    return update_tax() + update_tax("2");
}

function update_tax(taxNo = '') {
    var a = get_sub_total(),
        b = parseFloat($("#tax_rate" + taxNo).val());
    $("#tax" + taxNo + "_amount").text($("#tax_rate" + taxNo).val() + "%");
    $("#tax" + taxNo + "_name").text($("#tax" + taxNo).val());
    return parseFloat(0 < a && 0 < b ? (a = parseFloat(a / 100 * b), a = a.toFixed(2), $("#tax" + taxNo + "_total_amount").text("" + a), a) : ($("#tax" + taxNo + "_total_amount").text(0.00), 0));
}

function get_sub_total() {
    var a = 0.00;
    $(".item_quantity").each(function() {
        var b = $(this).attr("id"),
            b = b.replace("quantity", ""),
            b = b.replace("_", ""),
            b = parseInt(b),
            b = parseFloat($("#amount_" + b).val()),
            c = parseFloat($(this).val()),
            b = parseFloat(b * c);
        a += b;
    });
    return a;
}

function sum_sub_total() {
    var a = get_sub_total();
    var b = update_tax_all();
    a = a.toFixed(2);
    $("#sub_total").html("" + a);
    b = total_sum_amount(b, a);
    a = parseFloat($("#discount_amount").val()) || 0;
    $("#discount_amount").val(a);
    "percentage" == $("#discount_amount_type").val() && (a = parseFloat(a / 100 * b).toFixed(2));
    b = parseFloat(b - a).toFixed(2);
    $("#display_discount_amount").text("" + a);
    $("#total_payment_after_discount").text("" + b);
    return b;
}

function total_sum_amount(a, b) {
    b = parseFloat(b);
    var c = parseFloat(a);
    b = parseFloat(b + c);
    b = b.toFixed(2);
    $("#total_payment").text("" + b);
    return b;
}

function convert_number(a) {
    return Number(a.toString().match(/^\d+(?:\.\d{0,2})?/)).toFixed(2);
}

function add_new_item(description = '', quantity = 0, amount = 0, total = 0, timelog_ids = []) {
    var a = parseInt($("#items").val()) + 1;
    $("#item-table tr:last").after('<tr id="invoice_element_' + a + '"><td><a class="btn btn-sm btn-danger" onclick="javascript:remove_invoice_item(\'invoice_element_' + a + '\'); return false;"><i class="fa fa-trash"></i></a></td><td><input type="text" name="name[]" value="' + description + '" class="form-control" /></td><td><input type="text" name="quantity[]" id="quantity_' + a + '" class="form-control item_quantity" value="' + parseFloat(quantity).toFixed(2) + '"></td><td><input type="text" name="amount[]" id="amount_' + a + '" class="form-control item_amount" value="' + parseFloat(amount).toFixed(2) + '"></td><td><div id="total_' + a + '">' + parseFloat(total).toFixed(2) + '</div><input type="hidden" name="timelog_ids[]" id="timelog_ids_' + a + '" value="' + JSON.stringify(timelog_ids) + '" /></td>');
    $("#items").val(a);
    $("#items_count").val((parseInt($('#item-table tr').length) - 1));
};

function remove_invoice_item(elementId, force_remove = false) {
    let a = parseInt($('#item-table tr').length);
    if (a > 2 || force_remove === true) {
        let element = document.getElementById(elementId);
        element.parentNode.removeChild(element);
        sum_sub_total();
        $("#items_count").val((a - 1));
    }
}

function ganttChart(a) {
    $(function() {
        $(".gantt").gantt({
            source: a,
            minScale: "years",
            maxScale: "years",
            navigate: "scroll",
            itemsPerPage: 30,
            onItemClick: function(b) {},
            onAddClick: function(b, a) {},
            onRender: function() {}
        });
    });
};