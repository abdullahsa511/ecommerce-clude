const productConfiguratorStore = new Vuex.Store({
    state: {
        product: {},
        products: [],
        modelData: [],
        accessories: [],
        selectedAccessories: [],
        variants: [],
        selectedVariant: {},
        selectedGroup: {},
        selectedItems: {},
        selectedItem: {},
        selectedOptions: {},
        finishesDataByGrade: [],
        tags: [],
        textiles: [],
        selectedBoxOption: {},
        totalQuantity: 1,
        loading: false,
        error: null,
        hasMore: true,
        // Cart object structure for product configurator cart operations
        cartStatus: false,
        cartObject: {
            quantity: 0,
            product_id: null,
            product_code: null,
            description: null,
            image: null,
            quote_image: null,
            variant: {
                variant_id: null,
                item: {
                    item_id: null,
                    option: [
                        {
                            product_option_group_id: null,
                            product_option_id: null,
                            option_name: null,
                            option_image: null,
                            subOption:{
                                id: null,
                                name: null,
                                image: null
                            }
                        }
                    ]
                }
            },
            accessories: []
        },
        // PRODUCT DESK STARTED HERE
    },
    
    mutations: {
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        SET_MODEL_DATA(state, modelData) {
            state.modelData = modelData ? modelData : [];
        },
        SET_ACCESSORIES(state, accessories) {
            state.accessories = accessories ? accessories : [];
        },
        SET_FILTER_LOADING(state, filterLoading) {
            state.filterLoading = filterLoading;
        },
        SET_RESET_FILTER(state, resetFilter) {
            state.resetFilter = resetFilter;
        },
        SET_ERROR(state, error) {
            state.error = error;
        },
        SET_PRODUCT(state, product) {
            state.product = product ? product : {};
        },
        SET_PRODUCTS(state, products) {
            state.products = products ? products : [];
        },
        SET_VARIANTS(state, variants) {
            state.variants = variants.map((variant) => {
                variant = { ...variant };
                variant.image = JSON.parse(variant.image || '[{}]')[0].objectURL;
                return variant;
            });
        },
        SET_SELECTED_VARIANT(state, variant) {
            state.selectedVariant = variant ? { ...variant } : {};

        },
        SET_SELECTED_ITEMS(state, items) {
            state.selectedItems = items ? items.map(item => ({ ...item })) : [];
        },
        SET_SELECTED_ITEM(state, item) {
            state.selectedItem = item ? { ...item } : {};
        },
        SET_SELECTED_OPTIONS(state, options) {
            state.selectedOptions = options?.map(opt => ({ ...opt })) || [];
        },
        UPDATE_SELECTED_OPTIONS(state, option){
            //find the option index in the selected options by option group id
            const optionIndex = state.selectedOptions.findIndex(opt => opt.product_option_group_id === option.product_option_group_id);
            if (optionIndex !== -1) {
                state.selectedOptions.splice(optionIndex, 1, option);
            } else {
                state.selectedOptions.push(option);
            }
        },
        SET_FINISHES_DATA_BY_GRADE(state, data) {
            state.finishesDataByGrade = data?.map(opt => ({ ...opt })) || [];
        },
        SET_SELECTED_BOX_OPTION(state, item) {
            state.selectedBoxOption = item ? { ...item } : {};
        },
        SET_CART_STATUS(state, cartStatus) {
            state.cartStatus = cartStatus ? true : false;
        },
        SET_SELECTED_ACCESSORIES(state, accessories) {
            state.selectedAccessories = accessories ? accessories : [];
        },
        SET_TEXTILES_BY_TYPE(state, {type, data}) {
            // Vue cannot detect property addition like state.textiles[type] = ...
            // Solution: replace the whole textiles object to trigger reactivity/getters
            state.textiles = {
                ...state.textiles,
                [type]: data ? data : []
            };
        }
    },
    
    actions: {
        async loadProductConfigurator({ commit, state, dispatch }, options = {}) {
            const {
                product = null,
                configuration = [],
                modelData = null,
                accessories = null
            } = options;

            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            try {
                // console.log('configuration in loadProductConfigurator', configuration);
               commit('SET_PRODUCT', product||{});
               commit('SET_CART_STATUS', false);
               //set download model data
               commit('SET_MODEL_DATA', modelData||[]);
               //set accessories
               commit('SET_ACCESSORIES', accessories||[]);

               commit('SET_VARIANTS', configuration||[]);
                // set default variant is default variant or first variant in the payload
                const defaultVariant = configuration?.find(v => v.is_default === 1) || configuration[0];
                commit('SET_SELECTED_VARIANT', defaultVariant||{});
                // set all items
                const allItems = Object.values(defaultVariant?.items||{});
                commit('SET_SELECTED_ITEMS', allItems||[]);

                const defaultItem =  allItems?.find(item => item.is_default === 1) || allItems[0];
                commit('SET_SELECTED_ITEM', defaultItem||{});
                // console.log('selected item in loadProductConfigurator', defaultItem);

                const defaultOptions = defaultItem?.options||[];
                commit('SET_SELECTED_OPTIONS', defaultOptions);

                // check if product is already in pinboard
                const isProductInPinboard = await dispatch('checkProductPinboard', product?.product_id);
                if(isProductInPinboard){
                    commit('SET_CART_STATUS', true);
                }

                commit('SET_LOADING', false);
            } catch (error) {
                console.error('Error loading product configurator:', error);
                commit('SET_ERROR', error.message || 'Failed to load product configurator');
                commit('SET_LOADING', false);
                throw error;
            }
        },
        selectVariant({ commit, state }, variant) {
            //On selecting a vriant => dispatch this configuration.js logic
            // #1 Change all groups with its options
            const selectedVariant = state.variants.find(v => v.product_variant_id === variant.product_variant_id);
            if (selectedVariant) {
                // select the variant
                commit('SET_SELECTED_VARIANT', selectedVariant);

                const allItems = Object.values(selectedVariant.items);
                commit('SET_SELECTED_ITEMS', allItems);

                const defaultItem =  allItems.find(item => item.is_default === 1) || allItems[0];
                commit('SET_SELECTED_ITEM', defaultItem);
                // console.log('selected item in selectVariant', defaultItem);

                const defaultOptions = defaultItem.options;
                commit('SET_SELECTED_OPTIONS', defaultOptions);
            }
                
        },
        selectOption({ commit, state }, option) {
            commit('UPDATE_SELECTED_OPTIONS', option);
            // //Find item from state.selectedItems by matching all options of each item.options with the state.selectedOptions
            const findMatchingItem = () =>
                state.selectedItems.find(item =>
                    item.options.every(o =>
                        state.selectedOptions.some(opt =>
                            o.product_option_group_id === opt.product_option_group_id &&
                            o.product_option_id === opt.product_option_id
                        )
                    )
                );
            let matchingItem = findMatchingItem();
            if(!matchingItem){
                let changingOptionGroup = null;
                //Find the previous option group if exist and if not the find the next option group as changingOptionGroup

                const groups = state.selectedVariant?.productOptionGroups || [];
                const changedGroupIdx = groups.findIndex(
                    g => g.product_option_group_id === option.product_option_group_id
                );
                if (changedGroupIdx > 0) {
                    changingOptionGroup = groups[changedGroupIdx - 1];
                } else if (changedGroupIdx === 0 && groups.length > 1) {
                    changingOptionGroup = groups[1];
                }

                //Currently UPDATE_SELECTED_OPTIONS muation is manaing the selected options which is managing state.selectedOptions

                const selectedOptions = state.selectedOptions;

                //in the changingOptionGroup find the currently selected option and then find the previous option or next option in the changingOptionGroup and change to that option to find matching item 

                const productOptions = changingOptionGroup ? changingOptionGroup.productOptions || [] : [];
                const selForGroup = changingOptionGroup
                    ? selectedOptions.find(o => o.product_option_group_id === changingOptionGroup.product_option_group_id)
                    : null;
                let curIdx = selForGroup
                    ? productOptions.findIndex(o => o.product_option_id === selForGroup.product_option_id)
                    : 0;
                if (curIdx < 0) {
                    curIdx = 0;
                }

                // Keep continue until find the matching item

                if (changingOptionGroup) {
                    for (let i = 0; i < productOptions.length && !matchingItem; i++) {
                        if (i === curIdx) {
                            continue;
                        }
                        const opt = productOptions[i];
                        commit('UPDATE_SELECTED_OPTIONS', {
                            ...opt,
                            product_option_group_id: changingOptionGroup.product_option_group_id
                        });
                        matchingItem = findMatchingItem();
                    }
                }
            }
            if (matchingItem) {
                commit('SET_SELECTED_ITEM', matchingItem);
            }
            // console.log('selected item in selectOption', matchingItem);
            // api calling for get the product configuration

            // (async () => {
            //     try {
            //         const svcModule = await import('../services/productConfiguratorService.js');
            //         const response = await svcModule.default.getFinishesDataByGrade(option.option_name);
            //         commit('SET_FINISHES_DATA_BY_GRADE', response);
            //     } catch (error) {
            //         console.error('Error getting product configuration:', error);
            //     }
            // })();
        },
        selectItem({ commit, state }, item) {
            // after selecting an item => dispatch this configuration.js logic

            // #1 Change item group and option 
            // console.log('selectItem store', item);
            commit('SET_SELECTED_ITEM', {...item});
            // console.log('selected item in selectItem', item);
            // #2 Change select option and option group
            const selectedOptions = item.options.map(opt => ({ ...opt }));
    
            commit('SET_SELECTED_OPTIONS', selectedOptions);
        },
        selectBoxOption({ commit, state }, item) {
            // console.log('selectBoxOption store', item);
            commit('SET_SELECTED_BOX_OPTION', item);
        },
        getTotalQuantity({ commit, state }, quantity) {
            state.totalQuantity = quantity;
            // commit('SET_TOTAL_QUANTITY', quantity);
        },
        addToPinboard({ commit, state }, cartObject) {
            (async () => {
                try {
                    const preparedCartObject = {
                        id: cartObject.product_id,
                        model_type: 'product',
                        description: cartObject.description || '',
                        title: cartObject.product_code || '',
                        quantity: cartObject.quantity ?? 1,
                        unit_price: cartObject.unit_price ?? 0,
                        photo: cartObject.quote_image || null,
                        comments: cartObject.comments || null,
                        language_id: 1,
                        _raw_data: cartObject,
                    };

                    // get accessories from cartObject
                    const accessories = state.selectedAccessories||[];
                    cartObject.accessories = [];
                    cartObject.quantity = 0; // only set in options
                    preparedCartObject.options = cartObject;
                    preparedCartObject.accessories = accessories;

                    // const svcModule = await import('../services/pinboardService.js');
                    // const response = await svcModule.default.addToPinboardConfigurator(preparedCartObject);
                    const pinboardStoreModule = await import('../store/pinboardStore.js');
                    const pinboardStore = pinboardStoreModule.default;
                    const response = await pinboardStore.dispatch('addToPinboard', preparedCartObject);

                    if (response && response.success) {
                        commit('SET_CART_STATUS', true);
                    }
                } catch (error) {
                    console.error('Error adding to pinboard:', error);
                }
            })();
        },
        checkProductPinboard({ commit, state }, productId) {
            let pinboard = JSON.parse(
                localStorage.getItem('pinboardItems') || '{"pinboard_items":[]}'
            );
        
            pinboard.pinboard_items ||= [];
        
            const index = pinboard.pinboard_items.findIndex(
                item =>
                    item.model_id === productId &&
                    item.model_type === 'product'
            );
        
            const existingItem = index !== -1 ? pinboard.pinboard_items[index] : null;
            if(existingItem){
                return true;
            }else{
                return false;
            }
        },
        toggleAccessories({ commit, state }, accessories) {
            commit('SET_SELECTED_ACCESSORIES', accessories||[]);
        },
        getTextilesDataByGrade({ commit, state }, grade = '') {
            (async () => {
                try {
                    const svcModule = await import('../services/productConfiguratorService.js');
                    const response = await svcModule.default.getTextilesDataByType(grade);
                    commit('SET_TEXTILES_BY_TYPE', {type: grade, data: response||[]});
                } catch (error) {
                    console.error('Error getting tags:', error);
                }
            })();
        }
    },
    
    getters: {
        loading: state => state.loading,
        error: state => state.error,
        modelData: state => state.modelData,
        accessories: state => state.accessories,
        loadedProductConfigurator: state => state.loadedProductConfigurator,
        currentProduct: state => state.product,
        currentVariants: state => state.variants,
        activeVariant: state => state.selectedVariant,
        activeGroups: state => {
            return state.selectedVariant?.productOptionGroups?.map(group => {
                return {
                    ...group,
                    productOptions: group.productOptions.map(option => {
                        return { ...option, selected: state.selectedOptions?.some(opt => opt.product_option_id === option.product_option_id) || false }
                    })
                }
            }) || [];
        },
        activeItem: state => state.selectedItem,
        activeItems: state => Object.values(state.selectedVariant?.items || []),
        activeOptions: state => state.selectedOptions,
        cartObject: state => {
            return {
                quantity: state.totalQuantity,
                product_id: state.product?.product_id,
                product_code: state.product?.product_code,
                description: state.product?.description,
                image: state.product?.image,
                quote_image: state.selectedItem?.quote_image,
                variant: {
                    variant_id: state.selectedVariant?.product_variant_id,
                    item: {
                        item_id: state.selectedItem?.item_code,
                        options: state.selectedOptions?.map(opt => ({
                            product_option_group_id: opt.product_option_group_id,
                            product_option_id: opt.product_option_id,
                            option_name: opt.option_name,
                            subOption: opt.option_name.includes('Fabric') ? {
                                id: state.selectedBoxOption?.id,
                                name: state.selectedBoxOption?.name,
                                image: state.selectedBoxOption?.image
                            } : {}

                        }))
                    }
                },
                accessories: state.selectedAccessories
            }
        },
        finishesDataByGrade: state => state.finishesDataByGrade,
        selectedBoxOption: state => state.selectedBoxOption,
        totalQuantity: state => state.totalQuantity,
        cartStatus: state => state.cartStatus,
        selectedAccessories: state => state.selectedAccessories,
        textiles: state => state.textiles,
       
    }
});

export default productConfiguratorStore;
