var getMaxHeight = function ($elms) {
    var maxHeight = 0;
    $elms.each(function () {
        // � ��������� ������� ����� ������������ outerHeight()
        var height = $(this).height();
        if (height > maxHeight) {
            maxHeight = height;
        }
    });
    return maxHeight;
};