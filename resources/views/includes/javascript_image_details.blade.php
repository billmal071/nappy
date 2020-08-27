<script type="text/javascript">
    $(document).on('mouseenter', '.btnLike', 
        function () {
            if( $(this).hasClass( 'active' ) ) {
                $(this).find('i').removeClass('fa fa-heart').addClass('fa fa-heart-o');
            } else {
                $(this).find('i').removeClass('fa fa-heart-o').addClass('fa fa-heart');
            }
        }
    );
    
    $(document).on('mouseleave', '.btnLike', 
        function () {
            if( $(this).hasClass( 'active' ) ) {
                $(this).find('i').removeClass('fa fa-heart-o').addClass('fa fa-heart');
            } else {
                $(this).find('i').removeClass('fa fa-heart').addClass('fa fa-heart-o');
            }
       }
    );

    /*= Like =*/
    $(document).on('click', '.likeButton', function(e){
        var element     = $(this);
        var id          = element.attr("data-id");
        var like        = element.attr('data-like');
        var like_active = element.attr('data-unlike');
        var data        = 'id=' + id;

        e.preventDefault();

        element.blur();

        element.find('i').addClass('icon-spinner2 fa-spin');

        if( element.hasClass( 'active' ) ) {
            element.removeClass('active');
            element.find('i').removeClass('fa fa-heart').addClass('fa fa-heart-o');
            element.find('.textLike').html(like);

        } else {
            element.addClass('active');
            element.find('i').removeClass('fa fa-heart-o').addClass('fa fa-heart');
            element.find('.textLike').html(like_active);
        }

             $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               type: "POST",
               url: URL_BASE+"/ajax/like",
               data: data,
               success: function( result ){

                if( result == '') {
                      window.location.reload();
                      element.removeClass('likeButton');
                      element.removeClass('active');
                      console.log('empty');
                } else {
                    //element.parents('.actions').find('.like_count').html( result );
                    element.find('i').removeClass('icon-spinner2 fa-spin');
                    $('.like_'+id).html(result);
                }
             }//<-- RESULT
           });//<--- AJAX
    });//<----- CLICK

    $(document).on('click', '#btnFormPP', function(e){
        $('#form_pp').submit();
    });

    $(document).on('click', '#collection-md-close', 
        function () {
            $('#collections').modal('hide');
       }
    );

     $(document).on('click', '#rediToLog', 
        function () {
            location.href = "{{url('login')}}";
       }
    );

    @if (session('noty_error'))
        swal({
            title: "{{ trans('misc.error_oops') }}",
            text: "{{ trans('misc.already_sent_report') }}",
            type: "error",
            confirmButtonText: "{{ trans('users.ok') }}"
            });
    @endif

    @if (session('noty_success'))
        swal({
            title: "{{ trans('misc.thanks') }}",
            text: "{{ trans('misc.send_success') }}",
            type: "success",
            confirmButtonText: "{{ trans('users.ok') }}"
            });
    @endif

    /*= Add collection  =*/
    $(document).on('click', '#addCollection', function(e){
        var element     = $(this);

        e.preventDefault();
        element.blur();

        element.attr({'disabled' : 'true'});

        $('.wrap-loader').hide();

             $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               type: "POST",
               url: URL_BASE+"/collection/store",
               dataType: 'json',
               data: $("#addCollectionForm").serialize(),
               success: function( result ){

                if( result.success == true ){
                    $('.wrap-loader').hide();
                    $( result.data ).hide().appendTo('.collectionsData').slideDown( 1 );

                    $('input').iCheck({
                      radioClass: 'iradio_flat-green',
                      checkboxClass: 'icheckbox_square-green',
                    });

                    $('.no-collections').remove();
                    $("#titleCollection").val('');

                    element.removeAttr('disabled');

                    addImageCollection();

                } else {
                    $('.wrap-loader').hide();

                    var error = '';
                    for( $key in result.errors ){
                        error += '<li><i class="glyphicon glyphicon-remove myicon-right"></i> ' + result.errors[$key] + '</li>';
                        //error += '<div class="btn-block"><strong>* ' + result.errors[$key] + '</strong></div>';
                    }

                    $('#showErrors').html(error);
                    $('#dangerAlert').fadeIn(500)

                    element.removeAttr('disabled');

                }
             }
           });
    });

    //<----*********** addImageCollection ************------>
    function addImageCollection() {
        $(document).on('click', '.addImageCollection', function () {
            // $(".addImageCollection").click(function(){
            var _element = $(this);
            var imageID  = _element.attr("data-image-id");
            var collectionID  = _element.attr("data-collection-id");

            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: URL_BASE+'/collection/'+collectionID+'/i/'+imageID,
                dataType: 'json',
                data: null,
                success: function( response ) {
                    $('#collections').modal('hide');
                    $('.popout').addClass('alert-success').html(response.data);
                }
            });
        });
    }//<----*********** Click addImageCollection ************------>

    addImageCollection();
</script>