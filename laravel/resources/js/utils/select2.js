import $ from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';

window.$ = window.jQuery = $;
select2(window, $);

export function installSelect2(app) {
    app.directive('select2', {
        mounted(el, binding) {
            initSelect2(el, binding.value);
        },
        updated(el, binding) {
            if (!$.fn?.select2) return;

            $(el).val(el.value).trigger('change.select2');

            if (JSON.stringify(binding.value || {}) !== JSON.stringify(binding.oldValue || {})) {
                destroySelect2(el);
                initSelect2(el, binding.value);
            }
        },
        beforeUnmount(el) {
            destroySelect2(el);
        },
    });
}

export function initSelect2(el, options = {}) {
    if (!$.fn?.select2 || el.dataset.select2Ready === '1') return;

    const config = {
        width: '100%',
        theme: 'bootstrap-5',
        allowClear: Boolean(options?.allowClear),
        placeholder: options?.placeholder || el.dataset.placeholder || '',
        ...options,
    };

    $(el)
        .select2(config)
        .on('change.select2-vue', () => {
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });

    el.dataset.select2Ready = '1';
}

function destroySelect2(el) {
    if (!$.fn?.select2 || el.dataset.select2Ready !== '1') return;

    $(el).off('change.select2-vue').select2('destroy');
    delete el.dataset.select2Ready;
}
