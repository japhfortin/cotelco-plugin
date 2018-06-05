jQuery(document).ready(function($) {
    // datepicker
    $("#payment_date").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'mm/dd/yy',
    });
    $('#billing_month').datepicker({
        dateFormat: "yy/mm",
        changeMonth: true,
        changeYear: true,
        closeText: "Select",
        showButtonPanel: true,
        onClose: function(dateText, inst) {
            $('#ui-datepicker-div').removeClass('ui-datepicker-calendar-hidden');
            function isDonePressed(){
                return ($('#ui-datepicker-div').html().indexOf('ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover') > -1);
            }
            if (isDonePressed()){
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1)).trigger('change');
                $('#billing_month').focusout()//Added to remove focus from datepicker input box on selecting date
            }
        },
        beforeShow : function(input, inst) {
            $('#ui-datepicker-div').addClass('ui-datepicker-calendar-hidden');
            inst.dpDiv.addClass('month_year_datepicker')
            if ((datestr = $(this).val()).length > 0) {
                year = datestr.substring(datestr.length-4, datestr.length);
                month = datestr.substring(0, 2);
                $(this).datepicker('option', 'defaultDate', new Date(year, month-1, 1));
                $(this).datepicker('setDate', new Date(year, month-1, 1));
                $(".ui-datepicker-calendar").hide();
            }
        }
    })

    // mask
    $('#account_no').mask('00-0000-0000');
    $('#billing_month').mask('0000/00');
    $('#payment_date').mask('00/00/0000');
    $('#payment_or').mask('00000000');

});
