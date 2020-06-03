var getMaxHeight = function ($elms) {
    var maxHeight = 0;
    $elms.each(function () {
        // ¬ некоторых случа€х можно использовать outerHeight()
        var height = $(this).height();
        if (height > maxHeight) {
            maxHeight = height;
        }
    });
    return maxHeight;
};