(function($){
    var fileUploaded = false;
    var jcropObj = null;
    
    //Add New Post
    $('#addphotoform').submit(function(){
        if(!fileUploaded){
            if( $('#file_upload-queue .uploadify-progress-bar').size() < 1 )
            {
                showMessage($(this), 'Please choose a image.', true);    
            }else {
                $('#file_upload').uploadify('upload', '*');    
            }
            return false;
        }
    })
    
    
    //Ajax Upload
    $('#file_upload').uploadify({
        'swf' : '/js/uploadify/uploadify.swf',
        'uploader' : '/photo_uploader.php',
        'buttonText' : 'Choose File',
        'height' : 19,
        'width' : 76,
        'removeTimeout' : 1,
        'multi' : false,
        'auto' : false,
        'onSelect' : function(){
            //Clear Queue            
            $('#add-photo-button').show();
            $('#file_upload').addClass('hide');
            if( jcropObj != null)
                jcropObj.destroy();
            jcropObj = null;
            $('#add-photo-button').unbind('click');
            $('#jcrop-row').html('').hide();
            fileUploaded = false;
        },
        'onCancel': function(){
            $('#add-photo-button').hide();
            $('#file_upload').removeClass('hide');
            fileUploaded = false;
        },
        'onUploadSuccess' : function(file, data, response) {                
            var rsp = $.parseJSON(data);
            
            if(rsp.success == 0)
            {
                $('#addphotoform .loading-wrapper').hide();
                showMessage($('#addphotoform'), rsp.msg, true);
                hideMessage($('#addphotoform'), 2);
                $('#add-photo-button').hide();
                $('#file_upload').removeClass('hide');
                fileUploaded = false;
            }else{
                //If checked profile
                if($('#addphotoform input[name="post_visibility"]:checked').val() == 2){
                    
                    $('#addphotoform .loading-wrapper').hide();
                    //Show jCrop
                    $('#jcrop-row').html('<img src="/photos/tmp/' + rsp.file + '" />').show();
                    if( jcropObj != null)
                        jcropObj.destroy();
                    
                    $('#jcrop-row img').Jcrop({
                        aspectRatio: 1,
                        allowSelect: false,
                        minSize: [ 50, 50 ],
                        onChange: function(c){
                            $('#addphotoform #x1').val(c.x);
                            $('#addphotoform #x2').val(c.x2);
                            $('#addphotoform #y1').val(c.y);
                            $('#addphotoform #y2').val(c.y2);
                            $('#addphotoform #width').val($('#jcrop-row .jcrop-holder').width());
                        }
                    }, function(){
                        jcropObj = this;
                        jcropObj.animateTo([0,0,230,230]);
                    });
                    fileUploaded = true;
                    $('#addphotoform').append('<input type="hidden" name="file" value="' + rsp.file + '" />');
                } else {    
                    $('#addphotoform').append('<input type="hidden" name="file" value="' + rsp.file + '" />');                    
                    fileUploaded = true;
                    $('#addphotoform').submit();
                }
            }
        },
        'onUploadStart' : function(file) {
            //Check File Extension Validation
            var ext = file.name.substring(file.name.lastIndexOf(".")).toLowerCase();
            if( ext != '.jpg' && ext != '.jpeg' && ext != '.png' && ext != '.gif' )
            {
                showMessage($('#addphotoform'), 'Invalid file type! Please upload JPG, JPEG, PNG or GIF file.', true);
                hideMessage($('#addphotoform'), 2);
                $('#file_upload').uploadify('cancel', '*');    
            }else{
                $('#addphotoform .loading-wrapper').show();
            }
            
        }
    })
})(jQuery)