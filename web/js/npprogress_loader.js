NProgress.start();

// Increase randomly
var interval = setInterval(function() { NProgress.inc(); }, 500);

// Trigger finish when page fully loaded
jQuery(window).load(function () {
    clearInterval(interval);
    NProgress.done();
});

// Trigger bar when exiting the page
jQuery(window).unload(function () {
    NProgress.start();
});