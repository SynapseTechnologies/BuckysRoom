(function($){
    //View More Comments
    $(document).on('click', '.show-more-comments', function(){
        var link = $(this);
        if(link.attr('data-last-date') != '')
        {
            link.addClass('show-more-comments-loading');
            
            $.ajax({
                url: '/comments.php',
                data: 'action=get-comments&postID=' + link.attr('data-post-id') + '&last=' + link.attr('data-last-date'),
                type: 'post',
                dataType: 'xml',
                success: function(rsp){
                    link.removeClass('show-more-comments-loading');
                    link.before($(rsp).find('comment').text());
                    if($(rsp).find('hasmore').text() == 'yes')
                    {
                        link.attr('data-last-date', $(rsp).find('lastdate').text());
                    }else{
                        link.attr('data-last-date', '').hide();
                    }
                },
                error: function(rsp){
                    link.removeClass('show-more-comments-loading');
                }
            })
        }
        return false;
    })
    
    
    
    //Saving Comment using ajax
    $(document).on('submit', 'form.postcommentform', function(){
        var form = $(this);
        //Check the user input their commends
        if( form.find('input[name="comment"]').val() == '' )
        {
            return false;
        }else{
            form.find('.loading-wrapper').show();
            hideMessage(form);
            $.ajax({
                url: '/comments.php',
                type: 'post',
                data: form.serialize() + '&action=save-comment',
                dataType: 'xml',
                success: function(rsp){
                    form.find('.loading-wrapper').hide();
                    //Add New Comment
                    $(form).parent().after($(rsp).find('newcomment').text());
                    //Update the comment count
                    $(form).parents('.post-content').find('.post-like-comment a:eq(1)').html($(rsp).find('count').text())
                    $(form).find('input[name="comment"]').val('');
                },
                error: function(err){
                    form.find('.loading-wrapper').hide();
                    showMessage(form, err.responseText, true);
                    hideMessage(form, 3);
                }
            })
        }
        return false;
    })
    
    //Delete Post
    $(document).on('click', '.remove-post-link', function(){
        
        if (confirm('Are you sure to delete this post?')) {
            var link = $(this);
            link.html('<img src="/images/loading1.gif" alt="..." />')
            $.ajax({
                url: link.attr('href'),
                type: 'get',
                success: function(rsp){
                    if(rsp == 'success')
                    {
                        link.parents('.post-item').fadeOut(function(){
                            $(this).remove();
                        })
                    }else{
                        link.html('Delete');
                        link.parents('.post-content').find('.post-like-comment').before('<p class="message error">' + rsp + '</p>')
                    }
                }
            });
        }
        return false;
    })
    
    //Delete Comment
    $(document).on('click', '.remove-comment-link', function(){
        var link = $(this);
        link.html('<img src="/images/loading1.gif" alt="..." />')
        $.ajax({
            url: link.attr('href'),
            type: 'get',
            dataType: 'xml',
            success: function(rsp){
                link.parents('.post-content').find('.post-like-comment a:eq(1)').html($(rsp).find('commentcount').text());
                link.parents('.comment-item').fadeOut(function(){
                    $(this).remove();
                })
            },
            error: function(err)
            {
                link.html('Delete');
                link.after('<p class="message error">' + err.responseText + '</p>');      
            }
        })
        return false;
    })
    
    //Link/Unlike Post
    $(document).on('click', '.like-post-link', function(){
        var link = $(this);
        var oldVal = $(this).html();
        if(link.find('img').size() > 0)
            return false;
        link.html('<img src="/images/loading1.gif" alt="..." />');
        $.ajax({
            url: link.attr('href'),
            type: 'get',
            dataType: 'xml',
            success: function(rsp){
                if($(rsp).find('status').text() == 'success'){                    
                    link.parents('.post-content').find('.post-like-comment a:eq(0)').html($(rsp).find('likes').text());
                    if(oldVal.toLowerCase() == 'like')
                    {
                        link.html('Unlike');
                        link.attr('href', link.attr('href').replace('likePost', 'unlikePost'));
                    }else{
                        link.html('Like');
                        link.attr('href', link.attr('href').replace('unlikePost', 'likePost'));
                    }
                }else{
                    link.html(oldVal);
                    link.parent().parent().after($(rsp).find('message').text());      
                }
                
            },
            error: function(err)
            {
                link.html(oldVal);
                link.parent().parent().after('<p class="message error">' + err.responseText + '</p>');      
            }
        })
        return false;
    })
    
    $(document).on('click', '.post-like-comment .likes-link', function(){
        if( $(this).parents('.post-content').find('.liked-users').size() > 0 )
        {
            $(this).parents('.post-content').find('.liked-users').animate({'height': 'toggle'});
        }
        return false;
    })
    
    //Show More Stream
    $(window).scroll(function(){                
        if  ($(window).scrollTop() >= $(document).height() - $(window).height() - 10){                    
            
            if( $('#more-stream').size() > 0 && $('#more-stream').css('display') == 'none' )
            {
                $('#more-stream').show();
                var data = '';
                var pageType = $('#more-stream').attr('data-page');
                //Get More Photo
                if(pageType == 'photo' || pageType == 'page-photo'){
                    var lastDate = $('a.photo:last img').attr('data-posted-date');                                    
                    data = 'lastDate=' + lastDate + '&page=' + pageType;
                    data += '&user=' + $('#more-stream').attr('data-user-id');
                    if($('#more-stream').attr('data-album-id') != '')
                        data += '&albumID=' + $('#more-stream').attr('data-album-id');
                    
                    if (pageType == 'page-photo') {
                        data += '&pageID=' + $('#more-stream').attr('data-page-id');
                    }
                
                }else{
                    //Getting last post's posted date
                    var lastDate = $('.post-item:last .post-created-date').val();                
                    data = 'lastDate=' + lastDate + '&page=' + pageType;
                    if($('#more-stream').attr('data-page') == 'post') 
                    {
                       data += '&user=' + $('#more-stream').attr('data-user-id');
                    }
                    else if($('#more-stream').attr('data-page') == 'page-post') 
                    {
                       data += '&pageID=' + $('#more-stream').attr('data-page-id');
                    }
                    
                }
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "/get_data.php",
                    success: function(returnHTML){
                        $('#more-stream').hide();
                        if(returnHTML == '')
                            $('#more-stream').remove();
                        else{
                            if(pageType == 'photo' || pageType == 'page-photo')
                                $('a.photo:last').after(returnHTML);                            
                            else
                                $('.post-item:last').after(returnHTML);                            
                        }
                    }
                });    
            }
        }
    });
    
})(jQuery)