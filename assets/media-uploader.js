jQuery(function($){
    var frame,
        metaBox = $('#custom-img-uploader'), // Your meta box id here
        addImgLink = metaBox.find('.upload-custom-img'),
        delImgLink = metaBox.find( '.delete-custom-img'),
        imgContainer = metaBox.find( '.custom-img-container'),
        imgIdInput = metaBox.find( '.custom-img-id' );

    // ADD IMAGE LINK
    addImgLink.on( 'click', function( event ){
        event.preventDefault();
    
        // If the media frame already exists, reopen it.
        if ( frame ) {
          frame.open();
          return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select or upload an image',
            button: {
                text: 'Use this image'
            },
            type: 'image',
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected in the media frame...
        frame.on( 'select', function() {      
          // Get media attachment details from the frame state
          var attachment = frame.state().get('selection').first().toJSON();

          // Send the attachment URL to our custom image input field.
          imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

          // Send the attachment id to our hidden input
          imgIdInput.val( attachment.id );

          // Hide the add image link
          addImgLink.addClass( 'hidden' );

          // Unhide the remove image link
          delImgLink.removeClass( 'hidden' );
        });

        frame.open();
    });

    // DELETE IMAGE LINK
    delImgLink.on( 'click', function( event ){
        event.preventDefault();

        // Clear out the preview image
        imgContainer.html( '' );

        // Un-hide the add image link
        addImgLink.removeClass( 'hidden' );

        // Hide the delete image link
        delImgLink.addClass( 'hidden' );

        // Delete the image id from the hidden input
        imgIdInput.val( '' );
    });
});