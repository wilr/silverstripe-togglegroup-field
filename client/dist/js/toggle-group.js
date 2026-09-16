/**
 * Roving arrow-key navigation for the checkbox variant of the toggle group
 * (ToggleGroupSetField), matching the WAI-ARIA toggle group keyboard
 * interaction pattern. Native <input type="radio"> groups already support
 * arrow key navigation in every browser, so this is only needed for
 * checkboxes.
 */
(function ($) {
    $.entwine('ss', function ($) {
        $('.toggle-group-field .toggle-group__input[type=checkbox]').entwine({
            onkeydown: function (e) {
                var key = e.key;

                if (['ArrowRight', 'ArrowDown', 'ArrowLeft', 'ArrowUp'].indexOf(key) === -1) {
                    return;
                }

                e.preventDefault();

                var $inputs = $(this)
                    .closest('.toggle-group-field')
                    .find('.toggle-group__input[type=checkbox]');
                var index = $inputs.index(this);
                var next;

                if (key === 'ArrowRight' || key === 'ArrowDown') {
                    next = (index + 1) % $inputs.length;
                } else {
                    next = (index - 1 + $inputs.length) % $inputs.length;
                }

                $inputs.eq(next).trigger('focus');
            }
        });
    });
}(jQuery));
