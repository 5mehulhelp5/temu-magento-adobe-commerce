define([
    'jquery'
], function ($) {
    'use strict';

    return function (attributesData) {
        $(document).ready(function () {
            function init() {
                processAttributes(attributesData, null, true);

                attributesData.forEach(attr => {
                    $(`#${attr.html_id}`).on('change', function () {
                        const selectedValue = $(this).val();
                        handleParentChange(attr.id, selectedValue);
                    });
                });
            }
            function disableChildren(parentAttribute, isInit) {
                const childTemplateIds = parentAttribute.values.reduce((acc, value) => {
                    if (value.children_relation) {
                        acc.push(...Object.keys(value.children_relation));
                    }
                    return acc;
                }, []);

                childTemplateIds.forEach(childTemplatePid => {
                    const childAttribute = attributesData.find(attr => attr.id === childTemplatePid);
                    if (!childAttribute) {
                        return;
                    }

                    if (childAttribute.has_child) {
                        handleParentChange(childAttribute.id, [], isInit);
                    }

                    const modeValueSelect = document.getElementById(childAttribute.mode_value_html_id);
                    if (!modeValueSelect) {
                        return;
                    }

                    const recommendedSelect = document.getElementById(childAttribute.html_id);
                    const attrSelect = document.getElementById(childAttribute.attr_html_id);
                    const customInput = document.getElementById(childAttribute.custom_html_id);

                    disableRow(modeValueSelect.closest('.child-relation-row'));

                    modeValueSelect.disabled = true;

                    if (customInput) {
                        customInput.disabled = true;
                    }

                    if (attrSelect) {
                        attrSelect.disabled = true;
                    }

                    if (recommendedSelect) {
                        recommendedSelect.disabled = true;
                        while (recommendedSelect.options.length) {
                            recommendedSelect.remove(0);
                        }
                    }
                });
            }

            function handleParentChange(parentId, selectedValue, isInit = false) {
                const parentAttribute = attributesData.find(attr => attr.id === parentId);
                if (!parentAttribute) {
                    return;
                }

                disableChildren(parentAttribute, isInit);

                const selectedValueData = parentAttribute.values.find(value => value.id === selectedValue);
                if (!selectedValueData?.children_relation || !Object.keys(selectedValueData.children_relation).length) {
                    return;
                }

                Object.entries(selectedValueData.children_relation).forEach(([childTemplatePid, allowedValueIds]) => {
                    const childAttribute = attributesData.find(attr => attr.id === childTemplatePid);
                    if (!childAttribute) {
                        return;
                    }

                    const modeValueSelect = document.getElementById(childAttribute.mode_value_html_id);
                    const recommendedSelect = document.getElementById(childAttribute.html_id);

                    if (!recommendedSelect || !modeValueSelect) {
                        return;
                    }

                    modeValueSelect.disabled = false;
                    const event = new Event('change', {bubbles: true});
                    modeValueSelect.dispatchEvent(event);

                    enableRow(modeValueSelect.closest('.child-relation-row'));

                    childAttribute.values
                            .filter(value => allowedValueIds.includes(value.id))
                            .forEach(value => {
                                const option = new Option(value.name, value.id);
                                if (isInit && modeValueSelect.value === '1' && value.selected?.includes(value.id)) {
                                    option.selected = true;
                                }
                                recommendedSelect.add(option);
                            });

                    recommendedSelect.disabled = false;

                    if (childAttribute.has_child && recommendedSelect?.value) {
                        handleParentChange(childTemplatePid, recommendedSelect.value, isInit);
                    }
                });
            }

            function processAttributes(attributes, parentId = null, isInit = false) {
                attributes
                        .filter(attr => attr.parent_template_pid === parentId)
                        .forEach(attr => {
                            handleParentChange(attr.id, $(`#${attr.html_id}`).val(), isInit);
                            if (attr.has_child) {
                                processAttributes(attributes, attr.id, isInit);
                            }
                        });
            }

            function enableRow(row) {
                if (row) {
                    row.classList.add('visible');
                }
            }

            function disableRow(row) {
                if (row) {
                    row.classList.remove('visible');
                }
            }

            init();
        });
    };
});
