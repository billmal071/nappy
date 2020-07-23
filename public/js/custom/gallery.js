$(document).ready(function() {
    $(window).scroll(fetchImages);

    function fetchImages() {
        var page = $('.endless-pagination').data('next-page');

        if(page !== null && page != '') {

            clearTimeout( $.data( this, "scrollCheck" ) );

            $.data( this, "scrollCheck", setTimeout(function() {
                var scroll_position_for_posts_load = $(window).height() + $(window).scrollTop() + 600;
                if(scroll_position_for_posts_load >= $(document).height()) {
                    $("#below-img").LoadingOverlay("show");
                    $.get(page, function(data){
                        $('#imagesFlex').append(data.images);
                        $('.endless-pagination').data('next-page', data.next_page);
                        $('#imagesFlex').flexImages({ rowHeight: 320, truncate: false });
                        $("#below-img").LoadingOverlay("hide");
                    });
                }
            }, 350))

        }
    }

    $(document).on('mouseenter', '.hovercard', 
        function () {
            $(this).find('.hover-content').fadeIn();
       }
    );
    
    $(document).on('mouseleave', '.hovercard', 
       function () {
            $(this).find('.hover-content').fadeOut();
       }
    );

    // Format the related photos
    $('#modalImageDetails').on('shown.bs.modal', function (e) {
        $('.rel-flex-img').flexImages({ maxRows: 1, truncate: true });
        $('.spon-flex-img').flexImages({ maxRows: 1, truncate: true });
    })

    $('#modalImageDetails').on('hidden.bs.modal', function () {
        if ($('#modalImageDetails').hasClass('in') == false) {
            $('.modal-backdrop').remove();

            // Go back to the url before opening the modal
            history.go((-1)-goHistoryCount);
            goHistoryCount=0;

            $('body').css('overflow-y', 'auto');
        } else {
            
            $('body').css('overflow-y', 'hidden');
        }
    });

    window.onpopstate = function(event) {
        var prvUrl = document.referrer;
        var newLocUrl = document.location;
        var newLocNm = document.location.pathname;

        if (newLocNm.indexOf('photo') >= 0 ) {
            var imgId = newLocNm.split('/');

            var prvPathNm = prvUrl.replace(URL_BASE, '');
            history.replaceState("", "", prvPathNm);

            $('#'+imgId[2]).click();
        } else if (newLocUrl != prvUrl) {
            window.location.href = newLocUrl;
        }
    }
});