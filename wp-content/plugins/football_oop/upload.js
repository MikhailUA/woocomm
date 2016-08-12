jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        console.log('check');
        e.preventDefault();
        var image = wp.media({
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
            .on('select', function(e){
                // This will return the selected image from the Media Uploader, the result is an object
                var uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                // Output to the console uploaded_image
                console.log(uploaded_image);
                var image_url = uploaded_image.toJSON().url;
                console.log(uploaded_image.id);
                // Let's assign the url value to the input field
                $('#file-id').attr('value',uploaded_image.id);
                $('#image_url').val(image_url);
            });
    });

    $('#clear').click(function(){
        $('#file-id').attr('value',"");
        $('#image_url').val("");
    })

});
