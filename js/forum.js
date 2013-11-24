/**
* Forum
*/
(function($){
    
    $(document).ready(function() {
        
        $(".delete_topic_btn").click(function() {
            if (confirm('Are you sure to delete this topic?')) {
                document.location.href = $(this).attr('rel');                
            }
            else {
                return false;
            }
        });
        
        $(".delete_topic_reply_btn").click(function() {
            if (confirm('Are you sure to delete this reply?')) {
                
                
                var link = $(this);
                link.html('<img src="/images/loading1.gif" alt="..." />');
                
                $.ajax({
                    url: link.attr('href'),
                    type: 'get',
                    success: function(rsp){
                        if(rsp == 'success')
                        {
                            link.parents('.reply-tr').fadeOut(function(){
                                $(this).remove();
                            });
                            
                            location.reload(true);
                            
                        }else{
                            link.html('Delete');
                            link.parents('td').find('.topic-edit-btn-cont').after('<p class="message error">' + rsp + '</p>')
                        }
                    }
                });
                
                
                
                return false;
            }
            else {
                return false;
            }
        });
        
    });
    
    
   
})(jQuery);

