<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-field-info">
    <div class="nbd-field-info-1">
        <div><label><b><?php _e('Display type', 'web-to-print-online-designer'); ?></b></label></div>
    </div>
    <div class="nbd-field-info-2">
        <div>
            <select convert-to-number name="options[display_type]" ng-model="options.display_type">
                <option <?php selected( $options['display_type'], 1 ); ?> value="1"><?php _e('Default', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 2 ); ?> value="2"><?php _e('Price Matrix', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 3 ); ?> value="3"><?php _e('Bulk variation', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 4 ); ?> value="4"><?php _e('Group', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 5 ); ?> value="5"><?php _e('Step by step', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 6 ); ?> value="6"><?php _e('Show a part of fields in the popup', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
</div>
<div class="nbd-field-info" ng-if="options.display_type == 2">
    <p><?php _e('Allow fields with options: Data type - Multiple options | Enable - Yes | has at least one attribute | Field Conditional Logic - No', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-field-info" ng-hide="options.manual_build_pm">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Horizontal field', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[pm_hoz][]" multiple ng-model="options.pm_hoz">
                    <option value="{{field.field_index}}" ng-repeat="(fieldIndex, field) in availablePmHozFileds">{{options.fields[field.field_index].general.title.value}}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="nbd-field-info" ng-hide="options.manual_build_pm">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Vertical field', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[pm_ver][]" multiple ng-model="options.pm_ver">
                    <option  value="{{field.field_index}}" ng-repeat="(fieldIndex, field) in availablePmVerFileds">{{options.fields[field.field_index].general.title.value}}</option>
                </select>
            </div>
        </div>
    </div>
    <hr />
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Manual build price matrix', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <input type="checkbox" ng-model="options.manual_build_pm" ng-checked="options.manual_build_pm" name='options[manual_build_pm]' />
                <span><?php _e('You must enter price for each combination and individual price of each field will be ignored. The combination with empty price will be disabled in frontend. The combination price will be replace product base price.', 'web-to-print-online-designer'); ?></span>
            </div>
        </div>
    </div>
    <div class="nbd-field-info" ng-show="options.manual_build_pm">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Builder', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-all-pm-fields-wrap">
                <div>
                    <b><?php _e('All fields', 'web-to-print-online-designer'); ?></b>
                    <i><?php _e(' ( Drag desired fields to dropzone. )', 'web-to-print-online-designer'); ?></i>
                </div>
                <div class="nbd-all-pm-fields nbd-pm-fields" >
                    <div data-id="{{field.id}}" ng-repeat="(fieldIndex, field) in manualPMFields" nbd-pm-draggable class="nbd-pm-field nbd-darg-pm-field">{{options.fields[field.field_index].general.title.value}}</div>
                </div>
            </div>
            <div class="nbd-pm-horizontal-fields-wrap">
                <div><b><?php _e('Horizontal fields', 'web-to-print-online-designer'); ?></b></div>
                <div class="nbd-pm-horizontal-fields nbd-pm-fields" nbd-pm-droppable data-dir="hoz">
                    <div ng-dblclick="removePmFiled('hoz', field.id)" data-id="{{field.id}}" ng-repeat="(fieldIndex, field) in manualPMHozFields" class="nbd-pm-field" >{{options.fields[field.field_index].general.title.value}}</div>
                </div>
            </div>
            <div class="nbd-pm-vertical-fields-wrap">
                <div><b><?php _e('Vertical fields', 'web-to-print-online-designer'); ?></b></div>
                <div class="nbd-pm-vertical-fields nbd-pm-fields" nbd-pm-droppable data-dir="ver">
                    <div ng-dblclick="removePmFiled('ver', field.id)" data-id="{{field.id}}" ng-repeat="(fieldIndex, field) in manualPMVerFields" class="nbd-pm-field" >{{options.fields[field.field_index].general.title.value}}</div>
                </div>
            </div>
            <div class="nbd-pm-note"><?php _e('Double-click on an field to remove it.', 'web-to-print-online-designer'); ?></div>
            <div class="nbd-pm-builder-wrap" ng-if="manualPMVerFields.length > 0 && manualPMHozFields.length > 0">
                <div><b><?php _e('Manual Price Matrix', 'web-to-print-online-designer'); ?></b></div>
                <div class="nbd-table-wrap">
                    <table class="nbd-table">
                        <tbody>
                            <tr ng-repeat="field in manualPMHozFields">
                                <th ng-if="$index == 0" colspan="{{manualPMVerFields.length}}" rowspan="{{manualPMHozFields.length}}" ></th>
                                <th colspan="{{field.colspan}}" ng-repeat="n in [] | range: field.looptimes * field.general.attributes.options.length">
                                    {{field.general.attributes.options[n % field.general.attributes.options.length].name}}
                                </th>
                            </tr>
                            <tr ng-repeat="n in [] | range: manualPMRows">
                                <th ng-repeat="field in manualPMVerFields" ng-if="(n % field.rowspan) == 0" rowspan="{{field.rowspan}}" >
                                    {{field.general.attributes.options[(n / field.rowspan) % field.general.attributes.options.length].name}}
                                </th>
                                <td ng-repeat="m in [] | range: manualPMCols">
                                    <input type="text" class="nbd-short-ip" ng-change="updateManualPm()" ng-model="manualPMCells[n * manualPMCols + m]" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 10px;" ><a class="button button-primary" ng-click="clearPm()" ><?php _e('Clear', 'web-to-print-online-designer'); ?></a></div>
            </div>
        </div>
        <textarea style="display: none;" name="options[manual_pm]" ng-model="manual_pm"></textarea>
    </div>
</div>
<div class="nbd-field-info" ng-if="options.display_type == 3">
    <p><?php _e('Allow fields with options: Enable - Yes | Field Conditional Logic - No', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Bulk form field', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[bulk_fields][]" multiple ng-model="options.bulk_fields">
                    <option value="{{field.field_index}}" ng-repeat="(fieldIndex, field) in availableBulkFileds">{{options.fields[field.field_index].general.title.value}}</option>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="nbd-field-info" ng-show="options.display_type == 4">
    <p><b><?php _e('Manage option groups', 'web-to-print-online-designer'); ?></b></p>
    <div ng-repeat="(gIndex, group) in options.groups" class="nbd-group-wrap">
        <div class="nbd-group-img-wrap" ng-show="group.isExpand">
            <label><?php _e('Icon', 'web-to-print-online-designer'); ?></label>
            <div class="nbd-group-img-inner">
                <span class="dashicons dashicons-no remove-group-img" ng-click="remove_group_image( $index )"></span>
                <input ng-hide="true" ng-model="group.image" name="options[groups][{{$index}}][image]"/>
                <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_group_image( $index )" ng-src="{{group.image != 0 ? group.image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
            </div>
        </div>
        <div class="nbd-group-main" ng-show="group.isExpand">
            <div class="group-field">
                <label><?php _e('Title', 'web-to-print-online-designer'); ?></label>
                <input type="text" ng-model="group.title" name='options[groups][{{$index}}][title]' />
            </div>
            <div class="group-field">
                <label><?php _e('Description', 'web-to-print-online-designer'); ?></label>
                <textarea ng-model="group.des" value="{{group.des}}" name='options[groups][{{$index}}][des]' rows="5"></textarea>
            </div>
            <div class="group-field">
                <label><?php _e('Note', 'web-to-print-online-designer'); ?></label>
                <textarea ng-model="group.note" value="{{group.note}}" name='options[groups][{{$index}}][note]' rows="5"></textarea>
            </div>
            <div class="group-field">
                <label><?php _e('Number of column', 'web-to-print-online-designer'); ?></label>
                <select ng-model="group.cols" name='options[groups][{{$index}}][cols]' convert-to-number>
                    <option value="1">1 <?php _e('Column', 'web-to-print-online-designer'); ?></option>
                    <option value="2">2 <?php _e('Columns', 'web-to-print-online-designer'); ?></option>
                    <option value="3">3 <?php _e('Columns', 'web-to-print-online-designer'); ?></option>
                    <option value="4">4 <?php _e('Columns', 'web-to-print-online-designer'); ?></option>
                </select>
            </div>
            <div class="group-field">
                <label><?php _e('Group field list', 'web-to-print-online-designer'); ?></label>
                <select name="options[groups][{{$index}}][fields][]" multiple ng-model="group.fields">
                    <option value="{{field.id}}" ng-repeat="field in options.fields | filter: {id: gIndex}:availableGroupField">{{field.general.title.value}}</option>
                </select>
                <p><a class="button" ng-click="clear_group($index)"><span class="dashicons dashicons-no-alt"></span><?php _e('Clear all group fields', 'web-to-print-online-designer') ?></a></p>
            </div>
        </div> 
        <div ng-show="!group.isExpand" class="nbd-group-name-preview">{{group.title}}</div>
        <div class="nbd-group-actions">
            <span class="nbo-sort-group">
                <span ng-click="sort_group($index, 'up')" class="dashicons dashicons-arrow-up nbo-sort-up nbo-sort" title="<?php _e('Up', 'web-to-print-online-designer') ?>"></span>
                <span ng-click="sort_group($index, 'down')" class="dashicons dashicons-arrow-down nbo-sort-down nbo-sort" title="<?php _e('Down', 'web-to-print-online-designer') ?>"></span>
            </span>
            <a class="button nbd-mini-btn" ng-click="remove_group($index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
            <a class="button nbd-mini-btn"  ng-click="toggle_expand_group($index)" title="<?php _e('Expend', 'web-to-print-online-designer'); ?>">
                <span ng-show="group.isExpand" class="dashicons dashicons-arrow-up"></span>
                <span ng-show="!group.isExpand" class="dashicons dashicons-arrow-down"></span>
            </a>
        </div>
    </div>
    <div>
        <a class="button" ng-click="add_group()"><span class="dashicons dashicons-plus"></span><?php _e('Add group', 'web-to-print-online-designer'); ?></a>
    </div>
    <div class="nbd-field-info" style="margin-top: 15px">
        <div class="nbd-field-info-1">
            <label><b><?php _e( 'Show group as panel', 'web-to-print-online-designer' ); ?></b></label>
        </div>
        <div class="nbd-field-info-2">
            <input type="checkbox" ng-checked="options.group_panel == 'on'" name='options[group_panel]' />
        </div>
    </div>
</div>
<!-- Custom Kitalabel -->
<div class="nbd-field-info">
    <div class="nbd-field-top clearfix">
        <div class="nbd-field-info-1">
            <label><b><?php _e( 'Show options combination', 'web-to-print-online-designer' ); ?></b></label>
        </div>
        <div class="nbd-field-info-2">
            <input type="checkbox" ng-model="options.combination.show_op" ng-click="update_no_combination(options.combination.show_op)" />
        </div>
    </div>
    <div class="options-combination" ng-show="options.combination.show_op" style="overflow-x: auto; width: 100%;">
        <div class="option-default">
            <table>
                <tbody>
                    <tr>
                        <td style="padding: 5px 10px 5px 0;"><label><b>Enable combination price:{{options.combination.enable}} </b></label></td>
                        <td style="padding: 5px 10px;"><input type="checkbox" ng-checked="options.combination.enabled" name="options[combination][enabled]" ></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 10px 5px 0;"><label><b>Default option combination</b></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 10px 5px 0;"><label><b>Price: </b></label></td>
                        <td style="padding: 5px 10px;"><input string-to-number type="number" min="0" step="any"  ng-model="options.combination.default.price" name="options[combination][default][price]"></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 10px 5px 0;"><label><b>Qty: </b></label></td>
                        <td style="padding: 5px 10px;"><input string-to-number type="number" min="0" step="any"  ng-model="options.combination.default.qty" name="options[combination][default][qty]"></td>
                    </tr>
                </tbody>
            </table>           
        </div>
        <div class="nbd-field-btn button" ng-click="set_default_combination()">Set default</div>
        <span class="nbd-field-btn button" ng-click="importCombination()">Import CSV</span><div style="color : #77a464; padding: 10px 10px; font-weight: 600;" class="result-import"></div>
        <table class="nbd-table" style="text-align: center; width: 100%;">
            <thead>
                <tr>
                    <th><?php _e('No', 'web-to-print-online-designer'); ?></th>
                    <th><?php _e('Area', 'web-to-print-online-designer'); ?></th>
                    <th><?php _e('Size', 'web-to-print-online-designer'); ?></th>
                    <th><?php _e('Material', 'web-to-print-online-designer'); ?></th>
                    <th><?php _e('Price', 'web-to-print-online-designer'); ?></th>
                    <th><?php _e('Min Qty', 'web-to-print-online-designer'); ?></th>
                    <th><?php _e('Weight/1', 'web-to-print-online-designer'); ?></th>
                    <th><?php _e('Show more', 'web-to-print-online-designer'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat-start="(oaIndex, op_area) in options_areas">
                    <tr ng-repeat-start="(osIndex, op_size) in options_sizes[oaIndex]">
                        <tr ng-repeat-start="(omIndex, op_material) in options_materials" ng-hide="op_area.coming_soon == 'on' || op_size.coming_soon == 'on' || op_material.coming_soon == 'on'">
                            <td>{{options.combination.options[op_area.name][op_size.name][op_material.name].index}}
                                <input class="nbd-short-ip" ng-value="options.combination.options[op_area.name][op_size.name][op_material.name].index" type="hidden" name="options[combination][options][{{op_area.name}}][{{op_size.name}}][{{op_material.name}}][index]" />
                                <input class="nbd-short-ip" ng-value="op_area.coming_soon == 'on' || op_size.coming_soon == 'on' || op_material.coming_soon == 'on' ? 'on' : ''" type="hidden" name="options[combination][options][{{op_area.name}}][{{op_size.name}}][{{op_material.name}}][coming_soon]" />
                            </td>
                            <td>{{op_area.name}}</td>
                            <td>{{op_size.name}}</td>
                            <td>{{op_material.name}}</td>
                            <td><input string-to-number class="nbd-short-ip" ng-model="options.combination.options[op_area.name][op_size.name][op_material.name].price" type="number" min="0" step="any" name="options[combination][options][{{op_area.name}}][{{op_size.name}}][{{op_material.name}}][price]" /></td>
                            <td><input string-to-number class="nbd-short-ip" ng-model="options.combination.options[op_area.name][op_size.name][op_material.name].qty" type="number" min="0" step="any" name="options[combination][options][{{op_area.name}}][{{op_size.name}}][{{op_material.name}}][qty]" /></td>
                            <td><input string-to-number class="nbd-short-ip" ng-model="options.combination.options[op_area.name][op_size.name][op_material.name].weight" type="number" min="0" step="any" name="options[combination][options][{{op_area.name}}][{{op_size.name}}][{{op_material.name}}][weight]" /></td>
                            <td>
                                <a class="button nbd-mini-btn"  ng-click="show_quantity_break_combination(op_area.name, op_size.name, op_material.name)" title="<?php _e('Expend', 'web-to-print-online-designer'); ?>">
                                    <span ng-show="options.combination.options[op_area.name][op_size.name][op_material.name].show" class="dashicons dashicons-arrow-up"></span>
                                    <span ng-show="!options.combination.options[op_area.name][op_size.name][op_material.name].show" class="dashicons dashicons-arrow-down"></span>
                                </a>
                            </td>
                        </tr>
                        <tr ng-show="options.combination.options[op_area.name][op_size.name][op_material.name].show" ng-hide="op_area.coming_soon == 'on' || op_size.coming_soon == 'on' || op_material.coming_soon == 'on'">
                            <td colspan="8" style="max-width: 850px; overflow-x: auto;">
                                <style type="text/css">
                                    .custom-tab-qty-break td {
                                       padding: 5px 0px!important; 
                                    }
                                </style>
                                <table class="custom-tab-qty-break">
                                    <tr>
                                        <td><b>Qty</b></td>
                                        <td ng-repeat="( indexBreak , qty_break ) in options.combination.options[op_area.name][op_size.name][op_material.name].qty_breaks">
                                            <input string-to-number class="nbd-short-ip" ng-model="qty_break.qty" type="number" min="0" step="any" name="options[combination][options][{{op_area.name}}][{{op_size.name}}][{{op_material.name}}][qty_breaks][{{indexBreak}}][qty]" />
                                        </td>
                                        <td rowspan="1"><span class="dashicons dashicons-leftright" ng-click="kita_sort_qty_breaks(op_area.name, op_size.name, op_material.name)"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Price</b></td>
                                        <td ng-repeat="( indexBreak , qty_break ) in options.combination.options[op_area.name][op_size.name][op_material.name].qty_breaks">
                                            <input string-to-number class="nbd-short-ip" ng-model="qty_break.price" type="number" min="0" step="any" name="options[combination][options][{{op_area.name}}][{{op_size.name}}][{{op_material.name}}][qty_breaks][{{indexBreak}}][price]" />
                                        </td>
                                        <td rowspan="2"><span class="dashicons dashicons-plus" ng-click="kita_add_qty_breaks(op_area.name, op_size.name, op_material.name)"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Action</b></td>
                                        <td ng-repeat="( indexBreak , qty_break ) in options.combination.options[op_area.name][op_size.name][op_material.name].qty_breaks">
                                            <span class="dashicons dashicons-no" ng-click="kita_remove_qty_breaks( indexBreak , op_area.name, op_size.name, op_material.name )"></span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr ng-repeat-end></tr>
                    </tr>
                    <tr ng-repeat-end></tr>
                </tr>
                <tr ng-repeat-end></tr>
            </tbody>
        </table>
    </div>
</div>
<!-- END -->
<div class="nbd-field-info" ng-show="options.display_type == 6 && options.fields.length > 0">
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Show popup trigger button when', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[popup_trigger_field]" ng-model="options.popup_trigger_field" ng-change="updatePopupTriggerIndex()" style="width: 150px;">
                <option value="{{field.id}}" ng-repeat="field in allFields" >{{options.fields[field.field_index].general.title.value}}</option>
            </select> <?php _e('is', 'web-to-print-online-designer'); ?> 
            <span ng-if="options.popup_trigger_field !== ''">
                <span ng-if="options.fields[options.popup_trigger_index].general.data_type.value == 'i'">
                    <input name="options[popup_trigger_value]" ng-model="options.popup_trigger_value" type="text" style="width: 150px;" />
                </span>
                <span ng-if="options.fields[options.popup_trigger_index].general.data_type.value == 'm'">
                    <select name="options[popup_trigger_value]" ng-model="options.popup_trigger_value" style="width: 150px;">
                        <option value="{{$index}}" ng-repeat="pop in options.fields[options.popup_trigger_index].general.attributes.options">{{pop.name}}</option>
                    </select>
                </span>
            </span>
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Popup fields', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[popup_fields][]" multiple ng-model="options.popup_fields" nbd-select2>
                    <option value="{{field.id}}" ng-repeat="(fieldIndex, field) in allFields">{{options.fields[field.field_index].general.title.value}}</option>
                </select>
            </div>
        </div>
    </div>
</div>