/**
 * Ajax infinity scroll
 */
$(document).ready(function(){
    MasonryFix();

    var scrollPage = 12;
    var scrollStop = false;
    var scrollContinue = true;

    $(window).scroll(function(){
        if($(window).scrollTop() == $(document).height() - $(window).height()){
            if (!scrollStop && scrollContinue) {
                scrollContinue = false;
                $('div#scroll_loader').show();
                $.ajax({
                    url: "/ajax_scroll/" + scrollPage,
                    success: function(html){
                        if(html){
                            scrollPage = scrollPage + 12;
                            scrollContinue = true;
                            $("#postswrapper").append(html);
                            $('div#scroll_loader').hide();

                            MasonryFix();

                        }else{
                            scrollContinue = true
                            scrollStop = true;
                            $('div#scroll_loader').html('Daugiau nuotraukų nerasta.');
                        }
                    }
                });
            }
        }
    });
});

/**
 * Rate photo
 */
$(document).on("click", ".rate-like", function() {
    var url = $(this).attr("data-url");
    var selector = $(this);
    var selectorRatingBlock = $(this).parent();
    $.post( url, function( data ) {
        if(data.success) {
            if (data.userRating > 0) {
                $(selector).replaceWith('<span class="glyphicon glyphicon-hand-up icon-like rate-user-like"></span>');
            } else {
                $(selector).replaceWith('<span class="glyphicon glyphicon-hand-down icon-like rate-user-dislike"></span>');
            }
            $(selectorRatingBlock).children(".rate-like").remove();
            $(selectorRatingBlock).children(".rating").text(data.rating);
        } else {
            $("#modal-text").text(data.error);
            $("#modal-rating").modal();
        }
    }).fail(function(){

    });
});

/**
 * Fix different height bootstrap columns
 * to show correctly
 * @constructor
 */
function MasonryFix () {
    var container = document.querySelector('#postswrapper');
    var msnry = new Masonry( container, {
        itemSelector: '.items-block'
    });
}