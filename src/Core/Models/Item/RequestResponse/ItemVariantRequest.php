<?php

namespace App\Core\Models\Item\RequestResponse;

use stdClass;

class ItemVariantRequest {

    public stdClass $itemVariant;

    /** @var stdClass[] */
    public array $itemOptionGroups;

    /** @var stdClass[] */
    public array $itemOptions;

    /** @var stdClass[] */
    public array $errors;

    public bool $isValid = true;


    public function __construct(array $data, array $groups = [], array $options = []){
        $this->itemVariant = new stdClass();
        $this->itemVariant->product_id = $data['product_id']??null; 
        $this->itemVariant->item_id = $data['item_id']??null; 
        $this->itemVariant->product_variant_id = $data['product_variant_id']??null; 
        // $this->itemVariant->variant_name = $data['variant_name']??null;
        // $this->itemVariant->variant_description = $data['variant_description']??null; 
        // $this->itemVariant->variant_item_id = $data['variant_item_id']??null; 
        $this->itemVariant->sort_order = $data['sort_order']??null;
        $this->itemVariant->active_status = $data['active_status']??null;

        foreach($data['itemOptionGroups'] as $key => $itemOptionGroup){
            $group = new stdClass();
            // $group->item_option_group_id = $itemOptionGroup['item_option_group_id']??null;
            $group->product_id = $data['product_id']??null;
            $group->product_variant_id = $data['product_variant_id']??null;
            // if option group name is changed then product_option_group_id should be 0
            $group->product_option_group_id = $itemOptionGroup['item_option_group_id']??null;
            $group->option_group_name = $itemOptionGroup['option_group_name']??null;
            $group->sort_order = $itemOptionGroup['sort_order']??0;
            $group->description = $itemOptionGroup['description']??null;
            $group->active_status = $itemOptionGroup['active_status']??1;
            $uniqueGroupKey = $group->product_id.'-'.$group->product_variant_id.'-'.$group->option_group_name??$key;

            //Invalid Group name
            if(!isset($group->option_group_name) || empty($group->option_group_name)){
                $this->errors[$uniqueGroupKey]['group_error'] = 'Option group name is required';
                $this->isValid = false;
                continue;
            }
            //Duplicate Group name
            if(isset($this->itemOptionGroups[$uniqueGroupKey])){
                $this->errors[$uniqueGroupKey]['group_error'] = 'Option group name already exists';
                $this->isValid = false;
                continue;
            }
            //Option group with the id is not found
            if(isset($group->product_option_group_id) && !isset($groups[$group->product_option_group_id])){
                $this->errors[$uniqueGroupKey]['group_error'] = $group->option_group_name . ' - Option group not found';
                $this->isValid = false;
                continue;
            }
            //Option group with the id is not the same as the one sent
            if(isset($groups[$group->product_option_group_id]) && $groups[$group->product_option_group_id] != $group->option_group_name){
                $this->errors[$uniqueGroupKey]['group_error'] = $group->option_group_name . ' - Option group with wrong identifier sent';
                $this->isValid = false;
                continue;
            }
            
            
            $this->itemOptionGroups[$uniqueGroupKey] = $group;

            foreach($itemOptionGroup['itemOptions'] as $itemOption){
                $option = new stdClass();
                $option->item_option_id = $itemOption['item_option_id']??null; // if option name is changed then item_option_id should be 0
                $option->item_id = $data['item_id']??null;
                $option->product_id = $data['product_id']??null;
                $option->product_variant_id = $data['product_variant_id']??null;
                $option->product_option_group_id = $itemOptionGroup['item_option_group_id']??null; // TESTING
                // $option->option_group_name = $itemOptionGroup['product_option_group_id']??null;
                $option->product_option_id = $itemOption['product_option_id']??null;
                $option->option_name = $itemOption['option_name']??null;
                $option->description = $itemOption['option_description']??null;
                $option->option_description = $itemOption['option_description']??null;
                $option->is_default = $itemOption['is_default']??null;
                $option->type_id = $itemOption['type_id']??null;
                $option->price = $itemOption['price']??null;
                $option->sort_order = $itemOption['sort_order']??null;
                $option->active_status = $itemOption['active_status']??null;
                $option->product_option_group_unique_key = $group->product_id.'-'.$group->product_variant_id.'-'.$group->option_group_name;
                $option->product_option_unique_key = $option->product_id.'-'.$option->product_variant_id.'-'.$option->product_option_group_id.'-'.$option->option_name;
                $uniqueOptionKey = $option->product_id.'-'.$option->product_variant_id.'-'.$option->product_option_group_id.'-'.$option->option_name.'-'.$option->item_id;

                //Invalid Option name
                if(!isset($option->option_name) || empty($option->option_name)){
                    $this->errors[$uniqueGroupKey][$uniqueOptionKey]['option_error'] = 'Option name is required';
                    $this->isValid = false;
                    continue;
                }
                //Duplicate Option name
                if(isset($this->itemOptions[$uniqueOptionKey])){
                    $this->errors[$uniqueGroupKey][$uniqueOptionKey]['option_error'] = 'Option name already exists';
                    $this->isValid = false;
                    continue;
                }
                //Option with the id is not found
                if(isset($option->product_option_id) && !isset($options[$option->product_option_id])){
                    $this->errors[$uniqueGroupKey][$uniqueOptionKey]['option_error'] = $option->option_name . ' - Option not found';
                    $this->isValid = false;
                    continue;
                }
                //Option with the id is not the same as the one sent
                if(isset($options[$option->product_option_id]) && $options[$option->product_option_id] != $option->option_name){
                    $this->errors[$uniqueGroupKey][$uniqueOptionKey]['option_error'] = $option->option_name . ' - Option with wrong identifier sent';
                    $this->isValid = false;
                    continue;
                }
                $this->itemOptions[$uniqueOptionKey] = $option;
            }
        }
    }

}