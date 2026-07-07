<template>
<div class="mb-20 pb-0 pinboard-offcanvas-search" v-show="loggedInUser && loggedInUser.email">
    <div class="autocomplete position-relative w-100">
        <i class="fa-solid fa-search text-muted"
            style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); z-index: 11; pointer-events: none;"
            aria-hidden="true"></i>
        <input type="text" class="form-control th-choices-select z-index-10 font-size-16"
            id="choose-product-name" :placeholder="autoCompletePlaceholderText" autocomplete="off"
            :disabled="disableAutocomplete" @input="handleAutocomplete" @focus="onAutocompleteFocus"
            v-model="localFilter.searchValue" style="padding:11px 36px 11px 38px;" />
        <i class="fa fa-close hover" @click.prevent="handleClearAutocomplete"
            v-show="localFilter.searchValue"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 11;"
            role="button" aria-label="Clear search"></i>
        <ul v-show="autocompleteOpen && autocompleteSuggestions.length"
            class="dropdown-menu show pinboard-autocomplete-list w-100 shadow-sm mt-1"
            style="max-height: 260px; overflow-y: auto; z-index: 1060;">
            <li v-for="row in autocompleteSuggestions" :key="'ac-' + row.id"
                class="dropdown-item py-2 d-flex align-items-center gap-2" style="cursor: pointer;"
                @mousedown.prevent="selectAutocompleteProduct(row)">
                <img :src="row.dataSrc" :alt="row.title" width="48" height="36"
                    class="rounded flex-shrink-0" style="object-fit: cover;" />
                <span class="text-truncate small">
                    <span class="d-block fw-semibold">{{ row.title }}</span>
                    <span class="text-muted" v-if="row.sku">{{ row.sku }}</span>
                </span>
            </li>
        </ul>
    </div>
</div>
</template>