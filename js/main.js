jQuery(document).ready(function($){

    $('#inputGroupFile01').on('change',function(e){
        //put the file name on the label
        $(this).next('.custom-file-label').html(e.target.files[0].name);
    })

    $('.upload_meta').on('click',function () {

        var id =  jQuery(this).attr('data-id');
        var action = jQuery(this).attr('data-action');

        $.ajax({
            method: "GET",
            url: "assets/functions.php",
            data: {
                id: id,
                action: action
            }
        })
        .done(function( res ) {

            jQuery('.photo-'+ id + ' input.'+action).val(res);

        });
    });

    setInterval(checkPhotos, 10000000);
    function checkPhotos() {
        $.ajax({
            method: "GET",
            url: "assets/functions.php",
            data: {
                checkPhotos: true
            }
        })
        .done(function( res ) {

            var photosObj = jQuery.parseJSON(res);

            $(photosObj).each(function(i,v){
                jQuery('.photo-'+ v.id + ' input.views').val(v.views);
                jQuery('.photo-'+ v.id + ' input.downloads').val(v.downloads);
            });

        });
    }

    $("img").on("contextmenu",function(){
        return false;
    });

});

