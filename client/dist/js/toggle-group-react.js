/**
 * React implementations of ToggleGroupField and ToggleGroupSetField for
 * schema-driven CMS forms, such as Elemental's inline block editor.
 *
 * They render the same markup as the SilverStripe templates so the shared
 * stylesheet applies unchanged, but the inputs are controlled by the form
 * state so values are submitted when the block is saved.
 *
 * Written without a build step against the globals the admin bundle exposes
 * (React, Injector, FieldHolder).
 */
(function () {
    var unwrap = function (module) {
        return module && module.default ? module.default : module;
    };

    var React = unwrap(window.React);
    var Injector = unwrap(window.Injector);
    var fieldHolder = unwrap(window.FieldHolder);

    if (!React || !Injector || !fieldHolder) {
        return;
    }

    var h = React.createElement;

    var toValues = function (value) {
        if (value === null || typeof value === 'undefined' || value === '') {
            return [];
        }

        return (Array.isArray(value) ? value : [value]).map(String);
    };

    var renderIcon = function (icon) {
        if (!icon) {
            return null;
        }

        if (icon.svg) {
            return h('span', {
                className: 'toggle-group__icon',
                'aria-hidden': 'true',
                dangerouslySetInnerHTML: { __html: icon.svg }
            });
        }

        return h('span', {
            className: 'toggle-group__icon font-icon-' + icon.font,
            'aria-hidden': 'true'
        });
    };

    var createToggleGroup = function (multiple) {
        var ToggleGroup = function (props) {
            var data = props.data || {};
            var icons = data.icons || {};
            var iconsOnly = Boolean(data.iconsOnly);
            var selected = toValues(props.value);
            var source = props.source || [];

            var handleChange = function (event) {
                if (typeof props.onChange !== 'function') {
                    return;
                }

                var optionValue = event.target.value;
                var value;

                if (multiple) {
                    value = source
                        .map(function (option) { return String(option.value); })
                        .filter(function (candidate) {
                            return candidate === optionValue
                                ? event.target.checked
                                : selected.indexOf(candidate) !== -1;
                        });
                } else {
                    var match = source.find(function (option) {
                        return String(option.value) === optionValue;
                    });
                    value = match ? match.value : optionValue;
                }

                props.onChange(event, { id: props.id, value: value });
            };

            var className = (props.extraClass || '').split(' ')
                .filter(function (name) { return name && name !== 'toggle-group-field'; })
                .concat('toggle-group-field')
                .join(' ');

            return h(
                'ul',
                {
                    id: props.id,
                    className: className,
                    role: multiple ? 'group' : 'radiogroup'
                },
                source.map(function (option, index) {
                    var optionValue = String(option.value);
                    var optionId = props.id + '_' + (optionValue !== '' ? optionValue : 'empty' + index);
                    var icon = icons[optionValue];

                    return h(
                        'li',
                        { key: optionId, className: 'toggle-group__option' },
                        h('input', {
                            type: multiple ? 'checkbox' : 'radio',
                            id: optionId,
                            className: 'toggle-group__input',
                            name: multiple ? props.name + '[' + optionValue + ']' : props.name,
                            value: optionValue,
                            checked: selected.indexOf(optionValue) !== -1,
                            disabled: Boolean(option.disabled || props.disabled || props.readOnly),
                            onChange: handleChange
                        }),
                        h(
                            'label',
                            {
                                htmlFor: optionId,
                                className: 'toggle-group__label' + (iconsOnly ? ' toggle-group__label--icon-only' : '')
                            },
                            renderIcon(icon),
                            option.title !== null && option.title !== ''
                                ? h('span', {
                                    className: 'toggle-group__title' + (iconsOnly ? ' toggle-group__title--sr-only' : '')
                                }, option.title)
                                : null
                        )
                    );
                })
            );
        };

        return fieldHolder(ToggleGroup);
    };

    window.document.addEventListener('DOMContentLoaded', function () {
        Injector.component.registerMany({
            ToggleGroupField: createToggleGroup(false),
            ToggleGroupSetField: createToggleGroup(true)
        });
    });
})();
