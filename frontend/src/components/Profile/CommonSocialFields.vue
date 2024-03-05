<template>
    <fieldset>
        <legend>Social Links</legend>

        <social-field
            v-for="(row, index) in socials"
            :key="index"
            v-model:row="socials[index]"
            :index="index"
            @remove="remove(index)"
        />

        <div class="buttons mb-3">
            <button
                type="button"
                class="btn btn-secondary"
                @click="add"
            >
                <icon icon="plus-circle"/>
                <span>
                    Add Social Link
                </span>
            </button>
        </div>
    </fieldset>
</template>

<script setup lang="ts">
import Icon from '~/components/Common/Icon.vue';
import {useVModel} from "@vueuse/core";
import SocialField from "~/components/Profile/SocialField.vue";

const props = defineProps({
    socials: {
        type: Array,
        default: () => {
            return [];
        }
    }
});

const emit = defineEmits(['update:socials']);

const socials = useVModel(props, 'socials', emit);

const add = () => {
    socials.value.push({
        type: 'website',
        name: null,
        value: null
    });
};

const remove = (index) => {
    socials.value.splice(index, 1);
};
</script>
