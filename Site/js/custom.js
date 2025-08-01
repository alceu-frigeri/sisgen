$(".nav a").click(function(){ 

});
    
$(function() {
/*
    $.post('log.php', { width: screen.width, height:screen.height, page: getParameterByName("q")+">"+getParameterByName("sq") }, function(json) {
        if(!json.outcome == 'success') {
            Log.i("no");
        }  
    },'json');
    */
});

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
