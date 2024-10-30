/**
 * BLOCK: custom-query-block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//import CSS.
import './style.scss';
import './editor.scss';

var el = wp.element.createElement;
var {__} = wp.i18n;
var registerBlockType = wp.blocks.registerBlockType;
var AlignmentToolbar = wp.blocks.AlignmentToolbar;
var InspectorControls = wp.editor.InspectorControls;
var SelectControl = wp.components.SelectControl;
var TextControl = wp.components.TextControl;
var PanelBody = wp.components.PanelBody;
var RangeControl = wp.components.RangeControl;

var custom_post_types;
var available_templates;

fetch('/wp-json/custom-query-block/v1/get-post-types')
    .then(function (response) {
        // console.log('response', response.text());
        //console.log('response.status:', response.status);
        //console.log('response.statusText:', response.statusText);
        //console.log('response.ok:', response.ok);
        return response.text();
    })
    .then(function (responseBody) {
        // console.log('responseBody:', responseBody);
        // console.log(JSON.parse(responseBody));
        custom_post_types = JSON.parse(responseBody);
    });

fetch('/wp-json/custom-query-block/v1/get-templates')
    .then(function (response) {
        return response.text();
    })
    .then(function (responseBody) {
        // console.log(JSON.parse(responseBody));
        available_templates = JSON.parse(responseBody);
    });


registerBlockType('cqb/block-custom-query-block', {
    title: __('Custom Query'),
    description: __('Display some data based on a custom query selection'),
    icon: 'admin-tools',
    category: 'common',
    keywords: [
        __('Custom'),
        __('custom-query-block')
    ],

    attributes: {
        post_type: {
            type: 'string'
        },
        display_number: {
            type: 'integer',
            default: 10
        },
        order_by: {
            type: 'string'
        },
        cqb_block_description: {
            type: 'string'
        },
        cqb_template: {
            type: 'string',
            label: 'Template'
        }
    },

    edit: function (props) {
        var post_type = props.attributes.post_type;
        var order_by = props.attributes.order_by;
        var display_number = props.attributes.display_number;
        var cqb_block_description = props.attributes.cqb_block_description;
        var cqb_template = props.attributes.cqb_template;

        function setDescription(postType, displayNumber) {
            var description = '';

            if (typeof displayNumber != 'undefined' && displayNumber != '') {
                description += displayNumber + "x ";
            }

            if (typeof postType != 'undefined' && postType != '') {
                description += "'" + postType + "'";
            } else {
                description += "Any items";
            }

            props.setAttributes({cqb_block_description: description});
        }

        function onSelectPostType(postType) {
            props.setAttributes({post_type: postType});

            setDescription(
                postType,
                props.attributes.display_number
            );
        }

        function onSelectOrderBy(orderBy) {
            props.setAttributes({order_by: orderBy});
        }

        function onSelectTemplate(cqbTemplate) {
            props.setAttributes({cqb_template: cqbTemplate});
        }

        function onChangeDisplayNumber(displayNumber) {
            props.setAttributes({display_number: displayNumber});

            setDescription(
                props.attributes.post_type,
                displayNumber
            );
        }

        return [
            el(
                InspectorControls,
                {
                    key: 'inspector'
                },
                el(
                    PanelBody,
                    {
                        'title': __('Custom Query Settings')
                    },
                    el(
                        SelectControl,
                        {
                            label: __('Post Type'),
                            options: custom_post_types,
                            value: post_type,
                            onChange: onSelectPostType
                        }
                    ),
                    el(
                        RangeControl,
                        {
                            label: __('Number to Show'),
                            value: display_number,
                            min: 1,
                            max: 100,
                            onChange: onChangeDisplayNumber
                        }
                    ),
                    el(
                        SelectControl,
                        {
                            label: __('Order By'),
                            options: [
                                {value: 'date/desc', label: 'Newest to Oldest'},
                                {value: 'date/asc', label: 'Oldest to Newest'},
                                {value: 'title/asc', label: 'A → Z'},
                                {value: 'title/desc', label: 'Z → A'}
                            ],
                            value: order_by,
                            onChange: onSelectOrderBy
                        }
                    ),
                    el(
                        SelectControl,
                        {
                            label: __('Select Template'),
                            options: available_templates,
                            value: cqb_template,
                            onChange: onSelectTemplate
                        }
                    )
                )
            ),
            el('div', {className: 'cqb-wrapper-admin'},
                el('h3', {className: 'cqb-admin-title'}, 'Custom Query'),
                el('p', {className: 'cqb-admin-desc'}, cqb_block_description)
            )
        ];
    },

    save: function (props) {
    }
});
