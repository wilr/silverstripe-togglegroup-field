/**
 * Roving arrow-key navigation for the checkbox variant of the toggle group
 * (ToggleGroupSetField), matching the WAI-ARIA toggle group keyboard
 * interaction pattern. Native <input type="radio"> groups already support
 * arrow key navigation in every browser, so this is only needed for
 * checkboxes.
 *
 * Deliberately free of dependencies: the CMS does not guarantee jQuery is
 * defined by the time a Requirements script runs, and the fields render on the
 * front end too. The listener is delegated so it also covers groups added
 * after load, such as a form the CMS renders through React.
 */
(function () {
    var SELECTOR = '.toggle-group-field .toggle-group__input[type="checkbox"]';
    var FORWARD = ['ArrowRight', 'ArrowDown'];
    var BACKWARD = ['ArrowLeft', 'ArrowUp'];

    document.addEventListener('keydown', function (event) {
        var target = event.target;

        if (!target || typeof target.matches !== 'function' || !target.matches(SELECTOR)) {
            return;
        }

        var forward = FORWARD.indexOf(event.key) !== -1;

        if (!forward && BACKWARD.indexOf(event.key) === -1) {
            return;
        }

        var group = target.closest('.toggle-group-field');

        if (!group) {
            return;
        }

        var inputs = Array.prototype.slice.call(
            group.querySelectorAll('.toggle-group__input[type="checkbox"]')
        );
        var index = inputs.indexOf(target);

        if (index === -1 || inputs.length < 2) {
            return;
        }

        event.preventDefault();

        var next = forward
            ? (index + 1) % inputs.length
            : (index - 1 + inputs.length) % inputs.length;

        inputs[next].focus();
    });
})();
