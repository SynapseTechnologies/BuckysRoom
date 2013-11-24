(function($){
    $('#reportedform').submit(function(){
        
    })
    $('#reportedform #delete-objects').click(function(){
        var form = $('#reportedform');
        form.find('input[name="action"]').val('delete-objects');
        if(form.find('.tr .td-chk input[type="checkbox"]:checked').size() == 0)
        {
            showMessage(form, 'No data selected!', true);
            return false;
        }
        form.submit();
    });
    
    $('#reportedform #approve-objects').click(function(){
        var form = $('#reportedform');
        form.find('input[name="action"]').val('approve-objects');
        if(form.find('.tr .td-chk input[type="checkbox"]:checked').size() == 0)
        {
            showMessage(form, 'No data selected!', true);
            return false;
        }
        form.submit();
    });
    
    $('#reportedform #ban-users').click(function(){
        var form = $('#reportedform');
        form.find('input[name="action"]').val('ban-users');
        if(form.find('.tr .td-chk input[type="checkbox"]:checked').size() == 0)
        {
            showMessage(form, 'No data selected!', true);
            return false;
        }
        form.submit();
    });
    
    
})(jQuery)