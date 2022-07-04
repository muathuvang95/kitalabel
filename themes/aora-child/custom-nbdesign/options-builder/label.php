<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-option-field <?php echo $class; ?>" data-id="<?php echo $field['id']; ?>" ng-if="nbd_fields['<?php echo $field['id']; ?>'].enable">
    <?php include( get_stylesheet_directory().'/custom-nbdesign/options-builder/field-header.php' ); ?>
    <div class="nbd-field-content">
        <div class="nbd-label-wrap">
        <?php 
            $class_cs = 'nb-col-3';
            $max_len = 0;
            foreach ($field['general']['attributes']["options"] as $key => $_attr) {
                if( $max_len < strlen($_attr['name']) ) {
                    $max_len = strlen($_attr['name']);
                }
            }
            if($max_len > 12 ) {
                $class_cs = 'nb-col-2';
            }
            foreach ($field['general']['attributes']["options"] as $key => $attr): 
                $enable_subattr = isset($attr['enable_subattr']) ? $attr['enable_subattr'] : 0;
                $attr['sub_attributes'] = isset( $attr['sub_attributes'] ) ? $attr['sub_attributes'] : array();
                $show_subattr = ($enable_subattr == 'on' && count($attr['sub_attributes']) > 0) ? true : false;
                $field['general']['attributes']["options"][$key]['show_subattr'] = $show_subattr;
        ?>
        <?php if( !isset($attr['coming_soon']) || $attr['coming_soon'] != 'on' ): ?>
        <input ng ng-change="check_valid('' , '' , '<?php echo $field['id']; ?>');updateMapOptions('<?php echo $field['id']; ?>')" value="<?php echo $key; ?>" ng-model="nbd_fields['<?php echo $field['id']; ?>'].value" name="nbd-field[<?php echo $field['id']; ?>]<?php if($show_subattr) echo '[value]'; ?>" type="radio" id='nbd-field-<?php echo $field['id'].'-'.$key; ?>' 
            <?php 
                if( isset($form_values[$field['id']]) ){
                    $fvalue = (is_array($form_values[$field['id']]) && isset($form_values[$field['id']]['value'])) ? $form_values[$field['id']]['value'] : $form_values[$field['id']];
                    checked( $fvalue, $key );
                }else{
                    checked( isset($attr['selected']) ? $attr['selected'] : 'off', 'on' ); 
                }
            ?> />
        <?php endif; ?>
        <label ng-mouseover="nb_hover_option('<?php echo $field['id']; ?>' , '<?php echo $key ?>')" ng-mouseleave="nb_leave_option('<?php echo $field['id']; ?>' , '<?php echo $key ?>')" class="nbd-label <?php echo $class_cs; ?>" for='nbd-field-<?php echo $field['id'].'-'.$key; ?>' ng-class="'<?php echo isset($attr['coming_soon']) && ( $attr['coming_soon'] == 'on' || $attr['coming_soon'] == 1 ) ? 'coming-soon' : ''; ?>'"
            nbo-disabled="!status_fields['<?php echo $field['id']; ?>'][<?php echo $key; ?>].enable" nbo-disabled-type="class" >
            <?php echo $attr['name']; ?> 
            <?php if( isset($attr['coming_soon']) && ( $attr['coming_soon'] == 'on' || $attr['coming_soon'] == 1 ) ) {
                echo '<span class="data-tip">Unavailable for now</span>';
            }?>
        </label>
        <?php endforeach; ?>
        </div>
        <div class="nbo-invalid-option" 
            ng-class="nbd_fields['<?php echo $field['id']; ?>'].valid === false ? 'active' : ''"
            ng-if="nbd_fields['<?php echo $field['id']; ?>'].valid === false">{{nbd_fields['<?php echo $field['id']; ?>'].invalidOption}} <?php _e('is not available', 'web-to-print-online-designer'); ?></div>
        <?php 
            foreach ($field['general']['attributes']["options"] as $key => $attr): 
                if( $attr['show_subattr'] ):
                    $sattr_display_type = isset( $attr['sattr_display_type'] ) ? $attr['sattr_display_type'] : 'l';
                    switch($sattr_display_type){
                        case 's':
                            $tempalte = $currentDir .'/options-builder/sattr_swatch'.$prefix.'.php';
                            $wrap_class = 'nbd-swatch-wrap';
                            break;
                        case 'l':
                            $tempalte = $currentDir .'/options-builder/sattr_label.php';
                            $wrap_class = 'nbd-label-wrap';
                            break;
                        case 'r':
                            $tempalte = $currentDir .'/options-builder/sattr_radio.php';
                            $wrap_class = 'nbd-radio';
                            break;
                        default:
                            $tempalte = $currentDir .'/options-builder/sattr_dropdown.php';
                            $wrap_class = '';
                            break;
                    }
        ?>
        <div ng-if="nbd_fields['<?php echo $field['id']; ?>'].value == '<?php echo $key; ?>'" class="nbo-sub-attr-wrap <?php echo $wrap_class; ?>">
        <?php include( $tempalte ); ?>
        </div>
        <?php endif; endforeach; ?>
    </div>
</div>

