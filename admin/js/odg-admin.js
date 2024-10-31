(function ($) {
    'use strict';
    $(document).ready(function () {
        if ($('.odg-cron-type:checked').val() === "wp-cron") {
            $(".show-if-server").hide();
        } else {
            $(".show-if-server").show();
        }

        if ($('.odg-overwrite-demo-dir:checked').val() === "no") {
            $(".show-if-overwrite-dir").hide();
        } else {
            $(".show-if-overwrite-dir").show();
        }

        if ($('#o-demos').length) {
            if ($(".odg_url").length) {
                $(".odg_url").html(odg_url + $("#odg-cron-server-key").val());
            }
            $("#odg-cron-server-key").keyup(function () {
                var odg_key = $(this).val();
                $(".odg_url").html(odg_url + odg_key);
            });
            $(document).on("change", ".odg-cron-type", function (e) {
                var selected_value = $(this).val();
                if (selected_value === "wp-cron") {
                    $(".show-if-server").hide();
                } else {
                    $(".show-if-server").show();
                }
            });
        }
        if ($('body.post-type-o-demos').length) {
            $(document).on("change", ".odg-overwrite-demo-dir", function (e) {
                var selected_value = $(this).val();
                if (selected_value === "no") {
                    $(".show-if-overwrite-dir").hide();
                } else {
                    $(".show-if-overwrite-dir").show();
                }
            });
        }
        
        $(document).on('click', '#pdg_db_connect', function(e){
            e.preventDefault();
            $('#db_connect_loading').removeClass("success_checked");
            $('#db_connect_loading').removeClass("failed_checked");
            $('#db_connect_loading').show();
            $('#db_connect_loading').addClass('loading_active');
            var sdb_name = $('input[name="o-demos[old_db_name]"]').val();
            var sdb_user = $('input[name="o-demos[old_db_user]"]').val();
            var sdb_pass = $('input[name="o-demos[old_db_pwd]"]').val();
            var sdb = {sdb_name,sdb_user,sdb_pass};
            $.post(
                    ajaxurl,
                    {
                        action: "check-source_db-connection",
                        sdb: sdb,
                    },
            function (response) {
                $('#db_connect_loading').removeClass('loading_active');
                if(response === '1')
                    $('#db_connect_loading').addClass('success_checked');
                else
                    $('#db_connect_loading').addClass('failed_checked');
            }
            );
                    
        });
    });

})(jQuery);
