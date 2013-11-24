(function($){

    //Unfriend 
    $('#unfriend').click(function(){
        if($('#myfriendsform .tr .td-chk input[type="checkbox"]:checked').size() < 1)
        {
            showMessage($('#myfriendsform'), 'No item selected.', true);
            return;
        }
        $('#myfriendsform #action').val('unfriend');
        $('#myfriendsform').submit(); 
    })
    //Decline Friend Request
    $('#decline-request').click(function(){
        if($('#myfriendsform .tr .td-chk input[type="checkbox"]:checked').size() < 1)
        {
            showMessage($('#myfriendsform'), 'No item selected.', true);
            return;
        }
        $('#myfriendsform #action').val('decline');
        $('#myfriendsform').submit(); 
    })
    //Accept Friend Request
    $('#accept-request').click(function(){
        if($('#myfriendsform .tr .td-chk input[type="checkbox"]:checked').size() < 1)
        {
            showMessage($('#myfriendsform'), 'No item selected.', true);
            return;
        }
        $('#myfriendsform #action').val('accept');
        $('#myfriendsform').submit(); 
    })
    //Delete Friend Request
    $('#delete-request').click(function(){
        if($('#myfriendsform .tr .td-chk input[type="checkbox"]:checked').size() < 1)
        {
            showMessage($('#myfriendsform'), 'No item selected.', true);
            return;
        }
        $('#myfriendsform #action').val('delete');
        $('#myfriendsform').submit(); 
    })
    
})(jQuery)