<template>
    <div id="pinboard-items" class="th-pinboard-upper th-ofc-pinboard-item-upper pinboard-items-scroll">
        <!-- Placeholder (loading state) -->
        <div v-if="loading('getPinboard')" class="pinboard-loading-placeholder">
            <div v-for="n in 3" :key="'pinboard-loading-' + n"
                style="height:150px;border:1px solid #cfcfcf; background:white;border-radius:6px;margin-bottom:12px;">
            </div>
        </div>
        <draggable v-if="!loading('getPinboard') && Array.isArray(items)" v-model="items"
            :handle="'.draggable-handle'" :clone="cloneItem" ghost-class="pinboard-ghost"
            chosen-class="pinboard-chosen" drag-class="pinboard-drag" @start="onDragStart"
            @end="onDragEnd" tag="div">
            <transition-group>
                <div v-for="(item, index) in items" :key="getKey(item, index)"
                    class="row th-pinboard-item">
                    <!-- DRAG PREVIEW: thumbnail + title + close -->
                    <div v-if="item._isDragPreview" class="pinboard-drag-preview">
                        <img :src="item.photo" class="thumb" />
                        <span class="title">{{ item.title }}</span>
                        <i class="fa-solid fa-xmark close-icon"></i>
                    </div>
                    <!-- NORMAL ITEM -->
                    <template v-else>
                        <div class="pinboard col-md-12">

                            <!-- CARD ITEM -->
                            <div class="card-item">
                                <div class="card-left">
                                    <img :src="item.photo" :alt="item.title" />
                                </div>

                                <div class="card-content">
                                    <div class="card-header">
                                        <div>
                                            <h3>{{ item.title | capitalize }}</h3>
                                            <p class="type">{{ item.model_type | capitalize }}</p>
                                        </div>

                                        <div class="card-actions">
                                            <div class="text-darkgrey draggable-handle">
                                                <i class="fa fa-list"></i>
                                            </div>
                                            <div
                                                class="remove-pinboard-btn text-darkgrey border-0 bg-transparent">
                                                <i class="fa fa-times" :data-id="item.model_id"
                                                    :data-model="item.model_type"
                                                    @click.prevent="removePinboardItem(item, index)"></i>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- start pinboard item options -->
                                    <div class="th-item-product"
                                        v-if="item?.options?.variant?.item?.options?.length">
                                        <!--<span class="mb-2 th-title-20 text-success">Options:</span>-->
                                        <div class="th-item-footer">
                                            <div class="th-tag-name">
                                                <div class="th-tag"
                                                    v-for="option in item.options?.variant?.item?.options"
                                                    :key="option.product_option_id">
                                                    {{ option.option_name }}
                                                    <span
                                                        v-if="option.subOption && option.subOption.name">
                                                        -
                                                        <span
                                                            class="text-muted text-small text-success">(
                                                            {{ option.subOption.name }} )</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- PINBOARD ITEM ACCESSORIES -->
                                    <div class="th-item-product"
                                        v-if="item.accessories && item.accessories.length > 0">
                                        <span class="mb-2 th-title-20 text-success">Accessories:</span>
                                        <div class="th-item-footer">
                                            <div class="th-tag-name">
                                                <div class="th-tag"
                                                    v-for="accessory in item.accessories"
                                                    :key="accessory.product_accessories_id">
                                                    {{ accessory.title }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end pinboard item options -->



                                    <div class="card-footer">
                                        <span @click="toggleItemNote(item)" v-if="!item.comments[0]">
                                            {{ item._showNote ? '− Hide Note' : '+ Add Note' }}
                                        </span>
                                        <!-- note section start -->
                                        <div v-if="item._showNote || item.comments[0]">
                                            <div class="th-pinboard-item-edit mt-3"
                                                v-if="item.comments[0]"
                                                :data-edit-key="getKey(item, index)">
                                                <div
                                                    class="th-pinboard-edit-wrapper d-flex justify-content-between w-100">
                                                    <div class="w-100 th-pinboard-edit-content">
                                                        <div v-if="editingItems[getKey(item, index)]"
                                                            class="p-2">
                                                            <textarea
                                                                class="form-control item-comment-box border-0 p-0"
                                                                rows="1" :value="item.comments[0] || ''"
                                                                @input="updateItemComment(item, 'comments', $event.target.value)"></textarea>
                                                        </div>
                                                        <div v-else
                                                            class="p-2 text-muted th-display-pre-line th-pinboard-view-text">
                                                            {{ item.comments[0] || '' }}
                                                        </div>
                                                    </div>

                                                    <button class="btn" style="width: 100px;">
                                                        <span v-if="!editingItems[getKey(item, index)]"
                                                            @click="editItemComment(item, index)"><i
                                                                class="fa-solid fa-pencil"></i>
                                                            Edit</span>
                                                        <span v-else
                                                            @click="addPinboardItemComment(item, index)"><i
                                                                class="fa-solid fa-check"></i>
                                                            Post</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="th-pinboard-item-comment mt-3" v-else>
                                                <div class="d-flex align-items-start gap-2 cccc">
                                                    <!-- TEXTAREA -->
                                                    <textarea class="form-control item-comment-box"
                                                        placeholder="Add a Note" rows="1"
                                                        @input="updateItemComment(item, 'newComments', $event.target.value)"
                                                        :value="item.comments[0] || ''"></textarea>

                                                    <!-- POST BUTTON -->
                                                    <button class="th-btn-primary-post text-capitalize "
                                                        @click.prevent="addPinboardItemComment(item, index, true)">
                                                        Post
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end note section -->


                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            </transition-group>
        </draggable>

        <div v-if="!loading('getPinboard') && items.length === 0" class="text-center py-4">
            No items found
        </div>
    </div>
</template>