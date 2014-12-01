NProgress.start();

// Increase randomly
var interval = setInterval(function() { NProgress.inc(); }, 500);

// Trigger finish when page fully loaded
$(window).load(function () {
    clearInterval(interval);
    NProgress.done();
});

// Trigger bar when exiting the page
$(window).unload(function () {
    NProgress.start();
});