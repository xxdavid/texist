/**
 * (c) David Grudl
 */
$(function(){
    var timeoutId;
    $.ajaxSetup({
        timeout: 4000
    });
    $('textarea').keydown(function(e) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {
            $.post(window.location.href+'/process', { text: $('textarea').val() }, function(data) {
                $('#output').html(data);
            });
        }, 1000);
        if (e.which == 9 /* TAB */ && !e.ctrlKey && !e.altKey) {
            if (e.target.setSelectionRange) { // non-IE
                var start = e.target.selectionStart, end = e.target.selectionEnd;
                var top = e.target.scrollTop;
                if (start !== end) {
                    start = e.target.value.lastIndexOf("\n", start) + 1;
                }
                var sel = e.target.value.substring(start, end);
                if (e.shiftKey) {
                    sel = sel.replace(/^\t/gm, '');
                } else {
                    sel = sel.replace(/^/gm, "\t");
                }
                e.target.value = e.target.value.substring(0, start) + sel + e.target.value.substr(end);
                e.target.setSelectionRange(start === end ? start + 1 : start, start + sel.length);
                e.target.focus();
                e.target.scrollTop = top; // Firefox
            } else if (e.target.createTextRange) { // ie
                document.selection.createRange().text = "\t";
            }
            if ($.browser.opera) {
                $(this).one('keypress', function(e) { return false; });
            }
            return false;
        } else if (e.which == 27 /* ESC */ && !e.shiftKey && !e.ctrlKey && !e.altKey) {
            var inputs = $(':input');
            inputs.eq(inputs.index(e.target) - inputs.length + 1).focus();
        }
    }).focus();
});