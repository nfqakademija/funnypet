/**
 * Ajax infinity scroll
 */
$(document).ready(function(){
    MasonryFix();

    window.query = '';
    window.scrollPage = 12;
    window.scrollStop = false;
    window.scrollContinue = true;

    $(window).scroll(function(){
        if($(window).scrollTop() == $(document).height() - $(window).height()){
            if (! window.scrollStop &&  window.scrollContinue) {
                window.scrollContinue = false;
                $('div#scroll_loader').show();
                var query_part = window.query ? "?q="+window.query : '';
                $.ajax({
                    url: "/ajax_photo_display/" +  window.scrollPage + query_part,
                    success: function(html){
                        if(html){
                            window.scrollPage =  window.scrollPage + 12;
                            window.scrollContinue = true;
                            $("#postswrapper").append(html);
                            $('div#scroll_loader').hide();

                            MasonryFix();

                        }else{
                            window.scrollContinue = true
                            window.scrollStop = true;
                            $('div#scroll_loader').html('Daugiau nuotraukų nerasta.');
                        }
                    }
                });
            }
        }
    });
});

$(".search-action").bind("keypress click", function(event){
    if(
        (event.type == "click" && $(event.target).is("input") == false) ||
        (event.type == "keypress" && event.which == 13)
    ) {
        event.preventDefault();
        window.query = encodeURIComponent($("#search").val());
        $.ajax({
            url: "/ajax_photo_display/0?q="+window.query,
            success: function(html){
                if(html){
                    $("#postswrapper").children("div").remove();
                    //Reset page
                    window.scrollPage = 12;
                    window.scrollContinue = true;
                    window.scrollStop = false;
                    $('div#scroll_loader').hide();
                    $('div#scroll_loader').html('<img alt="Loading..." src="/css/img/loading.gif">');
                    $("#postswrapper").append(html);
                    MasonryFix();
                }
            }
        });
    }
});

/**
 * Auto complete
 */
$(function() {
    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }
    $( "#search" )
        // don't navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).autocomplete( "instance" ).menu.active ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            source: function( request, response ) {
                NProgress.start();
                $.getJSON( "/ajax_search_tags/", {
                    term: extractLast( request.term )
                }, response).
                    success(function(){
                        NProgress.done();
                    });
            },
            search: function() {
                // custom minLength
                $(".ui-autocomplete").css("z-index", 1512);
                var term = extractLast( this.value );
                if ( term.length < 2 ) {
                    return false;
                }
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.value );
                // add placeholder to get the comma-and-space at the end
                terms.push( "" );
                this.value = terms.join( ", " );
                return false;
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