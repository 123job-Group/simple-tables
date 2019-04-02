$.fn.simpletable = function () {
    let container = $(this);

    const selectsSelector = '.simple-table-controls select, .simple-table-filters select';
    const inputsSelector = '.simple-table-filters input, .simple-table-controls input';

    container.find(selectsSelector).change(function () {
        sendFilter();
    });

    container.find(inputsSelector).keyup(function (e) {
        if (e.keyCode == 13) {
            sendFilter();
        }
    });

    $('.simple-table-clearable span').click(function () {
        $(this).parent().find('input').first().val('');
        sendFilter();
    });

    $('.simple-table-reset').click(function () {
        const url = window.location.origin + window.location.pathname;
        window.location.replace(url);
    });

    const sendFilter = function () {
        const filter = container.find(inputsSelector + ',' + selectsSelector).serialize();
        const url = window.location.origin + window.location.pathname + '?' + filter;
        window.location.replace(url);
    };

};