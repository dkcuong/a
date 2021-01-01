<?php 
    // Cat added by Tony
    $arr_title = array(
        'name' => '<font color="red">*</font>' . __('name'),
        'full_name' => '<font color="red">*</font>' . __('full_name'),
        'title' => '<font color="red">*</font>' . __('title'),
        'description' => __('description'),
        'content' => '<font color="red">*</font>' . __('content'),
        'terms' => __('terms'),
        'privacy' => __('privacy'),
        'address' => __('address'),
        'opening' => __('opening'),
        'remarks' => __('remark'),
        'lang_info' => __d('movie','lang_info'),
        'lang_movie' => __d('movie','lang_movie')
    );
    $arr_language_tabs = array(
        'zho' => __('zho_language'),
        'chi' => __('chi_language'),
        'eng' => __('eng_language'),
    );
?>

<?php  if ( (isset($languages_list) && !empty($languages_list)) ): ?>
    <?php 	
        if ($languages_edit_data) {
            $languages_edit_data = Hash::combine($languages_edit_data,'{n}.language','{n}');
        }	
    ?>
    <div class="form-group">
        <div role="tabpanel">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <?php $flag = 1;

                foreach ($languages_list as $language): ?>
                    <li role="presentation"  class="<?php if ($flag == 1){ echo ('active'); } ?> ">

                        <?php $flag = 0    ?>
                    
                        <a href="#<?=($language); ?>" aria-controls="tab" role="tab" data-toggle="tab">
                            <?= isset($arr_language_tabs[$language]) ? $arr_language_tabs[$language] : '' ?>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="tab-content">
            <?php $flag = true;
            
            $key = -1;
            foreach ($languages_list as $language): 
                $key++;
            ?>
                
                <div role="tabpanel" class="tab-pane tab-panel-language <?php if ($flag == true) echo ('active'); ?>" id="<?=($language); ?>">

                    <?php 
                        $flag = false;
                    if (isset($languages_edit_data[$language]))
                    {
                        foreach ($language_input_fields as $field) 
                        {
                            $attr = array(
                                'class' => 'form-control language-' . $field,
                                'style' => 'margin-bottom:15px',
                                'div' => 'col-xs-12',
                                'value' => $languages_edit_data[ $language ][ $field ],
                            );

                            if ( strpos($field, 'id') !== false || strpos($field, 'language') !== false ) {
                                $attr['type'] = 'hidden';
                            }else if ( 
                                ( strpos($field, 'about') !== false  ) ||
                                ( strpos($field, 'terms') !== false  ) ||
                                ( strpos($field, 'content') !== false  ) ||
                                ( strpos($field, 'privacy') !== false  ) ||
                                ( strpos($field, 'description') !== false  )
                                ) {
                                $attr['class'] = 'form-control ckeditor';
                                $attr['type'] = 'textarea';
                            }

                            if (isset($arr_title[$field])) {
                                $attr['label'] = $arr_title[$field];
                            }
                            
                            if ($field == "name"  || $field == "full_name" || $field == "content" || $field == "title")
                            {
                                $attr['required'] = 'true';
                            }
                            echo '<div class="row">' . $this->Form->input($languages_model . '.' . $key . '.' . $field, $attr) . '</div>';
                        }
                    }
                    else
                    {
                        
                        foreach ($language_input_fields as $field) 
                        {
                        
                            $attr = array(
                                'class' => 'form-control',
                                'style' => 'margin-bottom:15px',
                                'div' => 'col-xs-12',
                            );
                        

                            if ( strpos($field, 'id') !== false ) {
                                $attr['type'] = 'hidden';
                            }else if ( strpos($field, 'language') !== false ) {
                                $attr['type'] = 'hidden';
                                $attr['value'] = $language;
                            }else if ( 
                                ( strpos($field, 'about') !== false  ) ||
                                ( strpos($field, 'terms') !== false  ) ||
                                ( strpos($field, 'content') !== false  ) ||
                                ( strpos($field, 'privacy') !== false  ) ||
                                ( strpos($field, 'description') !== false  )
                                ) {
                                $attr['class'] = 'form-control ckeditor';
                                $attr['type'] = 'textarea';
                            }

                            if (isset($arr_title[$field])) {
                                $attr['label'] = $arr_title[$field];
                            }
                            
                            if ($field == "name" || $field == "full_name" || $field == "content" || $field == "title")
                            {
                                $attr['required'] = 'true';
                            }

                            echo '<div class="row">' . $this->Form->input($languages_model . '.' . $key . '.' . $field, $attr) . '</div>';
                        }
                        
                    }
                    ?>
                </div> <!-- close tabpanel -->
            <?php endforeach ?>
        </div> <!-- close tab-content -->
    </div>
<?php endif; ?>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $('.language-name').on('keyup', function(){
            let full_name_element = $(this).closest('tab-panel-language').find('language-full_name').first();
            $(full_name_element).val($(this).val());
        })
    })
</script>