<template>
    <div class="relative bg-white pl-8 mb-4 parent-with-flex" :id="group.key" :style="{ maxWidth: field.maxWidth + '%' }">
        <div class="w-full">
            <div class="border-t border-b border-r border-l border-60 rounded-b-lg flexible-group">
                <component
                    v-for="(item, index) in group.fields"
                    :key="index"
                    :is="'form-' + item.component"
                    :resource-name="resourceName"
                    :resource-id="resourceId"
                    :resource="resource"
                    :field="item"
                    :errors="validationErrors"
                    :class="{ 'remove-bottom-border': index == group.fields.length - 1 }"
                />
            </div>
        </div>
        <div class="absolute z-10 bg-white border-t border-l border-b border-60 rounded-l pin-l pin-t w-8" >
            <button class="group-control btn border-r border-40 w-8 h-8 block" title="Move up" @click.prevent="moveUp">
                <icon type="arrow-up" view-box="0 0 8 4.8" width="10" height="10" />
            </button>
            <button class="group-control btn border-t border-r border-40 w-8 h-8 block" title="Move down" @click.prevent="moveDown">
                <icon type="arrow-down" view-box="0 0 8 4.8" width="10" height="10" />
            </button>
            <button class="group-control btn border-t border-r border-40 w-8 h-8 block" title="Delete" @click.prevent="confirmRemove">
                <icon type="delete" view-box="0 0 20 20" width="16" height="16" />
            </button>
            <div v-if="removeMessage" class="confirm-message">
                <span v-if="field.confirmRemoveMessage">{{ field.confirmRemoveMessage }}</span>
                <button @click.prevent="remove" class="text-danger btn mx-1 focus:outline-none">{{ field.confirmRemoveYes }}</button>
                <button @click.prevent="removeMessage=false" class="text-80 btn focus:outline-none">{{ field.confirmRemoveNo }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { BehavesAsPanel } from 'laravel-nova'

export default {
    mixins: [BehavesAsPanel],

    props: ['validationErrors', 'group', 'field'],

    data() {
        return {
            removeMessage: false,
        };
    },

    methods: {
        /**
         * Move this group up
         */
        moveUp() {
            this.$emit('move-up');
        },

        /**
         * Move this group down
         */
        moveDown() {
            this.$emit('move-down');
        },

        /**
         * Remove this group
         */
        remove() {
            this.$emit('remove');
        },

        /**
         * Confirm remove message
         */
        confirmRemove() {
            if(this.field.confirmRemove){
                this.removeMessage = true;
            } else {
                this.remove()
            }
        },
    },

    mounted() {
        let child = [...document.getElementsByClassName('parent-with-flex')];
        let parents = [];

        child.forEach(function (el) {
            parents.push(el.parentNode);
        });

        let uniquePatent = new Set(parents);

        uniquePatent.forEach((el) => {
            el.style.display = "flex";
            el.style.flexWrap = "wrap";
            el.style.justifyContent = "space-evenly";
        })
    },
}
</script>

<style>
    .group-control:focus {
        outline: none;
    }
    .group-control path {
        fill: #B7CAD6;
        transition: fill 200ms ease-out;
    }
    .group-control:hover path {
        fill: var(--primary);
    }
    .confirm-message{
        position: absolute;
        overflow: visible;
        right: 35px;
        bottom: 0;
        width: auto;
        border-radius: 4px;
        padding: 5px;
        border: 1px solid #B7CAD6;
        fill: var(--20);
        white-space: nowrap;
    }
    [dir=rtl] .confirm-message{
        right: auto;
        left: 35px;
    }
    .flexible-group {
        display: flex;
        flex-wrap: wrap;
    }
</style>
