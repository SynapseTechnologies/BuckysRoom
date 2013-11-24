function showMessage(form, message, error)
{
    $(form).find('.message').remove();
    if($(form).find('.row').size() > 0)
    {
        $(form).find('.row:eq(0)').before('<p class="message ' + (error ? 'error' : 'success') + '" style="display: none">' + message + '</p>');        
    }else{
        $(form).prepend('<p class="message ' + (error ? 'error' : 'success') + '" style="display: none">' + message + '</p>');        
    }
    
    $(form).find('.message').fadeIn('fast');
}

function hideMessage(form, delay)
{
    if(typeof delay != 'undefined' && delay > 0)
        setTimeout( function(){$(form).find('.message').remove();}, delay * 1000 );
    else
        $(form).find('.message').remove();
    
}

function showMessageHTML(form, message)
{
    $(form).find('.message').remove();
    if($(form).find('.row').size() > 0)
    {
        $(form).find('.row:eq(0)').before(message);        
    }else{
        $(form).prepend(message);        
    }
    
    $(form).find('.message').fadeIn('fast');
}

(function($){
    $(document).ready(function(){
        $('.input').focus(function(){
            $(this).removeClass('input-error');
        })
        $('.select').change(function(){
            $(this).parent().find('select').removeClass('select-error');
        })
        $('.textarea').focus(function(){
            $(this).removeClass('input-error');
        })    
        
        if( $('#footer_menu').size() > 0 )
        {    
            //Footer menu
            $('#footer_menu').on('mouseover', 'li.has-submenu', function(){
                $(this).addClass('hover');                
            });
            $('#footer_menu').on('mouseout', 'li.has-submenu', function(){
                $(this).removeClass('hover');                
            });
            
            
            $('#footer_menu').click(function(e){
                e.stopPropagation();
            })
            $(window).click(function(){
                $('#footer_menu li.hover').removeClass('hover');
            })
        }
        $('.table #chk_all').click(function(){
            if(this.checked)
                $(this).parents('.table').find('.tr .td-chk input[type="checkbox"]').prop('checked', true);
            else
                $(this).parents('.table').find('.tr .td-chk input[type="checkbox"]').prop('checked', false);
        })
        $('table #chk_all').click(function(){
            if(this.checked)
                $(this).parents('table').find('.td-chk input[type="checkbox"]').prop('checked', true);
            else
                $(this).parents('table').find('.td-chk input[type="checkbox"]').prop('checked', false);
        })
    })
    
    $(document).on('click', 'a.report-link', function(){
        
        if(confirm('Are you sure to report?')) {
            var link = $(this);
            link.html('<img src="/images/loading1.gif" />');
            $.ajax({
                url: '/report_object.php',
                type: 'post',
                data: {
                    'type': link.attr('data-type'),
                    'id': link.attr('data-id'),
                    'idHash': link.attr('data-idHash'),
                    'action': 'report'
                },
                dataType: 'xml',
                success: function(rsp){
                    link.parent().find('.message').remove();
                    if($(rsp).find('status').text() == 'success')
                    {
                        link.after('<p class="message success">' + $(rsp).find('message').text() + "</p>");
                        link.remove();
                    }else{                    
                        link.after('<p class="message error">' + $(rsp).find('message').text() + "</p>");
                        link.html('Report');
                    }
                },
                error: function(rsp){
                    link.parent().find('.message').remove();
                    link.after('<p class="message error">' + rsp.responseText + "</p>");
                    link.html('Report');
                }
            });
        }
            
        return false;
    })
    
})(jQuery)